<?php
/**
 * =====================================================
 * EUROCARE HUMANITAIRE - Contrôleur Public
 * =====================================================
 * Fichier : app/controllers/PublicController.php
 * Description : Toutes les pages publiques du site
 *   (accueil, à propos, missions, partenaires,
 *    transparence, témoignages, FAQ, contact).
 * =====================================================
 */

defined('BASEPATH') or die('Accès direct interdit.');

class PublicController
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // =====================================================
    // PAGE D'ACCUEIL
    // =====================================================
    public function accueil(array $params = []): void
    {
        // Statistiques globales
        $stats = Helpers::getGlobalStats();

        // Projets actifs mis en avant
        $projets = $this->db->query(
            'SELECT * FROM projets WHERE statut = ? AND featured = 1 ORDER BY ordre ASC LIMIT 3',
            ['actif']
        )->fetchAll();

        // Derniers articles publiés
        $articles = $this->db->query(
            'SELECT a.*, ca.nom AS categorie_nom, ca.couleur AS categorie_couleur,
                    CONCAT(u.prenom, " ", u.nom) AS auteur_nom
             FROM articles a
             LEFT JOIN categories_articles ca ON ca.id = a.categorie_id
             LEFT JOIN users u ON u.id = a.auteur_id
             WHERE a.statut = "publie"
             ORDER BY a.publie_le DESC
             LIMIT 3'
        )->fetchAll();

        // Témoignages featured
        $temoignages = $this->db->query(
            'SELECT * FROM temoignages WHERE statut = ? AND featured = 1 ORDER BY ordre ASC LIMIT 3',
            ['approuve']
        )->fetchAll();

        // Partenaires validés (logos)
        $partenaires = $this->db->query(
            'SELECT nom_organisation, logo, type_organisation, site_web
             FROM partenaires_profils
             WHERE statut = ? AND featured = 1
             ORDER BY ordre_affichage ASC LIMIT 8',
            ['valide']
        )->fetchAll();

        Helpers::view('public/accueil', [
            'pageTitle'   => 'Accueil',
            'metaDesc'    => Helpers::getSetting('site_description', ''),
            'heroHeader'  => true,
            'bodyClass'   => 'page-home',
            'stats'       => $stats,
            'projets'     => $projets,
            'articles'    => $articles,
            'temoignages' => $temoignages,
            'partenaires' => $partenaires,
        ]);
    }

    // =====================================================
    // PAGE À PROPOS
    // =====================================================
    public function apropos(array $params = []): void
    {
        $stats = Helpers::getGlobalStats();

        // Contenu CMS (si modifié par l'admin)
        $page = $this->db->findOne('pages_cms', ['slug' => 'a-propos']);

        Helpers::view('public/apropos', [
            'pageTitle' => 'À propos de nous',
            'metaDesc'  => 'Découvrez EuroCare Humanitaire, notre histoire, nos valeurs et notre équipe dévouée.',
            'bodyClass' => 'page-about',
            'stats'     => $stats,
            'page'      => $page,
        ]);
    }

    // =====================================================
    // PAGE NOS MISSIONS
    // =====================================================
    public function missions(array $params = []): void
    {
        $projets = $this->db->query(
            'SELECT * FROM projets WHERE statut IN ("actif","complete") ORDER BY featured DESC, ordre ASC'
        )->fetchAll();

        $stats = Helpers::getGlobalStats();

        Helpers::view('public/missions', [
            'pageTitle' => 'Nos missions',
            'metaDesc'  => 'Découvrez toutes nos missions humanitaires et projets d\'assistance sociale.',
            'bodyClass' => 'page-missions',
            'projets'   => $projets,
            'stats'     => $stats,
        ]);
    }

    // =====================================================
    // PAGE NOS ACTIONS
    // =====================================================
    public function actions(array $params = []): void
    {
        // Statistiques par type d'aide
        $typesAides = $this->db->query(
            'SELECT type_aide, COUNT(*) AS nb, COALESCE(SUM(montant),0) AS total
             FROM aides_octroyees WHERE statut = "complete"
             GROUP BY type_aide ORDER BY nb DESC'
        )->fetchAll();

        // Dernières aides (anonymisées RGPD)
        $dernieresAides = $this->db->query(
            'SELECT ao.type_aide, ao.description, ao.date_completion,
                    bp.type_beneficiaire, bp.niveau_urgence
             FROM aides_octroyees ao
             JOIN beneficiaires_profils bp ON bp.id = ao.beneficiaire_id
             WHERE ao.statut = "complete"
             ORDER BY ao.date_completion DESC
             LIMIT 6'
        )->fetchAll();

        Helpers::view('public/actions', [
            'pageTitle'      => 'Nos actions de terrain',
            'metaDesc'       => 'Découvrez l\'impact concret de nos actions humanitaires sur le terrain.',
            'bodyClass'      => 'page-actions',
            'typesAides'     => $typesAides,
            'dernieresAides' => $dernieresAides,
        ]);
    }

    // =====================================================
    // PAGE NOS PARTENAIRES
    // =====================================================
    public function partenaires(array $params = []): void
    {
        $partenaires = $this->db->query(
            'SELECT * FROM partenaires_profils
             WHERE statut = "valide"
             ORDER BY featured DESC, ordre_affichage ASC, nom_organisation ASC'
        )->fetchAll();

        // Grouper par type
        $parParType = [];
        foreach ($partenaires as $p) {
            $parParType[$p['type_organisation']][] = $p;
        }

        Helpers::view('public/partenaires', [
            'pageTitle'   => 'Nos partenaires institutionnels',
            'metaDesc'    => 'Découvrez les organisations partenaires qui collaborent avec EuroCare Humanitaire.',
            'bodyClass'   => 'page-partners',
            'partenaires' => $partenaires,
            'parParType'  => $parParType,
        ]);
    }

    // =====================================================
    // PAGE TRANSPARENCE FINANCIÈRE
    // =====================================================
    public function transparence(array $params = []): void
    {
        $stats = Helpers::getGlobalStats();

        // Dons par mois (12 derniers mois)
        $donsParMois = $this->db->query(
            'SELECT DATE_FORMAT(cree_le, "%Y-%m") AS mois,
                    COALESCE(SUM(montant),0) AS total,
                    COUNT(*) AS nb
             FROM dons WHERE statut = "valide"
               AND cree_le >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
             GROUP BY mois ORDER BY mois ASC'
        )->fetchAll();

        // Répartition par cause/projet
        $repartition = $this->db->query(
            'SELECT COALESCE(p.titre, d.cause, "Fonds général") AS cause,
                    COALESCE(SUM(d.montant),0) AS total
             FROM dons d
             LEFT JOIN projets p ON p.id = d.projet_id
             WHERE d.statut = "valide"
             GROUP BY cause ORDER BY total DESC LIMIT 6'
        )->fetchAll();

        // Aides par type
        $aidesParType = $this->db->query(
            'SELECT type_aide, COUNT(*) AS nb, COALESCE(SUM(montant),0) AS total
             FROM aides_octroyees WHERE statut IN ("approuve","complete")
             GROUP BY type_aide ORDER BY nb DESC'
        )->fetchAll();

        // Projets actifs avec progression
        $projets = $this->db->query(
            'SELECT titre, objectif_montant, montant_collecte, beneficiaires_aides, categorie
             FROM projets WHERE statut = "actif" ORDER BY ordre ASC LIMIT 4'
        )->fetchAll();

        Helpers::view('public/transparence', [
            'pageTitle'    => 'Transparence financière',
            'metaDesc'     => 'Consultez l\'utilisation transparente des fonds collectés par EuroCare Humanitaire.',
            'bodyClass'    => 'page-transparency',
            'stats'        => $stats,
            'donsParMois'  => $donsParMois,
            'repartition'  => $repartition,
            'aidesParType' => $aidesParType,
            'projets'      => $projets,
        ]);
    }

    // =====================================================
    // PAGE TÉMOIGNAGES
    // =====================================================
    public function temoignages(array $params = []): void
    {
        $page = (int)($_GET['page'] ?? 1);
        $total = $this->db->count('temoignages', ['statut' => 'approuve']);
        $pagination = Helpers::paginate($total, $page, 9);

        $temoignages = $this->db->query(
            'SELECT * FROM temoignages WHERE statut = "approuve"
             ORDER BY featured DESC, note DESC, cree_le DESC
             LIMIT ? OFFSET ?',
            [$pagination['perPage'], $pagination['offset']]
        )->fetchAll();

        Helpers::view('public/temoignages', [
            'pageTitle'   => 'Témoignages',
            'metaDesc'    => 'Découvrez les témoignages touchants de bénéficiaires et donateurs.',
            'bodyClass'   => 'page-testimonials',
            'temoignages' => $temoignages,
            'pagination'  => $pagination,
        ]);
    }

    // =====================================================
    // PAGE FAQ
    // =====================================================
    public function faq(array $params = []): void
    {
        // Récupérer toutes les FAQ actives groupées par catégorie
        $faqs = $this->db->query(
            'SELECT * FROM faq WHERE actif = 1 ORDER BY categorie ASC, ordre ASC'
        )->fetchAll();

        // Grouper par catégorie
        $faqParCat = [];
        foreach ($faqs as $faq) {
            $faqParCat[$faq['categorie']][] = $faq;
        }

        $categories = [
            'general'     => 'Questions générales',
            'dons'        => 'Dons et financement',
            'aide'        => 'Demander une aide',
            'partenariat' => 'Partenariats',
            'rgpd'        => 'Données personnelles (RGPD)',
        ];

        Helpers::view('public/faq', [
            'pageTitle'  => 'Foire aux questions',
            'metaDesc'   => 'Toutes les réponses à vos questions sur EuroCare Humanitaire.',
            'bodyClass'  => 'page-faq',
            'faqParCat'  => $faqParCat,
            'categories' => $categories,
        ]);
    }

    // =====================================================
    // PAGE CONTACT
    // =====================================================
    public function contact(array $params = []): void
    {
        Helpers::view('public/contact', [
            'pageTitle' => 'Nous contacter',
            'metaDesc'  => 'Contactez EuroCare Humanitaire par téléphone, email ou formulaire.',
            'bodyClass' => 'page-contact',
            'extraJs'   => ['contact.js'],
        ]);
    }

    /**
     * Traitement du formulaire de contact (POST)
     */
    public function contactEnvoyer(array $params = []): void
    {
        // Validation CSRF
        $token = Security::input('_csrf_token', 'post');
        if (!Session::validateCsrfToken($token)) {
            Helpers::redirectWithFlash('/contact', 'erreur', 'Session expirée. Veuillez réessayer.');
        }

        // Récupération et validation des données
        $nom      = Security::input('nom');
        $email    = Security::input('email');
        $tel      = Security::input('telephone');
        $sujet    = Security::input('sujet');
        $message  = Security::input('message');

        $errors = [];
        if (mb_strlen($nom) < 2)     $errors[] = 'Votre nom est requis (minimum 2 caractères).';
        if (!Security::validateEmail($email)) $errors[] = 'Adresse email invalide.';
        if (mb_strlen($sujet) < 3)   $errors[] = 'Le sujet est requis.';
        if (mb_strlen($message) < 10) $errors[] = 'Votre message doit contenir au moins 10 caractères.';

        if (!empty($errors)) {
            Session::flash('erreur', implode('<br>', $errors));
            Helpers::redirect('/contact');
        }

        // Anti-spam : rate limiting par IP
        $ip = Security::getIp();
        $recentCount = $this->db->query(
            'SELECT COUNT(*) FROM contacts WHERE ip = ? AND cree_le > DATE_SUB(NOW(), INTERVAL 1 HOUR)',
            [$ip]
        )->fetchColumn();

        if ($recentCount >= 3) {
            Helpers::redirectWithFlash('/contact', 'erreur', 'Trop de messages envoyés. Veuillez patienter avant de réessayer.');
        }

        // Enregistrement en base
        $this->db->insert('contacts', [
            'nom'     => mb_substr($nom, 0, 200),
            'email'   => mb_substr($email, 0, 255),
            'telephone' => mb_substr($tel, 0, 20),
            'sujet'   => mb_substr($sujet, 0, 255),
            'message' => mb_substr($message, 0, 5000),
            'statut'  => 'nouveau',
            'ip'      => $ip,
            'cree_le' => date('Y-m-d H:i:s'),
        ]);

        // Notification à l'administrateur
        $admins = $this->db->query(
            'SELECT id FROM users WHERE role = "admin" AND statut = "actif" LIMIT 3'
        )->fetchAll();

        foreach ($admins as $admin) {
            Helpers::createNotification(
                $admin['id'],
                'Nouveau message de contact',
                "Message de {$nom} ({$email}) : {$sujet}",
                NOTIF_INFO,
                BASE_URL . '/admin/messages'
            );
        }

        Logger::log('contact_envoye', 'contacts', null, null, ['email' => $email, 'sujet' => $sujet]);

        Helpers::redirectWithFlash('/contact', 'succes',
            'Votre message a bien été envoyé. Nous vous répondrons dans les plus brefs délais.');
    }

    // =====================================================
    // PAGES LÉGALES
    // =====================================================
    public function politique(array $params = []): void
    {
        $page = $this->db->findOne('pages_cms', ['slug' => 'politique-confidentialite']);
        Helpers::view('public/politique', [
            'pageTitle' => 'Politique de confidentialité',
            'metaDesc'  => 'Politique de confidentialité et protection des données personnelles (RGPD).',
            'bodyClass' => 'page-legal',
            'page'      => $page,
        ]);
    }

    public function conditions(array $params = []): void
    {
        $page = $this->db->findOne('pages_cms', ['slug' => 'conditions-utilisation']);
        Helpers::view('public/conditions', [
            'pageTitle' => 'Conditions d\'utilisation',
            'metaDesc'  => 'Conditions générales d\'utilisation de la plateforme EuroCare Humanitaire.',
            'bodyClass' => 'page-legal',
            'page'      => $page,
        ]);
    }
}
