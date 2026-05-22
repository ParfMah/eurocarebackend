<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>

<section class="page-hero">
  <div class="container">
    <div class="page-hero-content reveal">
      <span class="section-eyebrow" style="color:var(--color-gold-light);display:inline-block;margin-bottom:var(--space-3)">Nos causes</span>
      <h1 class="page-hero-title">Nos missions humanitaires</h1>
      <p class="page-hero-subtitle">Des projets concrets, des actions mesurables et un engagement total pour les personnes les plus vulnérables.</p>
    </div>
  </div>
</section>

<div class="breadcrumb-section">
  <div class="container">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Accueil</a></li>
      <li class="breadcrumb-separator">›</li>
      <li class="breadcrumb-item active">Nos missions</li>
    </ol>
  </div>
</div>

<!-- Intro missions -->
<section class="section">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-eyebrow">Impact réel</span>
      <h2 class="section-title">Projets actifs & résultats</h2>
      <div class="divider-gold"></div>
      <p class="section-subtitle">Chaque projet est suivi en temps réel. Vos dons sont directement fléchés vers ces causes.</p>
    </div>

    <?php if (!empty($projets)): ?>
    <div class="missions-grid">
      <?php foreach ($projets as $i => $p):
        $pct = $p['objectif_montant'] > 0 ? min(100, round(($p['montant_collecte'] / $p['objectif_montant']) * 100)) : 0;
        $catEmojis = ['enfance'=>'👶','sante'=>'🏥','emploi'=>'💼','urgence'=>'🆘','logement'=>'🏠','default'=>'🤝'];
        $emoji = $catEmojis[$p['categorie']] ?? $catEmojis['default'];
      ?>
      <article class="mission-card reveal delay-<?= ($i%3)*100 ?>">
        <div class="mission-card-header">
          <?php if ($p['image']): ?>
            <img src="<?= UPLOAD_URL ?>/projets/<?= Security::e($p['image']) ?>" alt="<?= Security::e($p['titre']) ?>" loading="lazy">
          <?php else: ?>
            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:4rem"><?= $emoji ?></div>
          <?php endif; ?>
          <div class="mission-card-header-overlay"></div>
          <span class="mission-category-badge"><?= Security::e(ucfirst($p['categorie'] ?? 'Humanitaire')) ?></span>
          <?php if ($p['statut'] === 'complete'): ?>
          <span style="position:absolute;top:var(--space-4);right:var(--space-4);background:var(--color-success);color:white;font-size:var(--text-xs);font-weight:600;padding:4px 10px;border-radius:var(--radius-full)">✅ Complété</span>
          <?php endif; ?>
        </div>
        <div class="mission-card-body">
          <h3 class="mission-title"><?= Security::e($p['titre']) ?></h3>
          <p class="mission-desc"><?= Security::e(Helpers::truncate($p['description_courte'] ?? $p['description'], 130)) ?></p>

          <!-- Progression don -->
          <div class="mission-progress">
            <div class="mission-progress-header">
              <span style="font-size:var(--text-xs);color:var(--color-gray-500)">Collecté</span>
              <span class="mission-progress-amounts"><?= Helpers::formatAmount((float)$p['montant_collecte']) ?> / <?= Helpers::formatAmount((float)$p['objectif_montant']) ?></span>
            </div>
            <div class="progress">
              <div class="progress-bar" data-progress="<?= $pct ?>" style="width:0%"></div>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:var(--text-xs);margin-top:var(--space-1);color:var(--color-gray-500)">
              <span><strong style="color:var(--color-blue-mid)"><?= $pct ?>%</strong> atteint</span>
              <span>👥 <?= (int)$p['beneficiaires_aides'] ?> personne(s) aidée(s)</span>
            </div>
          </div>

          <?php if ($p['date_fin']): ?>
          <div style="font-size:var(--text-xs);color:var(--color-gray-500);margin-bottom:var(--space-4)">
            📅 Fin prévue : <?= Helpers::formatDate($p['date_fin'],false,true) ?>
          </div>
          <?php endif; ?>

          <a href="<?= BASE_URL ?>/faire-un-don?projet=<?= (int)$p['id'] ?>" class="btn btn-primary btn-block">
            💝 Soutenir ce projet
          </a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div style="text-align:center;padding:var(--space-16);color:var(--color-gray-500)">
      <div style="font-size:3rem;margin-bottom:var(--space-4)">🚀</div>
      <p>De nouveaux projets seront bientôt disponibles.</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- Comment fonctionne l'aide -->
<section class="section bg-gray-50">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-eyebrow">Notre méthode</span>
      <h2 class="section-title">Comment l'aide est attribuée ?</h2>
      <div class="divider-gold"></div>
    </div>
    <div class="steps-grid">
      <?php foreach ([
        ['1','📋','Dépôt du dossier','Le bénéficiaire crée son dossier sur notre plateforme avec les justificatifs requis.'],
        ['2','🔍','Instruction sociale','Notre équipe de travailleurs sociaux étudie et vérifie chaque situation individuellement.'],
        ['3','✅','Validation et priorisation','Le dossier est validé et classé selon le niveau d\'urgence et les besoins identifiés.'],
        ['4','💝','Attribution de l\'aide','L\'aide est accordée et tracée : type, montant, date, responsable — tout est documenté.'],
      ] as $i=>$s): ?>
      <div class="step-card reveal delay-<?= $i*100 ?>">
        <div class="step-number-wrap"><span class="step-number"><?= $s[0] ?></span></div>
        <div style="font-size:2rem;margin-bottom:var(--space-3)"><?= $s[1] ?></div>
        <h3 class="step-title"><?= $s[2] ?></h3>
        <p class="step-desc"><?= $s[3] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Stats globales -->
<section class="impact-section section-sm">
  <div class="container">
    <div class="impact-grid">
      <?php $gs = $stats ?? Helpers::getGlobalStats();
      foreach ([
        [$gs['projets_actifs']?:4,'+','Projets actifs'],
        [number_format($gs['nombre_beneficiaires']?:1240,0,',',' '),'+','Personnes aidées'],
        [$gs['taux_redistribution']?:92,'%','Dons redistribués'],
        [$gs['nombre_partenaires']?:48,'+','Partenaires'],
      ] as $i=>$k): ?>
      <div class="impact-stat reveal delay-<?= $i*100 ?>">
        <span class="impact-value" data-count="<?= preg_replace('/[^0-9]/','',$k[0]) ?>" data-suffix="<?= $k[1] ?>"><?= $k[0] ?><?= $k[1] ?></span>
        <div class="impact-label"><?= $k[2] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="cta-section">
  <div class="container">
    <div class="cta-content reveal">
      <h2 class="cta-title">Vous avez besoin d'aide ?</h2>
      <p class="cta-subtitle">Créez votre dossier en quelques minutes. Notre équipe sociale vous accompagnera.</p>
      <div class="cta-actions">
        <a href="<?= BASE_URL ?>/inscription?type=beneficiaire" class="btn btn-gold btn-xl">🤝 Demander une aide</a>
        <a href="<?= BASE_URL ?>/faire-un-don"                 class="btn btn-outline-white btn-xl">💝 Soutenir nos projets</a>
      </div>
    </div>
  </div>
</section>
