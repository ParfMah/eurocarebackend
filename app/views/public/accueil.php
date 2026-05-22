<?php
/**
 * Vue : Page d'accueil
 * app/views/public/accueil.php
 */
defined('BASEPATH') or die('Accès direct interdit.');
?>

<!-- ====================================================
     SECTION HERO
     ==================================================== -->
<section class="hero" id="hero">
  <div class="hero-bg"></div>
  <!-- Particules décoratives -->
  <div class="hero-particles">
    <?php for ($i = 0; $i < 8; $i++):
      $size = rand(40, 120); $left = rand(5,95); $delay = rand(0,8); $dur = rand(12,25);
    ?>
    <div class="hero-particle" style="width:<?= $size ?>px;height:<?= $size ?>px;left:<?= $left ?>%;animation-duration:<?= $dur ?>s;animation-delay:-<?= $delay ?>s"></div>
    <?php endfor; ?>
  </div>

  <div class="container" style="width:100%">
    <div class="hero-split">
      <!-- Texte principal -->
      <div class="hero-content animate-fade-in">
        <div class="hero-eyebrow">
          <span class="hero-eyebrow-dot"></span>
          Organisation Humanitaire Européenne
        </div>
        <h1 class="hero-title">
          Ensemble pour un<br>
          <span class="accent">monde plus juste</span>
        </h1>
        <p class="hero-subtitle">
          EuroCare Humanitaire soutient les personnes vulnérables à travers l'Europe.
          Chaque don transforme une vie. Chaque action compte.
        </p>
        <div class="hero-actions">
          <a href="<?= BASE_URL ?>/faire-un-don" class="btn btn-gold btn-lg">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
            Faire un don maintenant
          </a>
          <a href="<?= BASE_URL ?>/nos-missions" class="btn btn-outline-white btn-lg">
            Découvrir nos missions
          </a>
        </div>
        <!-- Stats hero -->
        <div class="hero-stats">
          <div class="hero-stat-item">
            <div class="hero-stat-value" data-count="<?= (int)($stats['nombre_beneficiaires'] ?: 1240) ?>" data-suffix="+"><?= number_format($stats['nombre_beneficiaires'] ?: 1240, 0, ',', ' ') ?>+</div>
            <div class="hero-stat-label">Bénéficiaires aidés</div>
          </div>
          <div class="hero-stat-item">
            <div class="hero-stat-value" data-count="<?= (int)($stats['nombre_partenaires'] ?: 48) ?>" data-suffix="+"><?= $stats['nombre_partenaires'] ?: 48 ?>+</div>
            <div class="hero-stat-label">Partenaires actifs</div>
          </div>
          <div class="hero-stat-item">
            <div class="hero-stat-value" data-count="<?= (int)($stats['taux_redistribution'] ?: 92) ?>" data-suffix="%"><?= $stats['taux_redistribution'] ?: 92 ?>%</div>
            <div class="hero-stat-label">Dons redistribués</div>
          </div>
          <div class="hero-stat-item">
            <div class="hero-stat-value" data-count="<?= date('Y') - (int)($stats['annee_fondation'] ?: 2010) ?>" data-suffix=" ans"><?= date('Y') - ($stats['annee_fondation'] ?: 2010) ?> ans</div>
            <div class="hero-stat-label">D'expérience</div>
          </div>
        </div>
      </div>

      <!-- Cartes flottantes droite -->
      <div class="animate-fade-in-right delay-200" style="display:flex;flex-direction:column;gap:var(--space-4)">
        <div class="hero-card-float">
          <div class="hero-card-stat">
            <div class="hero-card-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.9)" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <div>
              <div class="hero-card-value"><?= Helpers::formatAmount($stats['total_dons'] ?: 0) ?></div>
              <div class="hero-card-label">Total des dons collectés</div>
            </div>
          </div>
        </div>
        <div class="hero-stats-grid">
          <?php
          $miniStats = [
            ['icon'=>'👶','val'=>'Orphelins','sub'=>'Soutenus'],
            ['icon'=>'🏥','val'=>'Médical','sub'=>'Accompagnement'],
            ['icon'=>'📚','val'=>'Scolaire','sub'=>'Aide à l\'éducation'],
            ['icon'=>'🏠','val'=>'Logement','sub'=>'Solutions trouvées'],
            ['icon'=>'💼','val'=>'Emploi','sub'=>'Réinsertion'],
            ['icon'=>'🤝','val'=>'Famille','sub'=>'En difficulté'],
          ];
          foreach ($miniStats as $ms): ?>
          <div class="hero-stat-box">
            <div style="font-size:1.5rem"><?= $ms['icon'] ?></div>
            <div style="font-size:11px;font-weight:600;color:rgba(255,255,255,.9);margin-top:4px"><?= $ms['val'] ?></div>
            <div style="font-size:10px;color:rgba(255,255,255,.55)"><?= $ms['sub'] ?></div>
          </div>
          <?php endforeach; ?>
        </div>
        <!-- Badge certifications -->
        <div class="hero-card-float" style="display:flex;align-items:center;gap:var(--space-4)">
          <div style="display:flex;gap:var(--space-3)">
            <div style="text-align:center">
              <div style="font-size:1.5rem">🏆</div>
              <div style="font-size:10px;color:rgba(255,255,255,.7)">Don en confiance</div>
            </div>
            <div style="text-align:center">
              <div style="font-size:1.5rem">🛡️</div>
              <div style="font-size:10px;color:rgba(255,255,255,.7)">RGPD</div>
            </div>
            <div style="text-align:center">
              <div style="font-size:1.5rem">✅</div>
              <div style="font-size:10px;color:rgba(255,255,255,.7)">Certifiée</div>
            </div>
          </div>
          <div style="flex:1">
            <div style="font-size:var(--text-sm);font-weight:600;color:white">Organisation certifiée</div>
            <div style="font-size:11px;color:rgba(255,255,255,.6)">Reconnue d'utilité publique</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Scroll indicator -->
  <div class="scroll-indicator" aria-hidden="true">
    <div class="scroll-mouse"><div class="scroll-wheel"></div></div>
    <span>Défiler</span>
  </div>
