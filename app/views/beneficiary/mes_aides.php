<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<div class="dashboard-page-header"><h1>Mes aides reçues</h1><p>Historique de toutes les aides accordées</p></div>
<?php if (!empty($aides)): ?>
<div class="aide-timeline" style="max-width:700px">
  <?php foreach ($aides as $a):
    $icons=['financiere'=>'💰','alimentaire'=>'🍎','medicale'=>'🏥','scolaire'=>'📚','logement'=>'🏠','materiel'=>'📦','psychologique'=>'🧠','juridique'=>'⚖️'];
    $ico=$icons[$a['type_aide']]??'🤝';
    $dotColors=['approuve'=>'#dbeafe','complete'=>'#d1fae5','en_cours'=>'#fef3c7','annule'=>'#fee2e2'];
  ?>
  <div class="aide-timeline-item">
    <div class="aide-timeline-dot" style="background:<?= $dotColors[$a['statut']]??'#f3f4f6' ?>"><?= $ico ?></div>
    <div class="aide-timeline-content">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:var(--space-2)">
        <div>
          <div class="aide-timeline-title"><?= Security::e(TYPES_AIDE[$a['type_aide']]??$a['type_aide']) ?></div>
          <div class="aide-timeline-date">Accordée le <?= Helpers::formatDate($a['date_attribution'],false,true) ?> par <?= Security::e($a['accordeur']??'L\'équipe EuroCare') ?></div>
        </div>
        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:var(--space-1)">
          <?php if ($a['montant']>0): ?><strong style="color:var(--color-success);font-size:var(--text-md)"><?= Helpers::formatAmount((float)$a['montant']) ?></strong><?php endif; ?>
          <span class="badge badge-<?= $a['statut']==='complete'?'green':($a['statut']==='approuve'?'blue':'yellow') ?>"><?= Security::e(ucfirst($a['statut'])) ?></span>
        </div>
      </div>
      <p class="aide-timeline-desc"><?= Security::e($a['description']) ?></p>
      <?php if ($a['date_completion']): ?><div style="font-size:var(--text-xs);color:var(--color-success)">✅ Complétée le <?= Helpers::formatDate($a['date_completion'],false,true) ?></div><?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php else: ?>
<div style="text-align:center;padding:var(--space-12);background:white;border-radius:var(--radius-xl);border:1px solid var(--color-gray-100)">
  <div style="font-size:3rem;margin-bottom:var(--space-4)">🎁</div>
  <p style="color:var(--color-gray-500)">Aucune aide accordée pour le moment.</p>
  <a href="<?= BASE_URL ?>/beneficiaire/mon-dossier" class="btn btn-primary mt-4">Compléter mon dossier</a>
</div>
<?php endif; ?>
