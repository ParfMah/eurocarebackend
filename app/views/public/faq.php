<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>

<section class="page-hero">
  <div class="container">
    <div class="page-hero-content reveal">
      <h1 class="page-hero-title">Foire aux questions</h1>
      <p class="page-hero-subtitle">Toutes les réponses à vos questions sur EuroCare Humanitaire, nos missions, les dons et les demandes d'aide.</p>
    </div>
  </div>
</section>

<div class="breadcrumb-section">
  <div class="container">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Accueil</a></li>
      <li class="breadcrumb-separator">›</li>
      <li class="breadcrumb-item active">FAQ</li>
    </ol>
  </div>
</div>

<section class="section">
  <div class="container" style="max-width:850px">

    <!-- Recherche FAQ -->
    <div class="reveal" style="margin-bottom:var(--space-10)">
      <div style="position:relative">
        <svg style="position:absolute;left:var(--space-4);top:50%;transform:translateY(-50%);color:var(--color-gray-400);pointer-events:none" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="faqSearch" class="form-control" placeholder="Rechercher dans la FAQ..." style="padding-left:3rem;font-size:var(--text-md);height:3.5rem;border-radius:var(--radius-xl)" oninput="filterFaq(this.value)">
      </div>
    </div>

    <?php if (!empty($faqParCat)): ?>

    <!-- Onglets catégories -->
    <div style="display:flex;flex-wrap:wrap;gap:var(--space-2);margin-bottom:var(--space-8)" class="reveal">
      <button class="btn btn-primary btn-sm" onclick="showCat('all',this)">Toutes</button>
      <?php foreach ($faqParCat as $cat => $items):
        $catLabel = $categories[$cat] ?? ucfirst($cat);
      ?>
      <button class="btn btn-outline btn-sm" onclick="showCat('<?= Security::e($cat) ?>',this)"><?= Security::e($catLabel) ?> (<?= count($items) ?>)</button>
      <?php endforeach; ?>
    </div>

    <!-- Accordéons par catégorie -->
    <?php foreach ($faqParCat as $cat => $items):
      $catLabel = $categories[$cat] ?? ucfirst($cat);
    ?>
    <div class="faq-category-block" data-cat="<?= Security::e($cat) ?>" style="margin-bottom:var(--space-8)">
      <h2 style="font-size:var(--text-lg);font-weight:700;color:var(--color-blue-deep);margin-bottom:var(--space-4);display:flex;align-items:center;gap:var(--space-2)">
        <span style="width:4px;height:1.25rem;background:var(--color-gold);border-radius:var(--radius-full);display:inline-block"></span>
        <?= Security::e($catLabel) ?>
        <span style="font-size:var(--text-sm);font-weight:400;color:var(--color-gray-400)">(<?= count($items) ?> question<?= count($items)>1?'s':'' ?>)</span>
      </h2>
      <div class="faq-accordion">
        <?php foreach ($items as $i => $faq): ?>
        <div class="faq-item reveal delay-<?= ($i%3)*100 ?>" data-question="<?= strtolower(Security::e($faq['question'])) ?>" data-answer="<?= strtolower(Security::e($faq['reponse'])) ?>">
          <div class="faq-question" role="button" tabindex="0" aria-expanded="false">
            <span><?= Security::e($faq['question']) ?></span>
            <span class="faq-icon">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </span>
          </div>
          <div class="faq-answer">
            <div class="faq-answer-inner"><?= Security::e($faq['reponse']) ?></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>

    <?php else: ?>
    <div class="text-center" style="padding:var(--space-16);color:var(--color-gray-500)">
      <div style="font-size:3rem;margin-bottom:var(--space-4)">❓</div>
      <p>La FAQ est en cours de rédaction. Revenez bientôt.</p>
    </div>
    <?php endif; ?>

    <!-- Contact si pas de réponse -->
    <div class="reveal" style="background:var(--color-blue-pale);border:1px solid var(--color-blue-border);border-radius:var(--radius-2xl);padding:var(--space-8);text-align:center;margin-top:var(--space-10)">
      <div style="font-size:2.5rem;margin-bottom:var(--space-3)">💬</div>
      <h3 style="font-size:var(--text-lg);font-weight:700;margin-bottom:var(--space-2)">Vous n'avez pas trouvé votre réponse ?</h3>
      <p style="color:var(--color-gray-600);margin-bottom:var(--space-5)">Notre équipe est là pour vous répondre dans les meilleurs délais.</p>
      <a href="<?= BASE_URL ?>/contact" class="btn btn-primary">Nous contacter directement →</a>
    </div>
  </div>
</section>

<script>
function filterFaq(q) {
  const query = q.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'');
  document.querySelectorAll('.faq-item').forEach(item => {
    const text = ((item.dataset.question||'') + ' ' + (item.dataset.answer||'')).normalize('NFD').replace(/[\u0300-\u036f]/g,'');
    item.style.display = text.includes(query) ? '' : 'none';
  });
  // Masquer les catégories vides
  document.querySelectorAll('.faq-category-block').forEach(block => {
    const visible = [...block.querySelectorAll('.faq-item')].some(i=>i.style.display!=='none');
    block.style.display = visible ? '' : 'none';
  });
}

function showCat(cat, btn) {
  document.querySelectorAll('.faq-category-block').forEach(b => {
    b.style.display = (cat === 'all' || b.dataset.cat === cat) ? '' : 'none';
  });
  document.querySelectorAll('[onclick^="showCat"]').forEach(b => b.classList.remove('btn-primary'));
  document.querySelectorAll('[onclick^="showCat"]').forEach(b => b.classList.add('btn-outline'));
  btn.classList.remove('btn-outline'); btn.classList.add('btn-primary');
}
</script>
