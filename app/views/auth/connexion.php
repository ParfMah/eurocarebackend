<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>

<div class="auth-header">
  <div class="auth-icon">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
  </div>
  <h1 class="auth-title">Connexion</h1>
  <p class="auth-subtitle">Accédez à votre espace personnel EuroCare Humanitaire.</p>
</div>

<form class="auth-form" method="POST" action="<?= BASE_URL ?>/connexion" data-validate novalidate>
  <input type="hidden" name="_csrf_token" value="<?= Security::e(Session::generateCsrfToken()) ?>">

  <div class="form-group">
    <label class="form-label" for="email">Adresse email <span class="required">*</span></label>
    <input
      type="email" id="email" name="email" class="form-control"
      placeholder="votre@email.com" required autocomplete="email"
      value="<?= Security::e($_POST['email'] ?? '') ?>"
      aria-required="true"
    >
  </div>

  <div class="form-group">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--space-2)">
      <label class="form-label" for="password" style="margin-bottom:0">Mot de passe <span class="required">*</span></label>
      <a href="<?= BASE_URL ?>/mot-de-passe-oublie" class="text-sm text-blue">Mot de passe oublié ?</a>
    </div>
    <div style="position:relative">
      <input
        type="password" id="password" name="password" class="form-control"
        placeholder="Votre mot de passe" required autocomplete="current-password"
        aria-required="true"
        style="padding-right:3rem"
      >
      <button type="button"
        data-toggle-password="password"
        style="position:absolute;right:var(--space-3);top:50%;transform:translateY(-50%);color:var(--color-gray-400);padding:0"
        aria-label="Afficher le mot de passe"
      >
        <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        <svg class="eye-close" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
      </button>
    </div>
  </div>

  <div class="form-group">
    <label class="form-check">
      <input type="checkbox" class="form-check-input" name="remember_me" value="1">
      <span class="form-check-label">Se souvenir de moi pendant <?= REMEMBER_ME_DAYS ?> jours</span>
    </label>
  </div>

  <button type="submit" class="btn btn-primary btn-auth">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
    Se connecter
  </button>
</form>

<div class="auth-footer">
  Pas encore de compte ?
  <a href="<?= BASE_URL ?>/inscription">Créer un compte gratuitement</a>
</div>

<div style="margin-top:var(--space-6);padding:var(--space-4);background:var(--color-blue-pale);border-radius:var(--radius-xl);border:1px solid var(--color-blue-border)">
  <p style="font-size:var(--text-xs);color:var(--color-gray-600);text-align:center;margin:0">
    🔒 Connexion sécurisée — vos données sont protégées et chiffrées
  </p>
</div>
