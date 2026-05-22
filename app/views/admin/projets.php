<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<div class="admin-page-header">
  <div class="admin-page-header-left"><h1>Projets & Causes</h1><p>Gestion des projets humanitaires actifs</p></div>
</div>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:var(--space-5)">
  <?php if (!empty($projets)): foreach ($projets as $p):
    $pct=$p['objectif_montant']>0?min(100,round(($p['montant_collecte']/$p['objectif_montant'])*100)):0;
  ?>
  <div class="admin-widget">
    <div class="admin-widget-header">
      <div class="admin-widget-title"><?= Security::e(Helpers::truncate($p['titre'],35)) ?></div>
      <span class="badge badge-<?= $p['statut']==='actif'?'green':($p['statut']==='complete'?'blue':'gray') ?>"><?= ucfirst($p['statut']) ?></span>
    </div>
    <div class="admin-widget-body">
      <div style="margin-bottom:var(--space-3)">
        <div style="display:flex;justify-content:space-between;font-size:var(--text-xs);margin-bottom:var(--space-1)">
          <span><?= Helpers::formatAmount((float)$p['montant_collecte']) ?></span>
          <span><?= $pct ?>% / <?= Helpers::formatAmount((float)$p['objectif_montant']) ?></span>
        </div>
        <div class="progress" style="height:8px"><div class="progress-bar" data-progress="<?= $pct ?>" style="width:0%"></div></div>
      </div>
      <div style="display:flex;justify-content:space-between;font-size:var(--text-xs);color:var(--color-gray-500)">
        <span>👥 <?= (int)$p['beneficiaires_aides'] ?> aidé(s)</span>
        <span>📁 <?= Security::e(ucfirst($p['categorie']??'—')) ?></span>
      </div>
    </div>
  </div>
  <?php endforeach; else: ?>
  <div style="text-align:center;padding:var(--space-12);color:var(--color-gray-400);grid-column:1/-1"><div style="font-size:3rem;margin-bottom:var(--space-3)">📁</div><p>Aucun projet créé</p></div>
  <?php endif; ?>
</div>
