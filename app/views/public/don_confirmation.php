<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<section class="section">
  <div class="container" style="max-width:600px">
    <div style="text-align:center;padding:var(--space-16) var(--space-8);background:white;border-radius:var(--radius-2xl);box-shadow:var(--shadow-xl);border:1px solid var(--color-gray-100)" class="reveal">
      <!-- Icône succès -->
      <div style="width:100px;height:100px;background:var(--color-success-bg);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto var(--space-6);border:3px solid #a7f3d0">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      </div>

      <h1 style="font-size:var(--text-2xl);font-weight:800;color:var(--color-gray-900);margin-bottom:var(--space-3)">Merci infiniment !</h1>
      <p style="color:var(--color-gray-600);line-height:var(--line-relaxed);margin-bottom:var(--space-6)">
        Votre don de <strong style="color:var(--color-blue-mid);font-size:1.1em"><?= Helpers::formatAmount((float)$don['montant']) ?></strong> a bien été enregistré et validé.
        <?php if (!$don['donateur_anonyme'] && ($don['email_anonyme'] ?? null)): ?>
        Un reçu fiscal vous a été envoyé à l'adresse <strong><?= Security::e($don['email_anonyme']) ?></strong>.
        <?php elseif (Auth::check()): ?>
        Un reçu fiscal est disponible dans votre espace donateur.
        <?php endif; ?>
      </p>

      <!-- Détails don -->
      <div style="background:var(--color-gray-50);border-radius:var(--radius-xl);padding:var(--space-5);margin-bottom:var(--space-6);text-align:left">
        <div style="display:flex;flex-direction:column;gap:var(--space-2)">
          <?php foreach ([
            ['Référence',      Security::e($don['uuid'])],
            ['Montant',        Helpers::formatAmount((float)$don['montant'])],
            ['Type',           STATUTS_DON_LABELS['valide']],
            ['Date',           Helpers::formatDate($don['valide_le']??$don['cree_le'],true,true)],
            ['Projet ciblé',   Security::e($don['projet_titre']??'Fonds général')],
          ] as [$lbl,$val]): ?>
          <div style="display:flex;justify-content:space-between;font-size:var(--text-sm);padding-bottom:var(--space-2);border-bottom:1px solid var(--color-gray-100)">
            <span style="color:var(--color-gray-500)"><?= $lbl ?></span>
            <span style="font-weight:500;text-align:right;max-width:60%;word-break:break-all"><?= $val ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Message déduction -->
      <div style="background:var(--color-success-bg);border:1px solid #a7f3d0;border-radius:var(--radius-lg);padding:var(--space-4);margin-bottom:var(--space-8)">
        <p style="font-size:var(--text-sm);color:#065f46;margin:0">
          💰 <strong>Déduction fiscale estimée : <?= Helpers::formatAmount((float)$don['montant'] * 0.66) ?></strong> (66% du montant du don)
        </p>
      </div>

      <!-- Actions -->
      <div style="display:flex;gap:var(--space-3);justify-content:center;flex-wrap:wrap">
        <a href="<?= BASE_URL ?>/" class="btn btn-primary">🏠 Retour à l'accueil</a>
        <a href="<?= BASE_URL ?>/transparence" class="btn btn-outline">📊 Voir l'impact</a>
        <?php if (Auth::check()): ?>
        <a href="<?= BASE_URL ?>/donateur/recus" class="btn btn-outline">📄 Mes reçus</a>
        <?php endif; ?>
      </div>

      <!-- Partager -->
      <div style="margin-top:var(--space-8);padding-top:var(--space-6);border-top:1px solid var(--color-gray-100)">
        <p style="font-size:var(--text-sm);color:var(--color-gray-600);margin-bottom:var(--space-3)">Partagez votre geste pour inspirer votre entourage 💝</p>
        <div style="display:flex;gap:var(--space-2);justify-content:center">
          <?php $msg = urlencode("Je viens de faire un don à EuroCare Humanitaire pour soutenir leurs actions. Rejoignez-moi ! ".BASE_URL); ?>
          <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(BASE_URL) ?>&quote=<?= $msg ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline">Facebook</a>
          <a href="https://twitter.com/intent/tweet?text=<?= $msg ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline">Twitter</a>
          <a href="https://wa.me/?text=<?= $msg ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline">WhatsApp</a>
        </div>
      </div>
    </div>
  </div>
</section>
