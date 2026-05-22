<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<div class="dashboard-page-header"><h1>Mes dons</h1><p>Historique complet de vos contributions</p></div>
<?php if (!empty($dons)): ?>
<div style="background:white;border-radius:var(--radius-xl);box-shadow:var(--shadow-xs);border:1px solid var(--color-gray-100);overflow:hidden">
  <table class="table">
    <thead><tr>
      <th>Date</th><th>Montant</th><th>Type</th><th>Cause</th><th>Statut</th><th>Reçu</th>
    </tr></thead>
    <tbody>
      <?php foreach ($dons as $d): ?>
      <tr>
        <td><?= Helpers::formatDate($d['cree_le'],true) ?></td>
        <td><strong style="color:var(--color-blue-mid)"><?= Helpers::formatAmount((float)$d['montant']) ?></strong></td>
        <td><span class="badge badge-blue"><?= Security::e(ucfirst($d['type'])) ?></span></td>
        <td style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= Security::e($d['projet_titre']??$d['cause']??'Fonds général') ?></td>
        <td><span class="badge badge-<?= $d['statut']==='valide'?'green':($d['statut']==='en_attente'?'yellow':'red') ?>"><?= Security::e(STATUTS_DON_LABELS[$d['statut']]??$d['statut']) ?></span></td>
        <td>
          <?php if ($d['statut']==='valide'): ?>
          <a href="<?= BASE_URL ?>/don/recu/<?= Security::e($d['uuid']) ?>" class="btn btn-sm btn-outline" style="font-size:11px;padding:4px 10px">📄 PDF</a>
          <?php else: ?><span style="color:var(--color-gray-400);font-size:var(--text-xs)">—</span><?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?= Helpers::paginationHtml($pagination, BASE_URL.'/donateur/mes-dons') ?>
<?php else: ?>
<div style="text-align:center;padding:var(--space-16);background:white;border-radius:var(--radius-xl)">
  <div style="font-size:3rem;margin-bottom:var(--space-4)">💝</div>
  <p style="color:var(--color-gray-500);margin-bottom:var(--space-5)">Vous n'avez pas encore effectué de don.</p>
  <a href="<?= BASE_URL ?>/faire-un-don" class="btn btn-gold">Faire mon premier don</a>
</div>
<?php endif; ?>