</section>

<!-- ====================================================
     SECTION IMPACT (compteurs animés)
     ==================================================== -->
<section class="impact-section section-sm" aria-label="Nos chiffres d'impact">
  <div class="container">
    <div class="impact-grid">
      <div class="impact-stat reveal">
        <span class="impact-value" data-count="<?= (int)($stats['nombre_beneficiaires'] ?: 1240) ?>" data-suffix="">0</span>
        <span class="suffix" style="display:none">+</span>
        <div class="impact-label">Bénéficiaires aidés depuis <?= $stats['annee_fondation'] ?? 2010 ?></div>
      </div>
      <div class="impact-stat reveal delay-100">
        <span class="impact-value" data-count="<?= round(($stats['total_dons'] ?: 180000) / 1000) ?>" data-suffix="K€">0</span>
        <div class="impact-label">Total des dons collectés</div>
      </div>
      <div class="impact-stat reveal delay-200">
        <span class="impact-value" data-count="<?= (int)($stats['nombre_partenaires'] ?: 48) ?>" data-suffix="">0</span>
        <div class="impact-label">Partenaires institutionnels</div>
      </div>
      <div class="impact-stat reveal delay-300">
        <span class="impact-value" data-count="<?= (int)($stats['taux_redistribution'] ?: 92) ?>" data-suffix="%">0</span>
        <div class="impact-label">Des dons directement redistribués</div>
      </div>
    </div>
  </div>
</section>

<!-- ====================================================
     SECTION COMMENT ÇA MARCHE
     ==================================================== -->
