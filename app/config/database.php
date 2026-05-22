<?php
/**
 * =====================================================
 * EUROCARE HUMANITAIRE - Configuration Base de Données
 * =====================================================
 * Fichier : app/config/database.php
 * Description : Paramètres de connexion MySQL
 *
 * IMPORTANT : En production, ces valeurs doivent être
 * définies via des variables d'environnement (.env)
 * et JAMAIS versionnées dans Git.
 * =====================================================
 */

defined('BASEPATH') or die('Accès direct interdit.');

// =====================================================
// PARAMÈTRES DE CONNEXION MYSQL
// (À remplacer par des variables d'environnement)
// =====================================================
define('DB_HOST',    getenv('DB_HOST')    ?: 'localhost');
define('DB_PORT',    getenv('DB_PORT')    ?: '3306');
define('DB_NAME',    getenv('DB_NAME')    ?: 'eurocare_humanitaire');
define('DB_USER',    getenv('DB_USER')    ?: 'root');
define('DB_PASS',    getenv('DB_PASS')    ?: '');
define('DB_CHARSET', 'utf8mb4');

// =====================================================
// OPTIONS PDO
// =====================================================
define('DB_OPTIONS', serialize([
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_PERSISTENT         => false,          // Pas de connexions persistantes
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci,
                                         time_zone = '+01:00',
                                         sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE'",
]));
