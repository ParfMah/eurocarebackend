<?php defined('BASEPATH') or die('Accès direct interdit.');
$role = $rolePreselect ?? ROLE_DONATEUR;
?>

<div class="auth-header">
  <div class="auth-icon">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
  </div>
  <h1 class="auth-title">Créer un compte</h1>
  <p class="auth-subtitle">Rejoignez notre communauté et accédez à votre espace personnel.</p>
</div>

<form class="auth-form" method="POST" action="<?= BASE_URL ?>/inscription" data-validate novalidate>
  <input type="hidden" name="_csrf_token" value="<?= Security::e(Session::generateCsrfToken()) ?>">

  <!-- Sélection du type de compte -->
  <div class="form-group">
    <p class="form-label">Je suis <span class="required">*</span></p>
    <div class="role-selector">
      <?php $roles = [
        ROLE_DONATEUR     => ['💝', 'Donateur',    'Je souhaite faire des dons'],
        ROLE_BENEFICIAIRE => ['🤝', 'Bénéficiaire','Je demande une aide'],
        ROLE_PARTENAIRE   => ['🏛️', 'Partenaire',  'Organisation / Institution'],
      ]; foreach ($roles as $rVal => [$icon, $name, $desc]): ?>
      <label class="role-option">
        <input type="radio" name="role" value="<?= $rVal ?>" <?= $role === $rVal ? 'checked' : '' ?> required>
        <div class="role-option-label">
          <span class="role-option-icon"><?= $icon ?></span>
          <span class="role-option-name"><?= $name ?></span>
          <span class="role-option-desc"><?= $desc ?></span>
        </div>
      </label>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Nom et prénom -->
  <div class="form-group" style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-3)">
    <div>
      <label class="form-label" for="prenom">Prénom <span class="required">*</span></label>
      <input type="text" id="prenom" name="prenom" class="form-control"
        placeholder="Votre prénom" required minlength="2" maxlength="100"
        autocomplete="given-name"
        value="<?= Security::e($_POST['prenom'] ?? '') ?>">
    </div>
    <div>
      <label class="form-label" for="nom">Nom <span class="required">*</span></label>
      <input type="text" id="nom" name="nom" class="form-control"
        placeholder="Votre nom" required minlength="2" maxlength="100"
        autocomplete="family-name"
        value="<?= Security::e($_POST['nom'] ?? '') ?>">
    </div>
  </div>

  <!-- Email -->
  <div class="form-group">
    <label class="form-label" for="email">Adresse email <span class="required">*</span></label>
    <input type="email" id="email" name="email" class="form-control"
      placeholder="votre@email.com" required autocomplete="email"
      value="<?= Security::e($_POST['email'] ?? '') ?>">
    <div class="form-hint">Un email de vérification vous sera envoyé.</div>
  </div>

  <!-- Pays -->
  <div class="form-group">
    <label class="form-label" for="pays">Pays de résidence</label>
    <select id="pays" name="pays" class="form-control">
      <option value="">Sélectionnez votre pays...</option>
      <?php foreach (PAYS_EUROPEENS as $code => $nom): ?>
      <option value="<?= Security::e($nom) ?>" <?= ($_POST['pays'] ?? '') === $nom ? 'selected' : '' ?>><?= Security::e($nom) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <!-- Mot de passe -->
  <div class="form-group">
    <label class="form-label" for="password">Mot de passe <span class="required">*</span></label>
    <div style="position:relative">
      <input type="password" id="password" name="password" class="form-control"
        placeholder="Min. 8 caractères" required autocomplete="new-password"
        minlength="8" data-password-strength="pwdStrength"
        style="padding-right:3rem">
      <button type="button" data-toggle-password="password"
        style="position:absolute;right:var(--space-3);top:50%;transform:translateY(-50%);color:var(--color-gray-400);padding:0"
        aria-label="Afficher le mot de passe">
        <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        <svg class="eye-close" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
      </button>
    </div>
    <div id="pwdStrength"></div>
    <div class="form-hint">Majuscule, minuscule, chiffre, caractère spécial requis.</div>
  </div>

  <div class="form-group">
    <label class="form-label" for="password_confirm">Confirmer le mot de passe <span class="required">*</span></label>
    <input type="password" id="password_confirm" name="password_confirm" class="form-control"
      placeholder="Répétez votre mot de passe" required autocomplete="new-password">
  </div>

  <!-- Consentements -->
  <div class="form-group" style="background:var(--color-gray-50);padding:var(--space-4);border-radius:var(--radius-lg);border:1px solid var(--color-gray-200)">
    <label class="form-check" style="margin-bottom:var(--space-3)">
      <input type="checkbox" class="form-check-input" name="cgv" value="1" required>
      <span class="form-check-label auth-policy">
        J'accepte les <a href="<?= BASE_URL ?>/conditions-utilisation" target="_blank">conditions d'utilisation</a>
        et la <a href="<?= BASE_URL ?>/politique-confidentialite" target="_blank">politique de confidentialité</a>. <span class="required">*</span>
      </span>
    </label>
    <label class="form-check">
      <input type="checkbox" class="form-check-input" name="newsletter" value="1" checked>
      <span class="form-check-label auth-policy">
        Je souhaite recevoir les actualités et rapports d'activité par email (optionnel).
      </span>
    </label>
  </div>

  <button type="submit" class="btn btn-primary btn-auth">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
    Créer mon compte
  </button>
</form>

<div class="auth-footer">
  Déjà un compte ?
  <a href="<?= BASE_URL ?>/connexion">Se connecter</a>
</div>

<div style="margin-top:var(--space-5);padding:var(--space-3) var(--space-4);background:var(--color-success-bg);border-radius:var(--radius-lg);border:1px solid #a7f3d0">
  <p style="font-size:var(--text-xs);color:#065f46;margin:0;text-align:center">
    🔒 Compte gratuit · Aucune carte bancaire requise · Données protégées RGPD
  </p>
</div>
