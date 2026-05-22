<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<div class="admin-page-header">
  <div class="admin-page-header-left"><h1>Utilisateurs</h1><p><?= (int)($pagination['total']??0) ?> compte(s)</p></div>
</div>
<div class="admin-table-card">
  <form method="GET" action="<?= BASE_URL ?>/admin/utilisateurs">
    <div class="admin-filters">
      <input type="text" name="q" class="admin-filter-input" placeholder="Nom, email..." value="<?= Security::e($search??'') ?>" style="min-width:200px">
      <select name="role" class="admin-filter-input admin-filter-select">
        <option value="">Tous rôles</option>
        <?php foreach (ROLES_LABELS as $v=>$l): ?><option value="<?= $v ?>" <?= ($roleFilter??'')===$v?'selected':'' ?>><?= Security::e($l) ?></option><?php endforeach; ?>
      </select>
      <select name="statut" class="admin-filter-input admin-filter-select">
        <option value="">Tous statuts</option>
        <?php foreach (STATUTS_USER_LABELS as $v=>$l): ?><option value="<?= $v ?>" <?= ($statutFilter??'')===$v?'selected':'' ?>><?= Security::e($l) ?></option><?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-primary btn-sm">Filtrer</button>
      <a href="<?= BASE_URL ?>/admin/utilisateurs" class="btn btn-ghost btn-sm">Reset</a>
    </div>
  </form>
  <div class="table-wrapper" style="border:none;box-shadow:none;border-radius:0">
    <table class="admin-data-table">
      <thead><tr>
        <th data-sort>Utilisateur</th><th data-sort>Rôle</th><th data-sort>Statut</th>
        <th data-sort>Email vérifié</th><th data-sort>Pays</th><th data-sort>Inscrit le</th><th>Actions</th>
      </tr></thead>
      <tbody>
        <?php if (!empty($users)): foreach ($users as $u): ?>
        <tr>
          <td><div class="table-user">
            <div class="table-avatar"><?= mb_strtoupper(mb_substr($u['prenom'],0,1).mb_substr($u['nom'],0,1)) ?></div>
            <div><div class="table-user-name"><?= Security::e($u['prenom'].' '.$u['nom']) ?></div><div class="table-user-email"><?= Security::e($u['email']) ?></div></div>
          </div></td>
          <td><span class="badge" style="background:<?= ROLES_COLORS[$u['role']] ?>20;color:<?= ROLES_COLORS[$u['role']] ?>"><?= Security::e(ROLES_LABELS[$u['role']]??$u['role']) ?></span></td>
          <td><span class="badge badge-<?= $u['statut']==='actif'?'green':($u['statut']==='suspendu'?'red':'yellow') ?>"><?= Security::e(STATUTS_USER_LABELS[$u['statut']]??$u['statut']) ?></span></td>
          <td><?= $u['email_verifie']?'<span class="badge badge-green">✓</span>':'<span class="badge badge-yellow">Non</span>' ?></td>
          <td style="font-size:var(--text-xs);color:var(--color-gray-500)"><?= Security::e($u['pays']??'—') ?></td>
          <td style="font-size:var(--text-xs);color:var(--color-gray-500)"><?= Helpers::formatDate($u['cree_le']) ?></td>
          <td><div class="table-actions">
            <?php if ($u['id']!==Auth::id()): ?>
            <?php if ($u['statut']==='actif'): ?>
            <button class="table-action-btn" title="Suspendre" data-action="suspend" data-url="<?= BASE_URL ?>/admin/utilisateurs/<?= (int)$u['id'] ?>/statut" data-statut="suspendu" data-confirm="Suspendre cet utilisateur ?" data-refresh="true" style="color:var(--color-warning)">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="10" y1="15" x2="10" y2="9"/><line x1="14" y1="15" x2="14" y2="9"/></svg>
            </button>
            <?php else: ?>
            <button class="table-action-btn" title="Activer" data-action="activate" data-url="<?= BASE_URL ?>/admin/utilisateurs/<?= (int)$u['id'] ?>/statut" data-statut="actif" data-confirm="Activer ce compte ?" data-refresh="true" style="color:var(--color-success)">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            </button>
            <?php endif; ?>
            <?php endif; ?>
          </div></td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="7" style="text-align:center;padding:var(--space-8);color:var(--color-gray-400)">Aucun utilisateur trouvé</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="admin-table-footer">
    <span>Total : <?= ($pagination['total']??0) ?></span>
    <?= Helpers::paginationHtml($pagination??Helpers::paginate(0), BASE_URL.'/admin/utilisateurs') ?>
  </div>
</div>
