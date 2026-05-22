<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<div class="dashboard-page-header"><h1>Messagerie</h1><p>Communiquez directement avec l'équipe sociale</p></div>

<!-- Nouveau message -->
<div style="background:white;border-radius:var(--radius-xl);border:1px solid var(--color-gray-100);padding:var(--space-5);margin-bottom:var(--space-6);box-shadow:var(--shadow-xs)">
  <h3 style="font-weight:700;font-size:var(--text-md);margin-bottom:var(--space-4)">✉️ Nouveau message</h3>
  <form method="POST" action="<?= BASE_URL ?>/beneficiaire/messages" data-validate novalidate>
    <input type="hidden" name="_csrf_token" value="<?= Security::e(Session::generateCsrfToken()) ?>">
    <div class="form-group">
      <label class="form-label">Sujet <span class="required">*</span></label>
      <input type="text" name="sujet" class="form-control" placeholder="Ex: Question sur mon dossier" required>
    </div>
    <div class="form-group">
      <label class="form-label">Message <span class="required">*</span></label>
      <textarea name="contenu" class="form-control" rows="4" required minlength="10" placeholder="Décrivez votre question ou demande..." data-maxlength="5000"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">📤 Envoyer le message</button>
  </form>
</div>

<!-- Messages reçus -->
<?php if (!empty($messages)): ?>
<h3 style="font-weight:700;font-size:var(--text-md);margin-bottom:var(--space-4)">📬 Messages reçus</h3>
<div style="display:flex;flex-direction:column;gap:var(--space-3)">
  <?php foreach ($messages as $m): ?>
  <div style="background:<?= !$m['lu']?'#fafbff':'white' ?>;border:1px solid <?= !$m['lu']?'var(--color-blue-border)':'var(--color-gray-100)' ?>;border-radius:var(--radius-xl);padding:var(--space-5);box-shadow:var(--shadow-xs)">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:var(--space-2);margin-bottom:var(--space-3)">
      <div>
        <div style="font-weight:700;font-size:var(--text-sm)"><?= Security::e($m['sujet']) ?></div>
        <div style="font-size:var(--text-xs);color:var(--color-gray-500)">De : <?= Security::e($m['exp_nom']??'Équipe EuroCare') ?> · <?= Helpers::formatDate($m['cree_le'],true,true) ?></div>
      </div>
      <?php if (!$m['lu']): ?><span class="badge badge-blue" style="font-size:10px">Nouveau</span><?php endif; ?>
    </div>
    <p style="font-size:var(--text-sm);color:var(--color-gray-700);line-height:var(--line-relaxed)"><?= nl2br(Security::e($m['contenu'])) ?></p>
  </div>
  <?php endforeach; ?>
</div>
<?php else: ?>
<div style="text-align:center;padding:var(--space-10);background:white;border-radius:var(--radius-xl);border:1px solid var(--color-gray-100)">
  <div style="font-size:3rem;margin-bottom:var(--space-3)">📭</div>
  <p style="color:var(--color-gray-500)">Aucun message pour le moment. N'hésitez pas à nous contacter.</p>
</div>
<?php endif; ?>
