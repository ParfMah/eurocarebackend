<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<div class="dashboard-page-header"><h1>Profil de mon organisation</h1><p>Informations de votre institution partenaire</p></div>
<form method="POST" action="<?= BASE_URL ?>/partenaire/mon-profil" enctype="multipart/form-data" data-validate novalidate>
<input type="hidden" name="_csrf_token" value="<?= Security::e(Session::generateCsrfToken()) ?>">
<div style="background:white;border-radius:var(--radius-xl);border:1px solid var(--color-gray-100);padding:var(--space-6);box-shadow:var(--shadow-xs)">
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4)">
    <div class="form-group" style="grid-column:span 2">
      <label class="form-label">Nom de l'organisation <span class="required">*</span></label>
      <input type="text" name="nom_organisation" class="form-control" required value="<?= Security::e($profil['nom_organisation']??'') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Type d'organisation <span class="required">*</span></label>
      <select name="type_organisation" class="form-control" required>
        <option value="">Choisir...</option>
        <?php foreach (TYPES_PARTENAIRE as $v=>$l): ?><option value="<?= $v ?>" <?= ($profil['type_organisation']??'')===$v?'selected':'' ?>><?= Security::e($l) ?></option><?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label class="form-label">Numéro d'enregistrement</label>
      <input type="text" name="numero_enregistrement" class="form-control" value="<?= Security::e($profil['numero_enregistrement']??'') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Site web</label>
      <input type="url" name="site_web" class="form-control" placeholder="https://" value="<?= Security::e($profil['site_web']??'') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Email de contact</label>
      <input type="email" name="email_contact" class="form-control" value="<?= Security::e($profil['email_contact']??'') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Téléphone</label>
      <input type="tel" name="telephone" class="form-control" value="<?= Security::e($profil['telephone']??'') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Pays</label>
      <select name="pays" class="form-control">
        <option value="">Choisir...</option>
        <?php foreach (PAYS_EUROPEENS as $c=>$n): ?><option value="<?= Security::e($n) ?>" <?= ($profil['pays']??'')===$n?'selected':'' ?>><?= Security::e($n) ?></option><?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label class="form-label">Ville</label>
      <input type="text" name="ville" class="form-control" value="<?= Security::e($profil['ville']??'') ?>">
    </div>
    <div class="form-group" style="grid-column:span 2">
      <label class="form-label">Description de l'organisation</label>
      <textarea name="description" class="form-control" rows="4" data-maxlength="3000"><?= Security::e($profil['description']??'') ?></textarea>
    </div>
    <div class="form-group" style="grid-column:span 2">
      <label class="form-label">Domaines d'action</label>
      <input type="text" name="domaines_action" class="form-control" placeholder="Ex: aide alimentaire, accompagnement social, hébergement..." value="<?= Security::e($profil['domaines_action']??'') ?>">
    </div>
  </div>
  <div style="display:flex;gap:var(--space-3);margin-top:var(--space-4)">
    <button type="submit" class="btn btn-primary">💾 Enregistrer</button>
    <a href="<?= BASE_URL ?>/partenaire/tableau-de-bord" class="btn btn-ghost">Retour</a>
  </div>
</div>
</form>
