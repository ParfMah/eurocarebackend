<?php
/**
 * Layout principal public
 * app/views/layouts/main.php
 */
defined('BASEPATH') or die('Accès direct interdit.');

$siteName   = Helpers::getSetting('site_nom', APP_NAME);
$pageTitle  = isset($pageTitle)  ? Security::e($pageTitle) . ' — ' . $siteName : $siteName;
$metaDesc   = isset($metaDesc)   ? Security::e($metaDesc)  : Helpers::getSetting('site_description', '');
$bodyClass  = $bodyClass  ?? '';
$heroHeader = $heroHeader ?? false;
$currentUser = Auth::user();
$unreadNotifs = $currentUser ? Helpers::countUnreadNotifications($currentUser['id']) : 0;
$csrfToken  = Session::generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?= $pageTitle ?></title>
  <meta name="description" content="<?= $metaDesc ?>">
  <meta name="robots" content="index, follow">
  <meta name="theme-color" content="#0d2b6e">
  <!-- Open Graph -->
  <meta property="og:title" content="<?= $pageTitle ?>">
  <meta property="og:description" content="<?= $metaDesc ?>">
  <meta property="og:type" content="website">
  <meta property="og:locale" content="fr_FR">
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
  <!-- CSS -->
  <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/main.css">
  <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/nav.css">
  <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/public.css">
  <?php if (!empty($extraCss)): foreach ($extraCss as $css): ?>
  <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/<?= Security::e($css) ?>">
  <?php endforeach; endif; ?>
</head>
<body class="<?= Security::e($bodyClass) ?>">

<!-- Chargeur de page -->
<div class="page-loader" id="pageLoader">
  <div class="loader-logo"><?= Security::e($siteName) ?></div>
  <div class="loader-spinner"></div>
</div>

<!-- Conteneur de toasts -->
<div id="toast-container" role="status" aria-live="polite"></div>

<!-- ====================================================
     BARRE D'ANNONCE SUPÉRIEURE
     ==================================================== -->
<div class="topbar" role="banner">
  <div class="container">
    <div class="topbar-inner">
      <div class="topbar-left">
        <span class="topbar-item">
          <svg viewBox="0 0 20 20" fill="currentColor"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/></svg>
          <?= Security::e(Helpers::getSetting('site_telephone', '+33 1 23 45 67 89')) ?>
        </span>
        <span class="topbar-item">
          <svg viewBox="0 0 20 20" fill="currentColor"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
          <?= Security::e(Helpers::getSetting('site_email', 'contact@eurocare-humanitaire.eu')) ?>
        </span>
      </div>
      <div class="topbar-right">
        <div class="topbar-social">
          <?php $fb = Helpers::getSetting('facebook_url'); if ($fb): ?>
          <a href="<?= Security::e($fb) ?>" target="_blank" rel="noopener" aria-label="Facebook">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
          </a>
          <?php endif; ?>
          <?php $tw = Helpers::getSetting('twitter_url'); if ($tw): ?>
          <a href="<?= Security::e($tw) ?>" target="_blank" rel="noopener" aria-label="Twitter">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
          </a>
          <?php endif; ?>
          <?php $li = Helpers::getSetting('linkedin_url'); if ($li): ?>
          <a href="<?= Security::e($li) ?>" target="_blank" rel="noopener" aria-label="LinkedIn">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
          </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ====================================================
     HEADER PRINCIPAL
     ==================================================== -->
