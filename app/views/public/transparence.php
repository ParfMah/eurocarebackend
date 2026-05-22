<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>

<section class="page-hero">
  <div class="container">
    <div class="page-hero-content reveal">
      <span class="section-eyebrow" style="color:var(--color-gold-light);display:inline-block;margin-bottom:var(--space-3)">Transparence totale</span>
      <h1 class="page-hero-title">Utilisation de vos dons</h1>
      <p class="page-hero-subtitle">Conformément à nos engagements, nous publions en temps réel l'intégralité de nos données financières et d'impact.</p>
    </div>
  </div>
</section>

<div class="breadcrumb-section">
  <div class="container">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Accueil</a></li>
      <li class="breadcrumb-separator">›</li>
      <li class="breadcrumb-item active">Transparence</li>
    </ol>
  </div>
</div>

<!-- KPIs principaux -->
<section class="section">
  <div class="container">
    <div class="grid grid-cols-4 gap-5" style="margin-bottom:var(--space-12)">
      <?php $gs = $stats ?? Helpers::getGlobalStats();
      foreach ([
        ['💰',Helpers::formatAmount($gs['total_dons']?:0),'Total des dons reçus','#1a56db'],
        ['✅',Helpers::formatAmount($gs['total_aide_accordee']?:0),'Montant redistribué','#059669'],
        [($gs['taux_redistribution']?:92).'%','','Taux de redistribution','#7c3aed'],
        ['👥',number_format($gs['nombre_beneficiaires']?:0,0,',',' '),'Personnes aidées','#d97706'],
      ] as $i=>[$ico,$val,$lbl,$col]): ?>
      <div class="stat-card gold reveal delay-<?= $i*100 ?>" style="--stat-color:<?= $col ?>">
        <div class="stat-icon" style="background:<?= $col ?>15;color:<?= $col ?>;font-size:1.75rem"><?= $ico ?></div>
        <div class="stat-value" style="color:<?= $col ?>"><?= Security::e($val) ?></div>
        <div class="stat-label"><?= Security::e($lbl) ?></div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Jauge redistribution -->
    <div class="reveal" style="background:white;border-radius:var(--radius-2xl);padding:var(--space-8);box-shadow:var(--shadow-md);border:1px solid var(--color-gray-100);margin-bottom:var(--space-10)">
      <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:var(--space-4);margin-bottom:var(--space-5)">
        <div>
          <h3 style="font-size:var(--text-xl);font-weight:700">Répartition des fonds collectés</h3>
          <p style="color:var(--color-gray-500);font-size:var(--text-sm);margin-top:4px">Exercice <?= date('Y') ?></p>
        </div>
        <div style="background:var(--color-success-bg);border:1px solid #a7f3d0;border-radius:var(--radius-lg);padding:var(--space-3) var(--space-5)">
          <span style="color:var(--color-success);font-weight:700;font-size:var(--text-lg)"><?= $gs['taux_redistribution']?:92 ?>%</span>
          <span style="color:var(--color-gray-600);font-size:var(--text-sm);margin-left:4px">redistribués directement</span>
        </div>
      </div>
      <div class="expense-chart">
        <?php foreach ([
          ['Aides directes aux bénéficiaires', $gs['taux_redistribution']?:92, '#059669'],
          ['Frais de fonctionnement',          5,   '#1a56db'],
          ['Communication & sensibilisation',  2,   '#7c3aed'],
          ['Réserve opérationnelle',            1,   '#d97706'],
        ] as $e): ?>
        <div class="expense-row">
          <div class="expense-label-row">
            <span class="expense-label"><?= $e[0] ?></span>
            <span class="expense-value"><?= $e[1] ?>%</span>
          </div>
          <div class="progress" style="height:10px">
            <div class="progress-bar" data-progress="<?= $e[1] ?>" style="width:0%;background:<?= $e[2] ?>"></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Dons par mois graphique -->
    <div class="admin-grid-2 gap-6">

      <!-- Graphe mensuel -->
      <div class="reveal" style="background:white;border-radius:var(--radius-2xl);padding:var(--space-8);box-shadow:var(--shadow-md);border:1px solid var(--color-gray-100)">
        <h3 style="font-size:var(--text-lg);font-weight:700;margin-bottom:var(--space-6)">📈 Évolution des dons (12 mois)</h3>
        <?php if (!empty($donsParMois)): ?>
        <div style="display:flex;align-items:flex-end;gap:6px;height:140px">
          <?php
          $maxDon = max(array_column($donsParMois,'total') ?: [1]);
          foreach ($donsParMois as $m):
            $h = $maxDon > 0 ? round(($m['total']/$maxDon)*100) : 0;
            $label = substr($m['mois'], 5, 2) . '/' . substr($m['mois'], 2, 2);
          ?>
          <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;height:100%;justify-content:flex-end">
            <div title="<?= Helpers::formatAmount((float)$m['total']) ?>" style="width:100%;background:var(--color-blue-mid);border-radius:4px 4px 0 0;height:0%;min-height:4px;transition:height .8s ease;cursor:pointer" data-progress="<?= $h ?>"></div>
            <div style="font-size:9px;color:var(--color-gray-400);white-space:nowrap"><?= $label ?></div>
          </div>
          <?php endforeach; ?>
        </div>
        <p style="font-size:var(--text-xs);color:var(--color-gray-400);margin-top:var(--space-3);text-align:center">Montants des dons validés par mois (en €)</p>
        <?php else: ?>
        <p class="text-gray text-center py-8">Aucune donnée disponible pour le moment.</p>
        <?php endif; ?>
      </div>

      <!-- Répartition par cause -->
      <div class="reveal delay-100" style="background:white;border-radius:var(--radius-2xl);padding:var(--space-8);box-shadow:var(--shadow-md);border:1px solid var(--color-gray-100)">
        <h3 style="font-size:var(--text-lg);font-weight:700;margin-bottom:var(--space-6)">🎯 Répartition par cause</h3>
        <?php if (!empty($repartition)):
          $maxRep = max(array_column($repartition,'total') ?: [1]);
        ?>
        <div class="expense-chart">
          <?php $colors = ['#1a56db','#059669','#7c3aed','#d97706','#dc2626','#0ea5e9'];
          foreach ($repartition as $i=>$r):
            $pct = $maxRep > 0 ? round(($r['total']/$maxRep)*100) : 0;
          ?>
          <div class="expense-row">
            <div class="expense-label-row">
              <span class="expense-label" style="font-size:var(--text-xs)"><?= Security::e(Helpers::truncate($r['cause'],35)) ?></span>
              <span class="expense-value" style="font-size:var(--text-xs)"><?= Helpers::formatAmount((float)$r['total']) ?></span>
            </div>
            <div class="progress" style="height:8px">
              <div class="progress-bar" data-progress="<?= $pct ?>" style="width:0%;background:<?= $colors[$i%6] ?>"></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="text-gray text-center">Données à venir.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- Projets financés -->
