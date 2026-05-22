<?php
/**
 * =====================================================
 * EUROCARE — DonController
 * =====================================================
 */
defined('BASEPATH') or die('Accès direct interdit.');

class DonController
{
    private Database $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function formulaire(array $p = []): void
    {
        $projetId = (int)($_GET['projet'] ?? 0);
        $projet   = $projetId ? $this->db->findOne('projets', ['id'=>$projetId,'statut'=>'actif']) : null;
        $projets  = $this->db->query("SELECT id,titre,description_courte FROM projets WHERE statut='actif' ORDER BY ordre ASC")->fetchAll();
        Helpers::view('public/don', [
            'pageTitle' => 'Faire un don',
            'metaDesc'  => 'Soutenez nos projets humanitaires par un don sécurisé.',
            'bodyClass' => 'page-donation',
            'projet'    => $projet,
            'projets'   => $projets,
        ]);
    }

    public function traiter(array $p = []): void
    {
        if (!Session::validateCsrfToken(Security::input('_csrf_token'))) {
            Helpers::redirectWithFlash('/faire-un-don','erreur','Session expirée.');
        }

        $montant   = filter_var(Security::input('montant'), FILTER_VALIDATE_FLOAT);
        $devise    = in_array(Security::input('devise'), array_keys(DEVISES)) ? Security::input('devise') : 'EUR';
        $type      = in_array(Security::input('type'), ['ponctuel','mensuel','annuel']) ? Security::input('type') : 'ponctuel';
        $projetId  = (int)Security::input('projet_id') ?: null;
        $cause     = Security::input('cause');
        $message   = Security::input('message');
        $anonyme   = isset($_POST['anonyme']);
        $prenom    = Security::input('prenom_anonyme');
        $email     = Security::input('email_anonyme');

        if (!$montant || $montant < 1) {
            Helpers::redirectWithFlash('/faire-un-don','erreur','Montant invalide (minimum 1 €).');
        }

        $uuid = Security::generateUuid();
        $this->db->insert('dons', [
            'uuid'              => $uuid,
            'user_id'           => Auth::id(),
            'donateur_anonyme'  => $anonyme ? 1 : 0,
            'prenom_anonyme'    => $anonyme ? mb_substr($prenom,0,100) : null,
            'email_anonyme'     => $anonyme && Security::validateEmail($email) ? $email : null,
            'montant'           => round($montant, 2),
            'devise'            => $devise,
            'type'              => $type,
            'cause'             => mb_substr($cause,0,255),
            'projet_id'         => $projetId,
            'statut'            => 'en_attente',
            'message'           => mb_substr($message,0,1000),
            'ip_donateur'       => Security::getIp(),
            'cree_le'           => date('Y-m-d H:i:s'),
        ]);

        // En prod : intégrer Stripe/PayPal ici avant de valider
        // Pour la démo : validation directe
        $donId = $this->db->lastInsertId();
        $this->db->update('dons', ['statut'=>'valide','valide_le'=>date('Y-m-d H:i:s')], ['id'=>$donId]);

        if ($projetId) {
            $this->db->query("UPDATE projets SET montant_collecte=montant_collecte+? WHERE id=?", [$montant,$projetId]);
        }

        Logger::log(ACTION_DON_CREATE,'dons',(int)$donId,null,['montant'=>$montant,'devise'=>$devise]);
        Helpers::redirect('/don/confirmation/'.$uuid);
    }

    public function confirmation(array $p = []): void
    {
        $uuid = Security::sanitize($p['uuid'] ?? '');
        $don  = $this->db->query("SELECT d.*,p.titre AS projet_titre FROM dons d LEFT JOIN projets p ON p.id=d.projet_id WHERE d.uuid=? LIMIT 1",[$uuid])->fetch();
        if (!$don) Helpers::redirect('/faire-un-don');
        Helpers::view('public/don_confirmation', ['pageTitle'=>'Merci pour votre don !','don'=>$don]);
    }

    public function dashboard(array $p = []): void
    {
        $user = Auth::user();
        $totalDons = (float)$this->db->query("SELECT COALESCE(SUM(montant),0) FROM dons WHERE user_id=? AND statut='valide'",[$user['id']])->fetchColumn();
        $nbDons    = (int)$this->db->query("SELECT COUNT(*) FROM dons WHERE user_id=? AND statut='valide'",[$user['id']])->fetchColumn();
        $derniers  = $this->db->query("SELECT d.*,p.titre AS projet_titre FROM dons d LEFT JOIN projets p ON p.id=d.projet_id WHERE d.user_id=? ORDER BY d.cree_le DESC LIMIT 5",[$user['id']])->fetchAll();
        Helpers::view('donor/dashboard', ['extraCss'=>['dashboard.css'],'pageTitle'=>'Mon espace donateur','totalDons'=>$totalDons,'nbDons'=>$nbDons,'derniers'=>$derniers,'user'=>$user], 'user_dashboard');
    }

