<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<div class="admin-page-header">
  <div class="admin-page-header-left"><h1>Messages de contact</h1><p><?= (int)($pagination['total']??0) ?> message(s)</p></div>
</div>
<div class="admin-table-card">
  <form method="GET" action="<?= BASE_URL ?>/admin/messages"><div class="admin-filters">
    <select name="statut" class="admin-filter-input admin-filter-select">
      <?php foreach (['nouveau'=>'🔴 Nouveaux','lu'=>'Lu','repondu'=>'Répondu','archive'=>'Archivé'] as $v=>$l): ?>
      <option value="<?= $v ?>" <?= ($statutFilter??'nouveau')===$v?'selected':'' ?>><?= $l ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-primary btn-sm">Filtrer</button>
  </div></form>
  <div class="table-wrapper" style="border:none;box-shadow:none;border-radius:0">
    <table class="admin-data-table">
      <thead><tr><th>Expéditeur</th><th>Sujet</th><th>Statut</th><th>Date</th><th>Actions</th></tr></thead>
      <tbody>
        <?php if (!empty($messages)): foreach ($messages as $m): ?>
        <tr style="<?= $m['statut']==='nouveau'?'background:var(--color-blue-pale)':'' ?>">
          <td><div class="table-user">
            <div class="table-avatar"><?= mb_strtoupper(mb_substr($m['nom'],0,1)) ?></div>
            <div><div class="table-user-name"><?= Security::e($m['nom']) ?></div><div class="table-user-email"><?= Security::e($m['email']) ?></div></div>
          </div></td>
          <td><?= Security::e(Helpers::truncate($m['sujet'],40)) ?></td>
          <td><span class="badge badge-<?= $m['statut']==='nouveau'?'blue':($m['statut']==='repondu'?'green':'gray') ?>"><?= ucfirst($m['statut']) ?></span></td>
          <td style="font-size:var(--text-xs);color:var(--color-gray-500)"><?= Helpers::formatDate($m['cree_le'],true) ?></td>
          <td><a href="<?= BASE_URL ?>/admin/messages/<?= (int)$m['id'] ?>" class="table-action-btn view" title="Lire">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </a></td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="5" style="text-align:center;padding:var(--space-8);color:var(--color-gray-400)">Aucun message</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="admin-table-footer"><?= Helpers::paginationHtml($pagination??Helpers::paginate(0), BASE_URL.'/admin/messages') ?></div>
</div>
