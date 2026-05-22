<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<div class="admin-page-header">
  <div class="admin-page-header-left"><h1>Témoignages</h1><p>Modération des témoignages soumis</p></div>
</div>
<div class="admin-table-card">
  <form method="GET"><div class="admin-filters">
    <select name="statut" class="admin-filter-input admin-filter-select">
      <?php foreach (['en_attente'=>'⏳ En attente','approuve'=>'✅ Approuvés','rejete'=>'❌ Rejetés'] as $v=>$l): ?>
      <option value="<?= $v ?>" <?= ($statutFilter??'')===$v?'selected':'' ?>><?= $l ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-primary btn-sm">Filtrer</button>
  </div></form>
  <div class="table-wrapper" style="border:none;box-shadow:none;border-radius:0">
    <table class="admin-data-table">
      <thead><tr><th>Auteur</th><th>Rôle</th><th>Note</th><th>Extrait</th><th>Statut</th><th>Actions</th></tr></thead>
      <tbody>
        <?php if (!empty($temoignages)): foreach ($temoignages as $t): ?>
        <tr>
          <td><strong style="font-size:var(--text-sm)"><?= Security::e($t['nom_affiche']) ?></strong><?php if ($t['pays']): ?><div style="font-size:11px;color:var(--color-gray-400)"><?= Security::e($t['pays']) ?></div><?php endif; ?></td>
          <td style="font-size:var(--text-xs);color:var(--color-gray-500)"><?= Security::e($t['role']??'—') ?></td>
          <td>{'★'×<?= (int)$t['note'] ?>}</td>
          <td style="max-width:200px;font-size:var(--text-xs);color:var(--color-gray-600)"><?= Security::e(Helpers::truncate($t['contenu'],80)) ?></td>
          <td><span class="badge badge-<?= $t['statut']==='approuve'?'green':($t['statut']==='en_attente'?'yellow':'red') ?>"><?= ucfirst($t['statut']) ?></span></td>
          <td><div class="table-actions">
            <button class="table-action-btn" style="color:var(--color-success)" title="Approuver" data-action="approuver" data-url="<?= BASE_URL ?>/admin/temoignages/<?= (int)$t['id'] ?>/statut" data-statut="approuve" data-refresh="true">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            </button>
            <button class="table-action-btn" style="color:var(--color-danger)" title="Rejeter" data-action="rejeter" data-url="<?= BASE_URL ?>/admin/temoignages/<?= (int)$t['id'] ?>/statut" data-statut="rejete" data-refresh="true">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div></td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="6" style="text-align:center;padding:var(--space-8);color:var(--color-gray-400)">Aucun témoignage</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="admin-table-footer"><?= Helpers::paginationHtml($pagination??Helpers::paginate(0), BASE_URL.'/admin/temoignages') ?></div>
</div>
