<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<div class="auth-header">
  <div class="auth-icon" style="background:#ecfdf5;color:var(--color-success)">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>
  </div>
  <h1 class="auth-title">Nouveau mot de passe</h1>
  <p class="auth-subtitle">Choisissez un nouveau mot de passe sécurisé pour votre compte.</p>
</div>
<form method="POST" action="<?= BASE_URL ?>/reinitialiser/<?= Security::e($token) ?>" data-validate novalidate>
  <input type="hidden" name="_csrf_token" value="<?= Security::e(Session::generateCsrfToken()) ?>">
  <div class="form-group">
    <label class="form-label" for="password">Nouveau mot de passe <span class="required">*</span></label>
    <div style="position:relative">
      <input type="password" id="password" name="password" class="form-control" required minlength="8" placeholder="Nouveau mot de passe" data-password-strength="pwdStr" style="padding-right:3rem">
      <button type="button" data-toggle-password="password" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:var(--color-gray-400);padding:0" aria-label="Afficher">
        <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        <svg class="eye-close" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><line x1="1" y1="1" x2="23" y2="23"/><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94"/></svg>
      </button>
    </div>
    <div id="pwdStr"></div>
  </div>
  <div class="form-group">
    <label class="form-label" for="password_confirm">Confirmer le mot de passe <span class="required">*</span></label>
    <input type="password" id="password_confirm" name="password_confirm" class="form-control" required placeholder="Confirmez le mot de passe">
  </div>
  <button type="submit" class="btn btn-primary btn-auth">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
    Définir le nouveau mot de passe
  </button>
</form>