    public function mesDons(array $p = []): void
    {
        $user  = Auth::user();
        $page  = max(1,(int)($_GET['page']??1));
        $total = (int)$this->db->count('dons',['user_id'=>$user['id']]);
        $pag   = Helpers::paginate($total, $page);
        $dons  = $this->db->query("SELECT d.*,p.titre AS projet_titre FROM dons d LEFT JOIN projets p ON p.id=d.projet_id WHERE d.user_id=? ORDER BY d.cree_le DESC LIMIT ? OFFSET ?",[$user['id'],$pag['perPage'],$pag['offset']])->fetchAll();
        Helpers::view('donor/mes_dons', ['extraCss'=>['dashboard.css'],'pageTitle'=>'Mes dons','dons'=>$dons,'pagination'=>$pag], 'user_dashboard');
    }

    public function profil(array $p = []): void
    {
        Helpers::view('donor/profil', ['extraCss'=>['dashboard.css'],'pageTitle'=>'Mon profil','user'=>Auth::user()], 'user_dashboard');
    }

    public function updateProfil(array $p = []): void
    {
        if (!Session::validateCsrfToken(Security::input('_csrf_token'))) Helpers::redirect('/donateur/profil');
        $user = Auth::user();
        $data = [
            'prenom'    => mb_substr(Security::input('prenom'),0,100),
            'nom'       => mb_substr(Security::input('nom'),0,100),
            'telephone' => mb_substr(Security::input('telephone'),0,20),
            'pays'      => mb_substr(Security::input('pays'),0,100),
            'ville'     => mb_substr(Security::input('ville'),0,100),
        ];
        if (!empty($_FILES['photo_profil']['name'])) {
            $up = Security::handleUpload('photo_profil','profils',ALLOWED_IMAGE_TYPES,2*1024*1024);
            if ($up['success']) {
                if ($user['photo_profil']) Security::deleteUpload($user['photo_profil'],'profils');
                $data['photo_profil'] = $up['filename'];
            }
        }
        Database::getInstance()->update('users', $data, ['id'=>$user['id']]);
        Auth::invalidateCache();
        Helpers::redirectWithFlash('/donateur/profil','succes','Profil mis à jour.');
    }

    public function recus(array $p = []): void
    {
        $user = Auth::user();
        $dons = $this->db->query("SELECT * FROM dons WHERE user_id=? AND statut='valide' ORDER BY cree_le DESC",[$user['id']])->fetchAll();
        Helpers::view('donor/recus', ['extraCss'=>['dashboard.css'],'pageTitle'=>'Mes reçus fiscaux','dons'=>$dons], 'user_dashboard');
    }

    public function impact(array $p = []): void
    {
        $user = Auth::user();
        $stats = [
            'total'   => (float)$this->db->query("SELECT COALESCE(SUM(montant),0) FROM dons WHERE user_id=? AND statut='valide'",[$user['id']])->fetchColumn(),
            'nb'      => (int)$this->db->query("SELECT COUNT(*) FROM dons WHERE user_id=? AND statut='valide'",[$user['id']])->fetchColumn(),
            'projets' => $this->db->query("SELECT DISTINCT p.titre,p.categorie FROM dons d JOIN projets p ON p.id=d.projet_id WHERE d.user_id=? AND d.statut='valide'"  ,[$user['id']])->fetchAll(),
        ];
        Helpers::view('donor/impact', ['extraCss'=>['dashboard.css'],'pageTitle'=>"L'impact de mes dons",'stats'=>$stats], 'user_dashboard');
    }

    /**
     * Page reçu fiscal HTML imprimable / PDF
     */
    public function recu(array $p = []): void
    {
        $uuid = Security::sanitize($p['uuid'] ?? '');
        $don  = $this->db->query(
            "SELECT d.*, p.titre AS projet_titre
             FROM dons d LEFT JOIN projets p ON p.id=d.projet_id
             WHERE d.uuid=? AND d.statut='valide' LIMIT 1",
            [$uuid]
        )->fetch();

        if (!$don) Helpers::redirectWithFlash('/faire-un-don','erreur','Reçu introuvable.');

        if (Auth::check() && Auth::id() !== (int)$don['user_id'] && !Auth::isStaff()) {
            Helpers::redirect('/tableau-de-bord');
        }

        $donateur = null;
        if ($don['user_id'] && !$don['donateur_anonyme']) {
            $donateur = $this->db->findOne('users',['id'=>$don['user_id']],'prenom,nom,email,adresse,pays,ville');
        }

        $siteName = Helpers::getSetting('site_nom', APP_NAME);
        Helpers::view('public/recu_don',[
            'pageTitle' => 'Reçu fiscal',
            'don'       => $don,
            'donateur'  => $donateur,
            'siteName'  => $siteName,
        ], 'none');
    }
}
