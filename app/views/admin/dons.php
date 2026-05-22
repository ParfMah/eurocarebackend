<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<div class="admin-page-header">
  <div class="admin-page-header-left">
    <h1>Gestion des dons</h1>
    <p><?= (int)($pagination['total']??0) ?> don(s) · Total validé : <strong><?= Helpers::formatAmount((float)($totaux['total_valide']??0)) ?></strong></p>
  </div>
  <a href="<?= BASE_URL ?>/admin/export/dons" class="btn btn-outline btn-sm">📥 CSV</a>
</div>
<div class="admin-table-card">
  <form method="GET" action="<?= BASE_URL ?>/admin/dons">
    <div class="admin-filters">
      <input type="text" name="q" class="admin-filter-input" placeholder="Nom, email, référence..." value="<?= Security::e($search??'') ?>" style="min-width:200px">
      <select name="statut" class="admin-filter-input admin-filter-select">
        <option value="">Tous statuts</option>
        <?php foreach (STATUTS_DON_LABELS as $v=>$l): ?><option value="<?= $v ?>" <?= ($statutFilter??'')===$v?'selected':'' ?>><?= Security::e($l) ?></option><?php endforeach; ?>
      </select>
      <input type="date" name="du" class="admin-filter-input" value="<?= Security::e($du??'') ?>" title="Du">
      <input type="date" name="au" class="admin-filter-input" value="<?= Security::e($au??'') ?>" title="Au">
      <button type="submit" class="btn btn-primary btn-sm">Filtrer</button>
      <a href="<?= BASE_URL ?>/admin/dons" class="btn btn-ghost btn-sm">Reset</a>
    </div>
  </form>
  <div class="table-wrapper" style="border:none;box-shadow:none;border-radius:0">
    <table class="admin-data-table">
      <thead><tr>
        <th data-sort>Donateur</th><th data-sort>Montant</th><th data-sort>Type</th>
        <th data-sort>Cause</th><th data-sort>Statut</th><th data-sort>Date</th><th>Actions</th>
      </tr></thead>
      <tbody>
        <?php if (!empty($dons)): foreach ($dons as $d): ?>
        <tr>
          <td>
            <?php if ($d['donateur_anonyme']): ?>
            <div class="table-user"><div class="table-avatar">?</div><div><div class="table-user-name">Anonyme</div><?php if ($d['email_anonyme']): ?><div class="table-user-email"><?= Security::e($d['email_anonyme']) ?></div><?php endif; ?></div></div>
            <?php else: ?>
            <div class="table-user"><div class="table-avatar"><?= mb_strtoupper(mb_substr($d['donateur_nom']??'?',0,1)) ?></div><div><div class="table-user-name"><?= Security::e($d['donateur_nom']??'—') ?></div><div class="table-user-email"><?= Security::e($d['donateur_email']??'') ?></div></div></div>
            <?php endif; ?>
          </td>
          <td><strong style="color:var(--color-blue-mid)"><?= Helpers::formatAmount((float)$d['montant']) ?></strong></td>
          <td><span class="badge badge-blue"><?= Security::e(ucfirst($d['type'])) ?></span></td>
          <td style="font-size:var(--text-xs);max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= Security::e($d['cause']??'Fonds général') ?></td>
          <td><span class="badge badge-<?= $d['statut']==='valide'?'green':($d['statut']==='en_attente'?'yellow':'red') ?>"><?= Security::e(STATUTS_DON_LABELS[$d['statut']]??$d['statut']) ?></span></td>
          <td style="color:var(--color-gray-500);font-size:var(--text-xs)"><?= Helpers::formatDate($d['cree_le']) ?></td>
          <td><div class="table-actions">
            <?php if ($d['statut']==='en_attente'): ?>
            <button class="table-action-btn" style="color:var(--color-success)" title="Valider"
              data-action="valider" data-url="<?= BASE_URL ?>/admin/dons/<?= (int)$d['id'] ?>/valider"
              data-confirm="Valider ce don de <?= Helpers::formatAmount((float)$d['montant']) ?> ?" data-refresh="true">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            </button>
            <?php endif; ?>
          </div></td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="7" style="text-align:center;padding:var(--space-8);color:var(--color-gray-400)">Aucun don trouvé</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="admin-table-footer">
    <span>Affichage <?= ($pagination['from']??0) ?>–<?= ($pagination['to']??0) ?> sur <?= ($pagination['total']??0) ?></span>
    <?= Helpers::paginationHtml($pagination??Helpers::paginate(0), BASE_URL.'/admin/dons') ?>
  </div>
</div>
