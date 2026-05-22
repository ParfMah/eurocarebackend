<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<div class="dashboard-page-header"><h1>Mes reçus fiscaux</h1><p>Documents à joindre à votre déclaration d'impôts</p></div>
<div style="background:var(--color-info-bg);border:1px solid #bae6fd;border-radius:var(--radius-xl);padding:var(--space-4) var(--space-5);margin-bottom:var(--space-6)">
  <p style="font-size:var(--text-sm);color:#0c4a6e;margin:0">ℹ️ <strong>Rappel :</strong> Vos dons sont déductibles à 66% de votre impôt sur le revenu, dans la limite de 20% de votre revenu imposable.</p>
</div>
<?php if (!empty($dons)): ?>
<div style="display:flex;flex-direction:column;gap:var(--space-3)">
  <?php foreach (array_filter($dons, fn($d)=>$d['statut']==='valide') as $d): ?>
  <div style="background:white;border:1px solid var(--color-gray-100);border-radius:var(--radius-xl);padding:var(--space-4) var(--space-5);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:var(--space-3)">
    <div style="display:flex;align-items:center;gap:var(--space-4)">
      <div style="width:2.5rem;height:2.5rem;background:var(--color-blue-pale);border-radius:var(--radius-lg);display:flex;align-items:center;justify-content:center;font-size:1.25rem">📄</div>
      <div>
        <div style="font-weight:600;font-size:var(--text-sm)">Reçu — <?= Helpers::formatAmount((float)$d['montant']) ?></div>
        <div style="font-size:var(--text-xs);color:var(--color-gray-500)"><?= Helpers::formatDate($d['valide_le']??$d['cree_le'],false,true) ?> · <?= Security::e($d['uuid']) ?></div>
      </div>
    </div>
    <div style="display:flex;gap:var(--space-2)">
      <span class="badge badge-green">✅ Déductible</span>
      <a href="<?= BASE_URL ?>/don/recu/<?= Security::e($d['uuid']) ?>" class="btn btn-sm btn-outline">📥 Télécharger</a>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php else: ?>
<div style="text-align:center;padding:var(--space-12);background:white;border-radius:var(--radius-xl)"><div style="font-size:3rem;margin-bottom:var(--space-4)">📄</div><p style="color:var(--color-gray-500)">Aucun reçu disponible pour le moment.</p></div>
<?php endif; ?>
