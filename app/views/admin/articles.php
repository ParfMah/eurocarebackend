<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<div class="admin-page-header">
  <div class="admin-page-header-left"><h1>Articles & Blog</h1><p><?= (int)($pagination['total']??0) ?> article(s)</p></div>
  <a href="<?= BASE_URL ?>/admin/articles/nouveau" class="btn btn-primary btn-sm">+ Nouvel article</a>
</div>
<div class="admin-table-card">
  <form method="GET" action="<?= BASE_URL ?>/admin/articles"><div class="admin-filters">
    <input type="text" name="q" class="admin-filter-input" placeholder="Titre de l'article..." value="<?= Security::e($search??'') ?>" style="min-width:220px">
    <select name="statut" class="admin-filter-input admin-filter-select">
      <option value="">Tous statuts</option>
      <?php foreach (['brouillon'=>'Brouillon','publie'=>'Publié','archive'=>'Archivé'] as $v=>$l): ?><option value="<?= $v ?>" <?= ($statutFilter??'')===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-primary btn-sm">Filtrer</button>
  </div></form>
  <div class="table-wrapper" style="border:none;box-shadow:none;border-radius:0">
    <table class="admin-data-table">
      <thead><tr><th data-sort>Titre</th><th>Catégorie</th><th>Auteur</th><th>Vues</th><th>Statut</th><th>Date</th><th>Actions</th></tr></thead>
      <tbody>
        <?php if (!empty($articles)): foreach ($articles as $a): ?>
        <tr>
          <td style="max-width:200px"><div class="truncate" title="<?= Security::e($a['titre']) ?>"><?= Security::e(Helpers::truncate($a['titre'],45)) ?></div>
            <?php if ($a['featured']): ?><span style="font-size:10px;color:var(--color-gold)">⭐ En une</span><?php endif; ?>
          </td>
          <td><span class="badge badge-blue" style="font-size:10px"><?= Security::e($a['cat_nom']??'—') ?></span></td>
          <td style="font-size:var(--text-xs);color:var(--color-gray-600)"><?= Security::e($a['auteur_nom']??'—') ?></td>
          <td style="font-size:var(--text-sm)">👁 <?= number_format((int)$a['vues'],0,',',' ') ?></td>
          <td><span class="badge badge-<?= $a['statut']==='publie'?'green':($a['statut']==='brouillon'?'yellow':'gray') ?>"><?= ucfirst($a['statut']) ?></span></td>
          <td style="font-size:var(--text-xs);color:var(--color-gray-500)"><?= Helpers::formatDate($a['publie_le']??$a['cree_le']) ?></td>
          <td><div class="table-actions">
            <a href="<?= BASE_URL ?>/actualites/<?= Security::e($a['slug']) ?>" target="_blank" class="table-action-btn view" title="Voir">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/></svg>
            </a>
            <a href="<?= BASE_URL ?>/admin/articles/<?= (int)$a['id'] ?>/editer" class="table-action-btn edit" title="Éditer">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </a>
          </div></td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="7" style="text-align:center;padding:var(--space-8);color:var(--color-gray-400)">Aucun article</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="admin-table-footer">
    <?= Helpers::paginationHtml($pagination??Helpers::paginate(0), BASE_URL.'/admin/articles') ?>
  </div>
</div>
