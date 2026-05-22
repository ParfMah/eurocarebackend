<?php
/**
 * Layout user dashboard - wrappeur du layout main
 * Injecte le dashboard.css et le layout dashboard
 */
defined('BASEPATH') or die('Accès direct interdit.');

// Ajouter dashboard.css aux CSS extras
if (!isset($extraCss)) $extraCss = [];
if (!in_array('dashboard.css', $extraCss)) {
    $extraCss[] = 'dashboard.css';
}

// Le $content actuel devient le contenu du dashboard
$dashboardContent = $content;

// Capturer le layout dashboard
ob_start();
$content = $dashboardContent;
require VIEWS_PATH . '/layouts/dashboard.php';
$content = ob_get_clean();

// Charger le layout main avec le contenu wrappé
require VIEWS_PATH . '/layouts/main.php';