<header class="site-header <?= $heroHeader ? 'hero-mode' : '' ?>" id="siteHeader" role="navigation">
  <div class="container">
    <div class="header-inner">

      <!-- Logo -->
      <a href="<?= BASE_URL ?>/" class="site-logo" aria-label="<?= Security::e($siteName) ?> - Accueil">
        <div class="logo-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            <path d="M9 12l2 2 4-4" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="logo-text">
          <span class="logo-name"><?= Security::e(Helpers::getSetting('site_nom', APP_NAME)) ?></span>
          <span class="logo-tagline">Organisation Humanitaire Européenne</span>
        </div>
      </a>

      <!-- Navigation desktop -->
      <nav aria-label="Navigation principale">
        <ul class="main-nav" role="list">
          <li class="nav-item">
            <a href="<?= BASE_URL ?>/" class="nav-link <?= (($_SERVER['REQUEST_URI'] ?? '/') === '/') ? 'active' : '' ?>">Accueil</a>
          </li>
          <li class="nav-item">
            <a href="<?= BASE_URL ?>/a-propos" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/a-propos') ? 'active' : '' ?>">À propos</a>
          </li>
          <li class="nav-item">
            <button class="nav-link" aria-haspopup="true" aria-expanded="false">
              Nos actions
              <svg class="nav-arrow" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
            </button>
            <ul class="dropdown-menu" role="menu">
              <li><a href="<?= BASE_URL ?>/nos-missions" class="dropdown-link" role="menuitem">
                <span class="dropdown-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg></span>
                <span>Nos missions</span>
              </a></li>
              <li><a href="<?= BASE_URL ?>/nos-actions" class="dropdown-link" role="menuitem">
                <span class="dropdown-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg></span>
                <span>Actions de terrain</span>
              </a></li>
              <li><a href="<?= BASE_URL ?>/nos-partenaires" class="dropdown-link" role="menuitem">
                <span class="dropdown-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg></span>
                <span>Nos partenaires</span>
              </a></li>
              <li role="separator" class="dropdown-divider"></li>
              <li><a href="<?= BASE_URL ?>/transparence" class="dropdown-link" role="menuitem">
                <span class="dropdown-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg></span>
                <span>Transparence financière</span>
              </a></li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="<?= BASE_URL ?>/actualites" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/actualites') ? 'active' : '' ?>">Actualités</a>
          </li>
          <li class="nav-item">
            <a href="<?= BASE_URL ?>/contact" class="nav-link">Contact</a>
          </li>
        </ul>
      </nav>

      <!-- Actions header -->
      <div class="header-actions">

        <?php if (Auth::check() && $currentUser): ?>
          <!-- Badge notifications -->
          <div class="notif-badge nav-item">
            <button class="notif-btn" aria-label="Notifications (<?= $unreadNotifs ?> non lues)" id="notifBtn">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
              <?php if ($unreadNotifs > 0): ?>
              <span class="notif-count"><?= min($unreadNotifs, 99) ?></span>
              <?php endif; ?>
            </button>
            <!-- Dropdown notifications -->
            <div class="dropdown-menu user-dropdown" id="notifDropdown" style="min-width:320px">
              <div class="user-dropdown-header">
                <div class="user-dropdown-name">Notifications</div>
                <?php if ($unreadNotifs > 0): ?>
                <div class="user-dropdown-role"><?= $unreadNotifs ?> non lue(s)</div>
                <?php endif; ?>
              </div>
              <div id="notifList" style="max-height:320px;overflow-y:auto">
                <div class="p-4 text-center text-gray text-sm">Chargement...</div>
              </div>
              <div style="padding:var(--space-3) var(--space-4);border-top:1px solid var(--color-gray-100)">
                <a href="<?= BASE_URL ?>/tableau-de-bord?tab=notifications" class="text-sm text-blue">Voir toutes les notifications</a>
              </div>
            </div>
          </div>

          <!-- Menu utilisateur -->
          <div class="nav-item">
            <button class="user-menu-trigger" id="userMenuBtn" aria-haspopup="true" aria-expanded="false">
              <div class="user-avatar">
                <?php if ($currentUser['photo_profil']): ?>
                  <img src="<?= Helpers::avatarUrl($currentUser['photo_profil']) ?>" alt="<?= Security::e($currentUser['prenom']) ?>">
                <?php else: ?>
                  <?= mb_strtoupper(mb_substr($currentUser['prenom'], 0, 1) . mb_substr($currentUser['nom'], 0, 1)) ?>
                <?php endif; ?>
              </div>
              <span class="user-name"><?= Security::e($currentUser['prenom']) ?></span>
              <svg class="user-arrow" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
            </button>
            <ul class="dropdown-menu user-dropdown" role="menu">
              <li class="user-dropdown-header">
                <div class="user-dropdown-name"><?= Security::e($currentUser['prenom'] . ' ' . $currentUser['nom']) ?></div>
                <div class="user-dropdown-role"><?= Security::e(ROLES_LABELS[$currentUser['role']] ?? $currentUser['role']) ?></div>
              </li>
              <li role="separator" class="dropdown-divider"></li>
              <li><a href="<?= BASE_URL ?>/tableau-de-bord" class="dropdown-link" role="menuitem">
                <span class="dropdown-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></span>
                Mon tableau de bord
              </a></li>
              <?php if (Auth::isAdmin() || Auth::isStaff()): ?>
              <li><a href="<?= BASE_URL ?>/admin" class="dropdown-link" role="menuitem">
                <span class="dropdown-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 15a3 3 0 100-6 3 3 0 000 6z"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg></span>
                Administration
              </a></li>
              <?php endif; ?>
              <li role="separator" class="dropdown-divider"></li>
              <li><a href="<?= BASE_URL ?>/deconnexion" class="dropdown-link" role="menuitem" style="color:var(--color-danger)">
                <span class="dropdown-icon" style="background:#fef2f2;color:var(--color-danger)"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></span>
                Se déconnecter
              </a></li>
            </ul>
          </div>

        <?php else: ?>
          <a href="<?= BASE_URL ?>/connexion" class="header-btn-auth btn-connexion">Connexion</a>
          <a href="<?= BASE_URL ?>/faire-un-don" class="header-don-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
            <span class="btn-text">Faire un don</span>
          </a>
        <?php endif; ?>

        <!-- Bouton menu mobile -->
        <button class="menu-toggle" id="menuToggle" aria-label="Ouvrir le menu" aria-expanded="false">
          <span class="hamburger-line"></span>
          <span class="hamburger-line"></span>
          <span class="hamburger-line"></span>
        </button>
      </div>
    </div>
  </div>
