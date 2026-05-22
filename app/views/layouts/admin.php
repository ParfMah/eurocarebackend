<?php
/**
 * Layout : Espace Administration
 * app/views/layouts/admin.php
 */
defined('BASEPATH') or die('Accès direct interdit.');

// Sécurité : vérification rôle admin/modérateur
if (!Auth::isStaff()) {
    Helpers::redirect('/tableau-de-bord?erreur=acces_refuse');
}

$siteName    = Helpers::getSetting('site_nom', APP_NAME);
$pageTitle   = isset($pageTitle) ? Security::e($pageTitle) . ' — Admin' : 'Administration';
$currentUser = Auth::user();
$unreadMsg   = (int)Database::getInstance()->query(
    'SELECT COUNT(*) FROM contacts WHERE statut = "nouveau"'
)->fetchColumn();
$pendingBenef = (int)Database::getInstance()->query(
    'SELECT COUNT(*) FROM beneficiaires_profils WHERE statut_dossier = "en_attente"'
)->fetchColumn();
$unreadNotifs = Helpers::countUnreadNotifications($currentUser['id']);
$currentPath  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

function isActive(string $prefix): string {
    global $currentPath;
    return str_starts_with($currentPath, $prefix) ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?></title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/main.css">
  <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/admin.css">
  <?php if (!empty($extraCss)): foreach ($extraCss as $css): ?>
  <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/<?= Security::e($css) ?>">
  <?php endforeach; endif; ?>
</head>
<body class="admin-body">

<div id="toast-container"></div>

<div class="admin-layout">

  <!-- Overlay mobile -->
  <div class="admin-overlay" id="adminOverlay"></div>

  <!-- ====================================================
       SIDEBAR
       ==================================================== -->
  <aside class="admin-sidebar" id="adminSidebar" aria-label="Navigation administration">

    <!-- Logo -->
    <a href="<?= BASE_URL ?>/admin" class="sidebar-logo">
      <div class="sidebar-logo-icon">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>
      </div>
      <div>
        <div class="sidebar-logo-text"><?= Security::e(Helpers::getSetting('site_nom','EuroCare')) ?></div>
        <div class="sidebar-logo-sub">Administration</div>
      </div>
    </a>

    <!-- Navigation -->
    <nav class="sidebar-nav" aria-label="Menu administration">

      <div class="sidebar-section-title">Principal</div>

      <a href="<?= BASE_URL ?>/admin" class="sidebar-link <?= $currentPath === '/admin' ? 'active' : '' ?>">
        <span class="sidebar-link-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></span>
        Tableau de bord
      </a>

      <a href="<?= BASE_URL ?>/admin/statistiques" class="sidebar-link <?= isActive('/admin/statistiques') ?>">
        <span class="sidebar-link-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span>
        Statistiques
      </a>

      <div class="sidebar-section-title">Gestion sociale</div>

      <a href="<?= BASE_URL ?>/admin/beneficiaires" class="sidebar-link <?= isActive('/admin/beneficiaires') ?>">
        <span class="sidebar-link-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></span>
        Bénéficiaires
        <?php if ($pendingBenef > 0): ?><span class="sidebar-link-badge"><?= $pendingBenef ?></span><?php endif; ?>
      </a>

      <a href="<?= BASE_URL ?>/admin/dons" class="sidebar-link <?= isActive('/admin/dons') ?>">
        <span class="sidebar-link-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg></span>
        Dons
      </a>

      <a href="<?= BASE_URL ?>/admin/partenaires" class="sidebar-link <?= isActive('/admin/partenaires') ?>">
        <span class="sidebar-link-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg></span>
        Partenaires
      </a>

      <div class="sidebar-section-title">Contenu</div>

      <a href="<?= BASE_URL ?>/admin/articles" class="sidebar-link <?= isActive('/admin/articles') ?>">
        <span class="sidebar-link-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></span>
        Articles / Blog
      </a>

      <a href="<?= BASE_URL ?>/admin/projets" class="sidebar-link <?= isActive('/admin/projets') ?>">
        <span class="sidebar-link-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg></span>
        Projets / Causes
      </a>

      <a href="<?= BASE_URL ?>/admin/temoignages" class="sidebar-link <?= isActive('/admin/temoignages') ?>">
        <span class="sidebar-link-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg></span>
        Témoignages
      </a>

      <a href="<?= BASE_URL ?>/admin/faq" class="sidebar-link <?= isActive('/admin/faq') ?>">
        <span class="sidebar-link-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></span>
        FAQ
      </a>

      <a href="<?= BASE_URL ?>/admin/pages" class="sidebar-link <?= isActive('/admin/pages') ?>">
        <span class="sidebar-link-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg></span>
        Pages CMS
      </a>

      <div class="sidebar-section-title">Administration</div>

      <a href="<?= BASE_URL ?>/admin/utilisateurs" class="sidebar-link <?= isActive('/admin/utilisateurs') ?>">
        <span class="sidebar-link-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
        Utilisateurs
      </a>

      <a href="<?= BASE_URL ?>/admin/messages" class="sidebar-link <?= isActive('/admin/messages') ?>">
        <span class="sidebar-link-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
        Messages
        <?php if ($unreadMsg > 0): ?><span class="sidebar-link-badge"><?= $unreadMsg ?></span><?php endif; ?>
      </a>

      <a href="<?= BASE_URL ?>/admin/audit" class="sidebar-link <?= isActive('/admin/audit') ?>">
        <span class="sidebar-link-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></span>
        Journal d'audit
      </a>

      <?php if (Auth::isAdmin()): ?>
      <a href="<?= BASE_URL ?>/admin/parametres" class="sidebar-link <?= isActive('/admin/parametres') ?>">
        <span class="sidebar-link-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg></span>
        Paramètres
      </a>
      <?php endif; ?>
    </nav>

    <!-- Profil utilisateur bas sidebar -->
    <div class="sidebar-user">
      <div class="sidebar-user-avatar">
        <?= mb_strtoupper(mb_substr($currentUser['prenom'],0,1).mb_substr($currentUser['nom'],0,1)) ?>
      </div>
      <div>
        <div class="sidebar-user-name"><?= Security::e($currentUser['prenom'].' '.$currentUser['nom']) ?></div>
        <div class="sidebar-user-role"><?= Security::e(ROLES_LABELS[$currentUser['role']] ?? $currentUser['role']) ?></div>
      </div>
    </div>
  </aside>

  <!-- ====================================================
       CONTENU PRINCIPAL
       ==================================================== -->
  <div class="admin-content">

    <!-- Topbar admin -->
    <header class="admin-topbar">
      <div class="admin-topbar-left">
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Basculer le menu">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <div class="admin-page-title"><?= $pageTitle ?></div>
      </div>

      <!-- Recherche globale -->
      <div class="admin-search">
        <svg class="admin-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" class="admin-search-input" placeholder="Rechercher..." id="adminGlobalSearch">
      </div>

      <div class="admin-topbar-right">
        <!-- Lien site public -->
        <a href="<?= BASE_URL ?>/" target="_blank" class="table-action-btn" title="Voir le site public" style="width:auto;height:auto;padding:6px 12px;font-size:13px;gap:6px;display:flex">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
          Site public
        </a>

        <!-- Notifications -->
        <div style="position:relative">
          <button class="notif-btn" id="adminNotifBtn" aria-label="Notifications">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
            <?php if ($unreadNotifs > 0): ?>
            <span class="notif-count"><?= min($unreadNotifs, 99) ?></span>
            <?php endif; ?>
          </button>
        </div>

        <!-- Déconnexion -->
        <a href="<?= BASE_URL ?>/deconnexion" class="btn btn-ghost btn-sm" style="gap:6px">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          Déconnexion
        </a>
      </div>
    </header>

    <!-- Messages flash -->
    <?php if (Session::hasFlash()): ?>
    <div style="padding:var(--space-4) var(--space-6) 0">
      <?php foreach (['succes'=>'success','erreur'=>'danger','info'=>'info','attention'=>'warning'] as $type => $cls): ?>
        <?php foreach (Session::getFlash($type) as $msg): ?>
        <div class="alert alert-<?= $cls ?>" role="alert">
          <div class="alert-content"><?= $msg ?></div>
          <button onclick="this.closest('.alert').remove()" style="margin-left:auto;opacity:.6">&times;</button>
        </div>
        <?php endforeach; ?>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Contenu de la page -->
    <main class="admin-main" id="adminMain">
      <?= $content ?>
    </main>
  </div>
</div>

<script>
const BASE_URL   = '<?= BASE_URL ?>';
const CSRF_TOKEN = '<?= Session::generateCsrfToken() ?>';
const IS_AUTH    = true;
const USER_ROLE  = '<?= Auth::role() ?>';
</script>
<script src="<?= ASSETS_URL ?>/js/main.js"></script>
<script src="<?= ASSETS_URL ?>/js/admin.js"></script>
<?php if (!empty($extraJs)): foreach ($extraJs as $js): ?>
<script src="<?= ASSETS_URL ?>/js/<?= Security::e($js) ?>"></script>
<?php endforeach; endif; ?>
</body>
</html>
