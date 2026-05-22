<?php
/**
 * =====================================================
 * EUROCARE HUMANITAIRE - Contrôleur Administrateur
 * =====================================================
 * Fichier : app/controllers/AdminController.php
 * =====================================================
 */

defined('BASEPATH') or die('Accès direct interdit.');

class AdminController
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // =====================================================
    // DASHBOARD ADMIN
    // =====================================================
    public function dashboard(array $params = []): void
    {
        $stats = [
            'total_dons'         => (float)$this->db->query("SELECT COALESCE(SUM(montant),0) FROM dons WHERE statut='valide'")->fetchColumn(),
            'dons_ce_mois'       => (float)$this->db->query("SELECT COALESCE(SUM(montant),0) FROM dons WHERE statut='valide' AND MONTH(cree_le)=MONTH(NOW()) AND YEAR(cree_le)=YEAR(NOW())")->fetchColumn(),
            'total_beneficiaires'=> (int)$this->db->query("SELECT COUNT(*) FROM beneficiaires_profils")->fetchColumn(),
            'en_attente'         => (int)$this->db->query("SELECT COUNT(*) FROM beneficiaires_profils WHERE statut_dossier='en_attente'")->fetchColumn(),
            'total_users'        => (int)$this->db->query("SELECT COUNT(*) FROM users WHERE supprime_le IS NULL")->fetchColumn(),
            'users_ce_mois'      => (int)$this->db->query("SELECT COUNT(*) FROM users WHERE MONTH(cree_le)=MONTH(NOW()) AND YEAR(cree_le)=YEAR(NOW())")->fetchColumn(),
            'messages_nouveaux'  => (int)$this->db->query("SELECT COUNT(*) FROM contacts WHERE statut='nouveau'")->fetchColumn(),
            'partenaires_attente'=> (int)$this->db->query("SELECT COUNT(*) FROM partenaires_profils WHERE statut='en_attente'")->fetchColumn(),
        ];

        // Derniers dons
        $derniersDons = $this->db->query(
            "SELECT d.*, COALESCE(CONCAT(u.prenom,' ',u.nom), d.prenom_anonyme, 'Anonyme') AS donateur_nom
             FROM dons d LEFT JOIN users u ON u.id = d.user_id
             ORDER BY d.cree_le DESC LIMIT 8"
        )->fetchAll();

        // Derniers bénéficiaires
        $derniersBenef = $this->db->query(
            "SELECT bp.*, CONCAT(u.prenom,' ',u.nom) AS nom_complet, u.email
             FROM beneficiaires_profils bp
             JOIN users u ON u.id = bp.user_id
             ORDER BY bp.cree_le DESC LIMIT 6"
        )->fetchAll();

        // Graphe dons par mois (6 derniers mois)
        $donsGraphe = $this->db->query(
            "SELECT DATE_FORMAT(cree_le,'%b') AS mois, COALESCE(SUM(montant),0) AS total
             FROM dons WHERE statut='valide' AND cree_le >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
             GROUP BY YEAR(cree_le), MONTH(cree_le), mois ORDER BY YEAR(cree_le), MONTH(cree_le)"
        )->fetchAll();

        // Journal d'audit récent
        $auditRecent = $this->db->query(
            "SELECT ja.action, ja.module, ja.severite, ja.cree_le,
                    COALESCE(CONCAT(u.prenom,' ',u.nom), 'Système') AS user_nom
             FROM journal_audit ja LEFT JOIN users u ON u.id = ja.user_id
             ORDER BY ja.cree_le DESC LIMIT 10"
        )->fetchAll();

        Helpers::view('admin/dashboard', [
            'pageTitle'     => 'Tableau de bord',
            'stats'         => $stats,
            'derniersDons'  => $derniersDons,
            'derniersBenef' => $derniersBenef,
            'donsGraphe'    => $donsGraphe,
            'auditRecent'   => $auditRecent,
        ], 'admin');
    }

    // =====================================================
    // GESTION DES UTILISATEURS
    // =====================================================
    public function utilisateurs(array $params = []): void
    {
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $search  = Security::input('q', 'get');
        $role    = Security::input('role', 'get');
        $statut  = Security::input('statut', 'get');

        $where  = ['u.supprime_le IS NULL'];
        $qParams = [];

        if ($search) {
            $where[]  = "(u.prenom LIKE ? OR u.nom LIKE ? OR u.email LIKE ?)";
            $qParams  = array_merge($qParams, ["%$search%","%$search%","%$search%"]);
        }
        if ($role)   { $where[] = 'u.role = ?';   $qParams[] = $role; }
        if ($statut) { $where[] = 'u.statut = ?'; $qParams[] = $statut; }

        $whereStr = implode(' AND ', $where);
        $total    = (int)$this->db->query("SELECT COUNT(*) FROM users u WHERE $whereStr", $qParams)->fetchColumn();
        $pag      = Helpers::paginate($total, $page);

        $users = $this->db->query(
            "SELECT u.id, u.uuid, u.email, u.prenom, u.nom, u.role, u.statut, u.email_verifie,
                    u.pays, u.derniere_connexion, u.cree_le
             FROM users u WHERE $whereStr ORDER BY u.cree_le DESC LIMIT ? OFFSET ?",
            array_merge($qParams, [$pag['perPage'], $pag['offset']])
        )->fetchAll();

        Helpers::view('admin/utilisateurs', [
            'pageTitle' => 'Utilisateurs',
            'users'     => $users,
            'pagination'=> $pag,
            'search'    => $search,
            'roleFilter'=> $role,
            'statutFilter'=>$statut,
        ], 'admin');
    }

    public function changerStatut(array $params = []): void
    {
        $userId = (int)($params['id'] ?? 0);
        $statut = Security::input('statut');
        $valideStatuts = [STATUT_USER_ACTIF, STATUT_USER_INACTIF, STATUT_USER_SUSPENDU];

        if (!$userId || !in_array($statut, $valideStatuts, true)) {
            Helpers::jsonError('Paramètres invalides.');
        }

        $user = $this->db->findOne('users', ['id' => $userId], 'id, prenom, nom, role');
        if (!$user) Helpers::jsonError('Utilisateur introuvable.');

        // Empêcher l'admin de se suspendre lui-même
        if ($userId === Auth::id()) Helpers::jsonError('Vous ne pouvez pas modifier votre propre statut.');

        $this->db->update('users', ['statut' => $statut], ['id' => $userId]);

        Logger::log(ACTION_USER_UPDATE, 'users', $userId, ['statut' => 'ancien'],
            ['statut' => $statut], 'attention');

        Helpers::jsonSuccess("Statut modifié avec succès.", ['statut' => $statut]);
    }

    // =====================================================
    // GESTION DES BÉNÉFICIAIRES
    // =====================================================
    public function beneficiaires(array $params = []): void
    {
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $statut = Security::input('statut', 'get');
        $urgence= Security::input('urgence', 'get');
        $search = Security::input('q', 'get');

        $where  = ['1=1'];
        $qP     = [];

        if ($statut) { $where[] = 'bp.statut_dossier = ?'; $qP[] = $statut; }
        if ($urgence){ $where[] = 'bp.niveau_urgence = ?'; $qP[] = $urgence; }
        if ($search) {
            $where[] = "(u.prenom LIKE ? OR u.nom LIKE ? OR bp.numero_dossier LIKE ?)";
            $qP      = array_merge($qP, ["%$search%","%$search%","%$search%"]);
        }

        $whereStr = implode(' AND ', $where);
        $total    = (int)$this->db->query(
            "SELECT COUNT(*) FROM beneficiaires_profils bp JOIN users u ON u.id=bp.user_id WHERE $whereStr", $qP
        )->fetchColumn();
        $pag = Helpers::paginate($total, $page);

        $beneficiaires = $this->db->query(
            "SELECT bp.id, bp.numero_dossier, bp.type_beneficiaire, bp.statut_dossier,
                    bp.niveau_urgence, bp.cree_le, bp.modifie_le,
                    u.prenom, u.nom, u.email, u.pays
             FROM beneficiaires_profils bp
             JOIN users u ON u.id = bp.user_id
             WHERE $whereStr
             ORDER BY FIELD(bp.niveau_urgence,'critique','eleve','modere','faible'), bp.cree_le ASC
             LIMIT ? OFFSET ?",
            array_merge($qP, [$pag['perPage'], $pag['offset']])
        )->fetchAll();

        Helpers::view('admin/beneficiaires', [
            'pageTitle'     => 'Dossiers bénéficiaires',
            'beneficiaires' => $beneficiaires,
            'pagination'    => $pag,
            'statutFilter'  => $statut,
            'urgenceFilter' => $urgence,
            'search'        => $search,
        ], 'admin');
    }

    public function beneficiaire(array $params = []): void
    {
        $id = (int)($params['id'] ?? 0);
        $benef = $this->db->query(
            "SELECT bp.*, u.prenom, u.nom, u.email, u.telephone, u.pays, u.ville, u.adresse, u.cree_le AS user_cree_le
             FROM beneficiaires_profils bp
             JOIN users u ON u.id = bp.user_id
             WHERE bp.id = ? LIMIT 1",
            [$id]
        )->fetch();

        if (!$benef) Helpers::redirectWithFlash('/admin/beneficiaires', 'erreur', 'Dossier introuvable.');

        $documents = $this->db->findAll('documents', ['user_id' => $benef['user_id']],
            'id, type_document, nom_original, taille, statut, cree_le', 'cree_le DESC');

        $aides = $this->db->query(
            "SELECT ao.*, CONCAT(u.prenom,' ',u.nom) AS accordeur_nom
             FROM aides_octroyees ao
             JOIN users u ON u.id = ao.accorde_par
             WHERE ao.beneficiaire_id = ?
             ORDER BY ao.cree_le DESC",
            [$id]
        )->fetchAll();

        $recommandations = $this->db->query(
            "SELECT rp.*, pp.nom_organisation
             FROM recommandations_partenaires rp
             JOIN partenaires_profils pp ON pp.id = rp.partenaire_id
             WHERE rp.beneficiaire_id = ? ORDER BY rp.cree_le DESC",
            [$id]
        )->fetchAll();

        Helpers::view('admin/beneficiaire_detail', [
            'pageTitle'       => 'Dossier #' . $benef['numero_dossier'],
            'benef'           => $benef,
            'documents'       => $documents,
            'aides'           => $aides,
            'recommandations' => $recommandations,
        ], 'admin');
    }

    public function changerStatutDossier(array $params = []): void
    {
        if (!Session::validateCsrfToken(Security::input('_csrf_token'))) {
            Helpers::jsonError('Token CSRF invalide.');
        }

        $id     = (int)($params['id'] ?? 0);
        $statut = Security::input('statut_dossier');
        $note   = Security::input('note_interne');

        $valides = array_keys(STATUTS_BENEF_LABELS);
        if (!$id || !in_array($statut, $valides, true)) {
            Helpers::jsonError('Paramètres invalides.');
        }

        $benef = $this->db->findOne('beneficiaires_profils', ['id' => $id]);
        if (!$benef) Helpers::jsonError('Dossier introuvable.');

        $updateData = [
            'statut_dossier' => $statut,
            'note_interne'   => mb_substr($note, 0, 2000),
        ];

        if (in_array($statut, ['verifie', 'prioritaire', 'aide'], true)) {
            $updateData['valide_par'] = Auth::id();
            $updateData['valide_le']  = date('Y-m-d H:i:s');
        }

        $this->db->update('beneficiaires_profils', $updateData, ['id' => $id]);

        // Notifier le bénéficiaire
        $labels = STATUTS_BENEF_LABELS;
        Helpers::createNotification(
            $benef['user_id'],
            'Mise à jour de votre dossier',
            "Votre dossier vient de passer au statut : {$labels[$statut]}.",
            NOTIF_VALIDATION,
            BASE_URL . '/beneficiaire/mon-dossier'
        );

        Logger::log(ACTION_BENEF_VALIDATE, 'beneficiaires_profils', $id,
            ['statut' => $benef['statut_dossier']], ['statut' => $statut]);

        Helpers::jsonSuccess('Statut du dossier mis à jour.', ['statut' => $statut]);
    }

    public function accorderAide(array $params = []): void
    {
        if (!Session::validateCsrfToken(Security::input('_csrf_token'))) {
            Helpers::redirectWithFlash('/admin/beneficiaires', 'erreur', 'Token invalide.');
        }

        $benefId   = (int)($params['id'] ?? 0);
        $typeAide  = Security::input('type_aide');
        $montant   = filter_var(Security::input('montant'), FILTER_VALIDATE_FLOAT) ?: null;
        $desc      = Security::input('description');
        $date      = Security::input('date_attribution') ?: date('Y-m-d');

        if (!$benefId || !array_key_exists($typeAide, TYPES_AIDE) || mb_strlen($desc) < 3) {
            Helpers::redirectWithFlash("/admin/beneficiaires/{$benefId}", 'erreur', 'Données invalides.');
        }

        $this->db->insert('aides_octroyees', [
            'beneficiaire_id'  => $benefId,
            'type_aide'        => $typeAide,
            'montant'          => $montant,
            'description'      => mb_substr($desc, 0, 2000),
            'statut'           => 'approuve',
            'accorde_par'      => Auth::id(),
            'date_attribution' => $date,
            'cree_le'          => date('Y-m-d H:i:s'),
        ]);

        $benef = $this->db->findOne('beneficiaires_profils', ['id' => $benefId], 'user_id, statut_dossier');
        if ($benef && $benef['statut_dossier'] !== 'aide') {
            $this->db->update('beneficiaires_profils', ['statut_dossier' => 'aide'], ['id' => $benefId]);
        }

        Helpers::createNotification(
            $benef['user_id'],
            'Aide accordée !',
            "Une aide de type « " . TYPES_AIDE[$typeAide] . " » vous a été accordée.",
            NOTIF_AIDE,
            BASE_URL . '/beneficiaire/mes-aides'
        );

        Logger::log(ACTION_AIDE_CREATE, 'aides_octroyees', $benefId, null,
            ['type_aide' => $typeAide, 'montant' => $montant]);

        Helpers::redirectWithFlash("/admin/beneficiaires/{$benefId}", 'succes', 'Aide enregistrée avec succès.');
    }

    // =====================================================
    // GESTION DES DONS
    // =====================================================
    public function dons(array $params = []): void
    {
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $statut = Security::input('statut', 'get');
        $search = Security::input('q', 'get');
        $du     = Security::input('du', 'get');
        $au     = Security::input('au', 'get');

        $where = ['1=1']; $qP = [];
        if ($statut) { $where[] = 'd.statut = ?';     $qP[] = $statut; }
        if ($search) { $where[] = "(COALESCE(CONCAT(u.prenom,' ',u.nom),'') LIKE ? OR d.email_anonyme LIKE ? OR d.uuid LIKE ?)";
            $qP = array_merge($qP, ["%$search%","%$search%","%$search%"]); }
        if ($du) { $where[] = 'DATE(d.cree_le) >= ?'; $qP[] = $du; }
        if ($au) { $where[] = 'DATE(d.cree_le) <= ?'; $qP[] = $au; }

        $ws = implode(' AND ', $where);
        $total = (int)$this->db->query("SELECT COUNT(*) FROM dons d LEFT JOIN users u ON u.id=d.user_id WHERE $ws",$qP)->fetchColumn();
        $pag = Helpers::paginate($total, $page);

        $dons = $this->db->query(
            "SELECT d.id, d.uuid, d.montant, d.devise, d.type, d.statut, d.donateur_anonyme,
                    d.prenom_anonyme, d.email_anonyme, d.cause, d.cree_le, d.valide_le,
                    CONCAT(u.prenom,' ',u.nom) AS donateur_nom, u.email AS donateur_email
             FROM dons d LEFT JOIN users u ON u.id=d.user_id
             WHERE $ws ORDER BY d.cree_le DESC LIMIT ? OFFSET ?",
            array_merge($qP, [$pag['perPage'], $pag['offset']])
        )->fetchAll();

        // Totaux filtrés
        $totaux = $this->db->query(
            "SELECT COALESCE(SUM(CASE WHEN statut='valide' THEN montant ELSE 0 END),0) AS total_valide,
                    COUNT(*) AS nb_total
             FROM dons d LEFT JOIN users u ON u.id=d.user_id WHERE $ws", $qP
        )->fetch();

        Helpers::view('admin/dons', [
            'pageTitle'   => 'Gestion des dons',
            'dons'        => $dons,
            'pagination'  => $pag,
            'totaux'      => $totaux,
            'statutFilter'=> $statut,
            'search'      => $search,
            'du' => $du, 'au' => $au,
        ], 'admin');
    }

    public function validerDon(array $params = []): void
    {
        $donId = (int)($params['id'] ?? 0);
        if (!$donId) Helpers::jsonError('ID invalide.');

        $don = $this->db->findOne('dons', ['id' => $donId]);
        if (!$don) Helpers::jsonError('Don introuvable.');

        $this->db->update('dons', [
            'statut'     => STATUT_DON_VALIDE,
            'valide_le'  => date('Y-m-d H:i:s'),
        ], ['id' => $donId]);

        // Mettre à jour le montant collecté du projet
        if ($don['projet_id']) {
            $this->db->query(
                "UPDATE projets SET montant_collecte = montant_collecte + ? WHERE id = ?",
                [(float)$don['montant'], $don['projet_id']]
            );
        }

        if ($don['user_id']) {
            Helpers::createNotification(
                $don['user_id'],
                'Don validé !',
                "Votre don de " . Helpers::formatAmount((float)$don['montant']) . " a été validé. Merci !",
                NOTIF_DON, BASE_URL . '/donateur/mes-dons'
            );
        }

        Logger::log(ACTION_DON_VALIDATE, 'dons', $donId, ['statut'=>'en_attente'], ['statut'=>'valide']);
        Helpers::jsonSuccess('Don validé avec succès.');
    }

    // =====================================================
    // PARTENAIRES
    // =====================================================
    public function listePartenaires(array $params = []): void
    {
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $statut = Security::input('statut', 'get');
        $type   = Security::input('type', 'get');

        $where = ['1=1']; $qP = [];
        if ($statut) { $where[] = 'pp.statut = ?';             $qP[] = $statut; }
        if ($type)   { $where[] = 'pp.type_organisation = ?';  $qP[] = $type; }

        $ws    = implode(' AND ', $where);
        $total = (int)$this->db->query("SELECT COUNT(*) FROM partenaires_profils pp WHERE $ws", $qP)->fetchColumn();
        $pag   = Helpers::paginate($total, $page);

        $partenaires = $this->db->query(
            "SELECT pp.*, u.email, u.prenom, u.nom
             FROM partenaires_profils pp
             JOIN users u ON u.id = pp.user_id
             WHERE $ws ORDER BY pp.statut='en_attente' DESC, pp.cree_le DESC
             LIMIT ? OFFSET ?",
            array_merge($qP, [$pag['perPage'], $pag['offset']])
        )->fetchAll();

        Helpers::view('admin/partenaires', [
            'pageTitle'   => 'Partenaires',
            'partenaires' => $partenaires,
            'pagination'  => $pag,
            'statutFilter'=> $statut,
            'typeFilter'  => $type,
        ], 'admin');
    }

    public function validerPartenaire(array $params = []): void
    {
        $id     = (int)($params['id'] ?? 0);
        $statut = Security::input('statut');
        if (!$id || !in_array($statut, ['valide','rejete','suspendu'], true)) {
            Helpers::jsonError('Paramètres invalides.');
        }

        $part = $this->db->findOne('partenaires_profils', ['id' => $id], 'id, user_id, nom_organisation');
        if (!$part) Helpers::jsonError('Partenaire introuvable.');

        $this->db->update('partenaires_profils', [
            'statut'     => $statut,
            'valide_par' => Auth::id(),
            'valide_le'  => date('Y-m-d H:i:s'),
        ], ['id' => $id]);

        $msgs = ['valide'=>'Votre dossier partenaire a été approuvé !','rejete'=>'Votre dossier partenaire a été rejeté.','suspendu'=>'Votre accès partenaire a été suspendu.'];
        Helpers::createNotification($part['user_id'], 'Mise à jour dossier partenaire', $msgs[$statut], NOTIF_VALIDATION);

        Logger::log(ACTION_PARTNER_VALIDATE, 'partenaires_profils', $id, null, ['statut' => $statut]);
        Helpers::jsonSuccess("Partenaire $statut avec succès.");
    }

    // =====================================================
    // ARTICLES
    // =====================================================
    public function articles(array $params = []): void
    {
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $statut = Security::input('statut', 'get');
        $search = Security::input('q', 'get');

        $where = ['1=1']; $qP = [];
        if ($statut) { $where[] = 'a.statut = ?'; $qP[] = $statut; }
        if ($search) { $where[] = 'a.titre LIKE ?'; $qP[] = "%$search%"; }

        $ws    = implode(' AND ', $where);
        $total = (int)$this->db->query("SELECT COUNT(*) FROM articles a WHERE $ws", $qP)->fetchColumn();
        $pag   = Helpers::paginate($total, $page);

        $articles = $this->db->query(
            "SELECT a.id, a.titre, a.slug, a.statut, a.featured, a.vues, a.publie_le, a.cree_le,
                    ca.nom AS cat_nom, CONCAT(u.prenom,' ',u.nom) AS auteur_nom
             FROM articles a
             LEFT JOIN categories_articles ca ON ca.id=a.categorie_id
             LEFT JOIN users u ON u.id=a.auteur_id
             WHERE $ws ORDER BY a.cree_le DESC LIMIT ? OFFSET ?",
            array_merge($qP, [$pag['perPage'], $pag['offset']])
        )->fetchAll();

        Helpers::view('admin/articles', [
            'pageTitle'   => 'Articles & Blog',
            'articles'    => $articles,
            'pagination'  => $pag,
            'statutFilter'=> $statut,
            'search'      => $search,
        ], 'admin');
    }

    public function nouvelArticle(array $params = []): void
    {
        $categories = $this->db->findAll('categories_articles', ['actif' => 1], 'id, nom', 'ordre ASC');
        Helpers::view('admin/article_form', [
            'pageTitle'  => 'Nouvel article',
            'categories' => $categories,
            'article'    => null,
        ], 'admin');
    }

    public function creerArticle(array $params = []): void
    {
        if (!Session::validateCsrfToken(Security::input('_csrf_token'))) {
            Helpers::redirectWithFlash('/admin/articles/nouveau', 'erreur', 'Token invalide.');
        }

        $titre       = Security::input('titre');
        $contenu     = Security::sanitizeHtml($_POST['contenu'] ?? '');
        $extrait     = Security::input('extrait');
        $categorieId = (int)Security::input('categorie_id');
        $statut      = in_array(Security::input('statut'), ['brouillon','publie','archive']) ? Security::input('statut') : 'brouillon';
        $featured    = isset($_POST['featured']) ? 1 : 0;
        $tags        = Security::input('tags');

        if (mb_strlen($titre) < 3 || mb_strlen($contenu) < 10) {
            Helpers::redirectWithFlash('/admin/articles/nouveau', 'erreur', 'Le titre et le contenu sont requis.');
        }

        $slug = Security::slugify($titre);
        // Assurer unicité du slug
        $existing = $this->db->count('articles', ['slug' => $slug]);
        if ($existing) $slug .= '-' . time();

        $uploadResult = null;
        if (!empty($_FILES['image_principale']['name'])) {
            $uploadResult = Security::handleUpload('image_principale', 'articles', ALLOWED_IMAGE_TYPES);
        }

        $articleId = $this->db->insert('articles', [
            'titre'           => mb_substr($titre, 0, 255),
            'slug'            => $slug,
            'contenu'         => $contenu,
            'extrait'         => mb_substr($extrait, 0, 500),
            'image_principale'=> $uploadResult['filename'] ?? null,
            'auteur_id'       => Auth::id(),
            'categorie_id'    => $categorieId ?: null,
            'statut'          => $statut,
            'featured'        => $featured,
            'tags'            => mb_substr($tags, 0, 500),
            'publie_le'       => $statut === 'publie' ? date('Y-m-d H:i:s') : null,
            'cree_le'         => date('Y-m-d H:i:s'),
        ]);

        Logger::log(ACTION_ARTICLE_CREATE, 'articles', $articleId);
        Helpers::redirectWithFlash('/admin/articles', 'succes', 'Article créé avec succès.');
    }

    // =====================================================
    // STATISTIQUES
    // =====================================================
    public function statistiques(array $params = []): void
    {
        $stats = Helpers::getGlobalStats();

        $donsParMois = $this->db->query(
            "SELECT DATE_FORMAT(cree_le,'%Y-%m') AS mois, SUM(montant) AS total, COUNT(*) AS nb
             FROM dons WHERE statut='valide' AND cree_le >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
             GROUP BY mois ORDER BY mois ASC"
        )->fetchAll();

        $parRole = $this->db->query(
            "SELECT role, COUNT(*) AS nb FROM users WHERE supprime_le IS NULL AND statut='actif' GROUP BY role"
        )->fetchAll();

        $aidesByType = $this->db->query(
            "SELECT type_aide, COUNT(*) AS nb, COALESCE(SUM(montant),0) AS total FROM aides_octroyees GROUP BY type_aide ORDER BY nb DESC"
        )->fetchAll();

        Helpers::view('admin/statistiques', [
            'pageTitle'   => 'Statistiques',
            'stats'       => $stats,
            'donsParMois' => $donsParMois,
            'parRole'     => $parRole,
            'aidesByType' => $aidesByType,
        ], 'admin');
    }

    // =====================================================
    // JOURNAL D'AUDIT
    // =====================================================
    public function audit(array $params = []): void
    {
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $filters = [
            'action'     => Security::input('action', 'get'),
            'module'     => Security::input('module', 'get'),
            'severite'   => Security::input('severite', 'get'),
            'date_debut' => Security::input('date_debut', 'get'),
            'date_fin'   => Security::input('date_fin', 'get'),
        ];

        $result = Logger::getAuditLogs($filters, $page, 50);

        Helpers::view('admin/audit', [
            'pageTitle'  => 'Journal d\'audit',
            'logs'       => $result['data'],
            'pagination' => $result,
            'filters'    => $filters,
        ], 'admin');
    }

    // =====================================================
    // PARAMÈTRES GLOBAUX
    // =====================================================
    public function parametres(array $params = []): void
    {
        $groupes = ['general', 'contact', 'reseaux', 'email', 'systeme', 'stats'];
        $allParams = $this->db->query(
            'SELECT * FROM parametres WHERE modifiable=1 ORDER BY groupe ASC, id ASC'
        )->fetchAll();

        $paramsByGroup = [];
        foreach ($allParams as $p) {
            $paramsByGroup[$p['groupe']][] = $p;
        }

        Helpers::view('admin/parametres', [
            'pageTitle'     => 'Paramètres du site',
            'paramsByGroup' => $paramsByGroup,
            'groupes'       => $groupes,
        ], 'admin');
    }

    public function saveParametres(array $params = []): void
    {
        if (!Session::validateCsrfToken(Security::input('_csrf_token'))) {
            Helpers::redirectWithFlash('/admin/parametres', 'erreur', 'Token invalide.');
        }

        $rows = $this->db->query('SELECT id, cle, type FROM parametres WHERE modifiable=1')->fetchAll();

        foreach ($rows as $row) {
            $val = Security::input($row['cle']);
            if ($row['type'] === 'booleen') {
                $val = isset($_POST[$row['cle']]) ? '1' : '0';
            }
            $this->db->update('parametres', ['valeur' => $val, 'modifie_le' => date('Y-m-d H:i:s')], ['id' => $row['id']]);
        }

        Logger::log(ACTION_SETTING_UPDATE, 'parametres', null, null, null, 'attention', 'Paramètres globaux mis à jour');

        // Invalider le cache des stats
        @unlink(ROOT_PATH . '/storage/cache/global_stats.json');

        Helpers::redirectWithFlash('/admin/parametres', 'succes', 'Paramètres sauvegardés avec succès.');
    }

    // =====================================================
    // MESSAGES CONTACT
    // =====================================================
    public function messages(array $params = []): void
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $statut = Security::input('statut', 'get') ?: 'nouveau';
        $total = (int)$this->db->count('contacts', $statut ? ['statut' => $statut] : []);
        $pag   = Helpers::paginate($total, $page);

        $messages = $this->db->query(
            "SELECT * FROM contacts " . ($statut ? "WHERE statut = ?" : "") . " ORDER BY cree_le DESC LIMIT ? OFFSET ?",
            $statut ? [$statut, $pag['perPage'], $pag['offset']] : [$pag['perPage'], $pag['offset']]
        )->fetchAll();

        Helpers::view('admin/messages', [
            'pageTitle'   => 'Messages de contact',
            'messages'    => $messages,
            'pagination'  => $pag,
            'statutFilter'=> $statut,
        ], 'admin');
    }

    public function message(array $params = []): void
    {
        $id  = (int)($params['id'] ?? 0);
        $msg = $this->db->findOne('contacts', ['id' => $id]);
        if (!$msg) Helpers::redirectWithFlash('/admin/messages', 'erreur', 'Message introuvable.');
        // Marquer comme lu
        if ($msg['statut'] === 'nouveau') $this->db->update('contacts', ['statut' => 'lu'], ['id' => $id]);

        Helpers::view('admin/message_detail', ['pageTitle' => 'Message de ' . $msg['nom'], 'msg' => $msg], 'admin');
    }

    // =====================================================
    // TÉMOIGNAGES
    // =====================================================
    public function temoignages(array $params = []): void
    {
        $page = max(1,(int)($_GET['page']??1));
        $statut = Security::input('statut','get');
        $where = $statut ? ['statut'=>$statut] : [];
        $total = $this->db->count('temoignages', $where);
        $pag   = Helpers::paginate($total, $page);
        $tems  = $this->db->query(
            "SELECT * FROM temoignages " . ($statut?"WHERE statut=?":'') . " ORDER BY cree_le DESC LIMIT ? OFFSET ?",
            $statut ? [$statut,$pag['perPage'],$pag['offset']] : [$pag['perPage'],$pag['offset']]
        )->fetchAll();
        Helpers::view('admin/temoignages', ['pageTitle'=>'Témoignages','temoignages'=>$tems,'pagination'=>$pag,'statutFilter'=>$statut], 'admin');
    }

    public function statTemoignage(array $params = []): void
    {
        $id = (int)($params['id']??0);
        $statut = Security::input('statut');
        if (!$id || !in_array($statut, ['approuve','rejete','en_attente'],true)) Helpers::jsonError('Invalide.');
        $this->db->update('temoignages', ['statut'=>$statut], ['id'=>$id]);
        Helpers::jsonSuccess('Statut mis à jour.');
    }

    // =====================================================
    // FAQ
    // =====================================================
    public function faq(array $params = []): void
    {
        $faqs = $this->db->query('SELECT * FROM faq ORDER BY categorie ASC, ordre ASC')->fetchAll();
        Helpers::view('admin/faq', ['pageTitle'=>'FAQ','faqs'=>$faqs], 'admin');
    }

    public function saveFaq(array $params = []): void
    {
        if (!Session::validateCsrfToken(Security::input('_csrf_token'))) {
            Helpers::redirectWithFlash('/admin/faq', 'erreur', 'Token invalide.');
        }
        $question  = Security::input('question');
        $reponse   = Security::input('reponse');
        $categorie = Security::input('categorie') ?: 'general';
        $ordre     = (int)Security::input('ordre');
        $faqId     = (int)Security::input('faq_id');

        if (mb_strlen($question)<3 || mb_strlen($reponse)<3) {
            Helpers::redirectWithFlash('/admin/faq', 'erreur', 'La question et la réponse sont requises.');
        }

        if ($faqId) {
            $this->db->update('faq', ['question'=>$question,'reponse'=>$reponse,'categorie'=>$categorie,'ordre'=>$ordre], ['id'=>$faqId]);
        } else {
            $this->db->insert('faq', ['question'=>$question,'reponse'=>$reponse,'categorie'=>$categorie,'ordre'=>$ordre,'actif'=>1,'cree_le'=>date('Y-m-d H:i:s')]);
        }
        Helpers::redirectWithFlash('/admin/faq', 'succes', 'FAQ sauvegardée.');
    }

    // =====================================================
    // PROJETS
    // =====================================================
    public function projets(array $params = []): void
    {
        $projets = $this->db->query('SELECT * FROM projets ORDER BY featured DESC, ordre ASC')->fetchAll();
        Helpers::view('admin/projets', ['pageTitle'=>'Projets / Causes','projets'=>$projets], 'admin');
    }

    // =====================================================
    // PAGES CMS
    // =====================================================
    public function pages(array $params = []): void
    {
        $pages = $this->db->query('SELECT * FROM pages_cms ORDER BY titre ASC')->fetchAll();
        Helpers::view('admin/pages', ['pageTitle'=>'Pages CMS','pages'=>$pages], 'admin');
    }

    public function savePage(array $params = []): void
    {
        if (!Session::validateCsrfToken(Security::input('_csrf_token'))) {
            Helpers::redirectWithFlash('/admin/pages', 'erreur', 'Token invalide.');
        }
        $id      = (int)($params['id'] ?? 0);
        $contenu = Security::sanitizeHtml($_POST['contenu'] ?? '');
        $titre   = Security::input('titre');
        $metaDesc= Security::input('meta_description');

        $this->db->update('pages_cms', [
            'contenu'          => $contenu,
            'titre'            => mb_substr($titre,0,255),
            'meta_description' => mb_substr($metaDesc,0,500),
            'modifie_par'      => Auth::id(),
            'modifie_le'       => date('Y-m-d H:i:s'),
        ], ['id' => $id]);

        Logger::log('modification_page', 'pages_cms', $id);
        Helpers::redirectWithFlash('/admin/pages', 'succes', 'Page mise à jour.');
    }

    // =====================================================
    // EXPORT
    // =====================================================
    public function exporter(array $params = []): void
    {
        $type = $params['type'] ?? '';
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="export_' . $type . '_' . date('Y-m-d') . '.csv"');
        echo "\xEF\xBB\xBF"; // BOM UTF-8

        $fp = fopen('php://output', 'w');

        switch ($type) {
            case 'dons':
                fputcsv($fp, ['UUID','Montant','Devise','Type','Statut','Donateur','Email','Date']);
                $rows = $this->db->query("SELECT d.uuid,d.montant,d.devise,d.type,d.statut,COALESCE(CONCAT(u.prenom,' ',u.nom),d.prenom_anonyme,'Anonyme') AS nom,COALESCE(u.email,d.email_anonyme,'') AS email,d.cree_le FROM dons d LEFT JOIN users u ON u.id=d.user_id ORDER BY d.cree_le DESC")->fetchAll();
                foreach ($rows as $r) fputcsv($fp, $r);
                break;
            case 'beneficiaires':
                fputcsv($fp, ['Dossier','Prénom','Nom','Email','Type','Statut','Urgence','Pays','Date']);
                $rows = $this->db->query("SELECT bp.numero_dossier,u.prenom,u.nom,u.email,bp.type_beneficiaire,bp.statut_dossier,bp.niveau_urgence,u.pays,bp.cree_le FROM beneficiaires_profils bp JOIN users u ON u.id=bp.user_id ORDER BY bp.cree_le DESC")->fetchAll();
                foreach ($rows as $r) fputcsv($fp, $r);
                break;
        }

        fclose($fp);
        exit;
    }
}

    // =====================================================
    // MÉTHODES MANQUANTES - Ajout complémentaire
    // =====================================================

    public function editerArticle(array $params = []): void
    {
        $id      = (int)($params['id'] ?? 0);
        $article = $this->db->findOne('articles', ['id' => $id]);
        if (!$article) Helpers::redirectWithFlash('/admin/articles', 'erreur', 'Article introuvable.');
        $categories = $this->db->findAll('categories_articles', ['actif' => 1], 'id, nom', 'ordre ASC');
        Helpers::view('admin/article_form', [
            'pageTitle'  => 'Éditer : ' . Helpers::truncate($article['titre'], 40),
            'article'    => $article,
            'categories' => $categories,
        ], 'admin');
    }

    public function updateArticle(array $params = []): void
    {
        if (!Session::validateCsrfToken(Security::input('_csrf_token'))) {
            Helpers::redirectWithFlash('/admin/articles', 'erreur', 'Token invalide.');
        }
        $id      = (int)($params['id'] ?? 0);
        $article = $this->db->findOne('articles', ['id' => $id]);
        if (!$article) Helpers::redirectWithFlash('/admin/articles', 'erreur', 'Article introuvable.');

        $titre   = Security::input('titre');
        $contenu = Security::sanitizeHtml($_POST['contenu'] ?? '');
        $statut  = in_array(Security::input('statut'), ['brouillon','publie','archive']) ? Security::input('statut') : 'brouillon';

        if (mb_strlen($titre) < 3) {
            Helpers::redirectWithFlash("/admin/articles/{$id}/editer", 'erreur', 'Titre requis.');
        }

        $data = [
            'titre'           => mb_substr($titre, 0, 255),
            'contenu'         => $contenu,
            'extrait'         => mb_substr(Security::input('extrait'), 0, 500),
            'categorie_id'    => (int)Security::input('categorie_id') ?: null,
            'statut'          => $statut,
            'featured'        => isset($_POST['featured']) ? 1 : 0,
            'tags'            => mb_substr(Security::input('tags'), 0, 500),
            'meta_description'=> mb_substr(Security::input('meta_description'), 0, 500),
            'modifie_le'      => date('Y-m-d H:i:s'),
        ];

        if ($statut === 'publie' && !$article['publie_le']) {
            $data['publie_le'] = date('Y-m-d H:i:s');
        }

        if (!empty($_FILES['image_principale']['name'])) {
            $up = Security::handleUpload('image_principale', 'articles', ALLOWED_IMAGE_TYPES);
            if ($up['success']) {
                if ($article['image_principale']) Security::deleteUpload($article['image_principale'], 'articles');
                $data['image_principale'] = $up['filename'];
            }
        }

        $this->db->update('articles', $data, ['id' => $id]);
        Logger::log(ACTION_ARTICLE_UPDATE, 'articles', $id);
        Helpers::redirectWithFlash('/admin/articles', 'succes', 'Article mis à jour.');
    }

    public function supprimerArticle(array $params = []): void
    {
        if (!Session::validateCsrfToken(Security::input('_csrf_token'))) {
            Helpers::redirectWithFlash('/admin/articles', 'erreur', 'Token invalide.');
        }
        $id = (int)($params['id'] ?? 0);
        $article = $this->db->findOne('articles', ['id' => $id], 'id, image_principale');
        if (!$article) Helpers::redirectWithFlash('/admin/articles', 'erreur', 'Article introuvable.');

        if ($article['image_principale']) Security::deleteUpload($article['image_principale'], 'articles');
        $this->db->delete('articles', ['id' => $id]);
        Logger::log('suppression_article', 'articles', $id);
        Helpers::redirectWithFlash('/admin/articles', 'succes', 'Article supprimé.');
    }

    public function repondreMessage(array $params = []): void
    {
        if (!Session::validateCsrfToken(Security::input('_csrf_token'))) {
            Helpers::redirectWithFlash('/admin/messages', 'erreur', 'Token invalide.');
        }
        $id  = (int)($params['id'] ?? 0);
        $msg = $this->db->findOne('contacts', ['id' => $id]);
        if (!$msg) Helpers::redirectWithFlash('/admin/messages', 'erreur', 'Message introuvable.');

        $reponse = Security::input('reponse');
        if (mb_strlen($reponse) < 5) {
            Helpers::redirectWithFlash("/admin/messages/{$id}", 'erreur', 'La réponse est trop courte.');
        }

        $this->db->update('contacts', [
            'statut'      => 'repondu',
            'reponse'     => mb_substr($reponse, 0, 5000),
            'repondu_par' => Auth::id(),
            'repondu_le'  => date('Y-m-d H:i:s'),
        ], ['id' => $id]);

        // Envoi email de réponse (simulation)
        $headers  = "MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM_ADDRESS . ">\r\n";
        $siteName = Helpers::getSetting('site_nom', APP_NAME);
        $html = "<p>Bonjour {$msg['nom']},</p><p>En réponse à votre message du " . Helpers::formatDate($msg['cree_le'],false,true) . " :</p><blockquote style='border-left:3px solid #1a56db;padding-left:12px;color:#4b5563'>" . nl2br(Security::e($msg['message'])) . "</blockquote><p><strong>Notre réponse :</strong></p>" . nl2br(Security::e($reponse)) . "<p>Cordialement,<br>L'équipe {$siteName}</p>";
        @mail($msg['email'], "Réponse à votre message — {$siteName}", $html, $headers);

        Helpers::redirectWithFlash("/admin/messages/{$id}", 'succes', 'Réponse envoyée par email.');
    }

    // =====================================================
    // MÉTHODES COMPLÉMENTAIRES UTILISATEURS & PROJETS
    // =====================================================

    public function utilisateur(array $params = []): void
    {
        $id   = (int)($params['id'] ?? 0);
        $user = $this->db->findOne('users', ['id' => $id]);
        if (!$user) Helpers::redirectWithFlash('/admin/utilisateurs', 'erreur', 'Utilisateur introuvable.');
        Helpers::view('admin/utilisateurs', [
            'pageTitle'   => 'Utilisateur : ' . $user['prenom'] . ' ' . $user['nom'],
            'users'       => [$user],
            'pagination'  => Helpers::paginate(1),
            'search'      => '', 'roleFilter' => '', 'statutFilter' => '',
        ], 'admin');
    }

    public function updateUtilisateur(array $params = []): void
    {
        if (!Session::validateCsrfToken(Security::input('_csrf_token'))) {
            Helpers::redirectWithFlash('/admin/utilisateurs', 'erreur', 'Token invalide.');
        }
        $id   = (int)($params['id'] ?? 0);
        $role = Security::input('role');
        if ($id && in_array($role, array_keys(ROLES_LABELS), true)) {
            $this->db->update('users', ['role' => $role], ['id' => $id]);
        }
        Helpers::redirectWithFlash('/admin/utilisateurs', 'succes', 'Utilisateur mis à jour.');
    }

    public function supprimerUser(array $params = []): void
    {
        if (!Session::validateCsrfToken(Security::input('_csrf_token'))) {
            Helpers::jsonError('Token invalide.');
        }
        $id = (int)($params['id'] ?? 0);
        if (!$id || $id === Auth::id()) Helpers::jsonError('Impossible de supprimer cet utilisateur.');
        $this->db->update('users', ['supprime_le' => date('Y-m-d H:i:s')], ['id' => $id]);
        Logger::log(ACTION_USER_DELETE, 'users', $id, null, null, 'critique');
        Helpers::jsonSuccess('Utilisateur supprimé.');
    }

    public function creerProjet(array $params = []): void
    {
        if (!Session::validateCsrfToken(Security::input('_csrf_token'))) {
            Helpers::redirectWithFlash('/admin/projets', 'erreur', 'Token invalide.');
        }
        $titre    = Security::input('titre');
        $objectif = filter_var(Security::input('objectif_montant'), FILTER_VALIDATE_FLOAT) ?: 0;
        $cat      = Security::input('categorie');
        $desc     = Security::input('description');
        $slug     = Security::slugify($titre);

        if (mb_strlen($titre) < 3) Helpers::redirectWithFlash('/admin/projets', 'erreur', 'Titre requis.');
        if ($this->db->count('projets', ['slug' => $slug])) $slug .= '-' . time();

        $this->db->insert('projets', [
            'titre'           => mb_substr($titre, 0, 255),
            'slug'            => $slug,
            'description'     => mb_substr($desc, 0, 10000),
            'description_courte' => mb_substr(Security::input('description_courte'), 0, 500),
            'objectif_montant'=> round($objectif, 2),
            'categorie'       => mb_substr($cat, 0, 100),
            'statut'          => 'actif',
            'featured'        => isset($_POST['featured']) ? 1 : 0,
            'cree_par'        => Auth::id(),
            'cree_le'         => date('Y-m-d H:i:s'),
        ]);

        Helpers::redirectWithFlash('/admin/projets', 'succes', 'Projet créé avec succès.');
    }

    public function updateProjet(array $params = []): void
    {
        if (!Session::validateCsrfToken(Security::input('_csrf_token'))) {
            Helpers::redirectWithFlash('/admin/projets', 'erreur', 'Token invalide.');
        }
        $id     = (int)($params['id'] ?? 0);
        $statut = in_array(Security::input('statut'), ['actif','complete','suspendu','termine'])
            ? Security::input('statut') : 'actif';

        $this->db->update('projets', [
            'titre'           => mb_substr(Security::input('titre'), 0, 255),
            'description_courte' => mb_substr(Security::input('description_courte'), 0, 500),
            'objectif_montant'=> filter_var(Security::input('objectif_montant'), FILTER_VALIDATE_FLOAT) ?: 0,
            'statut'          => $statut,
            'featured'        => isset($_POST['featured']) ? 1 : 0,
            'modifie_le'      => date('Y-m-d H:i:s'),
        ], ['id' => $id]);

        Helpers::redirectWithFlash('/admin/projets', 'succes', 'Projet mis à jour.');
    }
}
