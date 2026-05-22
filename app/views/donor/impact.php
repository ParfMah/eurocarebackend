<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<div class="dashboard-page-header"><h1>L'impact de mes dons</h1><p>Visualisez l'effet concret de vos contributions</p></div>
<div class="grid grid-cols-3 gap-5" style="margin-bottom:var(--space-8)">
  <div class="stat-card" style="--stat-color:#1a56db"><div class="stat-icon" style="font-size:1.5rem;background:#dbeafe">💝</div><div class="stat-value"><?= Helpers::formatAmount($stats['total']) ?></div><div class="stat-label">Total donné</div></div>
  <div class="stat-card" style="--stat-color:#059669"><div class="stat-icon" style="font-size:1.5rem;background:#d1fae5">📊</div><div class="stat-value"><?= (int)$stats['nb'] ?></div><div class="stat-label">Dons effectués</div></div>
  <div class="stat-card" style="--stat-color:#7c3aed"><div class="stat-icon" style="font-size:1.5rem;background:#ede9fe">🎯</div><div class="stat-value"><?= count($stats['projets']) ?></div><div class="stat-label">Projets soutenus</div></div>
</div>
<?php if (!empty($stats['projets'])): ?>
<div style="background:white;border-radius:var(--radius-xl);padding:var(--space-6);box-shadow:var(--shadow-xs);border:1px solid var(--color-gray-100);margin-bottom:var(--space-6)">
  <h3 style="font-weight:700;margin-bottom:var(--space-5)">🎯 Projets que vous avez soutenus</h3>
  <div style="display:flex;flex-direction:column;gap:var(--space-3)">
    <?php foreach ($stats['projets'] as $p): ?>
    <div style="display:flex;align-items:center;gap:var(--space-3);padding:var(--space-3) 0;border-bottom:1px solid var(--color-gray-50)">
      <div style="width:2.5rem;height:2.5rem;background:var(--color-blue-pale);border-radius:var(--radius-lg);display:flex;align-items:center;justify-content:center;font-size:1.25rem"><?= match($p['categorie']??'') { 'enfance'=>'👶','sante'=>'🏥','emploi'=>'💼','urgence'=>'🆘',default=>'🤝' } ?></div>
      <div style="font-weight:600;font-size:var(--text-sm)"><?= Security::e($p['titre']) ?></div>
      <span class="badge badge-blue" style="margin-left:auto"><?= Security::e(ucfirst($p['categorie']??'')) ?></span>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>
<div style="background:linear-gradient(135deg,#0d2b6e,#1a56db);border-radius:var(--radius-xl);padding:var(--space-8);text-align:center;color:white">
  <div style="font-size:3rem;margin-bottom:var(--space-3)">🙏</div>
  <h3 style="font-size:var(--text-xl);font-weight:700;margin-bottom:var(--space-3)">Merci pour votre générosité</h3>
  <p style="color:rgba(255,255,255,.8);max-width:500px;margin:0 auto var(--space-5);line-height:var(--line-relaxed)">Grâce à des donateurs engagés comme vous, nous pouvons continuer à changer des vies chaque jour.</p>
  <a href="<?= BASE_URL ?>/transparence" style="color:var(--color-gold-light);font-weight:600;text-decoration:none;font-size:var(--text-sm)">Voir comment vos dons sont utilisés →</a>
</div>
