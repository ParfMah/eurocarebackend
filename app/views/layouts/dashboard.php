<?php
/**
 * Layout : Dashboard utilisateur (donateur / bénéficiaire / partenaire)
 * app/views/layouts/dashboard.php
 * Inclus dans le layout main via le système de vues imbriquées.
 * Ce layout est injecté comme $content dans main.php.
 */
defined('BASEPATH') or die('Accès direct interdit.');

$user        = Auth::user();
$role        = Auth::role();
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$unread      = Helpers::countUnreadNotifications($user['id']);

function dashActive(string $path): string {
    global $currentPath;
    return $currentPath === $path ? 'active' : '';
}
?>

<div class="dashboard-layout">

  <!-- Sidebar navigation -->
  <aside class="dashboard-sidebar">
    <!-- Profil utilisateur -->
    <div style="display:flex;align-items:center;gap:var(--space-3);padding:var(--space-4);background:var(--color-blue-pale);border-radius:var(--radius-xl);margin-bottom:var(--space-6);border:1px solid var(--color-blue-border)">
      <div style="width:2.75rem;height:2.75rem;border-radius:50%;background:var(--color-blue-mid);display:flex;align-items:center;justify-content:center;font-weight:700;color:white;font-size:var(--text-sm);flex-shrink:0;overflow:hidden">
        <?php if ($user['photo_profil']): ?>
        <img src="<?= Helpers::avatarUrl($user['photo_profil']) ?>" style="width:100%;height:100%;object-fit:cover">
        <?php else: ?>
        <?= mb_strtoupper(mb_substr($user['prenom'],0,1).mb_substr($user['nom'],0,1)) ?>
        <?php endif; ?>
      </div>
      <div style="min-width:0">
        <div style="font-weight:700;font-size:var(--text-sm);color:var(--color-gray-900);overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= Security::e($user['prenom'].' '.$user['nom']) ?></div>
        <div style="font-size:var(--text-xs);color:var(--color-gray-500)"><?= Security::e(ROLES_LABELS[$role] ?? $role) ?></div>
      </div>
    </div>

    <ul class="dashboard-nav">

      <?php if ($role === ROLE_DONATEUR): ?>
      <!-- Nav Donateur -->
      <li class="dashboard-nav-section">Mon espace</li>
      <li><a href="<?= BASE_URL ?>/donateur/tableau-de-bord" class="dashboard-nav-link <?= dashActive('/donateur/tableau-de-bord') ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        Tableau de bord
      </a></li>
      <li><a href="<?= BASE_URL ?>/donateur/mes-dons" class="dashboard-nav-link <?= dashActive('/donateur/mes-dons') ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
        Mes dons
      </a></li>
      <li><a href="<?= BASE_URL ?>/donateur/recus" class="dashboard-nav-link <?= dashActive('/donateur/recus') ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        Reçus fiscaux
      </a></li>
      <li><a href="<?= BASE_URL ?>/donateur/impact" class="dashboard-nav-link <?= dashActive('/donateur/impact') ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        Mon impact
      </a></li>
      <li class="dashboard-nav-section">Actions</li>
      <li><a href="<?= BASE_URL ?>/faire-un-don" class="dashboard-nav-link" style="background:var(--color-gold-pale);color:var(--color-gold)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        Faire un don
      </a></li>

      <?php elseif ($role === ROLE_BENEFICIAIRE): ?>
      <!-- Nav Bénéficiaire -->
      <li class="dashboard-nav-section">Mon dossier</li>
      <li><a href="<?= BASE_URL ?>/beneficiaire/tableau-de-bord" class="dashboard-nav-link <?= dashActive('/beneficiaire/tableau-de-bord') ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        Vue d'ensemble
      </a></li>
      <li><a href="<?= BASE_URL ?>/beneficiaire/mon-dossier" class="dashboard-nav-link <?= dashActive('/beneficiaire/mon-dossier') ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
        Mon dossier social
      </a></li>
      <li><a href="<?= BASE_URL ?>/beneficiaire/mes-aides" class="dashboard-nav-link <?= dashActive('/beneficiaire/mes-aides') ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
        Aides reçues
      </a></li>
      <li><a href="<?= BASE_URL ?>/beneficiaire/messages" class="dashboard-nav-link <?= dashActive('/beneficiaire/messages') ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        Messagerie
      </a></li>

      <?php elseif ($role === ROLE_PARTENAIRE): ?>
      <!-- Nav Partenaire -->
      <li class="dashboard-nav-section">Mon organisation</li>
      <li><a href="<?= BASE_URL ?>/partenaire/tableau-de-bord" class="dashboard-nav-link <?= dashActive('/partenaire/tableau-de-bord') ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        Tableau de bord
      </a></li>
      <li><a href="<?= BASE_URL ?>/partenaire/mon-profil" class="dashboard-nav-link <?= dashActive('/partenaire/mon-profil') ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
        Mon profil
      </a></li>
      <li><a href="<?= BASE_URL ?>/partenaire/recommandations" class="dashboard-nav-link <?= dashActive('/partenaire/recommandations') ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
        Recommandations
      </a></li>
      <?php endif; ?>

      <!-- Commun à tous -->
      <li class="dashboard-nav-section">Compte</li>
      <li><a href="<?= BASE_URL ?>/<?= $role === ROLE_DONATEUR ? 'donateur' : ($role === ROLE_BENEFICIAIRE ? 'beneficiaire' : 'partenaire') ?>/profil" class="dashboard-nav-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Mon profil
      </a></li>
      <li><a href="<?= BASE_URL ?>/deconnexion" class="dashboard-nav-link" style="color:var(--color-danger)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Déconnexion
      </a></li>
    </ul>
  </aside>

  <!-- Contenu principal -->
  <main class="dashboard-main">
    <?php if (Session::hasFlash()): ?>
    <?php foreach (['succes'=>['success','✓'],'erreur'=>['danger','✕'],'info'=>['info','ℹ'],'attention'=>['warning','⚠']] as $type=>[$cls,$ico]): ?>
      <?php foreach (Session::getFlash($type) as $msg): ?>
      <div class="alert alert-<?= $cls ?>" role="alert" style="margin-bottom:var(--space-5)">
        <span class="alert-icon"><?= $ico ?></span>
        <div class="alert-content"><?= Security::e($msg) ?></div>
        <button onclick="this.closest('.alert').remove()" style="margin-left:auto;opacity:.6">&times;</button>
      </div>
      <?php endforeach; ?>
    <?php endforeach; endif; ?>

    <?= $content ?>
  </main>
</div>
