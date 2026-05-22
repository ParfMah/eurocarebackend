<?php
/**
 * =====================================================
 * EUROCARE HUMANITAIRE - Contrôleur Authentification
 * =====================================================
 * Fichier : app/controllers/AuthController.php
 * Description : Gestion complète de l'authentification :
 *   inscription, connexion, déconnexion, vérification
 *   email, réinitialisation de mot de passe.
 * =====================================================
 */

defined('BASEPATH') or die('Accès direct interdit.');

class AuthController
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // =====================================================
    // CONNEXION
    // =====================================================
    public function connexion(array $params = []): void
    {
        Helpers::view('auth/connexion', [
            'pageTitle' => 'Connexion',
            'metaDesc'  => 'Connectez-vous à votre espace EuroCare Humanitaire.',
            'bodyClass' => 'page-auth',
            'layout'    => 'auth',
        ], 'auth');
    }

    public function connecter(array $params = []): void
    {
        // Validation CSRF
        if (!Session::validateCsrfToken(Security::input('_csrf_token'))) {
            Helpers::redirectWithFlash('/connexion', 'erreur', 'Session expirée. Veuillez réessayer.');
        }

        $email      = mb_strtolower(trim(Security::input('email')));
        $password   = $_POST['password'] ?? '';
        $rememberMe = isset($_POST['remember_me']);

        if (!Security::validateEmail($email) || empty($password)) {
            Helpers::redirectWithFlash('/connexion', 'erreur', 'Veuillez remplir tous les champs.');
        }

        $result = Auth::attempt($email, $password, $rememberMe);

        if (!$result['success']) {
            Session::flash('erreur', $result['message']);
            Helpers::redirect('/connexion');
        }

        // Rediriger vers la page demandée ou le dashboard
        $redirect = Session::get('redirect_after_login', '');
        Session::remove('redirect_after_login');

        $role = Auth::role();
        $defaultRedirect = match($role) {
            ROLE_ADMIN, ROLE_MODERATEUR => '/admin',
            default                     => '/tableau-de-bord',
        };

        Session::flash('succes', 'Bienvenue, ' . Security::e(Auth::user()['prenom']) . ' !');
        Helpers::redirect($redirect ?: $defaultRedirect);
    }

    // =====================================================
    // DÉCONNEXION
    // =====================================================
    public function deconnecter(array $params = []): void
    {
        Auth::logout();
        Helpers::redirectWithFlash('/', 'succes', 'Vous avez été déconnecté avec succès.');
    }

    // =====================================================
    // INSCRIPTION
    // =====================================================
    public function inscription(array $params = []): void
    {
        // Récupérer le rôle souhaité (bénéficiaire, donateur, partenaire)
        $rolePreselect = Security::input('type', 'get', 'donateur');
        $rolesAutorisés = [ROLE_DONATEUR, ROLE_BENEFICIAIRE, ROLE_PARTENAIRE];
        if (!in_array($rolePreselect, $rolesAutorisés, true)) {
            $rolePreselect = ROLE_DONATEUR;
        }

        Helpers::view('auth/inscription', [
            'pageTitle'     => 'Créer un compte',
            'metaDesc'      => 'Créez votre compte sur EuroCare Humanitaire.',
            'bodyClass'     => 'page-auth',
            'rolePreselect' => $rolePreselect,
        ], 'auth');
    }

    public function inscrire(array $params = []): void
    {
        if (!Session::validateCsrfToken(Security::input('_csrf_token'))) {
            Helpers::redirectWithFlash('/inscription', 'erreur', 'Session expirée.');
        }

        // Récupération des champs
        $prenom  = Security::input('prenom');
        $nom     = Security::input('nom');
        $email   = mb_strtolower(trim(Security::input('email')));
        $password= $_POST['password'] ?? '';
        $passConf= $_POST['password_confirm'] ?? '';
        $role    = Security::input('role', 'post', ROLE_DONATEUR);
        $pays    = Security::input('pays');
        $cgv     = isset($_POST['cgv']);

        // Validation
        $errors = [];
        if (mb_strlen($prenom) < 2) $errors[] = 'Le prénom est requis (min. 2 caractères).';
        if (mb_strlen($nom) < 2)    $errors[] = 'Le nom est requis (min. 2 caractères).';
        if (!Security::validateEmail($email)) $errors[] = 'Adresse email invalide.';
        if (!in_array($role, [ROLE_DONATEUR, ROLE_BENEFICIAIRE, ROLE_PARTENAIRE], true)) {
            $errors[] = 'Type de compte invalide.';
        }
        if (!$cgv) $errors[] = 'Vous devez accepter les conditions d\'utilisation.';

        $pwdCheck = Security::validatePassword($password);
        if (!$pwdCheck['valid']) $errors = array_merge($errors, $pwdCheck['errors']);
        if ($password !== $passConf) $errors[] = 'Les mots de passe ne correspondent pas.';

        // Vérifier que l'email n'existe pas déjà
        if (empty($errors)) {
            $exists = $this->db->count('users', ['email' => $email]);
            if ($exists) $errors[] = 'Cette adresse email est déjà utilisée.';
        }

        if (!empty($errors)) {
            Session::flash('erreur', implode('<br>', $errors));
            Helpers::redirect('/inscription?type=' . $role);
        }

        // Création de l'utilisateur
        try {
            $this->db->beginTransaction();

            $userId = $this->db->insert('users', [
                'uuid'            => Security::generateUuid(),
                'email'           => $email,
                'password'        => Security::hashPassword($password),
                'role'            => $role,
                'statut'          => STATUT_USER_EN_ATTENTE,
                'prenom'          => mb_substr($prenom, 0, 100),
                'nom'             => mb_substr($nom, 0, 100),
                'pays'            => mb_substr($pays, 0, 100),
                'email_verifie'   => 0,
                'ip_inscription'  => Security::getIp(),
                'cree_le'         => date('Y-m-d H:i:s'),
            ]);

            if (!$userId) throw new RuntimeException('Erreur création utilisateur');

            // Générer et enregistrer le token de vérification email
            $token   = Security::generateToken(64);
            $expires = date('Y-m-d H:i:s', time() + EMAIL_VERIFY_EXPIRE);

            $this->db->insert('verification_emails', [
                'user_id'   => $userId,
                'token'     => $token,
                'expire_le' => $expires,
                'cree_le'   => date('Y-m-d H:i:s'),
            ]);

            $this->db->commit();

            // Envoyer l'email de vérification
            $this->envoyerEmailVerification($email, $prenom, $token);

            Logger::log(ACTION_REGISTER, 'users', $userId, null, ['email' => $email, 'role' => $role]);

            Helpers::redirectWithFlash('/connexion', 'succes',
                'Compte créé ! Veuillez vérifier votre boîte email pour activer votre compte.');

        } catch (Throwable $e) {
            $this->db->rollBack();
            Logger::logToFile('ERROR', 'Erreur inscription : ' . $e->getMessage());
            Helpers::redirectWithFlash('/inscription', 'erreur', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    // =====================================================
    // VÉRIFICATION EMAIL
    // =====================================================
    public function verifierEmail(array $params = []): void
    {
        $token = $params['token'] ?? '';

        if (empty($token)) {
            Helpers::redirectWithFlash('/connexion', 'erreur', 'Token de vérification invalide.');
        }

        $row = $this->db->query(
            'SELECT ve.*, u.statut, u.prenom FROM verification_emails ve
             JOIN users u ON u.id = ve.user_id
             WHERE ve.token = ? AND ve.utilise = 0 AND ve.expire_le > NOW() LIMIT 1',
            [$token]
        )->fetch();

        if (!$row) {
            Helpers::redirectWithFlash('/connexion', 'erreur',
                'Lien de vérification invalide ou expiré. Veuillez vous réinscrire ou demander un nouveau lien.');
        }

        // Activer le compte
        $this->db->update('users', [
            'email_verifie' => 1,
            'statut'        => STATUT_USER_ACTIF,
        ], ['id' => $row['user_id']]);

        $this->db->update('verification_emails', ['utilise' => 1], ['id' => $row['id']]);

        Logger::log(ACTION_EMAIL_VERIFY, 'users', $row['user_id']);

        Helpers::redirectWithFlash('/connexion', 'succes',
            "Bienvenue {$row['prenom']} ! Votre email est vérifié. Vous pouvez maintenant vous connecter.");
    }

    // =====================================================
    // MOT DE PASSE OUBLIÉ
    // =====================================================
    public function mdpOublie(array $params = []): void
    {
        Helpers::view('auth/mdp_oublie', [
            'pageTitle' => 'Mot de passe oublié',
            'bodyClass' => 'page-auth',
        ], 'auth');
    }

    public function mdpEnvoyer(array $params = []): void
    {
        if (!Session::validateCsrfToken(Security::input('_csrf_token'))) {
            Helpers::redirectWithFlash('/mot-de-passe-oublie', 'erreur', 'Session expirée.');
        }

        $email = mb_strtolower(trim(Security::input('email')));

        if (!Security::validateEmail($email)) {
            Helpers::redirectWithFlash('/mot-de-passe-oublie', 'erreur', 'Adresse email invalide.');
        }

        // Réponse identique peu importe si l'email existe (protection énumération)
        $successMsg = 'Si cette adresse existe, vous recevrez un email de réinitialisation dans quelques minutes.';

        $user = $this->db->findOne('users', ['email' => $email], 'id, prenom, statut');

        if ($user && $user['statut'] === STATUT_USER_ACTIF) {
            // Vérifier le rate limiting (pas plus d'1 demande par 15 minutes)
            $recent = $this->db->query(
                'SELECT COUNT(*) FROM reinitialisation_mdp WHERE user_id = ? AND cree_le > DATE_SUB(NOW(), INTERVAL 15 MINUTE)',
                [$user['id']]
            )->fetchColumn();

            if (!$recent) {
                $token   = Security::generateToken(64);
                $expires = date('Y-m-d H:i:s', time() + PASSWORD_RESET_EXPIRE);

                $this->db->insert('reinitialisation_mdp', [
                    'user_id'   => $user['id'],
                    'token'     => $token,
                    'expire_le' => $expires,
                    'ip'        => Security::getIp(),
                    'cree_le'   => date('Y-m-d H:i:s'),
                ]);

                $this->envoyerEmailReset($email, $user['prenom'], $token);
                Logger::log(ACTION_PASSWORD_RESET, 'users', $user['id']);
            }
        }

        Helpers::redirectWithFlash('/connexion', 'info', $successMsg);
    }

    // =====================================================
    // RÉINITIALISATION DU MOT DE PASSE
    // =====================================================
    public function mdpReinitForm(array $params = []): void
    {
        $token = $params['token'] ?? '';
        $row   = $this->db->query(
            'SELECT id FROM reinitialisation_mdp WHERE token = ? AND utilise = 0 AND expire_le > NOW() LIMIT 1',
            [$token]
        )->fetch();

        if (!$row) {
            Helpers::redirectWithFlash('/mot-de-passe-oublie', 'erreur',
                'Lien expiré ou invalide. Veuillez faire une nouvelle demande.');
        }

        Helpers::view('auth/mdp_reset', [
            'pageTitle' => 'Réinitialiser le mot de passe',
            'bodyClass' => 'page-auth',
            'token'     => $token,
        ], 'auth');
    }

    public function mdpReinitialiser(array $params = []): void
    {
        $token   = $params['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        if (!Session::validateCsrfToken(Security::input('_csrf_token'))) {
            Helpers::redirect('/reinitialiser/' . $token);
        }

        $row = $this->db->query(
            'SELECT * FROM reinitialisation_mdp WHERE token = ? AND utilise = 0 AND expire_le > NOW() LIMIT 1',
            [$token]
        )->fetch();

        if (!$row) {
            Helpers::redirectWithFlash('/mot-de-passe-oublie', 'erreur', 'Lien expiré ou invalide.');
        }

        $pwdCheck = Security::validatePassword($password);
        if (!$pwdCheck['valid']) {
            Session::flash('erreur', implode('<br>', $pwdCheck['errors']));
            Helpers::redirect('/reinitialiser/' . $token);
        }

        if ($password !== $confirm) {
            Session::flash('erreur', 'Les mots de passe ne correspondent pas.');
            Helpers::redirect('/reinitialiser/' . $token);
        }

        // Mise à jour du mot de passe
        $this->db->update('users', [
            'password'             => Security::hashPassword($password),
            'tentatives_connexion' => 0,
            'bloque_jusqu'         => null,
        ], ['id' => $row['user_id']]);

        $this->db->update('reinitialisation_mdp', ['utilise' => 1], ['id' => $row['id']]);

        Logger::log('reinitialisation_mdp_success', 'users', $row['user_id']);
        Helpers::redirectWithFlash('/connexion', 'succes', 'Mot de passe modifié avec succès. Vous pouvez vous connecter.');
    }

    // =====================================================
    // ENVOI D'EMAILS (simulation — à remplacer par SMTP)
    // =====================================================
    private function envoyerEmailVerification(string $email, string $prenom, string $token): void
    {
        $verifyUrl = BASE_URL . '/verifier-email/' . $token;
        $siteName  = Helpers::getSetting('site_nom', APP_NAME);
        $from      = MAIL_FROM_ADDRESS;
        $fromName  = MAIL_FROM_NAME;

        $subject = "Vérifiez votre email — {$siteName}";
        $html = "<!DOCTYPE html><html><body style='font-family:sans-serif;max-width:600px;margin:0 auto;padding:24px'>
            <div style='background:linear-gradient(135deg,#0d2b6e,#1a56db);padding:32px;text-align:center;border-radius:12px 12px 0 0'>
              <h1 style='color:white;margin:0;font-size:24px'>{$siteName}</h1>
            </div>
            <div style='background:#f9fafb;padding:32px;border-radius:0 0 12px 12px;border:1px solid #e5e7eb;border-top:none'>
              <h2 style='color:#111827'>Bonjour {$prenom} !</h2>
              <p style='color:#4b5563;line-height:1.6'>Bienvenue sur notre plateforme. Cliquez sur le bouton ci-dessous pour vérifier votre adresse email et activer votre compte :</p>
              <div style='text-align:center;margin:32px 0'>
                <a href='{$verifyUrl}' style='background:linear-gradient(135deg,#1a56db,#0d2b6e);color:white;padding:14px 32px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block'>Vérifier mon email</a>
              </div>
              <p style='color:#6b7280;font-size:13px'>Ce lien expire dans 24 heures. Si vous n'avez pas créé de compte, ignorez cet email.</p>
              <p style='color:#9ca3af;font-size:12px;word-break:break-all'>Lien : {$verifyUrl}</p>
            </div>
          </body></html>";

        // Utiliser mail() PHP natif (remplacer par PHPMailer/SMTP en production)
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: {$fromName} <{$from}>\r\n";
        $headers .= "Reply-To: " . Helpers::getSetting('site_email', $from) . "\r\n";

        @mail($email, $subject, $html, $headers);

        // Log de l'envoi (debug)
        Logger::logToFile('INFO', "Email vérification envoyé à {$email}");
    }

    private function envoyerEmailReset(string $email, string $prenom, string $token): void
    {
        $resetUrl = BASE_URL . '/reinitialiser/' . $token;
        $siteName = Helpers::getSetting('site_nom', APP_NAME);
        $from     = MAIL_FROM_ADDRESS;

        $subject = "Réinitialisation de votre mot de passe — {$siteName}";
        $html = "<!DOCTYPE html><html><body style='font-family:sans-serif;max-width:600px;margin:0 auto;padding:24px'>
            <div style='background:linear-gradient(135deg,#0d2b6e,#1a56db);padding:32px;text-align:center;border-radius:12px 12px 0 0'>
              <h1 style='color:white;margin:0;font-size:24px'>{$siteName}</h1>
            </div>
            <div style='background:#f9fafb;padding:32px;border-radius:0 0 12px 12px;border:1px solid #e5e7eb;border-top:none'>
              <h2 style='color:#111827'>Bonjour {$prenom} !</h2>
              <p style='color:#4b5563;line-height:1.6'>Vous avez demandé la réinitialisation de votre mot de passe. Cliquez sur le bouton ci-dessous :</p>
              <div style='text-align:center;margin:32px 0'>
                <a href='{$resetUrl}' style='background:linear-gradient(135deg,#1a56db,#0d2b6e);color:white;padding:14px 32px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block'>Réinitialiser mon mot de passe</a>
              </div>
              <p style='color:#6b7280;font-size:13px'>⚠️ Ce lien expire dans 1 heure. Si vous n'avez pas fait cette demande, ignorez cet email.</p>
            </div>
          </body></html>";

        $headers  = "MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: {$siteName} <{$from}>\r\n";

        @mail($email, $subject, $html, $headers);
        Logger::logToFile('INFO', "Email reset mot de passe envoyé à {$email}");
    }
}
