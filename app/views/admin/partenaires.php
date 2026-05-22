<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<div class="admin-page-header">
  <div class="admin-page-header-left"><h1>Partenaires institutionnels</h1><p><?= (int)($pagination['total']??0) ?> organisation(s)</p></div>
</div>
<div class="admin-table-card">
  <form method="GET" action="<?= BASE_URL ?>/admin/partenaires"><div class="admin-filters">
    <select name="statut" class="admin-filter-input admin-filter-select">
      <option value="">Tous statuts</option>
      <?php foreach (['en_attente'=>'En attente','valide'=>'Validé','suspendu'=>'Suspendu','rejete'=>'Rejeté'] as $v=>$l): ?><option value="<?= $v ?>" <?= ($statutFilter??'')===$v?'selected':'' ?>><?= Security::e($l) ?></option><?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-primary btn-sm">Filtrer</button>
    <a href="<?= BASE_URL ?>/admin/partenaires" class="btn btn-ghost btn-sm">Reset</a>
  </div></form>
  <div class="table-wrapper" style="border:none;box-shadow:none;border-radius:0">
    <table class="admin-data-table">
      <thead><tr><th>Organisation</th><th>Type</th><th>Contact</th><th>Pays</th><th>Statut</th><th>Actions</th></tr></thead>
      <tbody>
        <?php if (!empty($partenaires)): foreach ($partenaires as $p): ?>
        <tr>
          <td><div class="table-user">
            <div class="table-avatar" style="background:var(--color-blue-pale);color:var(--color-blue-mid)">🏛</div>
            <div><div class="table-user-name"><?= Security::e($p['nom_organisation']) ?></div><div class="table-user-email"><?= Security::e($p['email']??'') ?></div></div>
          </div></td>
          <td style="font-size:var(--text-xs)"><?= Security::e(TYPES_PARTENAIRE[$p['type_organisation']]??$p['type_organisation']) ?></td>
          <td style="font-size:var(--text-xs)"><?= Security::e($p['email_contact']??'—') ?></td>
          <td style="font-size:var(--text-xs);color:var(--color-gray-500)"><?= Security::e($p['pays']??'—') ?></td>
          <td><span class="badge badge-<?= $p['statut']==='valide'?'green':($p['statut']==='en_attente'?'yellow':'red') ?>"><?= ucfirst($p['statut']) ?></span></td>
          <td><div class="table-actions">
            <?php if ($p['statut']==='en_attente'): ?>
            <button class="table-action-btn" style="color:var(--color-success)" title="Valider" data-action="valider" data-url="<?= BASE_URL ?>/admin/partenaires/<?= (int)$p['id'] ?>/valider" data-statut="valide" data-confirm="Valider ce partenaire ?" data-refresh="true">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            </button>
            <button class="table-action-btn" style="color:var(--color-danger)" title="Rejeter" data-action="rejeter" data-url="<?= BASE_URL ?>/admin/partenaires/<?= (int)$p['id'] ?>/valider" data-statut="rejete" data-confirm="Rejeter ce partenaire ?" data-refresh="true">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            <?php endif; ?>
          </div></td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="6" style="text-align:center;padding:var(--space-8);color:var(--color-gray-400)">Aucun partenaire</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="admin-table-footer">
    <span>Total : <?= ($pagination['total']??0) ?></span>
    <?= Helpers::paginationHtml($pagination??Helpers::paginate(0), BASE_URL.'/admin/partenaires') ?>
  </div>
</div>
