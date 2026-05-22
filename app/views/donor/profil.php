<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<div class="dashboard-page-header"><h1>Mon profil</h1><p>Gérez vos informations personnelles</p></div>
<?php
$role   = Auth::role();
$action = match($role) { ROLE_DONATEUR=>'donateur', ROLE_BENEFICIAIRE=>'beneficiaire', ROLE_PARTENAIRE=>'partenaire', default=>'donateur' };
?>
<div style="display:grid;grid-template-columns:280px 1fr;gap:var(--space-6);align-items:start">
  <!-- Avatar -->
  <div style="background:white;border-radius:var(--radius-xl);padding:var(--space-6);box-shadow:var(--shadow-xs);border:1px solid var(--color-gray-100);text-align:center">
    <div style="width:96px;height:96px;border-radius:50%;background:var(--color-blue-pale);display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:700;color:var(--color-blue-mid);margin:0 auto var(--space-4);overflow:hidden;border:3px solid var(--color-blue-border)">
      <?php if ($user['photo_profil']): ?>
      <img src="<?= Helpers::avatarUrl($user['photo_profil']) ?>" style="width:100%;height:100%;object-fit:cover">
      <?php else: ?>
      <?= mb_strtoupper(mb_substr($user['prenom'],0,1).mb_substr($user['nom'],0,1)) ?>
      <?php endif; ?>
    </div>
    <div style="font-weight:700;font-size:var(--text-md)"><?= Security::e($user['prenom'].' '.$user['nom']) ?></div>
    <div style="font-size:var(--text-xs);color:var(--color-gray-500);margin-top:2px"><?= Security::e(ROLES_LABELS[$user['role']]??$user['role']) ?></div>
    <div style="margin-top:var(--space-3)"><span class="badge badge-<?= $user['email_verifie']?'green':'yellow' ?>"><?= $user['email_verifie']?'✅ Email vérifié':'⚠ Non vérifié' ?></span></div>
    <div style="font-size:var(--text-xs);color:var(--color-gray-400);margin-top:var(--space-3)">Membre depuis <?= Helpers::formatDate($user['cree_le'],false,true) ?></div>
  </div>

  <!-- Formulaire -->
  <div style="background:white;border-radius:var(--radius-xl);padding:var(--space-6);box-shadow:var(--shadow-xs);border:1px solid var(--color-gray-100)">
    <h3 style="font-weight:700;font-size:var(--text-md);margin-bottom:var(--space-5)">Informations personnelles</h3>
    <form method="POST" action="<?= BASE_URL ?>/<?= $action ?>/profil" enctype="multipart/form-data" data-validate novalidate>
      <input type="hidden" name="_csrf_token" value="<?= Security::e(Session::generateCsrfToken()) ?>">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4)">
        <div class="form-group">
          <label class="form-label">Prénom <span class="required">*</span></label>
          <input type="text" name="prenom" class="form-control" value="<?= Security::e($user['prenom']) ?>" required minlength="2">
        </div>
        <div class="form-group">
          <label class="form-label">Nom <span class="required">*</span></label>
          <input type="text" name="nom" class="form-control" value="<?= Security::e($user['nom']) ?>" required minlength="2">
        </div>
        <div class="form-group">
          <label class="form-label">Téléphone</label>
          <input type="tel" name="telephone" class="form-control" value="<?= Security::e($user['telephone']??'') ?>" placeholder="+33 6 00 00 00 00">
        </div>
        <div class="form-group">
          <label class="form-label">Pays</label>
          <select name="pays" class="form-control">
            <option value="">Sélectionner...</option>
            <?php foreach (PAYS_EUROPEENS as $code=>$nom): ?>
            <option value="<?= Security::e($nom) ?>" <?= ($user['pays']??'')===$nom?'selected':'' ?>><?= Security::e($nom) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Ville</label>
          <input type="text" name="ville" class="form-control" value="<?= Security::e($user['ville']??'') ?>">
        </div>
        <?php if ($role === ROLE_BENEFICIAIRE): ?>
        <div class="form-group" style="grid-column:span 2">
          <label class="form-label">Adresse complète</label>
          <textarea name="adresse" class="form-control" rows="2"><?= Security::e($user['adresse']??'') ?></textarea>
        </div>
        <?php endif; ?>
        <div class="form-group" style="grid-column:span 2">
          <label class="form-label">Photo de profil</label>
          <div class="drop-zone" style="padding:var(--space-4)" id="photoDropZone">
            <div class="drop-zone-icon" style="font-size:2rem">🖼️</div>
            <div class="drop-zone-text">Cliquez ou déposez une image (JPG, PNG, max 2 Mo)</div>
            <input type="file" name="photo_profil" accept="image/jpeg,image/png,image/webp" style="display:none" data-file-preview="photoPreview">
          </div>
          <div id="photoPreview" style="margin-top:var(--space-2)"></div>
        </div>
      </div>
      <div style="display:flex;gap:var(--space-3);margin-top:var(--space-4)">
        <button type="submit" class="btn btn-primary">💾 Enregistrer</button>
        <a href="<?= BASE_URL ?>/<?= $action ?>/tableau-de-bord" class="btn btn-ghost">Annuler</a>
      </div>
    </form>
  </div>
</div>
