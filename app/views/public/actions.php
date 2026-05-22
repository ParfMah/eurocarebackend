<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<section class="page-hero"><div class="container"><div class="page-hero-content reveal">
  <h1 class="page-hero-title">Nos actions de terrain</h1>
  <p class="page-hero-subtitle">Découvrez l'impact concret de nos interventions humanitaires auprès des plus vulnérables.</p>
</div></div></section>
<div class="breadcrumb-section"><div class="container"><ol class="breadcrumb">
  <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Accueil</a></li><li class="breadcrumb-separator">›</li>
  <li class="breadcrumb-item active">Nos actions</li>
</ol></div></div>
<section class="section"><div class="container">
  <div class="section-header reveal">
    <span class="section-eyebrow">Bilan opérationnel</span>
    <h2 class="section-title">Types d'aides accordées</h2>
    <div class="divider-gold"></div>
  </div>
  <?php if (!empty($typesAides)): ?>
  <div class="grid grid-cols-3 gap-5" style="margin-bottom:var(--space-12)">
    <?php foreach ($typesAides as $i=>$a):
      $icons=['financiere'=>'💰','alimentaire'=>'🍎','medicale'=>'🏥','scolaire'=>'📚','logement'=>'🏠','materiel'=>'📦','psychologique'=>'🧠','juridique'=>'⚖️'];
      $ico=$icons[$a['type_aide']]??'🤝'; $lbl=TYPES_AIDE[$a['type_aide']]??$a['type_aide'];
    ?>
    <div class="stat-card reveal delay-<?= ($i%3)*100 ?>">
      <div class="stat-icon" style="font-size:1.75rem;background:var(--color-blue-pale)"><?= $ico ?></div>
      <div class="stat-value"><?= (int)$a['nb'] ?></div>
      <div class="stat-label"><?= Security::e($lbl) ?></div>
      <?php if ($a['total']>0): ?><div style="font-size:var(--text-xs);color:var(--color-success);font-weight:600;margin-top:4px"><?= Helpers::formatAmount((float)$a['total']) ?></div><?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
  <!-- Dernières aides anonymisées -->
  <?php if (!empty($dernieresAides)): ?>
  <div class="section-header reveal">
    <span class="section-eyebrow">Récent</span>
    <h2 class="section-title">Dernières interventions</h2>
    <div class="divider-gold"></div>
    <p class="section-subtitle">Données anonymisées conformément au RGPD.</p>
  </div>
  <div class="grid grid-cols-3 gap-5">
    <?php foreach ($dernieresAides as $i=>$a):
      $icons=['financiere'=>'💰','alimentaire'=>'🍎','medicale'=>'🏥','scolaire'=>'📚','logement'=>'🏠','materiel'=>'📦','psychologique'=>'🧠','juridique'=>'⚖️'];
      $ico=$icons[$a['type_aide']]??'🤝';
    ?>
    <div class="card reveal delay-<?= ($i%3)*100 ?>" style="padding:var(--space-5)">
      <div style="display:flex;gap:var(--space-3);align-items:flex-start">
        <span style="font-size:2rem"><?= $ico ?></span>
        <div>
          <div style="font-weight:600;font-size:var(--text-sm)"><?= Security::e(TYPES_AIDE[$a['type_aide']]??$a['type_aide']) ?></div>
          <div style="font-size:var(--text-xs);color:var(--color-gray-500);margin-top:2px"><?= Security::e(TYPES_BENEFICIAIRE[$a['type_beneficiaire']]??'Bénéficiaire') ?></div>
          <div style="margin-top:var(--space-2)"><span class="badge" style="background:<?= URGENCE_COLORS[$a['niveau_urgence']]??'#6b7280' ?>20;color:<?= URGENCE_COLORS[$a['niveau_urgence']]??'#6b7280' ?>"><?= Security::e(URGENCE_LABELS[$a['niveau_urgence']]??'') ?></span></div>
          <?php if ($a['date_completion']): ?><div style="font-size:11px;color:var(--color-gray-400);margin-top:var(--space-2)"><?= Helpers::formatDate($a['date_completion'],false,true) ?></div><?php endif; ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div></section>
<section class="cta-section"><div class="container"><div class="cta-content reveal">
  <h2 class="cta-title">Soutenez nos actions</h2>
  <div class="cta-actions">
    <a href="<?= BASE_URL ?>/faire-un-don" class="btn btn-gold btn-xl">💝 Faire un don</a>
    <a href="<?= BASE_URL ?>/transparence" class="btn btn-outline-white btn-xl">📊 Transparence</a>
  </div>
</div></div></section>
