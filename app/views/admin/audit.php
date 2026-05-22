<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<div class="admin-page-header">
  <div class="admin-page-header-left"><h1>Journal d'audit</h1><p>Traçabilité de toutes les actions importantes</p></div>
</div>
<div class="admin-table-card">
  <form method="GET" action="<?= BASE_URL ?>/admin/audit"><div class="admin-filters">
    <input type="text" name="action" class="admin-filter-input" placeholder="Action..." value="<?= Security::e($filters['action']??'') ?>">
    <select name="severite" class="admin-filter-input admin-filter-select">
      <option value="">Toutes sévérités</option>
      <?php foreach (['info'=>'ℹ Info','attention'=>'⚠ Attention','critique'=>'🔴 Critique'] as $v=>$l): ?>
      <option value="<?= $v ?>" <?= ($filters['severite']??'')===$v?'selected':'' ?>><?= $l ?></option>
      <?php endforeach; ?>
    </select>
    <input type="date" name="date_debut" class="admin-filter-input" value="<?= Security::e($filters['date_debut']??'') ?>" title="Depuis">
    <input type="date" name="date_fin"   class="admin-filter-input" value="<?= Security::e($filters['date_fin']??'') ?>"   title="Jusqu'au">
    <button type="submit" class="btn btn-primary btn-sm">Filtrer</button>
    <a href="<?= BASE_URL ?>/admin/audit" class="btn btn-ghost btn-sm">Reset</a>
  </div></form>
  <div class="table-wrapper" style="border:none;box-shadow:none;border-radius:0">
    <table class="admin-data-table">
      <thead><tr><th>Sévérité</th><th>Action</th><th>Module</th><th>Utilisateur</th><th>IP</th><th>Date</th></tr></thead>
      <tbody>
        <?php if (!empty($logs)): foreach ($logs as $l): ?>
        <tr>
          <td><span class="badge badge-<?= $l['severite']==='critique'?'red':($l['severite']==='attention'?'yellow':'blue') ?>"><?= ucfirst($l['severite']) ?></span></td>
          <td><code style="font-size:11px;background:var(--color-gray-100);padding:2px 6px;border-radius:4px"><?= Security::e($l['action']) ?></code></td>
          <td style="font-size:var(--text-xs);color:var(--color-gray-500)"><?= Security::e($l['module']??'—') ?></td>
          <td style="font-size:var(--text-xs)"><?= Security::e($l['user_nom']??'Système') ?> <?php if ($l['user_role']): ?><span class="badge badge-gray" style="font-size:9px"><?= Security::e($l['user_role']) ?></span><?php endif; ?></td>
          <td style="font-size:var(--text-xs);color:var(--color-gray-400)"><?= Security::e($l['ip']??'—') ?></td>
          <td style="font-size:var(--text-xs);color:var(--color-gray-500);white-space:nowrap"><?= Helpers::formatDate($l['cree_le'],true,true) ?></td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="6" style="text-align:center;padding:var(--space-8);color:var(--color-gray-400)">Aucun log</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="admin-table-footer"><?= Helpers::paginationHtml($pagination??Helpers::paginate(0), BASE_URL.'/admin/audit') ?></div>
</div>
