<?php defined('BASEPATH') or die('Accès direct interdit.');

/* =====================================================
   PARTENAIRE CONTROLLER
   ===================================================== */
class PartenaireController
{
    private Database $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function dashboard(array $p = []): void
    {
        $user  = Auth::user();
        $profil= $this->db->findOne('partenaires_profils',['user_id'=>$user['id']]);
        $recos = $profil ? $this->db->query("SELECT rp.*,CONCAT(u.prenom,' ',u.nom) AS benef_nom FROM recommandations_partenaires rp JOIN beneficiaires_profils bp ON bp.id=rp.beneficiaire_id JOIN users u ON u.id=bp.user_id WHERE rp.partenaire_id=? ORDER BY rp.cree_le DESC LIMIT 5",[$profil['id']])->fetchAll() : [];
        Helpers::view('partner/dashboard',['pageTitle'=>'Espace partenaire','user'=>$user,'profil'=>$profil,'recos'=>$recos],'main');
    }

    public function profil(array $p = []): void
    {
        $user  = Auth::user();
        $profil= $this->db->findOne('partenaires_profils',['user_id'=>$user['id']]);
        Helpers::view('partner/profil',['pageTitle'=>'Mon profil organisation','user'=>$user,'profil'=>$profil],'main');
    }

    public function updateProfil(array $p = []): void
    {
        if (!Session::validateCsrfToken(Security::input('_csrf_token'))) Helpers::redirect('/partenaire/mon-profil');
        $user  = Auth::user();
        $profil= $this->db->findOne('partenaires_profils',['user_id'=>$user['id']]);

        $data = [
            'nom_organisation'      => mb_substr(Security::input('nom_organisation'),0,255),
            'type_organisation'     => Security::input('type_organisation'),
            'numero_enregistrement' => mb_substr(Security::input('numero_enregistrement'),0,100),
            'site_web'              => mb_substr(Security::input('site_web'),0,255),
            'description'           => mb_substr(Security::input('description'),0,3000),
            'domaines_action'       => mb_substr(Security::input('domaines_action'),0,1000),
            'pays'                  => mb_substr(Security::input('pays'),0,100),
            'ville'                 => mb_substr(Security::input('ville'),0,100),
            'telephone'             => mb_substr(Security::input('telephone'),0,20),
            'email_contact'         => mb_substr(Security::input('email_contact'),0,255),
            'modifie_le'            => date('Y-m-d H:i:s'),
        ];

        if (!empty($_FILES['logo']['name'])) {
            $up = Security::handleUpload('logo','partenaires',ALLOWED_IMAGE_TYPES,2*1024*1024);
            if ($up['success']) $data['logo'] = $up['filename'];
        }

        if ($profil) {
            $this->db->update('partenaires_profils',$data,['id'=>$profil['id']]);
        } else {
            $data['user_id']  = $user['id'];
            $data['statut']   = 'en_attente';
            $data['cree_le']  = date('Y-m-d H:i:s');
            $this->db->insert('partenaires_profils',$data);
        }
        Helpers::redirectWithFlash('/partenaire/mon-profil','succes','Profil mis à jour.');
    }

    public function recommandations(array $p = []): void
    {
        $user  = Auth::user();
        $profil= $this->db->findOne('partenaires_profils',['user_id'=>$user['id']],'id,statut');
        $recos = $profil ? $this->db->query("SELECT rp.*,CONCAT(u.prenom,' ',u.nom) AS benef_nom,bp.numero_dossier FROM recommandations_partenaires rp JOIN beneficiaires_profils bp ON bp.id=rp.beneficiaire_id JOIN users u ON u.id=bp.user_id WHERE rp.partenaire_id=? ORDER BY rp.cree_le DESC",[$profil['id']])->fetchAll() : [];
        Helpers::view('partner/recommandations',['pageTitle'=>'Mes recommandations','profil'=>$profil,'recos'=>$recos],'main');
    }

