<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<div class="admin-page-header">
  <div class="admin-page-header-left"><h1>Statistiques globales</h1><p>Vue d'ensemble des métriques de la plateforme</p></div>
</div>

<!-- KPIs -->
<div class="admin-stats-grid" style="margin-bottom:var(--space-6)">
  <?php foreach ([
    ['💝',Helpers::formatAmount($stats['total_dons']??0),'Total dons validés','#1a56db'],
    ['👥',number_format($stats['nombre_beneficiaires']??0,0,',',' '),'Bénéficiaires aidés','#059669'],
    ['🏛️',$stats['nombre_partenaires']??0,'Partenaires actifs','#7c3aed'],
    [($stats['taux_redistribution']??92).'%','','Taux redistribution','#d97706'],
  ] as $i=>[$ico,$val,$lbl,$col]): ?>
  <div class="admin-stat-card" style="--stat-color:<?= $col ?>">
    <div class="admin-stat-icon" style="background:<?= $col ?>15;font-size:1.5rem"><?= $ico ?></div>
    <div class="admin-stat-body">
      <div class="admin-stat-value"><?= Security::e($val) ?></div>
      <div class="admin-stat-label"><?= Security::e($lbl) ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<div class="admin-grid-2" style="margin-bottom:var(--space-5)">
  <!-- Dons par mois -->
  <div class="admin-widget">
    <div class="admin-widget-header"><div class="admin-widget-title">📈 Dons mensuels (12 mois)</div></div>
    <div class="admin-widget-body">
      <?php if (!empty($donsParMois)): $maxDon = max(array_column($donsParMois,'total')?:[1]); ?>
      <div style="display:flex;align-items:flex-end;gap:6px;height:140px;margin-bottom:var(--space-3)">
        <?php foreach ($donsParMois as $m): $h=round(($m['total']/$maxDon)*100); ?>
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;height:100%;justify-content:flex-end">
          <div title="<?= Helpers::formatAmount((float)$m['total']) ?> — <?= $m['nb'] ?> don(s)"
            style="width:100%;background:linear-gradient(0deg,var(--color-blue-mid),var(--color-blue-light));border-radius:4px 4px 0 0;height:0%;transition:height 1s ease"
            data-progress="<?= $h ?>"></div>
          <div style="font-size:9px;color:var(--color-gray-400)"><?= substr($m['mois'],5) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
      <div style="display:flex;justify-content:space-between;font-size:var(--text-xs);color:var(--color-gray-500)">
        <span>Total période : <strong><?= Helpers::formatAmount(array_sum(array_column($donsParMois,'total'))) ?></strong></span>
        <span><?= array_sum(array_column($donsParMois,'nb')) ?> dons</span>
      </div>
      <?php else: ?><p class="text-gray text-center">Aucune donnée</p><?php endif; ?>
    </div>
  </div>

  <!-- Utilisateurs par rôle -->
  <div class="admin-widget">
    <div class="admin-widget-header"><div class="admin-widget-title">👥 Répartition par rôle</div></div>
    <div class="admin-widget-body">
      <?php if (!empty($parRole)): $totalUsers=array_sum(array_column($parRole,'nb'));?>
      <div style="display:flex;flex-direction:column;gap:var(--space-3)">
        <?php foreach ($parRole as $r): $pct=$totalUsers>0?round(($r['nb']/$totalUsers)*100):0; ?>
        <div>
          <div style="display:flex;justify-content:space-between;font-size:var(--text-xs);margin-bottom:var(--space-1)">
            <span style="color:<?= ROLES_COLORS[$r['role']] ?>;font-weight:600"><?= Security::e(ROLES_LABELS[$r['role']]??$r['role']) ?></span>
            <span style="color:var(--color-gray-500)"><?= $r['nb'] ?> (<?= $pct ?>%)</span>
          </div>
          <div class="progress" style="height:8px">
            <div class="progress-bar" data-progress="<?= $pct ?>" style="width:0%;background:<?= ROLES_COLORS[$r['role']] ?>"></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?><p class="text-gray text-center">Aucune donnée</p><?php endif; ?>
    </div>
  </div>
</div>

<!-- Aides par type -->
<?php if (!empty($aidesByType)): ?>
<div class="admin-widget">
  <div class="admin-widget-header"><div class="admin-widget-title">🎁 Aides accordées par type</div></div>
  <div class="admin-widget-body">
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:var(--space-4)">
      <?php $icons=['financiere'=>'💰','alimentaire'=>'🍎','medicale'=>'🏥','scolaire'=>'📚','logement'=>'🏠','materiel'=>'📦','psychologique'=>'🧠','juridique'=>'⚖️'];
      foreach ($aidesByType as $a): ?>
      <div style="background:var(--color-gray-50);border-radius:var(--radius-xl);padding:var(--space-4);text-align:center;border:1px solid var(--color-gray-100)">
        <div style="font-size:1.75rem;margin-bottom:var(--space-2)"><?= $icons[$a['type_aide']]??'🤝' ?></div>
        <div style="font-size:var(--text-xl);font-weight:800;color:var(--color-blue-mid)"><?= (int)$a['nb'] ?></div>
        <div style="font-size:var(--text-xs);color:var(--color-gray-500);margin-top:2px"><?= Security::e(TYPES_AIDE[$a['type_aide']]??$a['type_aide']) ?></div>
        <?php if ($a['total']>0): ?><div style="font-size:var(--text-xs);color:var(--color-success);margin-top:4px;font-weight:600"><?= Helpers::formatAmount((float)$a['total']) ?></div><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>