<section class="section bg-gray-50" aria-labelledby="how-title">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-eyebrow">Processus transparent</span>
      <h2 class="section-title" id="how-title">Comment ça marche ?</h2>
      <div class="divider-gold"></div>
      <p class="section-subtitle">Un processus simple, sécurisé et entièrement traçable pour garantir que votre aide atteint vraiment ceux qui en ont besoin.</p>
    </div>
    <div class="steps-grid">
      <?php $steps = [
        ['num'=>'1','icon'=>'💝','title'=>'Vous faites un don','desc'=>'Choisissez un montant et une cause à soutenir. Votre don est sécurisé et vous recevez immédiatement un reçu fiscal.'],
        ['num'=>'2','icon'=>'🔍','title'=>'Vérification des dossiers','desc'=>'Notre équipe sociale vérifie et valide chaque dossier de bénéficiaire avec rigueur et impartialité.'],
        ['num'=>'3','icon'=>'✅','title'=>'Attribution de l\'aide','desc'=>'L\'aide est attribuée aux bénéficiaires prioritaires selon des critères d\'urgence et de besoin.'],
        ['num'=>'4','icon'=>'📊','title'=>'Suivi et transparence','desc'=>'Chaque don est tracé. Consultez en temps réel l\'utilisation de vos contributions dans notre espace transparence.'],
      ]; foreach ($steps as $i => $s): ?>
      <div class="step-card reveal delay-<?= $i*100 ?>">
        <div class="step-number-wrap">
          <span class="step-number"><?= $s['num'] ?></span>
        </div>
        <h3 class="step-title"><?= Security::e($s['title']) ?></h3>
        <p class="step-desc"><?= Security::e($s['desc']) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ====================================================
     SECTION NOS PROJETS
     ==================================================== -->
<?php if (!empty($projets)): ?>
<section class="section" aria-labelledby="projects-title">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-eyebrow">Nos causes</span>
      <h2 class="section-title" id="projects-title">Projets en cours</h2>
      <div class="divider-gold"></div>
      <p class="section-subtitle">Soutenez nos projets humanitaires actifs et suivez l'impact de vos dons en temps réel.</p>
    </div>
    <div class="missions-grid">
      <?php foreach ($projets as $i => $p):
        $pct = $p['objectif_montant'] > 0 ? min(100, round(($p['montant_collecte'] / $p['objectif_montant']) * 100)) : 0;
      ?>
      <article class="mission-card reveal delay-<?= $i*100 ?>" aria-label="Projet : <?= Security::e($p['titre']) ?>">
        <div class="mission-card-header">
          <?php if ($p['image']): ?>
            <img src="<?= UPLOAD_URL ?>/projets/<?= Security::e($p['image']) ?>" alt="<?= Security::e($p['titre']) ?>" loading="lazy">
          <?php else: ?>
            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:3rem">
              <?= match($p['categorie']) { 'enfance'=>'👶', 'sante'=>'🏥', 'emploi'=>'💼', 'urgence'=>'🆘', default=>'🤝' } ?>
            </div>
          <?php endif; ?>
          <div class="mission-card-header-overlay"></div>
          <span class="mission-category-badge"><?= Security::e(ucfirst($p['categorie'] ?? 'Humanitaire')) ?></span>
        </div>
        <div class="mission-card-body">
          <h3 class="mission-title"><?= Security::e($p['titre']) ?></h3>
          <p class="mission-desc"><?= Security::e(Helpers::truncate($p['description_courte'] ?? $p['description'], 120)) ?></p>
          <!-- Barre de progression -->
          <div class="mission-progress">
            <div class="mission-progress-header">
              <span>Collecté</span>
              <span class="mission-progress-amounts"><?= Helpers::formatAmount((float)$p['montant_collecte']) ?> / <?= Helpers::formatAmount((float)$p['objectif_montant']) ?></span>
            </div>
            <div class="progress">
              <div class="progress-bar" data-progress="<?= $pct ?>" style="width:0%"></div>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:var(--text-xs);margin-top:4px;color:var(--color-gray-500)">
              <span><?= $pct ?>% atteint</span>
              <span><?= $p['beneficiaires_aides'] ?> personne(s) aidée(s)</span>
            </div>
          </div>
          <a href="<?= BASE_URL ?>/faire-un-don?projet=<?= (int)$p['id'] ?>" class="btn btn-primary btn-block">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
            Soutenir ce projet
          </a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-10 reveal">
      <a href="<?= BASE_URL ?>/nos-missions" class="btn btn-outline">Voir tous nos projets →</a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ====================================================
     SECTION TÉMOIGNAGES
     ==================================================== -->
