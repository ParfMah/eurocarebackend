<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>

<section class="page-hero">
  <div class="container">
    <div class="page-hero-content reveal">
      <span class="section-eyebrow" style="color:var(--color-gold-light);display:inline-block;margin-bottom:var(--space-3)">Ils témoignent</span>
      <h1 class="page-hero-title">Histoires inspirantes</h1>
      <p class="page-hero-subtitle">Des vies transformées, des familles reconstruites, des espoirs renaissants. Découvrez les témoignages de ceux que nous avons eu la chance d'accompagner.</p>
    </div>
  </div>
</section>

<div class="breadcrumb-section">
  <div class="container">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Accueil</a></li>
      <li class="breadcrumb-separator">›</li>
      <li class="breadcrumb-item active">Témoignages</li>
    </ol>
  </div>
</div>

<section class="section">
  <div class="container">
    <?php if (!empty($temoignages)): ?>
    <div class="testimonials-grid">
      <?php foreach ($temoignages as $i => $t): ?>
      <blockquote class="testimonial-card reveal delay-<?= ($i%3)*100 ?>">
        <div class="testimonial-quote" aria-hidden="true">"</div>
        <div class="testimonial-stars" aria-label="Note : <?= (int)$t['note'] ?>/5">
          <?php for ($s = 0; $s < 5; $s++): ?>
          <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"
              <?= $s < $t['note'] ? 'fill="currentColor"' : 'fill="none" stroke="currentColor" stroke-width="1.5"' ?>/>
          </svg>
          <?php endfor; ?>
        </div>
        <p class="testimonial-text">"<?= Security::e($t['contenu']) ?>"</p>
        <footer class="testimonial-author">
          <div class="testimonial-avatar">
            <?php if ($t['photo']): ?>
              <img src="<?= UPLOAD_URL ?>/temoignages/<?= Security::e($t['photo']) ?>" alt="<?= Security::e($t['nom_affiche']) ?>">
            <?php else: ?>
              <?= mb_strtoupper(mb_substr($t['nom_affiche'],0,1)) ?>
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

    <!-- Pagination -->
    <?php if ($pagination['pages'] > 1): echo Helpers::paginationHtml($pagination, BASE_URL.'/temoignages'); endif; ?>

    <?php else: ?>
    <div style="text-align:center;padding:var(--space-16);color:var(--color-gray-500)">
      <div style="font-size:3rem;margin-bottom:var(--space-4)">💬</div>
      <p>Les témoignages seront publiés prochainement.</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- Partager son témoignage -->
<section class="section-sm bg-gray-50">
  <div class="container" style="max-width:700px">
    <div class="reveal" style="background:white;border-radius:var(--radius-2xl);padding:var(--space-10);box-shadow:var(--shadow-md);border:1px solid var(--color-gray-100);text-align:center">
      <div style="font-size:3rem;margin-bottom:var(--space-4)">✍️</div>
      <h3 style="font-size:var(--text-xl);font-weight:700;margin-bottom:var(--space-3)">Partagez votre expérience</h3>
      <p style="color:var(--color-gray-600);line-height:var(--line-relaxed);margin-bottom:var(--space-6)">Vous avez été aidé(e) par EuroCare ou vous avez soutenu nos actions en tant que donateur ? Votre témoignage peut inspirer et encourager d'autres personnes.</p>
      <?php if (Auth::check()): ?>
        <a href="<?= BASE_URL ?>/tableau-de-bord" class="btn btn-primary">Déposer mon témoignage</a>
      <?php else: ?>
        <div style="display:flex;gap:var(--space-3);justify-content:center;flex-wrap:wrap">
          <a href="<?= BASE_URL ?>/connexion" class="btn btn-primary">Se connecter pour témoigner</a>
          <a href="<?= BASE_URL ?>/inscription" class="btn btn-outline">Créer un compte</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="cta-section">
  <div class="container">
    <div class="cta-content reveal">
      <h2 class="cta-title">Votre tour d'agir</h2>
      <p class="cta-subtitle">Rejoignez des milliers de personnes qui font confiance à EuroCare pour changer des vies.</p>
      <div class="cta-actions">
        <a href="<?= BASE_URL ?>/faire-un-don" class="btn btn-gold btn-xl">💝 Faire un don</a>
        <a href="<?= BASE_URL ?>/a-propos"     class="btn btn-outline-white btn-xl">En savoir plus</a>
      </div>
    </div>
  </div>
</section>