    public function soumettre(array $p = []): void
    {
        if (!Session::validateCsrfToken(Security::input('_csrf_token'))) Helpers::redirect('/partenaire/recommandations');
        $user  = Auth::user();
        $profil= $this->db->findOne('partenaires_profils',['user_id'=>$user['id']],'id,statut');
        if (!$profil || $profil['statut'] !== 'valide') {
            Helpers::redirectWithFlash('/partenaire/recommandations','erreur','Votre profil doit être validé pour soumettre des recommandations.');
        }
        $numDossier = Security::input('numero_dossier');
        $benef = $this->db->findOne('beneficiaires_profils',['numero_dossier'=>$numDossier],'id');
        if (!$benef) { Helpers::redirectWithFlash('/partenaire/recommandations','erreur','Numéro de dossier introuvable.'); }

        $this->db->insert('recommandations_partenaires',[
            'partenaire_id'   => $profil['id'],
            'beneficiaire_id' => $benef['id'],
            'recommandation'  => mb_substr(Security::input('recommandation'),0,3000),
            'niveau_urgence'  => Security::input('niveau_urgence') ?: 'modere',
            'statut'          => 'soumise',
            'cree_le'         => date('Y-m-d H:i:s'),
        ]);
        Helpers::redirectWithFlash('/partenaire/recommandations','succes','Recommandation soumise avec succès.');
    }
}


/* =====================================================
   BLOG CONTROLLER
   ===================================================== */
class BlogController
{
    private Database $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function liste(array $p = []): void
    {
        $page    = max(1,(int)($_GET['page']??1));
        $catSlug = Security::input('cat','get');
        $where   = ["a.statut='publie'"]; $qP = [];
        if ($catSlug) { $where[] = 'ca.slug=?'; $qP[] = $catSlug; }

        $ws    = implode(' AND ',$where);
        $total = (int)$this->db->query("SELECT COUNT(*) FROM articles a LEFT JOIN categories_articles ca ON ca.id=a.categorie_id WHERE $ws",$qP)->fetchColumn();
        $pag   = Helpers::paginate($total,$page,ARTICLES_PER_PAGE);

        $articles = $this->db->query("SELECT a.*,ca.nom AS cat_nom,ca.couleur AS cat_color,CONCAT(u.prenom,' ',u.nom) AS auteur_nom FROM articles a LEFT JOIN categories_articles ca ON ca.id=a.categorie_id LEFT JOIN users u ON u.id=a.auteur_id WHERE $ws ORDER BY a.featured DESC,a.publie_le DESC LIMIT ? OFFSET ?",array_merge($qP,[$pag['perPage'],$pag['offset']]))->fetchAll();
        $categories = $this->db->findAll('categories_articles',['actif'=>1],'id,nom,slug,couleur','ordre ASC');

        // Incrémenter vue sur page accueil : non, seulement sur article
        Helpers::view('public/blog',['pageTitle'=>'Actualités','metaDesc'=>'Suivez les actualités et témoignages de EuroCare Humanitaire.','articles'=>$articles,'categories'=>$categories,'pagination'=>$pag,'catSlug'=>$catSlug]);
    }

    public function article(array $p = []): void
    {
        $slug = Security::sanitize($p['slug'] ?? '');
        $art  = $this->db->query("SELECT a.*,ca.nom AS cat_nom,ca.couleur AS cat_color,ca.slug AS cat_slug,CONCAT(u.prenom,' ',u.nom) AS auteur_nom FROM articles a LEFT JOIN categories_articles ca ON ca.id=a.categorie_id LEFT JOIN users u ON u.id=a.auteur_id WHERE a.slug=? AND a.statut='publie' LIMIT 1",[$slug])->fetch();
        if (!$art) Helpers::redirect('/actualites');

        $this->db->query("UPDATE articles SET vues=vues+1 WHERE id=?",[$art['id']]);

        $comms = $this->db->query("SELECT c.*,COALESCE(CONCAT(u.prenom,' ',u.nom),c.auteur_nom) AS nom FROM commentaires c LEFT JOIN users u ON u.id=c.user_id WHERE c.article_id=? AND c.statut='approuve' ORDER BY c.cree_le ASC",[$art['id']])->fetchAll();
        $related = $this->db->query("SELECT id,titre,slug,image_principale,publie_le FROM articles WHERE statut='publie' AND id!=? AND categorie_id=? ORDER BY publie_le DESC LIMIT 3",[$art['id'],$art['categorie_id']??0])->fetchAll();

        Helpers::view('public/article',['pageTitle'=>$art['titre'],'metaDesc'=>Helpers::truncate($art['extrait']??$art['contenu'],160),'article'=>$art,'commentaires'=>$comms,'related'=>$related]);
    }

