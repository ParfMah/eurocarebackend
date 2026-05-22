<?php defined('BASEPATH') or die('Accès direct interdit.');
$isEdit  = !empty($article);
$action  = $isEdit ? BASE_URL.'/admin/articles/'.$article['id'].'/editer' : BASE_URL.'/admin/articles/nouveau';
?>
<div class="admin-page-header">
  <div class="admin-page-header-left">
    <h1><?= $isEdit ? 'Éditer l\'article' : 'Nouvel article' ?></h1>
    <?php if ($isEdit): ?><p><?= Security::e(Helpers::truncate($article['titre'],60)) ?></p><?php endif; ?>
  </div>
  <a href="<?= BASE_URL ?>/admin/articles" class="btn btn-ghost btn-sm">← Retour</a>
</div>

<form method="POST" action="<?= $action ?>" enctype="multipart/form-data" data-validate novalidate>
  <input type="hidden" name="_csrf_token" value="<?= Security::e(Session::generateCsrfToken()) ?>">

  <div style="display:grid;grid-template-columns:1fr 300px;gap:var(--space-5);align-items:start">

    <!-- Contenu principal -->
    <div style="display:flex;flex-direction:column;gap:var(--space-4)">
      <div class="admin-form-card">
        <div class="admin-form-section">
          <h3 class="admin-form-section-title">📝 Contenu de l'article</h3>
          <div class="form-group">
            <label class="form-label">Titre <span class="required">*</span></label>
            <input type="text" name="titre" class="form-control" required minlength="3"
              value="<?= Security::e($article['titre']??'') ?>" placeholder="Titre de l'article..." data-maxlength="255">
          </div>
          <div class="form-group">
            <label class="form-label">Extrait / Résumé</label>
            <textarea name="extrait" class="form-control" rows="2"
              placeholder="Résumé court affiché dans les listes d'articles..." data-maxlength="500"><?= Security::e($article['extrait']??'') ?></textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Contenu complet <span class="required">*</span></label>
            <textarea name="contenu" class="form-control" rows="16" required minlength="10"
              style="font-family:var(--font-mono);font-size:var(--text-sm);line-height:1.6"
              data-editor data-maxlength="100000"><?= Security::e($article['contenu']??'') ?></textarea>
            <div class="form-hint">HTML basique accepté (titres, paragraphes, listes, liens)</div>
          </div>
        </div>
      </div>

      <!-- SEO -->
      <div class="admin-form-card">
        <div class="admin-form-section">
          <h3 class="admin-form-section-title">🔍 SEO & Métadonnées</h3>
          <div class="admin-form-grid">
            <div class="form-group">
              <label class="form-label">Méta-titre (SEO)</label>
              <input type="text" name="meta_titre" class="form-control"
                value="<?= Security::e($article['meta_titre']??'') ?>" placeholder="Titre pour moteurs de recherche" data-maxlength="255">
            </div>
            <div class="form-group">
              <label class="form-label">Tags (séparés par virgule)</label>
              <input type="text" name="tags" class="form-control"
                value="<?= Security::e($article['tags']??'') ?>" placeholder="aide, humanitaire, témoignage...">
            </div>
            <div class="form-group col-span-2">
              <label class="form-label">Méta-description (SEO)</label>
              <textarea name="meta_description" class="form-control" rows="2" data-maxlength="500"><?= Security::e($article['meta_description']??'') ?></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Sidebar droite -->
    <div style="display:flex;flex-direction:column;gap:var(--space-4);position:sticky;top:80px">

      <!-- Publication -->
      <div class="admin-form-card">
        <div class="admin-form-section">
          <h3 class="admin-form-section-title">📤 Publication</h3>
          <div class="form-group">
            <label class="form-label">Statut</label>
            <select name="statut" class="form-control">
              <?php foreach (['brouillon'=>'Brouillon','publie'=>'Publié','archive'=>'Archivé'] as $v=>$l): ?>
              <option value="<?= $v ?>" <?= ($article['statut']??'brouillon')===$v?'selected':'' ?>><?= $l ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Catégorie</label>
            <select name="categorie_id" class="form-control">
              <option value="">Aucune catégorie</option>
              <?php foreach ($categories as $cat): ?>
              <option value="<?= (int)$cat['id'] ?>" <?= ($article['categorie_id']??0)===(int)$cat['id']?'selected':'' ?>><?= Security::e($cat['nom']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Temps de lecture (min.)</label>
            <input type="number" name="temps_lecture" class="form-control" min="1" max="60"
              value="<?= (int)($article['temps_lecture']??3) ?>">
          </div>
          <div class="form-group">
            <label class="form-check">
              <input type="checkbox" class="form-check-input" name="featured" value="1"
                <?= !empty($article['featured'])?'checked':'' ?>>
              <span class="form-check-label">⭐ Mettre en avant (En une)</span>
            </label>
          </div>
        </div>
      </div>

      <!-- Image -->
      <div class="admin-form-card">
        <div class="admin-form-section">
          <h3 class="admin-form-section-title">🖼️ Image principale</h3>
          <?php if (!empty($article['image_principale'])): ?>
          <img src="<?= UPLOAD_URL ?>/articles/<?= Security::e($article['image_principale']) ?>"
            style="width:100%;height:140px;object-fit:cover;border-radius:var(--radius-lg);margin-bottom:var(--space-3)">
          <?php endif; ?>
          <div class="drop-zone" style="padding:var(--space-4)" id="imgDropZone">
            <div style="font-size:1.75rem">🖼️</div>
            <div class="drop-zone-text">JPG, PNG, WebP — max 5 Mo</div>
            <input type="file" name="image_principale" accept="image/jpeg,image/png,image/webp"
              style="display:none" data-file-preview="imgPreview">
          </div>
          <div id="imgPreview" style="margin-top:var(--space-2)"></div>
        </div>
      </div>

      <!-- Boutons -->
      <div style="display:flex;flex-direction:column;gap:var(--space-2)">
        <button type="submit" class="btn btn-primary btn-block">💾 <?= $isEdit?'Enregistrer les modifications':'Créer l\'article' ?></button>
        <a href="<?= BASE_URL ?>/admin/articles" class="btn btn-ghost btn-block">Annuler</a>
      </div>
    </div>
  </div>
</form>