<?php if (!empty($temoignages)): ?>
<section class="section bg-gray-50" aria-labelledby="testimonials-title">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-eyebrow">Ils témoignent</span>
      <h2 class="section-title" id="testimonials-title">Histoires inspirantes</h2>
      <div class="divider-gold"></div>
      <p class="section-subtitle">Des vies transformées. Des familles reconstruites. Des espoirs renaissants.</p>
    </div>
    <div class="testimonials-grid">
      <?php foreach ($temoignages as $i => $t): ?>
      <blockquote class="testimonial-card reveal delay-<?= $i*100 ?>">
        <div class="testimonial-quote" aria-hidden="true">"</div>
        <div class="testimonial-stars" aria-label="Note : <?= (int)$t['note'] ?> étoiles sur 5">
          <?php for ($s = 0; $s < 5; $s++): ?>
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" <?= $s < $t['note'] ? 'fill="currentColor"' : 'fill="none" stroke="currentColor" stroke-width="1.5"' ?>/></svg>
          <?php endfor; ?>
        </div>
        <p class="testimonial-text"><?= Security::e($t['contenu']) ?></p>
        <footer class="testimonial-author">
          <div class="testimonial-avatar">
            <?php if ($t['photo']): ?>
            <img src="<?= UPLOAD_URL ?>/temoignages/<?= Security::e($t['photo']) ?>" alt="<?= Security::e($t['nom_affiche']) ?>">
            <?php else: ?>
            <?= mb_strtoupper(mb_substr($t['nom_affiche'], 0, 1)) ?>
            <?php endif; ?>
          </div>
          <div>
            <div class="testimonial-name"><?= Security::e($t['nom_affiche']) ?></div>
            <?php if ($t['role']): ?>
            <div class="testimonial-role"><?= Security::e($t['role']) ?></div>
            <?php endif; ?>
          </div>
          <?php if ($t['pays']): ?>
          <div class="testimonial-country">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
            <?= Security::e($t['pays']) ?>
          </div>
          <?php endif; ?>
        </footer>
      </blockquote>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-10 reveal">
      <a href="<?= BASE_URL ?>/temoignages" class="btn btn-outline">Lire tous les témoignages →</a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ====================================================
     SECTION DERNIÈRES ACTUALITÉS
     ==================================================== -->
<?php if (!empty($articles)): ?>
<section class="section" aria-labelledby="news-title">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-eyebrow">Actualités</span>
      <h2 class="section-title" id="news-title">Dernières nouvelles</h2>
      <div class="divider-gold"></div>
    </div>
    <div class="blog-grid">
      <?php foreach ($articles as $i => $a): ?>
      <article class="article-card <?= $i === 0 ? 'article-featured' : '' ?> reveal delay-<?= $i*100 ?>">
        <div style="overflow:hidden;<?= $i===0 ? 'height:320px' : 'height:200px' ?>">
          <?php if ($a['image_principale']): ?>
          <img class="article-card-img" src="<?= UPLOAD_URL ?>/articles/<?= Security::e($a['image_principale']) ?>" alt="<?= Security::e($a['titre']) ?>" loading="lazy" style="height:100%;width:100%">
          <?php else: ?>
          <div class="article-card-img" style="height:100%;background:linear-gradient(135deg,var(--color-blue-mid),var(--color-blue-deep));display:flex;align-items:center;justify-content:center;font-size:3rem">📰</div>
          <?php endif; ?>
        </div>
        <div class="article-card-body">
          <?php if ($a['categorie_nom']): ?>
          <span class="article-category" style="color:<?= Security::e($a['categorie_couleur'] ?? 'var(--color-blue-mid)') ?>"><?= Security::e($a['categorie_nom']) ?></span>
          <?php endif; ?>
          <h3 class="article-title"><a href="<?= BASE_URL ?>/actualites/<?= Security::e($a['slug']) ?>" style="color:inherit;text-decoration:none"><?= Security::e($a['titre']) ?></a></h3>
          <?php if ($i === 0): ?>
          <p class="article-excerpt"><?= Security::e(Helpers::truncate($a['extrait'] ?? $a['contenu'], 180)) ?></p>
          <?php endif; ?>
          <div class="article-meta">
            <div class="article-author-avatar"><?= mb_strtoupper(mb_substr($a['auteur_nom'] ?? 'E', 0, 1)) ?></div>
            <span><?= Security::e($a['auteur_nom'] ?? 'EuroCare') ?></span>
            <span>·</span>
            <span><?= Helpers::formatDate($a['publie_le'] ?? $a['cree_le'], false, true) ?></span>
            <?php if ($a['temps_lecture']): ?>
            <span>· <?= (int)$a['temps_lecture'] ?> min</span>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/actualites/<?= Security::e($a['slug']) ?>" class="article-read-more">
              Lire <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-10 reveal">
      <a href="<?= BASE_URL ?>/actualites" class="btn btn-outline">Toutes les actualités →</a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ====================================================
     SECTION PARTENAIRES
     ==================================================== -->
