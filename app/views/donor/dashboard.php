<?php
/**
 * app/views/donor/dashboard.php
 * Utilise le layout dashboard injecté via Helpers::view avec layout 'dashboard'
 */
defined('BASEPATH') or die('Accès direct interdit.');
?>
<div class="dashboard-page-header">
  <h1>Bienvenue, <?= Security::e($user['prenom']) ?> 👋</h1>
  <p>Tableau de bord donateur — <?= date('d/m/Y') ?></p>
</div>

<div class="grid grid-cols-3 gap-5" style="margin-bottom:var(--space-6)">
  <div class="stat-card" style="--stat-color:#1a56db"><div class="stat-icon" style="background:#dbeafe;font-size:1.5rem">💝</div><div class="stat-value"><?= Helpers::formatAmount($totalDons) ?></div><div class="stat-label">Total des dons</div></div>
  <div class="stat-card" style="--stat-color:#059669"><div class="stat-icon" style="background:#d1fae5;font-size:1.5rem">📊</div><div class="stat-value"><?= (int)$nbDons ?></div><div class="stat-label">Dons effectués</div></div>
  <div class="stat-card" style="--stat-color:#d97706"><div class="stat-icon" style="background:#fef3c7;font-size:1.5rem">📄</div><div class="stat-value"><?= Helpers::formatAmount($totalDons * 0.66) ?></div><div class="stat-label">Économie fiscale estimée</div></div>
</div>

<div style="display:flex;flex-wrap:wrap;gap:var(--space-3);margin-bottom:var(--space-8)">
  <a href="<?= BASE_URL ?>/faire-un-don"      class="btn btn-gold">💝 Nouveau don</a>
  <a href="<?= BASE_URL ?>/donateur/mes-dons"  class="btn btn-outline">Historique</a>
  <a href="<?= BASE_URL ?>/donateur/recus"     class="btn btn-outline">📄 Reçus</a>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-5)">
  <div style="background:white;border-radius:var(--radius-xl);border:1px solid var(--color-gray-100);overflow:hidden;box-shadow:var(--shadow-xs)">
    <div style="padding:var(--space-4) var(--space-5);border-bottom:1px solid var(--color-gray-100);display:flex;justify-content:space-between">
      <h3 style="font-weight:700;font-size:var(--text-md)">Derniers dons</h3>
      <a href="<?= BASE_URL ?>/donateur/mes-dons" class="text-sm text-blue">Voir tout →</a>
    </div>
    <?php if (!empty($derniers)): ?>
    <table style="width:100%;font-size:var(--text-sm)">
      <thead style="background:var(--color-gray-50)"><tr>
        <th style="padding:var(--space-2) var(--space-4);text-align:left;font-size:11px;text-transform:uppercase;color:var(--color-gray-400)">Date</th>
        <th style="padding:var(--space-2) var(--space-4);text-align:left;font-size:11px;text-transform:uppercase;color:var(--color-gray-400)">Montant</th>
        <th style="padding:var(--space-2) var(--space-4);text-align:left;font-size:11px;text-transform:uppercase;color:var(--color-gray-400)">Cause</th>
      </tr></thead>
      <tbody>
        <?php foreach ($derniers as $d): ?>
        <tr style="border-top:1px solid var(--color-gray-50)">
          <td style="padding:var(--space-3) var(--space-4);color:var(--color-gray-500)"><?= Helpers::formatDate($d['cree_le']) ?></td>
          <td style="padding:var(--space-3) var(--space-4);font-weight:700;color:var(--color-blue-mid)"><?= Helpers::formatAmount((float)$d['montant']) ?></td>
          <td style="padding:var(--space-3) var(--space-4);color:var(--color-gray-600);max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= Security::e(Helpers::truncate($d['projet_titre']??$d['cause']??'Fonds général',20)) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
    <div style="padding:var(--space-8);text-align:center"><div style="font-size:2.5rem;margin-bottom:var(--space-3)">💝</div>
    <p style="font-size:var(--text-sm);color:var(--color-gray-500);margin-bottom:var(--space-3)">Aucun don pour le moment.</p>
    <a href="<?= BASE_URL ?>/faire-un-don" class="btn btn-gold btn-sm">Premier don</a></div>
    <?php endif; ?>
  </div>

  <div style="display:flex;flex-direction:column;gap:var(--space-4)">
    <div style="background:linear-gradient(135deg,#0d2b6e,#1a56db);border-radius:var(--radius-xl);padding:var(--space-6);color:white">
      <div style="font-size:var(--text-sm);color:rgba(255,255,255,.7);margin-bottom:var(--space-3)">🌍 Votre impact</div>
      <p style="font-size:var(--text-sm);color:rgba(255,255,255,.8);line-height:var(--line-relaxed)">Grâce à vos <strong><?= $nbDons ?></strong> don(s) pour <strong><?= Helpers::formatAmount($totalDons) ?></strong>, vous soutenez nos missions humanitaires.</p>
      <a href="<?= BASE_URL ?>/donateur/impact" style="display:inline-flex;align-items:center;gap:8px;margin-top:var(--space-4);color:var(--color-gold-light);font-size:var(--text-sm);font-weight:600;text-decoration:none">Voir l'impact →</a>
    </div>
    <div style="background:var(--color-success-bg);border:1px solid #a7f3d0;border-radius:var(--radius-xl);padding:var(--space-5)">
      <div style="font-weight:700;font-size:var(--text-sm);color:#065f46;margin-bottom:var(--space-2)">💰 Avantage fiscal</div>
      <div style="font-size:var(--text-2xl);font-weight:800;color:#059669"><?= Helpers::formatAmount($totalDons * 0.66) ?></div>
      <div style="font-size:var(--text-xs);color:#065f46;margin-top:2px">Déduction estimée à 66%</div>
      <a href="<?= BASE_URL ?>/donateur/recus" style="display:inline-block;margin-top:var(--space-2);font-size:var(--text-xs);color:var(--color-success);font-weight:600;text-decoration:none">Télécharger reçus →</a>
    </div>
  </div>
</div>
