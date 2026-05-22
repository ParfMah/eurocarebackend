<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<div class="admin-page-header">
  <div class="admin-page-header-left"><h1><?= Security::e($msg['sujet']) ?></h1><p>De : <?= Security::e($msg['nom']) ?> — <?= Helpers::formatDate($msg['cree_le'],true,true) ?></p></div>
  <a href="<?= BASE_URL ?>/admin/messages" class="btn btn-ghost btn-sm">← Retour</a>
</div>
<div style="display:grid;grid-template-columns:1fr 280px;gap:var(--space-5);align-items:start">
  <div>
    <div class="admin-form-card">
      <div class="admin-form-section">
        <div style="background:var(--color-gray-50);border-radius:var(--radius-lg);padding:var(--space-5);margin-bottom:var(--space-5)">
          <p style="font-size:var(--text-sm);color:var(--color-gray-700);line-height:var(--line-relaxed);white-space:pre-wrap"><?= Security::e($msg['message']) ?></p>
        </div>
        <?php if ($msg['reponse']): ?>
        <div style="background:var(--color-blue-pale);border:1px solid var(--color-blue-border);border-radius:var(--radius-lg);padding:var(--space-5)">
          <div style="font-size:var(--text-xs);font-weight:600;color:var(--color-blue-mid);margin-bottom:var(--space-2)">✉️ Réponse envoyée le <?= Helpers::formatDate($msg['repondu_le'],true,true) ?></div>
          <p style="font-size:var(--text-sm);color:var(--color-gray-700)"><?= Security::e($msg['reponse']) ?></p>
        </div>
        <?php else: ?>
        <form method="POST" action="<?= BASE_URL ?>/admin/messages/<?= (int)$msg['id'] ?>/repondre" data-validate novalidate>
          <input type="hidden" name="_csrf_token" value="<?= Security::e(Session::generateCsrfToken()) ?>">
          <div class="form-group"><label class="form-label">Votre réponse <span class="required">*</span></label>
          <textarea name="reponse" class="form-control" rows="6" required minlength="10" placeholder="Rédigez votre réponse..."></textarea></div>
          <button type="submit" class="btn btn-primary">📤 Envoyer la réponse par email</button>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div style="background:white;border:1px solid var(--color-gray-100);border-radius:var(--radius-xl);padding:var(--space-5);box-shadow:var(--shadow-xs)">
    <h4 style="font-weight:700;font-size:var(--text-sm);margin-bottom:var(--space-4)">Informations</h4>
    <?php foreach ([['Nom',$msg['nom']],['Email',$msg['email']],['Téléphone',$msg['telephone']??'—'],['Sujet',$msg['sujet']],['Statut',ucfirst($msg['statut'])],['IP',$msg['ip']??'—'],['Date',Helpers::formatDate($msg['cree_le'],true,true)]] as [$l,$v]): ?>
    <div style="padding:var(--space-2) 0;border-bottom:1px solid var(--color-gray-50)">
      <div style="font-size:10px;text-transform:uppercase;color:var(--color-gray-400)"><?= $l ?></div>
      <div style="font-size:var(--text-sm);font-weight:500;margin-top:2px;word-break:break-all"><?= Security::e((string)$v) ?></div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