<?php if (!empty($projets)): ?>
<section class="section bg-gray-50">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-eyebrow">Projets</span>
      <h2 class="section-title">Projets financés en cours</h2>
      <div class="divider-gold"></div>
    </div>
    <div class="grid grid-cols-2 gap-6">
      <?php foreach ($projets as $i=>$p):
        $pct = $p['objectif_montant']>0 ? min(100,round(($p['montant_collecte']/$p['objectif_montant'])*100)) : 0;
      ?>
      <div class="reveal delay-<?= ($i%2)*100 ?>" style="background:white;border-radius:var(--radius-xl);padding:var(--space-6);box-shadow:var(--shadow-sm);border:1px solid var(--color-gray-100)">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:var(--space-4)">
          <h3 style="font-size:var(--text-md);font-weight:700"><?= Security::e($p['titre']) ?></h3>
          <span class="badge badge-blue"><?= Security::e(ucfirst($p['categorie']??'')) ?></span>
        </div>
        <div class="mission-progress">
          <div class="mission-progress-header">
            <span style="font-size:var(--text-sm)">Progression</span>
            <span class="mission-progress-amounts"><?= Helpers::formatAmount((float)$p['montant_collecte']) ?> / <?= Helpers::formatAmount((float)$p['objectif_montant']) ?></span>
          </div>
          <div class="progress"><div class="progress-bar" data-progress="<?= $pct ?>" style="width:0%"></div></div>
          <div style="display:flex;justify-content:space-between;font-size:var(--text-xs);margin-top:var(--space-1);color:var(--color-gray-500)">
            <span><?= $pct ?>% atteint</span><span><?= (int)$p['beneficiaires_aides'] ?> aidé(s)</span>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- Aides par type -->