</header>

<!-- ====================================================
     MENU MOBILE
     ==================================================== -->
<div class="mobile-overlay" id="mobileOverlay" aria-hidden="true"></div>
<nav class="mobile-nav" id="mobileNav" aria-label="Menu mobile" aria-hidden="true">
  <div class="mobile-nav-header">
    <a href="<?= BASE_URL ?>/" class="site-logo">
      <div class="logo-icon" style="width:36px;height:36px">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>
      </div>
      <span class="logo-name" style="color:var(--color-blue-deep)"><?= Security::e(Helpers::getSetting('site_nom', APP_NAME)) ?></span>
    </a>
    <button class="mobile-nav-close" id="mobileNavClose" aria-label="Fermer le menu">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
  </div>
  <div class="mobile-nav-body">
    <ul class="mobile-nav-list">
      <li><a href="<?= BASE_URL ?>/" class="mobile-nav-link">Accueil</a></li>
      <li><a href="<?= BASE_URL ?>/a-propos" class="mobile-nav-link">À propos</a></li>
      <li>
        <button class="mobile-nav-link has-sub w-full" data-sub="actions">
          Nos actions
          <svg class="mobile-nav-arrow" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
        </button>
        <ul class="mobile-submenu" id="sub-actions">
          <li><a href="<?= BASE_URL ?>/nos-missions"   class="mobile-submenu-link">Nos missions</a></li>
          <li><a href="<?= BASE_URL ?>/nos-actions"    class="mobile-submenu-link">Actions de terrain</a></li>
          <li><a href="<?= BASE_URL ?>/nos-partenaires"class="mobile-submenu-link">Nos partenaires</a></li>
          <li><a href="<?= BASE_URL ?>/transparence"   class="mobile-submenu-link">Transparence financière</a></li>
        </ul>
      </li>
      <li><a href="<?= BASE_URL ?>/actualites"    class="mobile-nav-link">Actualités</a></li>
      <li><a href="<?= BASE_URL ?>/temoignages"   class="mobile-nav-link">Témoignages</a></li>
      <li><a href="<?= BASE_URL ?>/faq"           class="mobile-nav-link">FAQ</a></li>
      <li><a href="<?= BASE_URL ?>/contact"       class="mobile-nav-link">Contact</a></li>
      <hr class="mobile-nav-divider">
      <div class="mobile-nav-section-title">Mon espace</div>
      <?php if (Auth::check()): ?>
      <li><a href="<?= BASE_URL ?>/tableau-de-bord" class="mobile-nav-link">Mon tableau de bord</a></li>
      <?php if (Auth::isAdmin()): ?>
      <li><a href="<?= BASE_URL ?>/admin"           class="mobile-nav-link">Administration</a></li>
      <?php endif; ?>
      <li><a href="<?= BASE_URL ?>/deconnexion"     class="mobile-nav-link" style="color:var(--color-danger)">Déconnexion</a></li>
      <?php else: ?>
      <li><a href="<?= BASE_URL ?>/connexion"   class="mobile-nav-link">Connexion</a></li>
      <li><a href="<?= BASE_URL ?>/inscription" class="mobile-nav-link">Créer un compte</a></li>
      <?php endif; ?>
    </ul>
  </div>
  <div class="mobile-nav-footer">
    <a href="<?= BASE_URL ?>/faire-un-don" class="btn btn-gold btn-block">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
      Faire un don maintenant
    </a>
  </div>
