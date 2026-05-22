<?php
/**
 * =====================================================
 * EUROCARE HUMANITAIRE - Point d'entrée principal
 * =====================================================
 * Fichier : public/index.php
 * Description : Front controller unique. Toutes les
 *   requêtes HTTP passent par ce fichier via .htaccess.
 *   Initialise l'application et dispatche les routes.
 * =====================================================
 */

// Constante de sécurité (bloque l'accès direct aux fichiers internes)
define('BASEPATH', true);

// =====================================================
// CHARGEMENT DES FICHIERS CORE
// =====================================================
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/config/constants.php';

// Classes core
require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/core/Session.php';
require_once APP_PATH . '/core/Security.php';
require_once APP_PATH . '/core/Auth.php';
require_once APP_PATH . '/core/Logger.php';
require_once APP_PATH . '/core/Helpers.php';
require_once APP_PATH . '/core/Mailer.php';
require_once APP_PATH . '/core/RateLimiter.php';
require_once APP_PATH . '/core/Router.php';

// =====================================================
// DÉMARRAGE DE LA SESSION
// =====================================================
Session::start();

// =====================================================
// MODE MAINTENANCE
// =====================================================
if (Helpers::getSetting('maintenance_mode', false) && !Auth::isAdmin()) {
    http_response_code(503);
    require VIEWS_PATH . '/errors/maintenance.php';
    exit;
}

// =====================================================
// DÉFINITION DES ROUTES
// =====================================================
$router = new Router();

// ---------- PAGES PUBLIQUES ----------
$router->get('/',                         'PublicController@accueil');
$router->get('/a-propos',                 'PublicController@apropos');
$router->get('/nos-missions',             'PublicController@missions');
$router->get('/nos-actions',              'PublicController@actions');
$router->get('/nos-partenaires',          'PublicController@partenaires');
$router->get('/transparence',             'PublicController@transparence');
$router->get('/temoignages',              'PublicController@temoignages');
$router->get('/faq',                      'PublicController@faq');
$router->get('/contact',                  'PublicController@contact');
$router->post('/contact',                 'PublicController@contactEnvoyer');
$router->get('/politique-confidentialite','PublicController@politique');
$router->get('/conditions-utilisation',   'PublicController@conditions');

// ---------- BLOG / ACTUALITÉS ----------
$router->get('/actualites',               'BlogController@liste');
$router->get('/actualites/categorie/{slug}','BlogController@categorie');
$router->get('/actualites/{slug}',        'BlogController@article');
$router->post('/actualites/{slug}/commenter','BlogController@commenter');
$router->get('/recherche',                'BlogController@recherche');

// ---------- DONS (public) ----------
$router->get('/faire-un-don',             'DonController@formulaire');
$router->post('/faire-un-don',            'DonController@traiter');
$router->get('/don/confirmation/{uuid}',  'DonController@confirmation');
$router->get('/don/recu/{uuid}',          'DonController@recu');

// ---------- DEMANDE D'AIDE (public) ----------
$router->get('/demander-une-aide',        'AideController@formulairePublic');

// ---------- AUTHENTIFICATION ----------
$router->get('/inscription',              'AuthController@inscription',      ['guest']);
$router->post('/inscription',             'AuthController@inscrire',          ['guest']);
$router->get('/connexion',                'AuthController@connexion',         ['guest']);
$router->post('/connexion',               'AuthController@connecter',         ['guest']);
$router->get('/deconnexion',              'AuthController@deconnecter',       ['auth']);
$router->get('/mot-de-passe-oublie',      'AuthController@mdpOublie',         ['guest']);
$router->post('/mot-de-passe-oublie',     'AuthController@mdpEnvoyer',        ['guest']);
$router->get('/reinitialiser/{token}',    'AuthController@mdpReinitForm',     ['guest']);
$router->post('/reinitialiser/{token}',   'AuthController@mdpReinitialiser',  ['guest']);
$router->get('/verifier-email/{token}',   'AuthController@verifierEmail');

// ---------- TABLEAU DE BORD (dispatch selon rôle) ----------
$router->get('/tableau-de-bord',          'DashboardController@index',        ['auth']);

// ---------- ESPACE DONATEUR ----------
$router->get('/donateur/tableau-de-bord', 'DonController@dashboard',         ['donateur']);
$router->get('/donateur/mes-dons',        'DonController@mesDons',            ['donateur']);
$router->get('/donateur/profil',          'DonController@profil',             ['donateur']);
$router->post('/donateur/profil',         'DonController@updateProfil',       ['donateur']);
$router->get('/donateur/recus',           'DonController@recus',              ['donateur']);
$router->get('/donateur/impact',          'DonController@impact',             ['donateur']);

