<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<div class="admin-page-header">
  <div class="admin-page-header-left"><h1>Pages CMS</h1><p>Modifier le contenu des pages statiques du site</p></div>
</div>
<div style="display:flex;flex-direction:column;gap:var(--space-4)">
  <?php if (!empty($pages)): foreach ($pages as $page): ?>
  <div class="admin-form-card">
    <div class="admin-form-section">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--space-4)">
        <h3 class="admin-form-section-title" style="margin-bottom:0"><?= Security::e($page['titre']) ?></h3>
        <div style="display:flex;gap:var(--space-2);align-items:center">
          <a href="<?= BASE_URL ?>/<?= Security::e($page['slug']) ?>" target="_blank" class="btn btn-outline btn-sm">Voir →</a>
          <span class="badge badge-<?= $page['statut']==='publie'?'green':'yellow' ?>"><?= ucfirst($page['statut']) ?></span>
        </div>
      </div>
      <form method="POST" action="<?= BASE_URL ?>/admin/pages/<?= (int)$page['id'] ?>" data-validate novalidate>
        <input type="hidden" name="_csrf_token" value="<?= Security::e(Session::generateCsrfToken()) ?>">
        <div class="form-group">
          <label class="form-label">Titre de la page</label>
          <input type="text" name="titre" class="form-control" value="<?= Security::e($page['titre']) ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Méta-description (SEO)</label>
          <input type="text" name="meta_description" class="form-control" value="<?= Security::e($page['meta_description']??'') ?>" placeholder="Description pour les moteurs de recherche...">
        </div>
        <div class="form-group">
          <label class="form-label">Contenu HTML</label>
          <textarea name="contenu" class="form-control" rows="8" data-editor style="font-family:var(--font-mono);font-size:13px"><?= Security::e($page['contenu']??'') ?></textarea>
          <div class="form-hint">HTML autorisé : &lt;h2&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;strong&gt;, &lt;a&gt;...</div>
        </div>
        <button type="submit" class="btn btn-primary btn-sm">💾 Enregistrer cette page</button>
      </form>
    </div>
  </div>
  <?php endforeach; else: ?>
  <div style="text-align:center;padding:var(--space-12);background:white;border-radius:var(--radius-xl);color:var(--color-gray-400)">Aucune page CMS configurée</div>
  <?php endif; ?>
</div>
