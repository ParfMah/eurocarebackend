<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>

<section class="page-hero">
  <div class="container">
    <div class="page-hero-content reveal">
      <h1 class="page-hero-title">Actualités & Blog</h1>
      <p class="page-hero-subtitle">Suivez nos actions, témoignages et actualités humanitaires en temps réel.</p>
    </div>
  </div>
</section>

<div class="breadcrumb-section">
  <div class="container">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Accueil</a></li>
      <li class="breadcrumb-separator">›</li>
      <li class="breadcrumb-item active">Actualités</li>
    </ol>
  </div>
</div>

<section class="section">
  <div class="container">
    <div style="display:flex;gap:var(--space-10);align-items:flex-start">

      <!-- Articles -->
      <div style="flex:1;min-width:0">

        <!-- Filtres catégories -->
        <?php if (!empty($categories)): ?>
        <div style="display:flex;flex-wrap:wrap;gap:var(--space-2);margin-bottom:var(--space-8)">
          <a href="<?= BASE_URL ?>/actualites" class="btn btn-sm <?= !$catSlug ? 'btn-primary' : 'btn-outline' ?>">Toutes</a>
          <?php foreach ($categories as $cat): ?>
          <a href="<?= BASE_URL ?>/actualites/categorie/<?= Security::e($cat['slug']) ?>" class="btn btn-sm <?= $catSlug===$cat['slug'] ? 'btn-primary' : 'btn-outline' ?>" style="border-color:<?= Security::e($cat['couleur']) ?>;<?= $catSlug===$cat['slug'] ? 'background:'.$cat['couleur'].';border-color:'.$cat['couleur'] : 'color:'.$cat['couleur'] ?>">
            <?= Security::e($cat['nom']) ?>
          </a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($articles)): ?>
        <div class="blog-grid">
          <?php foreach ($articles as $i => $a): ?>
          <article class="article-card <?= $i===0?'article-featured':'' ?> reveal delay-<?= ($i%3)*100 ?>">
            <div style="overflow:hidden;height:<?= $i===0?'320':'200' ?>px">
              <?php if ($a['image_principale']): ?>
              <img class="article-card-img" src="<?= UPLOAD_URL ?>/articles/<?= Security::e($a['image_principale']) ?>" alt="<?= Security::e($a['titre']) ?>" loading="lazy" style="height:100%;width:100%;object-fit:cover">
              <?php else: ?>
              <div style="height:100%;background:linear-gradient(135deg,<?= Security::e($a['cat_color']??'#1a56db') ?>,#0d2b6e);display:flex;align-items:center;justify-content:center;font-size:3rem">📰</div>
              <?php endif; ?>
            </div>
            <div class="article-card-body">
              <?php if ($a['cat_nom']): ?>
              <span class="article-category" style="color:<?= Security::e($a['cat_color']??'var(--color-blue-mid)') ?>"><?= Security::e($a['cat_nom']) ?></span>
              <?php endif; ?>
              <h2 class="article-title">
                <a href="<?= BASE_URL ?>/actualites/<?= Security::e($a['slug']) ?>" style="color:inherit;text-decoration:none;hover:color:var(--color-blue-mid)"><?= Security::e($a['titre']) ?></a>
              </h2>
              <?php if ($i===0 || !empty($a['extrait'])): ?>
              <p class="article-excerpt"><?= Security::e(Helpers::truncate($a['extrait']??$a['contenu'],180)) ?></p>
              <?php endif; ?>
              <div class="article-meta">
                <div class="article-author-avatar"><?= mb_strtoupper(mb_substr($a['auteur_nom']??'E',0,1)) ?></div>
                <span><?= Security::e($a['auteur_nom']??'EuroCare') ?></span>
                <span>·</span>
                <span><?= Helpers::formatDate($a['publie_le']??$a['cree_le'],false,true) ?></span>
                <?php if ($a['temps_lecture']): ?><span>· <?= (int)$a['temps_lecture'] ?> min</span><?php endif; ?>
                <?php if ($a['vues']): ?><span>· 👁 <?= number_format((int)$a['vues'],0,',',' ') ?></span><?php endif; ?>
                <a href="<?= BASE_URL ?>/actualites/<?= Security::e($a['slug']) ?>" class="article-read-more">Lire <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
              </div>
            </div>
          </article>
          <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['pages'] > 1): ?>
        <?= Helpers::paginationHtml($pagination, BASE_URL.'/actualites') ?>
        <?php endif; ?>

        <?php else: ?>
        <div style="text-align:center;padding:var(--space-16);color:var(--color-gray-500)">
          <div style="font-size:3rem;margin-bottom:var(--space-4)">📰</div>
          <p>Aucun article publié pour le moment.</p>
        </div>
        <?php endif; ?>
      </div>

      <!-- Sidebar -->
      <aside style="width:280px;flex-shrink:0;display:flex;flex-direction:column;gap:var(--space-6)" class="reveal delay-300">

        <!-- Recherche -->
        <div style="background:white;border-radius:var(--radius-xl);padding:var(--space-5);box-shadow:var(--shadow-sm);border:1px solid var(--color-gray-100)">
          <h4 style="font-weight:600;margin-bottom:var(--space-3);font-size:var(--text-sm)">Rechercher</h4>
          <form action="<?= BASE_URL ?>/recherche" method="GET">
            <div style="display:flex;gap:var(--space-2)">
              <input type="text" name="q" class="form-control" placeholder="Mot clé..." style="flex:1">
              <button type="submit" class="btn btn-primary btn-sm" style="flex-shrink:0">🔍</button>
            </div>
          </form>
        </div>

        <!-- Catégories -->
        <?php if (!empty($categories)): ?>
        <div style="background:white;border-radius:var(--radius-xl);padding:var(--space-5);box-shadow:var(--shadow-sm);border:1px solid var(--color-gray-100)">
          <h4 style="font-weight:600;margin-bottom:var(--space-4);font-size:var(--text-sm)">Catégories</h4>
          <ul style="list-style:none;display:flex;flex-direction:column;gap:var(--space-2)">
            <?php foreach ($categories as $cat): ?>
            <li>
              <a href="<?= BASE_URL ?>/actualites/categorie/<?= Security::e($cat['slug']) ?>" style="display:flex;align-items:center;justify-content:space-between;font-size:var(--text-sm);color:var(--color-gray-700);text-decoration:none;padding:var(--space-2) var(--space-3);border-radius:var(--radius-lg);transition:all .15s" onmouseover="this.style.background='var(--color-blue-pale)';this.style.color='var(--color-blue-mid)'" onmouseout="this.style.background='';this.style.color='var(--color-gray-700)'">
                <span style="display:flex;align-items:center;gap:var(--space-2)">
                  <span style="width:8px;height:8px;border-radius:50%;background:<?= Security::e($cat['couleur']??'#1a56db') ?>;flex-shrink:0"></span>
                  <?= Security::e($cat['nom']) ?>
                </span>
              </a>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>

        <!-- CTA don -->
        <div style="background:linear-gradient(135deg,#1a56db,#0d2b6e);border-radius:var(--radius-xl);padding:var(--space-6);text-align:center;color:white">
          <div style="font-size:2.5rem;margin-bottom:var(--space-3)">💝</div>
          <h4 style="font-weight:700;margin-bottom:var(--space-2)">Soutenez nos actions</h4>
          <p style="font-size:var(--text-xs);color:rgba(255,255,255,.75);margin-bottom:var(--space-4)">Chaque don, aussi petit soit-il, change une vie.</p>
          <a href="<?= BASE_URL ?>/faire-un-don" class="btn btn-gold btn-block btn-sm">Faire un don →</a>
        </div>
      </aside>
    </div>
  </div>
</section>

<!-- Sur mobile, masquer la sidebar -->
<style>
@media (max-width:768px) {
  .container > div[style*="display:flex"] { flex-direction: column !important; }
  aside[style*="width:280px"] { width: 100% !important; }
}
</style>
