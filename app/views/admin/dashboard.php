<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>

<div class="admin-page-header">
  <div class="admin-page-header-left">
    <h1>Tableau de bord</h1>
    <p>Vue d'ensemble — <?= date('d/m/Y') ?></p>
  </div>
  <div style="display:flex;gap:var(--space-3)">
    <a href="<?= BASE_URL ?>/admin/export/dons"          class="btn btn-outline btn-sm">📥 Export dons</a>
    <a href="<?= BASE_URL ?>/admin/export/beneficiaires" class="btn btn-outline btn-sm">📥 Export bénéficiaires</a>
  </div>
</div>

<!-- KPI Cards -->
<div class="admin-stats-grid" data-auto-refresh>
  <?php $cards = [
    ['💝', Helpers::formatAmount($stats['dons_ce_mois']),    'Dons ce mois',      '#1a56db'],
    ['🏦', Helpers::formatAmount($stats['total_dons']),      'Total dons',        '#059669'],
    ['🤝', number_format($stats['total_beneficiaires'],0,',',' '), 'Bénéficiaires', '#7c3aed'],
    ['👥', number_format($stats['total_users'],0,',',' '),   'Utilisateurs',      '#d97706'],
  ]; foreach ($cards as $c): ?>
  <div class="admin-stat-card" style="--stat-color:<?= $c[3] ?>">
    <div class="admin-stat-icon" style="background:<?= $c[3] ?>15;font-size:1.5rem"><?= $c[0] ?></div>
    <div class="admin-stat-body">
      <div class="admin-stat-value"><?= Security::e($c[1]) ?></div>
      <div class="admin-stat-label"><?= Security::e($c[2]) ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Alertes prioritaires -->
<?php if ($stats['en_attente'] > 0 || $stats['messages_nouveaux'] > 0 || $stats['partenaires_attente'] > 0): ?>
<div style="display:flex;flex-wrap:wrap;gap:var(--space-3);margin-bottom:var(--space-6)">
  <?php if ($stats['en_attente'] > 0): ?>
  <a href="<?= BASE_URL ?>/admin/beneficiaires?statut=en_attente" class="alert alert-warning" style="flex:1;min-width:200px;text-decoration:none">
    <span class="alert-icon">⚠</span>
    <div class="alert-content"><strong><?= $stats['en_attente'] ?> dossier(s)</strong> en attente de traitement</div>
  </a>
  <?php endif; ?>
  <?php if ($stats['messages_nouveaux'] > 0): ?>
  <a href="<?= BASE_URL ?>/admin/messages" class="alert alert-info" style="flex:1;min-width:200px;text-decoration:none">
    <span class="alert-icon">✉</span>
    <div class="alert-content"><strong><?= $stats['messages_nouveaux'] ?> message(s)</strong> non lus</div>
  </a>
  <?php endif; ?>
  <?php if ($stats['partenaires_attente'] > 0): ?>
  <a href="<?= BASE_URL ?>/admin/partenaires?statut=en_attente" class="alert alert-info" style="flex:1;min-width:200px;text-decoration:none">
    <span class="alert-icon">🏛</span>
    <div class="alert-content"><strong><?= $stats['partenaires_attente'] ?> partenaire(s)</strong> en attente</div>
  </a>
  <?php endif; ?>
</div>
<?php endif; ?>