    public function commenter(array $p = []): void
    {
        if (!Session::validateCsrfToken(Security::input('_csrf_token'))) Helpers::redirect('/actualites');
        $slug    = Security::sanitize($p['slug']??'');
        $art     = $this->db->findOne('articles',['slug'=>$slug,'statut'=>'publie'],'id,commentaires_actifs');
        if (!$art || !$art['commentaires_actifs']) Helpers::redirect('/actualites/'.$slug);

        $contenu = Security::input('contenu'); $auteur = Security::input('auteur_nom'); $email = Security::input('auteur_email');
        if (mb_strlen($contenu)<3) { Helpers::redirectWithFlash('/actualites/'.$slug,'erreur','Commentaire trop court.'); }

        $this->db->insert('commentaires',['article_id'=>$art['id'],'user_id'=>Auth::id(),'auteur_nom'=>mb_substr($auteur,0,100),'auteur_email'=>mb_substr($email,0,255),'contenu'=>mb_substr($contenu,0,3000),'statut'=>'en_attente','ip'=>Security::getIp(),'cree_le'=>date('Y-m-d H:i:s')]);
        Helpers::redirectWithFlash('/actualites/'.$slug,'info','Commentaire soumis, il sera publié après modération.');
    }

    public function categorie(array $p = []): void
    {
        $_GET['cat'] = $p['slug'] ?? '';
        $this->liste($p);
    }

    public function recherche(array $p = []): void
    {
        $q       = Security::input('q','get');
        $page    = max(1,(int)($_GET['page']??1));
        $results = [];
        if (mb_strlen($q) >= 2) {
            $total   = (int)$this->db->query("SELECT COUNT(*) FROM articles WHERE statut='publie' AND (titre LIKE ? OR contenu LIKE ? OR tags LIKE ?)",array_fill(0,3,"%$q%"))->fetchColumn();
            $pag     = Helpers::paginate($total,$page,ARTICLES_PER_PAGE);
            $results = $this->db->query("SELECT a.id,a.titre,a.slug,a.extrait,a.image_principale,a.publie_le,ca.nom AS cat_nom FROM articles a LEFT JOIN categories_articles ca ON ca.id=a.categorie_id WHERE a.statut='publie' AND (a.titre LIKE ? OR a.contenu LIKE ? OR a.tags LIKE ?) ORDER BY a.publie_le DESC LIMIT ? OFFSET ?",array_merge(array_fill(0,3,"%$q%"),[$pag['perPage'],$pag['offset']]))->fetchAll();
        } else { $pag = Helpers::paginate(0); }
        Helpers::view('public/recherche',['pageTitle'=>'Recherche','q'=>$q,'results'=>$results,'pagination'=>$pag??Helpers::paginate(0)]);
    }
}


/* =====================================================
   API CONTROLLER (AJAX endpoints)
   ===================================================== */
class ApiController
{
    public function getNotifications(array $p = []): void
    {
        if (!Auth::check()) Helpers::jsonError('Non autorisé.',[], 401);
        $notifs = Database::getInstance()->query(
            "SELECT id,titre,message,type,lien,lu,cree_le FROM notifications WHERE user_id=? ORDER BY cree_le DESC LIMIT 15",
            [Auth::id()]
        )->fetchAll();
        foreach ($notifs as &$n) $n['time_ago'] = Helpers::timeAgo($n['cree_le']);
        Helpers::jsonSuccess('OK',['notifications'=>$notifs]);
    }

    public function lireNotification(array $p = []): void
    {
        if (!Auth::check()) Helpers::jsonError('Non autorisé.',[], 401);
        $id = (int)(json_decode(file_get_contents('php://input'),true)['id'] ?? 0);
        if (!$id) Helpers::jsonError('ID manquant.');
        Database::getInstance()->update('notifications',['lu'=>1,'lu_le'=>date('Y-m-d H:i:s')],['id'=>$id,'user_id'=>Auth::id()]);
        Helpers::jsonSuccess('Notification lue.');
    }

    public function getStats(array $p = []): void
    {
        $stats = Helpers::getGlobalStats();
        Helpers::jsonSuccess('OK',['stats'=>$stats]);
    }

    public function newsletter(array $p = []): void
    {
        $data  = json_decode(file_get_contents('php://input'),true) ?? [];
        $email = $data['email'] ?? Security::input('email');
        if (!Security::validateEmail($email)) Helpers::jsonError('Email invalide.');
        // Ici : intégrer votre service email (Brevo, Mailchimp, etc.)
        Logger::logToFile('INFO',"Newsletter inscription: $email");
        Helpers::jsonSuccess('Inscription enregistrée. Merci !');
    }
}
