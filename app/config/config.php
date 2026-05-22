<?php
/**
 * =====================================================
 * EUROCARE HUMANITAIRE - Configuration Principale
 * =====================================================
 * Fichier : app/config/config.php
 * Description : Paramètres globaux de l'application
 * =====================================================
 */

// Sécurité : blocage d'accès direct
defined('BASEPATH') or die('Accès direct interdit.');

// =====================================================
// ENVIRONNEMENT
// =====================================================
define('APP_ENV',     getenv('APP_ENV') ?: 'production'); // 'development' | 'production'
define('APP_DEBUG',   APP_ENV === 'development');

// =====================================================
// CHEMINS DU PROJET
// =====================================================
define('ROOT_PATH',   dirname(__DIR__, 2));          // Racine du projet
define('APP_PATH',    ROOT_PATH . '/app');            // Dossier app/
define('PUBLIC_PATH', ROOT_PATH . '/public');         // Dossier public/
define('ASSETS_PATH', PUBLIC_PATH . '/assets');       // public/assets/
define('UPLOAD_PATH', PUBLIC_PATH . '/assets/uploads');// public/assets/uploads/
define('VIEWS_PATH',  APP_PATH . '/views');           // app/views/
define('STORAGE_PATH',ROOT_PATH . '/storage');        // Stockage privé

// =====================================================
// URL DE BASE
// =====================================================
$protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('BASE_URL',    $protocol . '://' . $host);
define('ASSETS_URL',  BASE_URL . '/assets');
define('UPLOAD_URL',  BASE_URL . '/assets/uploads');

// =====================================================
// APPLICATION
// =====================================================
define('APP_NAME',    'EuroCare Humanitaire');
define('APP_VERSION', '1.0.0');
define('APP_LOCALE',  'fr_FR');
define('APP_TIMEZONE','Europe/Paris');
define('APP_CHARSET', 'UTF-8');

// =====================================================
// SESSION
// =====================================================
define('SESSION_NAME',     'eurocare_session');
define('SESSION_LIFETIME', 7200);          // 2 heures en secondes
define('SESSION_SECURE',   APP_ENV === 'production');
define('SESSION_HTTPONLY',  true);
define('SESSION_SAMESITE',  'Strict');
define('REMEMBER_ME_DAYS',  30);           // Durée du cookie "Se souvenir"

// =====================================================
// SÉCURITÉ
// =====================================================
define('CSRF_TOKEN_NAME',  '_csrf_token');
define('CSRF_LIFETIME',    3600);          // 1 heure
define('MAX_LOGIN_ATTEMPTS', 5);           // Tentatives avant blocage
define('LOGIN_LOCKOUT_TIME', 900);         // 15 minutes de blocage
define('TOKEN_LENGTH',     64);            // Longueur des tokens aléatoires
define('PASSWORD_MIN_LENGTH', 8);
define('BCRYPT_COST',      12);            // Coût du hashage bcrypt

// =====================================================
// UPLOADS
// =====================================================
define('MAX_UPLOAD_SIZE',    10 * 1024 * 1024); // 10 Mo
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_DOC_TYPES',   ['application/pdf', 'image/jpeg', 'image/png',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
define('UPLOAD_SUBFOLDERS', ['profils', 'documents', 'articles', 'projets', 'partenaires', 'temoignages']);

// =====================================================
// PAGINATION
// =====================================================
define('ITEMS_PER_PAGE',       20);  // Éléments par page (tableaux admin)
define('ARTICLES_PER_PAGE',    9);   // Articles par page (blog)
define('COMMENTS_PER_PAGE',    10);  // Commentaires par page

// =====================================================
// EMAIL
// =====================================================
define('MAIL_FROM_ADDRESS', 'noreply@eurocare-humanitaire.eu');
define('MAIL_FROM_NAME',    'EuroCare Humanitaire');
define('MAIL_REPLY_TO',     'contact@eurocare-humanitaire.eu');

// =====================================================
// TOKENS D'EXPIRATION
// =====================================================
define('EMAIL_VERIFY_EXPIRE',   86400);  // 24h
define('PASSWORD_RESET_EXPIRE', 3600);   // 1h
define('API_TOKEN_EXPIRE',      2592000); // 30 jours

// =====================================================
// CONFIGURATION PHP GLOBALE
// =====================================================
date_default_timezone_set(APP_TIMEZONE);
mb_internal_encoding(APP_CHARSET);
mb_http_output(APP_CHARSET);

// Gestion des erreurs selon l'environnement
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('log_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', ROOT_PATH . '/storage/logs/php_errors.log');
}

// En-têtes de sécurité HTTP
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    if (APP_ENV === 'production') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
    // CSP (Content Security Policy) - ajuster selon les besoins
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self';");
}
