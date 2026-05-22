<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<section class="page-hero"><div class="container"><div class="page-hero-content reveal">
  <h1 class="page-hero-title">Nos partenaires institutionnels</h1>
  <p class="page-hero-subtitle">Des organisations qui partagent nos valeurs et amplifient notre impact sur le terrain.</p>
</div></div></section>
<div class="breadcrumb-section"><div class="container"><ol class="breadcrumb">
  <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Accueil</a></li><li class="breadcrumb-separator">›</li>
  <li class="breadcrumb-item active">Nos partenaires</li>
</ol></div></div>
<section class="section"><div class="container">
  <?php $types=['ong'=>['🌍','ONG'],'hopital'=>['🏥','Hôpitaux'],'ecole'=>['🏫','Écoles'],'association'=>['🤝','Associations'],'service_social'=>['🏛️','Services sociaux'],'entreprise_mecene'=>['💼','Entreprises mécènes'],'fondation'=>['🎗️','Fondations'],'autre'=>['🔷','Autres']]; ?>
  <?php if (!empty($parParType)): foreach ($parParType as $type=>$parts): $ti=$types[$type]??['🔷',ucfirst($type)]; ?>
  <div style="margin-bottom:var(--space-12)">
    <h2 style="font-size:var(--text-xl);font-weight:700;margin-bottom:var(--space-6);display:flex;align-items:center;gap:var(--space-3)">
      <span style="width:3rem;height:3rem;background:var(--color-blue-pale);border-radius:var(--radius-xl);display:flex;align-items:center;justify-content:center;font-size:1.25rem"><?= $ti[0] ?></span>
      <?= Security::e($ti[1]) ?> <span style="font-size:var(--text-sm);font-weight:400;color:var(--color-gray-400)">(<?= count($parts) ?>)</span>
    </h2>
    <div class="grid grid-cols-3 gap-5">
      <?php foreach ($parts as $i=>$p): ?>
      <div class="card reveal delay-<?= ($i%3)*100 ?>" style="padding:var(--space-6)">
        <div style="display:flex;align-items:center;gap:var(--space-4);margin-bottom:var(--space-4)">
          <div style="width:3.5rem;height:3.5rem;border-radius:var(--radius-xl);background:var(--color-gray-100);display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0">
            <?php if ($p['logo']): ?><img src="<?= UPLOAD_URL ?>/partenaires/<?= Security::e($p['logo']) ?>" style="width:100%;height:100%;object-fit:contain;padding:4px"><?php else: ?><span style="font-size:1.5rem"><?= $ti[0] ?></span><?php endif; ?>
          </div>
          <div>
            <div style="font-weight:700;font-size:var(--text-sm)"><?= Security::e($p['nom_organisation']) ?></div>
            <?php if ($p['pays']||$p['ville']): ?><div style="font-size:var(--text-xs);color:var(--color-gray-500)"><?= Security::e(implode(', ',array_filter([$p['ville'],$p['pays']]))) ?></div><?php endif; ?>
          </div>
        </div>
        <?php if ($p['description']): ?><p style="font-size:var(--text-xs);color:var(--color-gray-600);line-height:var(--line-relaxed)"><?= Security::e(Helpers::truncate($p['description'],100)) ?></p><?php endif; ?>
        <?php if ($p['site_web']): ?><a href="<?= Security::e($p['site_web']) ?>" target="_blank" rel="noopener" class="text-sm text-blue" style="display:inline-block;margin-top:var(--space-2)">Visiter →</a><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endforeach; else: ?>
  <div style="text-align:center;padding:var(--space-16);color:var(--color-gray-500)"><div style="font-size:3rem;margin-bottom:var(--space-4)">🤝</div><p>La liste des partenaires sera publiée prochainement.</p></div>
  <?php endif; ?>
</div></section>
<section class="cta-section"><div class="container"><div class="cta-content reveal">
  <h2 class="cta-title">Rejoignez notre réseau</h2>
  <p class="cta-subtitle">Votre organisation partage nos valeurs ? Devenez partenaire institutionnel d'EuroCare.</p>
  <div class="cta-actions">
    <a href="<?= BASE_URL ?>/inscription?type=partenaire" class="btn btn-gold btn-xl">Devenir partenaire</a>
    <a href="<?= BASE_URL ?>/contact" class="btn btn-outline-white btn-xl">Nous contacter</a>
  </div>
</div></div></section>