<div class="admin-grid-2" style="margin-bottom:var(--space-5)">

  <!-- Graphe dons -->
  <div class="admin-widget">
    <div class="admin-widget-header">
      <div class="admin-widget-title">📊 Dons — 6 derniers mois</div>
      <a href="<?= BASE_URL ?>/admin/statistiques" class="text-sm text-blue">Voir tout →</a>
    </div>
    <div class="admin-widget-body">
      <?php if (!empty($donsGraphe)): $maxDon = max(array_column($donsGraphe,'total') ?: [1]); ?>
      <div style="display:flex;align-items:flex-end;gap:8px;height:120px">
        <?php foreach ($donsGraphe as $d): $h = $maxDon > 0 ? round(($d['total']/$maxDon)*100) : 0; ?>
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;height:100%;justify-content:flex-end">
          <div title="<?= Helpers::formatAmount((float)$d['total']) ?>"
               data-progress="<?= $h ?>"
               style="width:100%;background:var(--color-blue-mid);border-radius:4px 4px 0 0;height:0%;transition:height .8s ease;min-height:4px"></div>
          <div style="font-size:9px;color:var(--color-gray-400)"><?= Security::e($d['mois']) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?><p class="text-gray text-center">Aucune donnée</p><?php endif; ?>
    </div>
  </div>

  <!-- Activité récente -->
  <div class="admin-widget">
    <div class="admin-widget-header">
      <div class="admin-widget-title">🔍 Activité récente</div>
      <a href="<?= BASE_URL ?>/admin/audit" class="text-sm text-blue">Voir tout →</a>
    </div>
    <div class="admin-widget-body" style="padding:0">
      <div class="activity-list" style="padding:0 var(--space-5)">
        <?php
        $icons = ['connexion'=>'🔑','deconnexion'=>'🚪','creation_don'=>'💝',
                  'validation_dossier'=>'✅','creation_aide'=>'🤝','creation_utilisateur'=>'👤',
                  'modification_parametre'=>'⚙️'];
        $bgMap = ['info'=>'#dbeafe','attention'=>'#fef3c7','critique'=>'#fee2e2'];
        foreach (array_slice($auditRecent, 0, 7) as $log):
          $ico = $icons[$log['action']] ?? '📌';
          $bg  = $bgMap[$log['severite']] ?? '#f3f4f6';
        ?>
        <div class="activity-item">
          <div class="activity-icon" style="background:<?= $bg ?>"><?= $ico ?></div>
          <div style="flex:1;min-width:0">
            <div class="activity-text"><?= Security::e(str_replace('_',' ',ucfirst($log['action']))) ?> — <span class="text-gray"><?= Security::e($log['user_nom']) ?></span></div>
          </div>
          <div class="activity-time"><?= Helpers::timeAgo($log['cree_le']) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<div class="admin-grid-2">
  <!-- Derniers dons -->
  <div class="admin-widget">
    <div class="admin-widget-header">
      <div class="admin-widget-title">💝 Derniers dons</div>
      <a href="<?= BASE_URL ?>/admin/dons" class="text-sm text-blue">Voir tout →</a>
    </div>
    <div class="table-wrapper" style="border:none;box-shadow:none;border-radius:0">
      <table class="admin-data-table">
        <thead><tr><th>Donateur</th><th>Montant</th><th>Statut</th><th>Date</th></tr></thead>
        <tbody>
          <?php foreach ($derniersDons as $d): ?>
          <tr>
            <td><div class="truncate" style="max-width:140px"><?= Security::e($d['donateur_anonyme'] ? 'Anonyme' : $d['donateur_nom']) ?></div></td>
            <td><strong><?= Helpers::formatAmount((float)$d['montant']) ?></strong></td>
            <td><span class="badge badge-<?= $d['statut']==='valide'?'green':($d['statut']==='en_attente'?'yellow':'red') ?>"><?= Security::e(STATUTS_DON_LABELS[$d['statut']]??$d['statut']) ?></span></td>
            <td class="text-gray" style="font-size:var(--text-xs)"><?= Helpers::formatDate($d['cree_le']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Derniers bénéficiaires -->
  <div class="admin-widget">
    <div class="admin-widget-header">
      <div class="admin-widget-title">🤝 Dossiers récents</div>
      <a href="<?= BASE_URL ?>/admin/beneficiaires" class="text-sm text-blue">Voir tout →</a>
    </div>
    <div class="table-wrapper" style="border:none;box-shadow:none;border-radius:0">
      <table class="admin-data-table">
        <thead><tr><th>Bénéficiaire</th><th>Urgence</th><th>Statut</th></tr></thead>
        <tbody>
          <?php foreach ($derniersBenef as $b): ?>
          <tr>
            <td>
              <div class="table-user">
                <div class="table-avatar"><?= mb_strtoupper(mb_substr($b['prenom'],0,1).mb_substr($b['nom'],0,1)) ?></div>
                <div>
                  <div class="table-user-name"><?= Security::e($b['prenom'].' '.$b['nom']) ?></div>
                  <div class="table-user-email"><?= Security::e($b['numero_dossier']) ?></div>
                </div>
              </div>
            </td>
            <td><span class="badge" style="background:<?= URGENCE_COLORS[$b['niveau_urgence']] ?>20;color:<?= URGENCE_COLORS[$b['niveau_urgence']] ?>"><?= Security::e(URGENCE_LABELS[$b['niveau_urgence']]??'') ?></span></td>
            <td><span class="badge" style="background:<?= STATUTS_BENEF_COLORS[$b['statut_dossier']] ?>20;color:<?= STATUTS_BENEF_COLORS[$b['statut_dossier']] ?>"><?= Security::e(STATUTS_BENEF_LABELS[$b['statut_dossier']]??'') ?></span></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