<section class="section">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-eyebrow">Bilan des aides</span>
      <h2 class="section-title">Types d'aides accordées</h2>
      <div class="divider-gold"></div>
    </div>
    <div class="transparency-grid">
      <?php if (!empty($aidesParType)):
        foreach ($aidesParType as $a):
          $typeLabel = TYPES_AIDE[$a['type_aide']] ?? $a['type_aide'];
      ?>
      <div class="transparency-card reveal">
        <div style="font-size:2.5rem;margin-bottom:var(--space-3)">
          <?= match($a['type_aide']) { 'financiere'=>'💰','alimentaire'=>'🍎','medicale'=>'🏥','scolaire'=>'📚','logement'=>'🏠','materiel'=>'📦','psychologique'=>'🧠','juridique'=>'⚖️',default=>'🤝' } ?>
        </div>
        <h3 style="font-size:var(--text-md);font-weight:700;margin-bottom:var(--space-2)"><?= Security::e($typeLabel) ?></h3>
        <div style="font-size:var(--text-2xl);font-weight:800;color:var(--color-blue-mid);margin:var(--space-2) 0"><?= (int)$a['nb'] ?></div>
        <div style="font-size:var(--text-xs);color:var(--color-gray-500)">Aides accordées</div>
        <?php if ($a['total'] > 0): ?>
        <div style="font-size:var(--text-sm);font-weight:600;color:var(--color-success);margin-top:var(--space-2)"><?= Helpers::formatAmount((float)$a['total']) ?></div>
        <?php endif; ?>
      </div>
      <?php endforeach;
      else: ?>
      <div style="grid-column:span 3;text-align:center;color:var(--color-gray-500);padding:var(--space-12)">
        Les données d'aides seront disponibles prochainement.
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Engagement RGPD -->
<section class="section-sm bg-gray-50">
  <div class="container">
    <div style="background:white;border-radius:var(--radius-2xl);padding:var(--space-10);box-shadow:var(--shadow-md);border:1px solid var(--color-gray-100);max-width:800px;margin:0 auto;text-align:center" class="reveal">
      <div style="font-size:3rem;margin-bottom:var(--space-4)">🛡️</div>
      <h3 style="font-size:var(--text-xl);font-weight:700;margin-bottom:var(--space-4)">Protection des données — RGPD</h3>
      <p style="color:var(--color-gray-600);line-height:var(--line-relaxed);margin-bottom:var(--space-6)">
        Toutes les données présentées sur cette page sont anonymisées conformément au Règlement Général sur la Protection des Données (RGPD). Aucune information permettant d'identifier personnellement un bénéficiaire n'est publiée sans son consentement explicite.
      </p>
      <div style="display:flex;flex-wrap:wrap;gap:var(--space-3);justify-content:center">
        <a href="<?= BASE_URL ?>/politique-confidentialite" class="btn btn-outline btn-sm">Politique de confidentialité</a>
        <a href="<?= BASE_URL ?>/contact" class="btn btn-outline btn-sm">Exercer vos droits RGPD</a>
      </div>
    </div>
  </div>
</section>