</nav>

<!-- ====================================================
     MESSAGES FLASH
     ==================================================== -->
<?php if (Session::hasFlash()): ?>
<div class="container" style="padding-top:var(--space-4);padding-bottom:0" id="flashContainer">
  <?php foreach (['succes' => ['success','✓'], 'erreur' => ['danger','✕'], 'info' => ['info','ℹ'], 'attention' => ['warning','⚠']] as $type => [$cls, $icon]): ?>
    <?php foreach (Session::getFlash($type) as $msg): ?>
    <div class="alert alert-<?= $cls ?> animate-fade-in" role="alert">
      <span class="alert-icon"><?= $icon ?></span>
      <div class="alert-content"><?= Security::e($msg) ?></div>
      <button onclick="this.closest('.alert').remove()" aria-label="Fermer" style="margin-left:auto;padding:0 var(--space-2);font-size:1.1rem;color:inherit;opacity:.6">&times;</button>
    </div>
    <?php endforeach; ?>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ====================================================
     CONTENU PRINCIPAL
     ==================================================== -->
<main id="main-content" role="main">
  <?= $content ?>
</main>

<!-- ====================================================
     FOOTER
     ==================================================== -->
<footer class="site-footer" role="contentinfo">
  <div class="container">
    <div class="footer-top">
      <div class="footer-grid">

        <!-- Colonne marque -->
        <div class="footer-brand">
          <a href="<?= BASE_URL ?>/" class="footer-logo">
            <div class="logo-icon" style="width:44px;height:44px;flex-shrink:0">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>
            </div>
            <div class="footer-logo-text logo-text">
              <span class="logo-name"><?= Security::e(Helpers::getSetting('site_nom', APP_NAME)) ?></span>
              <span class="logo-tagline">Organisation Humanitaire Européenne</span>
            </div>
          </a>
          <p class="footer-desc"><?= Security::e(Helpers::getSetting('site_description', '')) ?></p>
          <div class="footer-social">
            <?php $networks = ['facebook_url' => ['M24 12.073...','Facebook'], 'twitter_url' => ['M18.244...','X (Twitter)'], 'linkedin_url' => ['M20.447...','LinkedIn'], 'instagram_url' => ['...','Instagram']]; ?>
            <?php foreach (['facebook_url','twitter_url','linkedin_url','instagram_url'] as $net): ?>
              <?php $url = Helpers::getSetting($net); if ($url): ?>
              <a href="<?= Security::e($url) ?>" class="footer-social-link" target="_blank" rel="noopener" aria-label="<?= $net ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/></svg>
              </a>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
          <!-- Certifications -->
          <div class="footer-certifications">
            <div class="cert-badge">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              Don en confiance
            </div>
            <div class="cert-badge">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              RGPD Conforme
            </div>
            <div class="cert-badge">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
              ISO 9001
            </div>
          </div>
        </div>

        <!-- Colonne navigation -->
        <div>
          <h4 class="footer-col-title">Navigation</h4>
          <ul class="footer-links">
            <li><a href="<?= BASE_URL ?>/a-propos"        class="footer-link">À propos de nous</a></li>
            <li><a href="<?= BASE_URL ?>/nos-missions"     class="footer-link">Nos missions</a></li>
            <li><a href="<?= BASE_URL ?>/nos-actions"      class="footer-link">Nos actions</a></li>
            <li><a href="<?= BASE_URL ?>/nos-partenaires"  class="footer-link">Nos partenaires</a></li>
            <li><a href="<?= BASE_URL ?>/transparence"     class="footer-link">Transparence</a></li>
            <li><a href="<?= BASE_URL ?>/actualites"       class="footer-link">Actualités</a></li>
            <li><a href="<?= BASE_URL ?>/temoignages"      class="footer-link">Témoignages</a></li>
          </ul>
        </div>

        <!-- Colonne agir -->
        <div>
          <h4 class="footer-col-title">Agir</h4>
          <ul class="footer-links">
            <li><a href="<?= BASE_URL ?>/faire-un-don"       class="footer-link">Faire un don</a></li>
            <li><a href="<?= BASE_URL ?>/demander-une-aide"   class="footer-link">Demander une aide</a></li>
            <li><a href="<?= BASE_URL ?>/inscription"         class="footer-link">Créer un compte</a></li>
            <li><a href="<?= BASE_URL ?>/nos-partenaires"     class="footer-link">Devenir partenaire</a></li>
            <li><a href="<?= BASE_URL ?>/contact"             class="footer-link">Nous contacter</a></li>
            <li><a href="<?= BASE_URL ?>/faq"                 class="footer-link">FAQ</a></li>
          </ul>
        </div>

        <!-- Colonne contact -->
        <div>
          <h4 class="footer-col-title">Contact</h4>
          <ul class="footer-links">
            <li class="footer-link" style="color:rgba(255,255,255,.6);gap:var(--space-3)">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
              <?= nl2br(Security::e(Helpers::getSetting('site_adresse', ''))) ?>
            </li>
            <li><a href="tel:<?= preg_replace('/[^+0-9]/', '', Helpers::getSetting('site_telephone', '')) ?>" class="footer-link">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.67A2 2 0 012 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 9.91a16 16 0 006.05 6.05l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
              <?= Security::e(Helpers::getSetting('site_telephone', '')) ?>
            </a></li>
            <li><a href="mailto:<?= Security::e(Helpers::getSetting('site_email', '')) ?>" class="footer-link">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
              <?= Security::e(Helpers::getSetting('site_email', '')) ?>
            </a></li>
            <li class="footer-link" style="color:rgba(255,255,255,.6)">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              <?= Security::e(Helpers::getSetting('site_horaires', 'Lun-Ven : 9h-18h')) ?>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer bottom -->
  <div class="footer-bottom">
    <div class="container">
      <div class="footer-bottom-inner">
        <p class="footer-copyright">
          &copy; <?= date('Y') ?> <?= Security::e(Helpers::getSetting('site_nom', APP_NAME)) ?>.
          Tous droits réservés. Fondée en <?= Security::e(Helpers::getSetting('fondation_annee', '2010')) ?>.
        </p>
        <ul class="footer-legal">
          <li><a href="<?= BASE_URL ?>/politique-confidentialite">Politique de confidentialité</a></li>
          <li><a href="<?= BASE_URL ?>/conditions-utilisation">Conditions d'utilisation</a></li>
          <li><a href="<?= BASE_URL ?>/faq">FAQ</a></li>
        </ul>
      </div>
    </div>
  </div>
</footer>

<!-- ====================================================
     SCRIPTS JAVASCRIPT
     ==================================================== -->
<script>
  const BASE_URL   = '<?= BASE_URL ?>';
  const CSRF_TOKEN = '<?= $csrfToken ?>';
  const IS_AUTH    = <?= Auth::check() ? 'true' : 'false' ?>;
  const USER_ROLE  = '<?= Auth::role() ?? '' ?>';
</script>
<script src="<?= ASSETS_URL ?>/js/main.js"></script>
<script src="<?= ASSETS_URL ?>/js/nav.js"></script>
<?php if (!empty($extraJs)): foreach ($extraJs as $js): ?>
<script src="<?= ASSETS_URL ?>/js/<?= Security::e($js) ?>"></script>
<?php endforeach; endif; ?>

</body>
</html>
