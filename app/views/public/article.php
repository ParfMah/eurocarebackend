<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>

<div class="breadcrumb-section">
  <div class="container">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Accueil</a></li>
      <li class="breadcrumb-separator">›</li>
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/actualites">Actualités</a></li>
      <?php if ($article['cat_nom']): ?>
      <li class="breadcrumb-separator">›</li>
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/actualites/categorie/<?= Security::e($article['cat_slug']??'') ?>"><?= Security::e($article['cat_nom']) ?></a></li>
      <?php endif; ?>
      <li class="breadcrumb-separator">›</li>
      <li class="breadcrumb-item active"><?= Security::e(Helpers::truncate($article['titre'],40)) ?></li>
    </ol>
  </div>
</div>

<section class="section">
  <div class="container">
    <div style="display:flex;gap:var(--space-10);align-items:flex-start">

      <!-- Article principal -->
      <article style="flex:1;min-width:0">

        <!-- En-tête article -->
        <div style="margin-bottom:var(--space-8)">
          <?php if ($article['cat_nom']): ?>
          <span class="article-category" style="color:<?= Security::e($article['cat_color']??'var(--color-blue-mid)') ?>;font-size:var(--text-sm);font-weight:600;text-transform:uppercase;letter-spacing:.06em;display:inline-block;margin-bottom:var(--space-3)"><?= Security::e($article['cat_nom']) ?></span>
          <?php endif; ?>
          <h1 style="font-size:clamp(var(--text-2xl),4vw,var(--text-4xl));font-weight:800;line-height:1.15;letter-spacing:-.02em;color:var(--color-gray-900);margin-bottom:var(--space-5)"><?= Security::e($article['titre']) ?></h1>
          <div style="display:flex;align-items:center;flex-wrap:wrap;gap:var(--space-4);font-size:var(--text-sm);color:var(--color-gray-500);padding-bottom:var(--space-5);border-bottom:1px solid var(--color-gray-200)">
            <div style="display:flex;align-items:center;gap:var(--space-2)">
              <div style="width:2rem;height:2rem;border-radius:50%;background:var(--color-blue-pale);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:var(--text-xs);color:var(--color-blue-mid)"><?= mb_strtoupper(mb_substr($article['auteur_nom']??'E',0,1)) ?></div>
              <span><?= Security::e($article['auteur_nom']??'EuroCare') ?></span>
            </div>
            <span>📅 <?= Helpers::formatDate($article['publie_le']??$article['cree_le'],false,true) ?></span>
            <?php if ($article['temps_lecture']): ?><span>⏱ <?= (int)$article['temps_lecture'] ?> min de lecture</span><?php endif; ?>
            <span>👁 <?= number_format((int)$article['vues'],0,',',' ') ?> vue(s)</span>
            <?php if ($article['tags']): ?>
            <div style="display:flex;flex-wrap:wrap;gap:var(--space-1)">
              <?php foreach (explode(',',$article['tags']) as $tag): ?>
              <span style="background:var(--color-gray-100);color:var(--color-gray-600);font-size:11px;padding:2px 8px;border-radius:9999px"><?= Security::e(trim($tag)) ?></span>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Image principale -->
        <?php if ($article['image_principale']): ?>
        <div style="border-radius:var(--radius-2xl);overflow:hidden;margin-bottom:var(--space-8);box-shadow:var(--shadow-md)">
          <img src="<?= UPLOAD_URL ?>/articles/<?= Security::e($article['image_principale']) ?>" alt="<?= Security::e($article['titre']) ?>" style="width:100%;max-height:480px;object-fit:cover">
        </div>
        <?php endif; ?>

        <!-- Contenu -->
        <div style="font-size:var(--text-md);line-height:var(--line-loose);color:var(--color-gray-700);margin-bottom:var(--space-10)"
             class="article-content">
          <?= $article['contenu'] ?>
        </div>

        <!-- Partage -->
        <div style="padding:var(--space-5) var(--space-6);background:var(--color-gray-50);border-radius:var(--radius-xl);border:1px solid var(--color-gray-200);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:var(--space-3);margin-bottom:var(--space-10)">
          <span style="font-weight:600;font-size:var(--text-sm)">🔗 Partager cet article</span>
          <div style="display:flex;gap:var(--space-2)">
            <?php $url = urlencode(BASE_URL.'/actualites/'.Security::e($article['slug'])); $titre = urlencode($article['titre']); ?>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $url ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline">Facebook</a>
            <a href="https://twitter.com/intent/tweet?url=<?= $url ?>&text=<?= $titre ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline">X (Twitter)</a>
            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= $url ?>&title=<?= $titre ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline">LinkedIn</a>
            <button class="btn btn-sm btn-outline" data-copy="<?= BASE_URL ?>/actualites/<?= Security::e($article['slug']) ?>">📋 Copier</button>
          </div>
        </div>

        <!-- Commentaires -->
        <?php if ($article['commentaires_actifs']): ?>
        <div style="margin-bottom:var(--space-10)">
          <h3 style="font-size:var(--text-xl);font-weight:700;margin-bottom:var(--space-6)">
            💬 Commentaires (<?= count($commentaires) ?>)
          </h3>

          <?php if (!empty($commentaires)): ?>
          <div style="display:flex;flex-direction:column;gap:var(--space-4);margin-bottom:var(--space-8)">
            <?php foreach ($commentaires as $c): ?>
            <div style="background:var(--color-gray-50);border:1px solid var(--color-gray-100);border-radius:var(--radius-xl);padding:var(--space-5)">
              <div style="display:flex;align-items:center;gap:var(--space-3);margin-bottom:var(--space-3)">
                <div style="width:2.25rem;height:2.25rem;border-radius:50%;background:var(--color-blue-pale);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:var(--text-xs);color:var(--color-blue-mid)">
                  <?= mb_strtoupper(mb_substr($c['nom']??'?',0,1)) ?>
                </div>
                <div>
                  <div style="font-weight:600;font-size:var(--text-sm)"><?= Security::e($c['nom']??'Anonyme') ?></div>
                  <div style="font-size:var(--text-xs);color:var(--color-gray-500)"><?= Helpers::formatDate($c['cree_le'],true,true) ?></div>
                </div>
              </div>
              <p style="font-size:var(--text-sm);color:var(--color-gray-700);line-height:var(--line-relaxed)"><?= Security::e($c['contenu']) ?></p>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>

          <!-- Formulaire commentaire -->
          <div style="background:white;border:1px solid var(--color-gray-200);border-radius:var(--radius-2xl);padding:var(--space-6)">
            <h4 style="font-weight:600;margin-bottom:var(--space-5)">Laisser un commentaire</h4>
            <form method="POST" action="<?= BASE_URL ?>/actualites/<?= Security::e($article['slug']) ?>/commenter" data-validate novalidate>
              <input type="hidden" name="_csrf_token" value="<?= Security::e(Session::generateCsrfToken()) ?>">
              <?php if (!Auth::check()): ?>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-3)">
                <div class="form-group"><label class="form-label">Nom <span class="required">*</span></label><input type="text" name="auteur_nom" class="form-control" placeholder="Votre nom" required></div>
                <div class="form-group"><label class="form-label">Email</label><input type="email" name="auteur_email" class="form-control" placeholder="votre@email.com"></div>
              </div>
              <?php endif; ?>
              <div class="form-group"><label class="form-label">Commentaire <span class="required">*</span></label><textarea name="contenu" class="form-control" rows="4" required minlength="3" placeholder="Partagez votre réaction ou question..."></textarea></div>
              <button type="submit" class="btn btn-primary">Publier le commentaire</button>
              <p style="font-size:var(--text-xs);color:var(--color-gray-500);margin-top:var(--space-2)">Les commentaires sont modérés avant publication.</p>
            </form>
          </div>
        </div>
        <?php endif; ?>
      </article>

      <!-- Sidebar -->
      <aside style="width:280px;flex-shrink:0" class="reveal delay-200">
        <div style="background:var(--color-blue-pale);border:1px solid var(--color-blue-border);border-radius:var(--radius-xl);padding:var(--space-6);margin-bottom:var(--space-5)">
          <h4 style="font-weight:700;margin-bottom:var(--space-4);color:var(--color-blue-deep)">💝 Soutenez nos actions</h4>
          <p style="font-size:var(--text-sm);color:var(--color-gray-600);margin-bottom:var(--space-4);line-height:var(--line-relaxed)">Chaque don aide directement des familles en difficulté.</p>
          <a href="<?= BASE_URL ?>/faire-un-don" class="btn btn-primary btn-block">Faire un don</a>
        </div>
        <?php if (!empty($related)): ?>
        <div style="background:white;border:1px solid var(--color-gray-100);border-radius:var(--radius-xl);padding:var(--space-5)">
          <h4 style="font-weight:600;margin-bottom:var(--space-4);font-size:var(--text-sm)">Articles similaires</h4>
          <div style="display:flex;flex-direction:column;gap:var(--space-3)">
            <?php foreach ($related as $r): ?>
            <a href="<?= BASE_URL ?>/actualites/<?= Security::e($r['slug']) ?>" style="display:flex;gap:var(--space-3);text-decoration:none;color:inherit">
              <div style="width:3.5rem;height:3.5rem;border-radius:var(--radius-lg);background:var(--color-blue-pale);overflow:hidden;flex-shrink:0">
                <?php if ($r['image_principale']): ?><img src="<?= UPLOAD_URL ?>/articles/<?= Security::e($r['image_principale']) ?>" style="width:100%;height:100%;object-fit:cover"><?php else: ?><div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:1.5rem">📰</div><?php endif; ?>
              </div>
              <div>
                <div style="font-size:var(--text-xs);font-weight:600;color:var(--color-gray-900);line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden"><?= Security::e($r['titre']) ?></div>
                <div style="font-size:11px;color:var(--color-gray-400);margin-top:2px"><?= Helpers::formatDate($r['publie_le']??'') ?></div>
              </div>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
      </aside>
    </div>
  </div>
</section>

<style>
.article-content h2 { font-size:var(--text-2xl);font-weight:700;margin:var(--space-8) 0 var(--space-4);color:var(--color-gray-900); }
.article-content h3 { font-size:var(--text-xl);font-weight:700;margin:var(--space-6) 0 var(--space-3); }
.article-content p  { margin-bottom:var(--space-4); }
.article-content ul,.article-content ol { margin:var(--space-4) 0 var(--space-4) var(--space-6);list-style:disc; }
.article-content li { margin-bottom:var(--space-2); }
.article-content a  { color:var(--color-blue-mid); }
.article-content blockquote { border-left:4px solid var(--color-gold);padding-left:var(--space-5);margin:var(--space-6) 0;color:var(--color-gray-600);font-style:italic; }
.article-content img { border-radius:var(--radius-xl);margin:var(--space-6) 0;box-shadow:var(--shadow-md);max-width:100%; }
@media(max-width:768px){
  article+aside{display:none}
}
</style>