<?php if (!empty($partenaires)): ?>
<section class="section-sm bg-gray-50" aria-labelledby="partners-title">
  <div class="container">
    <div class="section-header reveal" style="margin-bottom:var(--space-10)">
      <span class="section-eyebrow">Ils nous font confiance</span>
      <h2 class="section-title" id="partners-title">Nos partenaires</h2>
      <div class="divider-gold"></div>
    </div>
    <div class="partners-logos reveal">
      <?php foreach ($partenaires as $p): ?>
      <div class="partner-logo-item" title="<?= Security::e($p['nom_organisation']) ?>">
        <?php if ($p['logo']): ?>
        <img src="<?= UPLOAD_URL ?>/partenaires/<?= Security::e($p['logo']) ?>" alt="<?= Security::e($p['nom_organisation']) ?>" loading="lazy">
        <?php else: ?>
        <span class="partner-logo-placeholder"><?= Security::e($p['nom_organisation']) ?></span>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ====================================================
     SECTION NEWSLETTER
     ==================================================== -->
<section class="newsletter-section">
  <div class="container">
    <div class="newsletter-inner">
      <div class="newsletter-text">
        <h3 class="newsletter-title">Restez informé de nos actions</h3>
        <p class="newsletter-sub">Recevez nos rapports d'activité, actualités et témoignages chaque mois.</p>
      </div>
      <form class="newsletter-form" data-newsletter action="<?= BASE_URL ?>/api/newsletter" method="POST" novalidate>
        <input type="hidden" name="_csrf_token" value="<?= Security::e(Session::generateCsrfToken()) ?>">
        <input type="email" name="email" class="form-control" placeholder="Votre adresse email..." required aria-label="Email pour la newsletter">
        <button type="submit" class="btn btn-primary">S'inscrire gratuitement</button>
      </form>
    </div>
  </div>
</section>

<!-- ====================================================
     SECTION CTA DON
     ==================================================== -->
<section class="cta-section">
  <div class="container">
    <div class="cta-content reveal">
      <h2 class="cta-title">Chaque euro compte.<br>Chaque geste change une vie.</h2>
      <p class="cta-subtitle">Rejoignez les <?= number_format($stats['donateurs_actifs'] ?: 850, 0, ',', ' ') ?> donateurs qui soutiennent déjà nos actions. Ensemble, nous pouvons faire la différence.</p>
      <div class="cta-actions">
        <a href="<?= BASE_URL ?>/faire-un-don" class="btn btn-gold btn-xl">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
          Faire un don maintenant
        </a>
        <a href="<?= BASE_URL ?>/inscription" class="btn btn-outline-white btn-xl">
          Créer un compte donateur
        </a>
      </div>
    </div>
  </div>
</section>

<!-- Bouton retour en haut -->
<button id="backToTop" aria-label="Retour en haut" style="position:fixed;bottom:var(--space-6);right:var(--space-6);width:3rem;height:3rem;background:var(--color-blue-mid);color:white;border:none;border-radius:var(--radius-full);display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:var(--shadow-xl);opacity:0;visibility:hidden;transition:all var(--transition-normal);z-index:var(--z-sticky)" class="no-print">
  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 15l-6-6-6 6"/></svg>
</button>
<style>
#backToTop.visible { opacity: 1; visibility: visible; }
#backToTop:hover   { background: var(--color-blue-deep); transform: translateY(-2px); }
</style>
