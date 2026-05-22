<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>

<section class="page-hero">
  <div class="container">
    <div class="page-hero-content reveal">
      <span class="section-eyebrow" style="color:var(--color-gold-light);display:inline-block;margin-bottom:var(--space-3)">Notre organisation</span>
      <h1 class="page-hero-title">À propos d'EuroCare Humanitaire</h1>
      <p class="page-hero-subtitle">Une organisation fondée sur des valeurs d'humanité, de transparence et d'efficacité au service des personnes vulnérables d'Europe.</p>
    </div>
  </div>
</section>

<div class="breadcrumb-section">
  <div class="container">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Accueil</a></li>
      <li class="breadcrumb-separator">›</li>
      <li class="breadcrumb-item active">À propos</li>
    </ol>
  </div>
</div>

<!-- Histoire -->
<section class="section">
  <div class="container">
    <div class="about-intro">
      <div class="reveal">
        <span class="section-eyebrow">Notre histoire</span>
        <h2 class="section-title" style="margin-top:var(--space-3)">Fondée sur un engagement profond</h2>
        <div class="divider-gold" style="margin:var(--space-4) 0"></div>
        <p style="color:var(--color-gray-700);line-height:var(--line-relaxed);margin-bottom:var(--space-4)">Depuis <?= Security::e(Helpers::getSetting('fondation_annee','2010')) ?>, EuroCare Humanitaire œuvre sans relâche pour apporter une aide concrète aux personnes les plus vulnérables d'Europe. Née de la conviction que chaque être humain mérite dignité et soutien, notre organisation a accompagné des milliers de familles à travers les moments les plus difficiles.</p>
        <p style="color:var(--color-gray-700);line-height:var(--line-relaxed);margin-bottom:var(--space-6)">Reconnue d'utilité publique et certifiée « Don en confiance », nous faisons de la transparence et de l'efficacité nos piliers fondamentaux. Chaque euro est tracé, chaque aide est documentée, chaque bénéficiaire est accompagné avec respect.</p>
        <div class="about-values">
          <?php foreach ([
            ['🏛️','Transparence','100% des actions documentées et publiées.'],
            ['🤝','Dignité','Chaque personne traitée avec respect absolu.'],
            ['⚡','Efficacité','92% des dons redistribués directement.'],
            ['🌍','Universalité','Aide sans discrimination aucune.'],
          ] as $v): ?>
          <div class="value-item">
            <div class="value-icon"><span style="font-size:1.25rem"><?= $v[0] ?></span></div>
            <div><div class="value-text-title"><?= $v[1] ?></div><div class="value-text-desc"><?= $v[2] ?></div></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="about-image-wrap reveal delay-200">
        <div style="height:450px;background:linear-gradient(135deg,#1a56db,#0d2b6e);border-radius:var(--radius-2xl);display:flex;align-items:center;justify-content:center;box-shadow:var(--shadow-xl);position:relative;overflow:hidden">
          <div style="position:absolute;inset:0;background:radial-gradient(ellipse at 30% 30%,rgba(184,134,11,.2),transparent 60%)"></div>
          <span style="font-size:7rem;z-index:1">🤝</span>
        </div>
        <div class="about-image-badge">
          <div style="font-size:2rem;font-weight:800;color:var(--color-blue-deep)" data-count="<?= date('Y')-(int)Helpers::getSetting('fondation_annee',2010) ?>" data-suffix="">0</div>
          <div style="font-size:12px;color:var(--color-gray-500)">ans d'expérience</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Chiffres -->
<section class="impact-section section-sm">
  <div class="container">
    <div class="impact-grid">
      <?php $gs = $stats ?? Helpers::getGlobalStats();
      foreach ([
        [number_format($gs['nombre_beneficiaires']?:1240,0,',',' '),'+','Bénéficiaires aidés'],
        [number_format(($gs['total_dons']?:180000)/1000,0,',',' '),'K€','Dons collectés'],
        [$gs['nombre_partenaires']?:48,'+','Partenaires actifs'],
        [$gs['taux_redistribution']?:92,'%','Dons redistribués'],
      ] as $i=>$k): ?>
      <div class="impact-stat reveal delay-<?= $i*100 ?>">
        <span class="impact-value"><?= Security::e($k[0]) ?><span class="suffix"><?= $k[1] ?></span></span>
        <div class="impact-label"><?= $k[2] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Domaines -->
