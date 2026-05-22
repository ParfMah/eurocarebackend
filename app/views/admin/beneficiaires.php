<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<div class="admin-page-header">
  <div class="admin-page-header-left">
    <h1>Dossiers bénéficiaires</h1>
    <p><?= (int)($pagination['total']??0) ?> dossier(s)</p>
  </div>
  <a href="<?= BASE_URL ?>/admin/export/beneficiaires" class="btn btn-outline btn-sm">📥 CSV</a>
</div>

<div class="admin-table-card">
  <form method="GET" action="<?= BASE_URL ?>/admin/beneficiaires">
    <div class="admin-filters">
      <input type="text" name="q" class="admin-filter-input" placeholder="Nom, email, n° dossier..." value="<?= Security::e($search??'') ?>" style="min-width:200px">
      <select name="statut" class="admin-filter-input admin-filter-select">
        <option value="">Tous statuts</option>
        <?php foreach (STATUTS_BENEF_LABELS as $v=>$l): ?><option value="<?= $v ?>" <?= ($statutFilter??'')===$v?'selected':'' ?>><?= Security::e($l) ?></option><?php endforeach; ?>
      </select>
      <select name="urgence" class="admin-filter-input admin-filter-select">
        <option value="">Toutes urgences</option>
        <?php foreach (URGENCE_LABELS as $v=>$l): ?><option value="<?= $v ?>" <?= ($urgenceFilter??'')===$v?'selected':'' ?>><?= Security::e($l) ?></option><?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-primary btn-sm">Filtrer</button>
      <a href="<?= BASE_URL ?>/admin/beneficiaires" class="btn btn-ghost btn-sm">Réinitialiser</a>
    </div>
  </form>
  <div class="table-wrapper" style="border:none;box-shadow:none;border-radius:0">
    <table class="admin-data-table">
      <thead><tr>
        <th data-sort>Bénéficiaire</th><th data-sort>N° Dossier</th><th data-sort>Type</th>
        <th data-sort>Urgence</th><th data-sort>Statut</th><th data-sort>Date</th><th>Actions</th>
      </tr></thead>
      <tbody>
        <?php if (!empty($beneficiaires)): foreach ($beneficiaires as $b): ?>
        <tr>
          <td><div class="table-user">
            <div class="table-avatar"><?= mb_strtoupper(mb_substr($b['prenom'],0,1).mb_substr($b['nom'],0,1)) ?></div>
            <div><div class="table-user-name"><?= Security::e($b['prenom'].' '.$b['nom']) ?></div><div class="table-user-email"><?= Security::e($b['email']) ?></div></div>
          </div></td>
          <td><code style="font-size:11px;background:var(--color-gray-100);padding:2px 6px;border-radius:4px"><?= Security::e($b['numero_dossier']) ?></code></td>
          <td style="font-size:var(--text-xs)"><?= Security::e(TYPES_BENEFICIAIRE[$b['type_beneficiaire']]??$b['type_beneficiaire']) ?></td>
          <td><span class="badge" style="background:<?= URGENCE_COLORS[$b['niveau_urgence']] ?>20;color:<?= URGENCE_COLORS[$b['niveau_urgence']] ?>"><?= Security::e(URGENCE_LABELS[$b['niveau_urgence']]??'') ?></span></td>
          <td><span class="badge" style="background:<?= STATUTS_BENEF_COLORS[$b['statut_dossier']] ?>20;color:<?= STATUTS_BENEF_COLORS[$b['statut_dossier']] ?>"><?= Security::e(STATUTS_BENEF_LABELS[$b['statut_dossier']]??'') ?></span></td>
          <td style="color:var(--color-gray-500);font-size:var(--text-xs)"><?= Helpers::formatDate($b['cree_le']) ?></td>
          <td><div class="table-actions">
            <a href="<?= BASE_URL ?>/admin/beneficiaires/<?= (int)$b['id'] ?>" class="table-action-btn view" title="Voir">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </a>
          </div></td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="7" style="text-align:center;padding:var(--space-8);color:var(--color-gray-400)">Aucun dossier trouvé</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="admin-table-footer">
    <span>Affichage <?= ($pagination['from']??0) ?>–<?= ($pagination['to']??0) ?> sur <?= ($pagination['total']??0) ?></span>
    <?= Helpers::paginationHtml($pagination??Helpers::paginate(0), BASE_URL.'/admin/beneficiaires') ?>
  </div>
</div>
