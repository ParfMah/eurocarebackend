<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<div class="admin-page-header">
  <div class="admin-page-header-left"><h1>Paramètres du site</h1><p>Configuration globale de la plateforme</p></div>
</div>

<form method="POST" action="<?= BASE_URL ?>/admin/parametres" data-validate novalidate>
<input type="hidden" name="_csrf_token" value="<?= Security::e(Session::generateCsrfToken()) ?>">

<?php $groupeLabels = ['general'=>'⚙️ Général','contact'=>'📞 Contact','reseaux'=>'🌐 Réseaux sociaux','email'=>'📧 Email SMTP','systeme'=>'🔧 Système']; ?>
<?php foreach ($groupeLabels as $groupe=>$label): if (empty($paramsByGroup[$groupe])) continue; ?>
<div class="admin-form-card" style="margin-bottom:var(--space-5)">
  <div class="admin-form-section">
    <h3 class="admin-form-section-title"><?= $label ?></h3>
    <div class="admin-form-grid">
      <?php foreach ($paramsByGroup[$groupe] as $p): ?>
      <div class="form-group <?= in_array($p['type'],['texte','email','url','couleur'])?'':'col-span-2' ?>">
        <label class="form-label"><?= Security::e($p['label']??$p['cle']) ?></label>
        <?php if ($p['type']==='booleen'): ?>
          <label class="form-check" style="margin-top:var(--space-2)">
            <input type="checkbox" class="form-check-input" name="<?= Security::e($p['cle']) ?>" value="1" <?= $p['valeur']?'checked':'' ?>>
            <span class="form-check-label"><?= Security::e($p['description']??'Activer') ?></span>
          </label>
        <?php elseif ($p['type']==='couleur'): ?>
          <input type="color" name="<?= Security::e($p['cle']) ?>" class="form-control" style="height:2.5rem;padding:2px" value="<?= Security::e($p['valeur']??'#1a56db') ?>">
        <?php else: ?>
          <input type="<?= $p['type']==='email'?'email':($p['type']==='url'?'url':'text') ?>"
            name="<?= Security::e($p['cle']) ?>"
            class="form-control"
            value="<?= Security::e($p['valeur']??'') ?>"
            placeholder="<?= Security::e($p['description']??'') ?>">
        <?php endif; ?>
        <?php if ($p['description'] && $p['type']!=='booleen'): ?><div class="form-hint"><?= Security::e($p['description']) ?></div><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endforeach; ?>

<div style="position:sticky;bottom:0;background:white;border-top:1px solid var(--color-gray-200);padding:var(--space-4) var(--space-6);display:flex;gap:var(--space-3);z-index:10;box-shadow:0 -4px 12px rgba(0,0,0,.05)">
  <button type="submit" class="btn btn-primary">💾 Enregistrer tous les paramètres</button>
  <a href="<?= BASE_URL ?>/admin" class="btn btn-ghost">Annuler</a>
</div>
</form>
