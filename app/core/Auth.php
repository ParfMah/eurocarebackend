<?php
/**
 * =====================================================
 * EUROCARE HUMANITAIRE - Classe Auth
 * =====================================================
 * Fichier : app/core/Auth.php
 * Description : Gestion complète de l'authentification,
 *               des connexions, déconnexions, rôles et
 *               permissions utilisateurs.
 * =====================================================
 */

defined('BASEPATH') or die('Accès direct interdit.');

class Auth
{
    /** @var array|null Cache de l'utilisateur connecté */
    private static ?array $currentUser = null;

    // =====================================================
    // CONNEXION / DÉCONNEXION
    // =====================================================

    /**
     * Tente de connecter un utilisateur avec email + mot de passe.
     *
     * @param  string $email       Adresse email
     * @param  string $password    Mot de passe en clair
     * @param  bool   $rememberMe  Se souvenir de moi
     * @return array ['success' => bool, 'message' => string, 'user' => array|null]
     */
    public static function attempt(string $email, string $password, bool $rememberMe = false): array
    {
        $db   = Database::getInstance();
        $email = mb_strtolower(trim($email));

        // Récupération de l'utilisateur par email
        $user = $db->findOne('users', ['email' => $email, 'supprime_le' => null],
            'id, uuid, email, password, role, statut, prenom, nom, email_verifie, tentatives_connexion, bloque_jusqu, photo_profil'
        );

        if (!$user) {
            // Simulation d'une vérification de hash pour éviter les timing attacks
            password_verify('dummy_password', '$2y$12$invalidhashtopreventtimingatk');
            Logger::log(ACTION_FAILED_LOGIN, 'users', null, null,
                ['email' => $email, 'raison' => 'utilisateur_inexistant']);
            return ['success' => false, 'message' => 'Identifiants incorrects.'];
        }

        // Vérification du blocage temporaire
        if ($user['bloque_jusqu'] && strtotime($user['bloque_jusqu']) > time()) {
            $minutesRestants = ceil((strtotime($user['bloque_jusqu']) - time()) / 60);
            return [
                'success' => false,
                'message' => "Compte temporairement bloqué. Réessayez dans $minutesRestants minute(s)."
            ];
        }

        // Vérification du mot de passe
        if (!password_verify($password, $user['password'])) {
            self::incrementLoginAttempts($user['id'], $user['tentatives_connexion']);
            Logger::log(ACTION_FAILED_LOGIN, 'users', $user['id'], null,
                ['email' => $email, 'raison' => 'mot_de_passe_incorrect']);
            return ['success' => false, 'message' => 'Identifiants incorrects.'];
        }

        // Vérification du statut du compte
        if ($user['statut'] !== STATUT_USER_ACTIF) {
            $messages = [
                STATUT_USER_INACTIF    => 'Votre compte est inactif. Contactez l\'administrateur.',
                STATUT_USER_SUSPENDU   => 'Votre compte a été suspendu. Contactez le support.',
                STATUT_USER_EN_ATTENTE => 'Veuillez vérifier votre adresse email avant de vous connecter.',
            ];
            return [
                'success' => false,
                'message' => $messages[$user['statut']] ?? 'Compte non autorisé.'
            ];
        }

        // Vérification de l'email (si non vérifié)
        if (!$user['email_verifie']) {
            return [
                'success' => false,
                'message' => 'Veuillez vérifier votre adresse email. Consultez votre boîte de réception.'
            ];
        }

        // ✅ Connexion réussie
        self::loginUser($user, $rememberMe);

        return ['success' => true, 'message' => 'Connexion réussie.', 'user' => $user];
    }

