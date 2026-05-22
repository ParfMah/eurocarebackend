<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<div class="dashboard-page-header">
  <h1>Mon espace bénéficiaire</h1>
  <p>Suivez votre dossier et l'avancement de vos demandes d'aide</p>
</div>

<!-- Statut dossier -->
<?php if ($dossier): ?>
<div class="dossier-status-card" style="--status-color:<?= STATUTS_BENEF_COLORS[$dossier['statut_dossier']] ?? '#6b7280' ?>;margin-bottom:var(--space-6)">
  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:var(--space-3)">
    <div>
      <div style="font-size:var(--text-xs);color:var(--color-gray-500);text-transform:uppercase;letter-spacing:.05em;margin-bottom:var(--space-1)">Numéro de dossier</div>
      <div style="font-size:var(--text-lg);font-weight:800;color:var(--color-gray-900)"><?= Security::e($dossier['numero_dossier']) ?></div>
    </div>
    <div style="text-align:right">
      <span class="badge" style="font-size:var(--text-sm);padding:var(--space-2) var(--space-4);background:<?= STATUTS_BENEF_COLORS[$dossier['statut_dossier']] ?>20;color:<?= STATUTS_BENEF_COLORS[$dossier['statut_dossier']] ?>">
        <?= Security::e(STATUTS_BENEF_LABELS[$dossier['statut_dossier']] ?? $dossier['statut_dossier']) ?>
      </span>
      <div style="font-size:var(--text-xs);color:var(--color-gray-400);margin-top:var(--space-1)">Urgence : <strong><?= Security::e(URGENCE_LABELS[$dossier['niveau_urgence']] ?? '') ?></strong></div>
    </div>
  </div>

  <!-- Timeline statuts -->
  <div class="status-timeline" style="margin-top:var(--space-5)">
    <?php $steps = ['en_attente'=>'En attente','en_etude'=>'En étude','verifie'=>'Vérifié','prioritaire'=>'Prioritaire','aide'=>'Aidé'];
    $current = $dossier['statut_dossier'];
    $order   = array_keys($steps);
    $curIdx  = array_search($current, $order);
    foreach ($steps as $k=>$l):
      $idx   = array_search($k, $order);
      $done  = $idx < $curIdx;
      $isCur = $k === $current;
    ?>
    <div class="status-step <?= $done?'done':($isCur?'current':'') ?>">
      <div class="status-dot"><?= $done?'✓':($isCur?'●':($idx+1)) ?></div>
      <div class="status-step-label"><?= Security::e($l) ?></div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php else: ?>
<div style="background:var(--color-warning-bg);border:1px solid #fde68a;border-radius:var(--radius-xl);padding:var(--space-5);margin-bottom:var(--space-6)">
  <p style="color:#92400e;font-size:var(--text-sm);margin:0">⚠️ Vous n'avez pas encore créé votre dossier social. <a href="<?= BASE_URL ?>/beneficiaire/mon-dossier" style="color:#92400e;font-weight:700">Créer mon dossier →</a></p>
</div>
<?php endif; ?>

<!-- Actions rapides -->
<div style="display:flex;flex-wrap:wrap;gap:var(--space-3);margin-bottom:var(--space-8)">
  <a href="<?= BASE_URL ?>/beneficiaire/mon-dossier" class="btn btn-primary">📋 Mon dossier</a>
  <a href="<?= BASE_URL ?>/beneficiaire/mes-aides"   class="btn btn-outline">🎁 Aides reçues</a>
  <a href="<?= BASE_URL ?>/beneficiaire/messages"    class="btn btn-outline">✉️ Messages</a>
</div>

<!-- Dernières aides -->
<?php if (!empty($aides)): ?>
<div style="background:white;border-radius:var(--radius-xl);border:1px solid var(--color-gray-100);padding:var(--space-5);box-shadow:var(--shadow-xs);margin-bottom:var(--space-5)">
  <h3 style="font-weight:700;margin-bottom:var(--space-4)">🎁 Dernières aides accordées</h3>
  <div class="aide-timeline">
    <?php foreach ($aides as $a):
      $icons=['financiere'=>'💰','alimentaire'=>'🍎','medicale'=>'🏥','scolaire'=>'📚','logement'=>'🏠','materiel'=>'📦','psychologique'=>'🧠','juridique'=>'⚖️'];
      $ico=$icons[$a['type_aide']]??'🤝';
      $colors=['approuve'=>'#dbeafe','complete'=>'#d1fae5','en_cours'=>'#fef3c7','annule'=>'#fee2e2'];
      $col=$colors[$a['statut']]??'#f3f4f6';
    ?>
    <div class="aide-timeline-item">
      <div class="aide-timeline-dot" style="background:<?= $col ?>"><?= $ico ?></div>
      <div class="aide-timeline-content">
        <div style="display:flex;justify-content:space-between;align-items:flex-start">
          <div class="aide-timeline-title"><?= Security::e(TYPES_AIDE[$a['type_aide']]??$a['type_aide']) ?></div>
          <?php if ($a['montant']>0): ?><strong style="color:var(--color-success)"><?= Helpers::formatAmount((float)$a['montant']) ?></strong><?php endif; ?>
        </div>
        <div class="aide-timeline-date"><?= Helpers::formatDate($a['date_attribution'],false,true) ?></div>
        <p class="aide-timeline-desc"><?= Security::e(Helpers::truncate($a['description'],100)) ?></p>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<!-- Notifications récentes -->
<?php if (!empty($notifs)): ?>
<div style="background:white;border-radius:var(--radius-xl);border:1px solid var(--color-gray-100);padding:var(--space-5);box-shadow:var(--shadow-xs)">
  <h3 style="font-weight:700;margin-bottom:var(--space-4)">🔔 Notifications récentes</h3>
  <div style="display:flex;flex-direction:column;gap:var(--space-2)">
    <?php foreach ($notifs as $n): ?>
    <div style="display:flex;gap:var(--space-3);padding:var(--space-3);background:<?= !$n['lu']?'var(--color-blue-pale)':'var(--color-gray-50)' ?>;border-radius:var(--radius-lg)">
      <div style="width:2rem;height:2rem;border-radius:50%;background:var(--color-blue-mid);display:flex;align-items:center;justify-content:center;font-size:.875rem;flex-shrink:0;color:white">🔔</div>
      <div>
        <div style="font-size:var(--text-sm);font-weight:600"><?= Security::e($n['titre']) ?></div>
        <div style="font-size:var(--text-xs);color:var(--color-gray-500)"><?= Helpers::timeAgo($n['cree_le']) ?></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>
