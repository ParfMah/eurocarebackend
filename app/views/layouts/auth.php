<?php
/**
 * Layout : Pages d'authentification
 * app/views/layouts/auth.php
 */
defined('BASEPATH') or die('Accès direct interdit.');
$siteName = Helpers::getSetting('site_nom', APP_NAME);
$pageTitle = isset($pageTitle) ? Security::e($pageTitle) . ' — ' . $siteName : $siteName;
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
  <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/auth.css">
</head>
<body class="auth-body">

<div id="toast-container"></div>

<div class="auth-wrapper">
  <!-- Panneau gauche décoratif -->
  <div class="auth-side" aria-hidden="true">
    <div class="auth-side-content">
      <a href="<?= BASE_URL ?>/" class="auth-side-logo">
        <div class="logo-icon" style="width:52px;height:52px">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <div>
          <div style="font-size:var(--text-xl);font-weight:var(--font-extrabold);color:white;line-height:1"><?= Security::e($siteName) ?></div>
          <div style="font-size:var(--text-xs);color:rgba(255,255,255,.6);letter-spacing:.1em;text-transform:uppercase;margin-top:2px">Organisation Humanitaire</div>
        </div>
      </a>
      <div class="auth-side-testimonial">
        <blockquote class="auth-testimonial-quote">
          "Grâce à EuroCare, ma famille a pu traverser cette période difficile. L'aide reçue a tout changé."
        </blockquote>
        <div class="auth-testimonial-author">
          <div class="auth-testimonial-avatar">M</div>
          <div>
            <div style="font-weight:600;color:white">Marie L.</div>
            <div style="font-size:var(--text-xs);color:rgba(255,255,255,.6)">Bénéficiaire — France</div>
          </div>
        </div>
      </div>
      <!-- Statistiques latérales -->
      <div class="auth-side-stats">
        <?php $gs = Helpers::getGlobalStats(); ?>
        <div class="auth-side-stat">
          <div class="auth-side-stat-value"><?= number_format($gs['nombre_beneficiaires'] ?: 1240, 0, ',', ' ') ?>+</div>
          <div class="auth-side-stat-label">Bénéficiaires aidés</div>
        </div>
        <div class="auth-side-stat">
          <div class="auth-side-stat-value"><?= $gs['taux_redistribution'] ?: 92 ?>%</div>
          <div class="auth-side-stat-label">Redistribués</div>
        </div>
        <div class="auth-side-stat">
          <div class="auth-side-stat-value"><?= $gs['nombre_partenaires'] ?: 48 ?>+</div>
          <div class="auth-side-stat-label">Partenaires</div>
        </div>
      </div>
    </div>
    <!-- Décoration fond -->
    <div class="auth-side-bg"></div>
  </div>

  <!-- Panneau droit : formulaire -->
  <div class="auth-main">
    <div class="auth-form-wrap">
      <!-- Lien retour -->
      <a href="<?= BASE_URL ?>/" class="auth-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        Retour au site
      </a>

      <?php if (Session::hasFlash()): ?>
      <?php foreach (['succes'=>['success','✓'],'erreur'=>['danger','✕'],'info'=>['info','ℹ'],'attention'=>['warning','⚠']] as $type => [$cls,$icon]): ?>
        <?php foreach (Session::getFlash($type) as $msg): ?>
        <div class="alert alert-<?= $cls ?>" role="alert" style="margin-bottom:var(--space-5)">
          <span class="alert-icon"><?= $icon ?></span>
          <div class="alert-content"><?= $msg ?></div>
        </div>
        <?php endforeach; ?>
      <?php endforeach; ?>
      <?php endif; ?>

      <?= $content ?>
    </div>
  </div>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';
const CSRF_TOKEN = '<?= Session::generateCsrfToken() ?>';
</script>
<script src="<?= ASSETS_URL ?>/js/main.js"></script>
</body>
</html>
