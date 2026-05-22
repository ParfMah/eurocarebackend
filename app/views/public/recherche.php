<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<section class="section"><div class="container">
  <h1 style="font-size:var(--text-2xl);font-weight:700;margin-bottom:var(--space-2)">Résultats de recherche</h1>
  <p style="color:var(--color-gray-500);margin-bottom:var(--space-8)">
    <?php if ($q): ?>
    <?= (int)$pagination['total'] ?> résultat(s) pour "<strong><?= Security::e($q) ?></strong>"
    <?php else: ?>Entrez un terme de recherche<?php endif; ?>
  </p>
  <form action="<?= BASE_URL ?>/recherche" method="GET" style="display:flex;gap:var(--space-3);margin-bottom:var(--space-8);max-width:500px">
    <input type="text" name="q" class="form-control" value="<?= Security::e($q) ?>" placeholder="Rechercher dans les articles...">
    <button type="submit" class="btn btn-primary">Rechercher</button>
  </form>
  <?php if (!empty($results)): ?>
  <div style="display:flex;flex-direction:column;gap:var(--space-5)">
    <?php foreach ($results as $a): ?>
    <div style="background:white;border-radius:var(--radius-xl);padding:var(--space-5);box-shadow:var(--shadow-sm);border:1px solid var(--color-gray-100);display:flex;gap:var(--space-4)">
      <?php if ($a['image_principale']): ?>
      <img src="<?= UPLOAD_URL ?>/articles/<?= Security::e($a['image_principale']) ?>" style="width:80px;height:80px;object-fit:cover;border-radius:var(--radius-lg);flex-shrink:0">
      <?php else: ?>
      <div style="width:80px;height:80px;background:var(--color-blue-pale);border-radius:var(--radius-lg);display:flex;align-items:center;justify-content:center;font-size:2rem;flex-shrink:0">📰</div>
      <?php endif; ?>
      <div>
        <?php if ($a['cat_nom']): ?><span class="article-category"><?= Security::e($a['cat_nom']) ?></span><?php endif; ?>
        <h2 style="font-size:var(--text-md);font-weight:700;margin-bottom:var(--space-2)"><a href="<?= BASE_URL ?>/actualites/<?= Security::e($a['slug']) ?>" style="color:inherit;text-decoration:none"><?= Security::e($a['titre']) ?></a></h2>
        <?php if ($a['extrait']): ?><p style="font-size:var(--text-sm);color:var(--color-gray-600)"><?= Security::e(Helpers::truncate($a['extrait'],120)) ?></p><?php endif; ?>
        <div style="font-size:var(--text-xs);color:var(--color-gray-400);margin-top:var(--space-2)"><?= Helpers::formatDate($a['publie_le']??'',false,true) ?></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php if ($pagination['pages']>1): echo Helpers::paginationHtml($pagination, BASE_URL.'/recherche?q='.urlencode($q)); endif; ?>
  <?php elseif ($q): ?>
  <div style="text-align:center;padding:var(--space-12);color:var(--color-gray-500)">
    <div style="font-size:3rem;margin-bottom:var(--space-4)">🔍</div>
    <p>Aucun article trouvé pour "<?= Security::e($q) ?>".</p>
    <a href="<?= BASE_URL ?>/actualites" class="btn btn-outline mt-4">Voir tous les articles</a>
  </div>
  <?php endif; ?>
</div></section>