// ---------- ESPACE BÉNÉFICIAIRE ----------
$router->get('/beneficiaire/tableau-de-bord', 'BeneficiaireController@dashboard', ['beneficiaire']);
$router->get('/beneficiaire/mon-dossier',    'BeneficiaireController@dossier',    ['beneficiaire']);
$router->post('/beneficiaire/mon-dossier',   'BeneficiaireController@saveDossier',['beneficiaire']);
$router->post('/beneficiaire/document',      'BeneficiaireController@uploadDoc',  ['beneficiaire']);
$router->get('/beneficiaire/mes-aides',      'BeneficiaireController@mesAides',   ['beneficiaire']);
$router->get('/beneficiaire/messages',       'BeneficiaireController@messages',   ['beneficiaire']);
$router->post('/beneficiaire/messages',      'BeneficiaireController@envoyerMsg', ['beneficiaire']);
$router->get('/beneficiaire/profil',         'BeneficiaireController@profil',     ['beneficiaire']);
$router->post('/beneficiaire/profil',        'BeneficiaireController@updateProfil',['beneficiaire']);

// ---------- ESPACE PARTENAIRE ----------
$router->get('/partenaire/tableau-de-bord',  'PartenaireController@dashboard',    ['partenaire']);
$router->get('/partenaire/mon-profil',        'PartenaireController@profil',       ['partenaire']);
$router->post('/partenaire/mon-profil',       'PartenaireController@updateProfil', ['partenaire']);
$router->get('/partenaire/recommandations',   'PartenaireController@recommandations',['partenaire']);
$router->post('/partenaire/recommandations',  'PartenaireController@soumettre',    ['partenaire']);

// ---------- ESPACE ADMINISTRATEUR ----------
$router->get('/admin',                        'AdminController@dashboard',         ['admin']);
$router->get('/admin/utilisateurs',           'AdminController@utilisateurs',      ['admin']);
$router->get('/admin/utilisateurs/{id}',      'AdminController@utilisateur',       ['admin']);
$router->post('/admin/utilisateurs/{id}',     'AdminController@updateUtilisateur', ['admin']);
$router->post('/admin/utilisateurs/{id}/supprimer','AdminController@supprimerUser',['admin']);
$router->post('/admin/utilisateurs/{id}/statut',   'AdminController@changerStatut',['admin']);

$router->get('/admin/beneficiaires',          'AdminController@beneficiaires',     ['moderateur']);
$router->get('/admin/beneficiaires/{id}',     'AdminController@beneficiaire',      ['moderateur']);
$router->post('/admin/beneficiaires/{id}/statut','AdminController@changerStatutDossier',['moderateur']);
$router->post('/admin/beneficiaires/{id}/aide',  'AdminController@accorderAide',   ['moderateur']);

$router->get('/admin/dons',                   'AdminController@dons',              ['moderateur']);
$router->get('/admin/dons/{id}',              'AdminController@don',               ['moderateur']);
$router->post('/admin/dons/{id}/valider',     'AdminController@validerDon',        ['admin']);

$router->get('/admin/partenaires',            'AdminController@listePartenaires',  ['moderateur']);
$router->post('/admin/partenaires/{id}/valider','AdminController@validerPartenaire',['admin']);

$router->get('/admin/articles',               'AdminController@articles',          ['moderateur']);
$router->get('/admin/articles/nouveau',       'AdminController@nouvelArticle',     ['moderateur']);
$router->post('/admin/articles/nouveau',      'AdminController@creerArticle',      ['moderateur']);
$router->get('/admin/articles/{id}/editer',   'AdminController@editerArticle',     ['moderateur']);
$router->post('/admin/articles/{id}/editer',  'AdminController@updateArticle',     ['moderateur']);
$router->post('/admin/articles/{id}/supprimer','AdminController@supprimerArticle', ['moderateur']);

$router->get('/admin/projets',                'AdminController@projets',           ['admin']);
$router->post('/admin/projets',               'AdminController@creerProjet',       ['admin']);
$router->post('/admin/projets/{id}',          'AdminController@updateProjet',      ['admin']);

$router->get('/admin/messages',               'AdminController@messages',          ['moderateur']);
$router->get('/admin/messages/{id}',          'AdminController@message',           ['moderateur']);
$router->post('/admin/messages/{id}/repondre','AdminController@repondreMessage',   ['moderateur']);

$router->get('/admin/temoignages',            'AdminController@temoignages',       ['moderateur']);
$router->post('/admin/temoignages/{id}/statut','AdminController@statTemoignage',   ['moderateur']);

$router->get('/admin/faq',                    'AdminController@faq',               ['admin']);
$router->post('/admin/faq',                   'AdminController@saveFaq',           ['admin']);

$router->get('/admin/audit',                  'AdminController@audit',             ['admin']);
$router->get('/admin/statistiques',           'AdminController@statistiques',      ['admin']);
$router->get('/admin/parametres',             'AdminController@parametres',        ['admin']);
$router->post('/admin/parametres',            'AdminController@saveParametres',    ['admin']);
$router->get('/admin/pages',                  'AdminController@pages',             ['admin']);
$router->post('/admin/pages/{id}',            'AdminController@savePage',          ['admin']);
$router->get('/admin/export/{type}',          'AdminController@exporter',          ['admin']);

// ---------- API AJAX ----------
$router->post('/api/notifications/lire',      'ApiController@lireNotification',   ['auth']);
$router->get('/api/notifications',            'ApiController@getNotifications',    ['auth']);
$router->get('/api/stats',                    'ApiController@getStats');
$router->post('/api/newsletter',              'ApiController@newsletter');

// =====================================================
// DISPATCH
// =====================================================
$router->dispatch();
