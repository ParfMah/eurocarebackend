<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<div class="admin-page-header">
  <div class="admin-page-header-left"><h1>FAQ</h1><p>Gestion des questions fréquentes</p></div>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-5);align-items:start">
  <!-- Formulaire ajout/édition -->
  <div class="admin-form-card">
    <div class="admin-form-section">
      <h3 class="admin-form-section-title">Ajouter / Modifier une question</h3>
      <form method="POST" action="<?= BASE_URL ?>/admin/faq" data-validate novalidate>
        <input type="hidden" name="_csrf_token" value="<?= Security::e(Session::generateCsrfToken()) ?>">
        <input type="hidden" name="faq_id" id="editFaqId" value="">
        <div class="form-group">
          <label class="form-label">Question <span class="required">*</span></label>
          <input type="text" name="question" id="faqQuestion" class="form-control" required minlength="5">
        </div>
        <div class="form-group">
          <label class="form-label">Réponse <span class="required">*</span></label>
          <textarea name="reponse" id="faqReponse" class="form-control" rows="5" required minlength="5"></textarea>
        </div>
        <div class="admin-form-grid">
          <div class="form-group">
            <label class="form-label">Catégorie</label>
            <select name="categorie" id="faqCat" class="form-control">
              <?php foreach (['general'=>'Général','dons'=>'Dons','aide'=>'Aide','partenariat'=>'Partenariat','rgpd'=>'RGPD'] as $v=>$l): ?><option value="<?= $v ?>"><?= $l ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Ordre d'affichage</label>
            <input type="number" name="ordre" id="faqOrdre" class="form-control" min="0" value="0">
          </div>
        </div>
        <button type="submit" class="btn btn-primary">💾 Enregistrer</button>
      </form>
    </div>
  </div>
  <!-- Liste FAQ -->
  <div class="admin-widget">
    <div class="admin-widget-header"><div class="admin-widget-title">Questions existantes (<?= count($faqs??[]) ?>)</div></div>
    <div style="max-height:500px;overflow-y:auto">
      <?php if (!empty($faqs)): foreach ($faqs as $f): ?>
      <div style="padding:var(--space-3) var(--space-4);border-bottom:1px solid var(--color-gray-100);display:flex;align-items:flex-start;justify-content:space-between;gap:var(--space-3)">
        <div style="flex:1;min-width:0">
          <div style="font-weight:600;font-size:var(--text-sm)"><?= Security::e(Helpers::truncate($f['question'],55)) ?></div>
          <div style="font-size:11px;color:var(--color-gray-400)"><?= Security::e($f['categorie']) ?> · Ordre <?= $f['ordre'] ?></div>
        </div>
        <button class="btn btn-sm btn-outline" style="flex-shrink:0;font-size:11px" onclick="editFaq(<?= $f['id'] ?>,'<?= addslashes($f['question']) ?>','<?= addslashes($f['reponse']) ?>','<?= $f['categorie'] ?>',<?= $f['ordre'] ?>)">Éditer</button>
      </div>
      <?php endforeach; else: ?><div style="padding:var(--space-8);text-align:center;color:var(--color-gray-400)">Aucune question</div><?php endif; ?>
    </div>
  </div>
</div>
<script>
function editFaq(id,q,r,cat,ordre){
  document.getElementById('editFaqId').value=id;
  document.getElementById('faqQuestion').value=q;
  document.getElementById('faqReponse').value=r;
  document.getElementById('faqCat').value=cat;
  document.getElementById('faqOrdre').value=ordre;
  document.getElementById('faqQuestion').scrollIntoView({behavior:'smooth',block:'center'});
}
</script>