<section class="section bg-gray-50">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-eyebrow">Ce que nous faisons</span>
      <h2 class="section-title">Nos domaines d'intervention</h2>
      <div class="divider-gold"></div>
    </div>
    <div class="grid grid-cols-3 gap-6">
      <?php foreach ([
        ['👶','Enfance & Jeunesse','Soutien aux orphelins, aide scolaire, accompagnement psychologique.','#1a56db'],
        ['🏥','Santé & Médical','Accès aux soins pour les personnes sans couverture médicale.','#059669'],
        ['🏠','Logement & Urgence','Solutions d\'hébergement d\'urgence et aide à la précarité.','#7c3aed'],
        ['💼','Emploi & Réinsertion','Formations professionnelles et accompagnement à l\'emploi.','#d97706'],
        ['👴','Personnes âgées','Assistance aux seniors isolés et aide dans les démarches.','#dc2626'],
        ['👨‍👩‍👧','Familles en difficulté','Aide alimentaire, soutien financier et accompagnement éducatif.','#0ea5e9'],
      ] as $i=>[$ico,$titre,$desc,$col]): ?>
      <div class="card reveal delay-<?= ($i%3)*100 ?>" style="text-align:center;padding:var(--space-8)">
        <div style="width:4rem;height:4rem;background:<?= $col ?>15;border-radius:var(--radius-2xl);display:flex;align-items:center;justify-content:center;margin:0 auto var(--space-4);font-size:2rem"><?= $ico ?></div>
        <h3 style="font-size:var(--text-md);font-weight:700;margin-bottom:var(--space-3)"><?= $titre ?></h3>
        <p style="font-size:var(--text-sm);color:var(--color-gray-600);line-height:var(--line-relaxed)"><?= $desc ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Équipe -->
<section class="section">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-eyebrow">Notre équipe dirigeante</span>
      <h2 class="section-title">Une gouvernance transparente</h2>
      <div class="divider-gold"></div>
    </div>
    <div class="team-grid">
      <?php foreach ([
        ['🎓','Dr. Émile Durand','Président Directeur Général','#1a56db'],
        ['💼','Sophie Martin','Directrice des Opérations','#059669'],
        ['📊','Jean-Paul Lefèvre','Responsable Financier','#7c3aed'],
        ['🤝','Marie Nguyen','Coordinatrice Sociale','#d97706'],
      ] as $m): ?>
      <div class="team-card reveal">
        <div class="team-photo" style="background:<?= $m[3] ?>15;color:<?= $m[3] ?>;font-size:2rem"><?= $m[0] ?></div>
        <div class="team-name"><?= Security::e($m[1]) ?></div>
        <div class="team-role"><?= Security::e($m[2]) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Certifications -->
<section class="section-sm bg-gray-50">
  <div class="container">
    <div class="section-header reveal" style="margin-bottom:var(--space-8)">
      <span class="section-eyebrow">Accréditations</span>
      <h2 class="section-title">Nos certifications</h2>
      <div class="divider-gold"></div>
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:var(--space-5);justify-content:center">
      <?php foreach ([
        ['🏆','Don en confiance','Label qualité ONG françaises'],
        ['🛡️','RGPD Conforme','Protection des données garantie'],
        ['✅','ISO 9001','Certification qualité processus'],
        ['🏛️','Utilité publique','Reconnue par décret ministériel'],
        ['🇪🇺','Réseau européen','Membre réseau humanitaire EU'],
      ] as $c): ?>
      <div class="reveal" style="background:white;border:1px solid var(--color-gray-200);border-radius:var(--radius-2xl);padding:var(--space-6) var(--space-8);text-align:center;min-width:170px;box-shadow:var(--shadow-xs);transition:all var(--transition-normal)"
        onmouseover="this.style.boxShadow='var(--shadow-lg)';this.style.borderColor='var(--color-blue-border)'"
        onmouseout="this.style.boxShadow='var(--shadow-xs)';this.style.borderColor='var(--color-gray-200)'">
        <div style="font-size:2.5rem;margin-bottom:var(--space-2)"><?= $c[0] ?></div>
        <div style="font-weight:700;color:var(--color-gray-900);font-size:var(--text-sm);margin-bottom:4px"><?= $c[1] ?></div>
        <div style="font-size:var(--text-xs);color:var(--color-gray-500)"><?= $c[2] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="cta-section">
  <div class="container">
    <div class="cta-content reveal">
      <h2 class="cta-title">Rejoignez notre mission</h2>
      <p class="cta-subtitle">Que vous souhaitiez faire un don, demander une aide ou devenir partenaire institutionnel.</p>
      <div class="cta-actions">
        <a href="<?= BASE_URL ?>/faire-un-don"    class="btn btn-gold btn-xl">💝 Faire un don</a>
        <a href="<?= BASE_URL ?>/nos-partenaires" class="btn btn-outline-white btn-xl">Devenir partenaire</a>
      </div>
    </div>
  </div>
</section>
