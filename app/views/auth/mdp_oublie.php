<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<div class="auth-header">
  <div class="auth-icon">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
  </div>
  <h1 class="auth-title">Mot de passe oublié ?</h1>
  <p class="auth-subtitle">Renseignez votre email et nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>
</div>
<form method="POST" action="<?= BASE_URL ?>/mot-de-passe-oublie" data-validate novalidate>
  <input type="hidden" name="_csrf_token" value="<?= Security::e(Session::generateCsrfToken()) ?>">
  <div class="form-group">
    <label class="form-label" for="email">Adresse email <span class="required">*</span></label>
    <input type="email" id="email" name="email" class="form-control" placeholder="votre@email.com" required autocomplete="email">
  </div>
  <button type="submit" class="btn btn-primary btn-auth">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.67A2 2 0 012 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 9.91a16 16 0 006.05 6.05l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
    Envoyer le lien de réinitialisation
  </button>
</form>
<div class="auth-footer"><a href="<?= BASE_URL ?>/connexion">← Retour à la connexion</a></div>