    /**
     * Authentifie l'utilisateur en session.
     * Mise à jour de la dernière connexion et reset des tentatives.
     *
     * @param array $user      Données utilisateur
     * @param bool  $rememberMe Cookie persistant
     */
    private static function loginUser(array $user, bool $rememberMe = false): void
    {
        $db = Database::getInstance();

        // Régénération de l'ID de session (anti-fixation)
        Session::regenerate();

        // Stockage en session
        Session::set('user_id',   $user['id']);
        Session::set('user_role', $user['role']);
        Session::set('user_uuid', $user['uuid']);
        Session::set('logged_in', true);
        Session::set('login_time', time());

        // Mise à jour base de données
        $db->update('users', [
            'derniere_connexion' => date('Y-m-d H:i:s'),
            'tentatives_connexion' => 0,
            'bloque_jusqu' => null,
        ], ['id' => $user['id']]);

        // Cookie "Se souvenir de moi"
        if ($rememberMe) {
            $token   = bin2hex(random_bytes(32));
            $expires = time() + (REMEMBER_ME_DAYS * 86400);
            $db->insert('sessions_utilisateurs', [
                'user_id'    => $user['id'],
                'token'      => hash('sha256', $token),
                'ip'         => Security::getIp(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'expire_le'  => date('Y-m-d H:i:s', $expires),
            ]);
            setcookie('remember_token', $token, [
                'expires'  => $expires,
                'path'     => '/',
                'secure'   => SESSION_SECURE,
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
        }

        // Journal d'audit
        Logger::log(ACTION_LOGIN, 'users', $user['id']);

        // Cache utilisateur invalidé
        self::$currentUser = null;
    }

    /**
     * Déconnecte l'utilisateur.
     */
    public static function logout(): void
    {
        $userId = self::id();

        if ($userId) {
            Logger::log(ACTION_LOGOUT, 'users', $userId);

            // Suppression des tokens "Se souvenir"
            if (isset($_COOKIE['remember_token'])) {
                $token = hash('sha256', $_COOKIE['remember_token']);
                Database::getInstance()->query(
                    'DELETE FROM sessions_utilisateurs WHERE token = ? AND user_id = ?',
                    [$token, $userId]
                );
                setcookie('remember_token', '', time() - 3600, '/');
            }
        }

        // Destruction de la session
        Session::destroy();
        self::$currentUser = null;
    }

    // =====================================================
    // VÉRIFICATIONS D'ÉTAT
    // =====================================================

    /**
     * Vérifie si un utilisateur est connecté.
     */
    public static function check(): bool
    {
        // Vérification cookie "Se souvenir de moi" si pas de session
        if (!Session::get('logged_in') && isset($_COOKIE['remember_token'])) {
            self::authenticateFromCookie();
        }

        return Session::get('logged_in', false) === true;
    }

    /**
     * Vérifie si l'utilisateur n'est PAS connecté.
     */
    public static function guest(): bool
    {
        return !self::check();
    }

    /**
     * Vérifie si l'utilisateur a l'un des rôles spécifiés.
     *
     * @param  string[] $roles Liste de rôles acceptés
     * @return bool
     */
    public static function hasRole(array $roles): bool
    {
        if (!self::check()) return false;
        return in_array(Session::get('user_role'), $roles, true);
    }

    /**
     * Vérifie si l'utilisateur est administrateur.
     */
    public static function isAdmin(): bool
    {
        return self::hasRole([ROLE_ADMIN]);
    }

    /**
     * Vérifie si l'utilisateur est admin ou modérateur.
     */
    public static function isStaff(): bool
    {
        return self::hasRole([ROLE_ADMIN, ROLE_MODERATEUR]);
    }

    // =====================================================
    // DONNÉES DE L'UTILISATEUR CONNECTÉ
    // =====================================================

    /**
     * Retourne l'ID de l'utilisateur connecté.
     *
     * @return int|null
     */
    public static function id(): ?int
    {
        $id = Session::get('user_id');
        return $id ? (int)$id : null;
    }

    /**
     * Retourne le rôle de l'utilisateur connecté.
     */
    public static function role(): ?string
    {
        return Session::get('user_role');
    }

    /**
     * Retourne les données complètes de l'utilisateur connecté.
     * Utilise un cache pour éviter les requêtes répétées.
     *
     * @return array|null
     */
    public static function user(): ?array
    {
        if (!self::check()) return null;

        if (self::$currentUser === null) {
            self::$currentUser = Database::getInstance()->findOne(
                'users',
                ['id' => self::id()],
                'id, uuid, email, role, statut, prenom, nom, telephone, pays, ville,
                 photo_profil, email_verifie, notifications_email, newsletter, cree_le, derniere_connexion'
            ) ?: null;
        }

        return self::$currentUser;
    }

    /**
     * Retourne le nom complet de l'utilisateur connecté.
     */
    public static function fullName(): string
    {
        $user = self::user();
        if (!$user) return '';
        return trim($user['prenom'] . ' ' . $user['nom']);
    }

    // =====================================================
    // AUTHENTIFICATION VIA COOKIE
    // =====================================================

    /**
     * Tente d'authentifier l'utilisateur depuis un cookie "Se souvenir de moi".
     */
    private static function authenticateFromCookie(): void
    {
        $cookieToken = $_COOKIE['remember_token'] ?? '';
        if (empty($cookieToken)) return;

        $hashedToken = hash('sha256', $cookieToken);
        $db          = Database::getInstance();

        $session = $db->query(
            'SELECT su.user_id, su.expire_le, u.statut, u.role, u.uuid
             FROM sessions_utilisateurs su
             JOIN users u ON u.id = su.user_id
             WHERE su.token = ? AND su.expire_le > NOW() AND u.statut = ?
             LIMIT 1',
            [$hashedToken, STATUT_USER_ACTIF]
        )->fetch();

        if ($session) {
            // Récupération complète de l'utilisateur
            $user = $db->findOne('users', ['id' => $session['user_id']],
                'id, uuid, email, password, role, statut, prenom, nom, email_verifie, tentatives_connexion, bloque_jusqu, photo_profil'
            );
            if ($user) {
                self::loginUser($user, true);
            }
        }
    }

    // =====================================================
    // GESTION DES TENTATIVES DE CONNEXION
    // =====================================================

    /**
     * Incrémente le compteur de tentatives échouées.
     * Bloque le compte après MAX_LOGIN_ATTEMPTS tentatives.
     *
     * @param int $userId     ID de l'utilisateur
     * @param int $current    Nombre actuel de tentatives
     */
    private static function incrementLoginAttempts(int $userId, int $current): void
    {
        $db          = Database::getInstance();
        $newAttempts = $current + 1;
        $updateData  = ['tentatives_connexion' => $newAttempts];

        if ($newAttempts >= MAX_LOGIN_ATTEMPTS) {
            $updateData['bloque_jusqu'] = date('Y-m-d H:i:s', time() + LOGIN_LOCKOUT_TIME);
        }

        $db->update('users', $updateData, ['id' => $userId]);
    }

    // =====================================================
    // NETTOYAGE DES SESSIONS EXPIRÉES
    // =====================================================

    /**
     * Supprime les sessions expirées de la base de données.
     * À appeler périodiquement (cron job recommandé).
     */
    public static function cleanExpiredSessions(): int
    {
        return Database::getInstance()->query(
            'DELETE FROM sessions_utilisateurs WHERE expire_le < NOW()'
        )->rowCount();
    }

    /**
     * Invalide le cache de l'utilisateur connecté.
     * À appeler après modification du profil.
     */
    public static function invalidateCache(): void
    {
        self::$currentUser = null;
    }
}
