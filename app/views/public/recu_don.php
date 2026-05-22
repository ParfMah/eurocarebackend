<?php
/**
 * app/views/public/recu_don.php
 * Page de reçu fiscal — rendu HTML imprimable
 * Accessible via /don/recu/{uuid}
 */
defined('BASEPATH') or die('Accès direct interdit.');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Reçu fiscal — <?= Security::e($siteName) ?></title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@700&display=swap');

    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      font-family: 'Inter', sans-serif;
      background: #f0f2f7;
      color: #1f2937;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    .recu-wrapper {
      max-width: 720px;
      margin: 32px auto;
      background: white;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 20px 60px rgba(0,0,0,.12);
    }

    /* En-tête */
    .recu-header {
      background: linear-gradient(135deg, #0d2b6e 0%, #1a56db 100%);
      padding: 36px 48px;
      color: white;
      position: relative;
      overflow: hidden;
    }

    .recu-header::after {
      content: '';
      position: absolute;
      right: -50px;
      top: -50px;
      width: 200px;
      height: 200px;
      border: 2px solid rgba(255,255,255,.08);
      border-radius: 50%;
    }

    .recu-logo {
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 20px;
    }

    .recu-logo-icon {
      width: 48px; height: 48px;
      background: rgba(255,255,255,.15);
      border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: 22px;
    }

    .recu-logo-name { font-size: 20px; font-weight: 800; }
    .recu-logo-sub  { font-size: 11px; color: rgba(255,255,255,.6); margin-top: 2px; }

    .recu-title-row {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
    }

    .recu-title   { font-family: 'Playfair Display', serif; font-size: 26px; color: white; }
    .recu-subtitle{ font-size: 13px; color: rgba(255,255,255,.7); margin-top: 4px; }

    .recu-number  {
      text-align: right;
      font-size: 12px;
      color: rgba(255,255,255,.65);
    }
    .recu-number strong { font-size: 16px; color: white; display: block; }

    /* Corps */
    .recu-body { padding: 40px 48px; }

    .recu-section-title {
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .1em;
      color: #9ca3af;
      margin-bottom: 12px;
      margin-top: 28px;
    }

    .recu-section-title:first-child { margin-top: 0; }

    .recu-table {
      width: 100%;
      border-collapse: collapse;
    }

    .recu-table td {
      padding: 10px 0;
      border-bottom: 1px solid #f3f4f6;
      font-size: 14px;
    }

    .recu-table td:first-child { color: #6b7280; width: 50%; }
    .recu-table td:last-child  { font-weight: 600; color: #111827; text-align: right; }

    .recu-table tr:last-child td { border-bottom: none; }

    /* Montant principal */
    .recu-amount-box {
      background: linear-gradient(135deg, #eff6ff, #dbeafe);
      border: 1px solid #bfdbfe;
      border-radius: 12px;
      padding: 24px 32px;
      text-align: center;
      margin: 24px 0;
    }

    .recu-amount-label { font-size: 12px; text-transform: uppercase; letter-spacing: .08em; color: #3b82f6; font-weight: 600; }
    .recu-amount-value { font-size: 42px; font-weight: 800; color: #0d2b6e; margin: 8px 0; letter-spacing: -0.03em; }
    .recu-amount-deduction {
      font-size: 14px;
      color: #059669;
      font-weight: 600;
    }

    /* Certification */
    .recu-certification {
      background: #ecfdf5;
      border: 1px solid #a7f3d0;
      border-radius: 12px;
      padding: 20px 24px;
      margin-top: 28px;
    }

    .recu-certification p {
      font-size: 13px;
      color: #065f46;
      line-height: 1.6;
      margin-bottom: 8px;
    }

    .recu-certification p:last-child { margin-bottom: 0; }

    /* Signature -->*/
    .recu-signature {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      margin-top: 36px;
      padding-top: 24px;
      border-top: 1px solid #e5e7eb;
    }

    .recu-stamp {
      width: 100px;
      height: 100px;
      border: 3px solid #1a56db;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 12px;
      color: #1a56db;
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .03em;
      line-height: 1.3;
    }

    .recu-sign-text { text-align: right; font-size: 12px; color: #6b7280; }
    .recu-sign-name { font-size: 16px; font-weight: 700; color: #111827; margin-top: 4px; }

    /* Pied de page -->*/
    .recu-footer {
      background: #f9fafb;
      border-top: 1px solid #e5e7eb;
      padding: 20px 48px;
      text-align: center;
      font-size: 11px;
      color: #9ca3af;
      line-height: 1.6;
    }

    /* Boutons actions -->*/
    .recu-actions {
      display: flex;
      justify-content: center;
      gap: 12px;
      padding: 24px 48px;
      background: white;
    }

    .btn-print {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 24px;
      background: #1a56db;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      font-family: inherit;
      transition: background .2s;
    }

    .btn-print:hover { background: #0d2b6e; }

    .btn-back {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 24px;
      background: transparent;
      color: #374151;
      border: 1.5px solid #d1d5db;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      font-family: inherit;
      text-decoration: none;
      transition: all .2s;
    }

    .btn-back:hover { border-color: #1a56db; color: #1a56db; }

    /* PRINT */
    @media print {
      body { background: white; }
      .recu-wrapper { box-shadow: none; border-radius: 0; margin: 0; max-width: 100%; }
      .recu-actions { display: none; }
    }

    @media (max-width: 640px) {
      .recu-header, .recu-body { padding: 24px; }
      .recu-amount-value { font-size: 32px; }
      .recu-title-row { flex-direction: column; gap: 12px; }
    }
  </style>
</head>
<body>

<!-- Boutons actions (hors impression) -->
<div class="recu-actions no-print" style="background:transparent;margin-top:16px">
  <button class="btn-print" onclick="window.print()">
    🖨️ Imprimer / Sauvegarder PDF
  </button>
  <a href="<?= BASE_URL ?>/tableau-de-bord" class="btn-back">← Retour</a>
</div>

<div class="recu-wrapper">

  <!-- En-tête -->
  <div class="recu-header">
    <div class="recu-logo">
      <div class="recu-logo-icon">🛡️</div>
      <div>
        <div class="recu-logo-name"><?= Security::e($siteName) ?></div>
        <div class="recu-logo-sub">Organisation Humanitaire Européenne</div>
      </div>
    </div>
    <div class="recu-title-row">
      <div>
        <div class="recu-title">Reçu de don</div>
        <div class="recu-subtitle">Document fiscal officiel — à conserver</div>
      </div>
      <div class="recu-number">
        Référence<br>
        <strong><?= Security::e(strtoupper(substr($don['uuid'],0,8))) ?></strong>
      </div>
    </div>
  </div>

  <!-- Corps -->
  <div class="recu-body">

    <!-- Montant mis en valeur -->
    <div class="recu-amount-box">
      <div class="recu-amount-label">Montant du don</div>
      <div class="recu-amount-value"><?= Helpers::formatAmount((float)$don['montant'], $don['devise'] ?? 'EUR') ?></div>
      <div class="recu-amount-deduction">
        💰 Déduction fiscale estimée : <?= Helpers::formatAmount((float)$don['montant'] * 0.66) ?>
        <span style="color:#059669;font-weight:400"> (66% du montant)</span>
      </div>
    </div>

    <!-- Informations du don -->
    <div class="recu-section-title">Détails du don</div>
    <table class="recu-table">
      <tr>
        <td>Référence complète</td>
        <td><code style="font-size:12px;background:#f3f4f6;padding:2px 6px;border-radius:4px"><?= Security::e($don['uuid']) ?></code></td>
      </tr>
      <tr>
        <td>Date du don</td>
        <td><?= Helpers::formatDate($don['valide_le'] ?? $don['cree_le'], true, true) ?></td>
      </tr>
      <tr>
        <td>Type</td>
        <td><?= Security::e(ucfirst($don['type'] ?? 'ponctuel')) ?></td>
      </tr>
      <tr>
        <td>Cause soutenue</td>
        <td><?= Security::e($don['projet_titre'] ?? $don['cause'] ?? 'Fonds général humanitaire') ?></td>
      </tr>
      <tr>
        <td>Mode de paiement</td>
        <td><?= Security::e($don['methode_paiement'] ?? 'Paiement en ligne sécurisé') ?></td>
      </tr>
    </table>

    <!-- Informations du donateur -->
    <div class="recu-section-title">Donateur</div>
    <table class="recu-table">
      <?php if (!$don['donateur_anonyme'] && !empty($donateur)): ?>
      <tr><td>Prénom et Nom</td><td><?= Security::e($donateur['prenom'] . ' ' . $donateur['nom']) ?></td></tr>
      <tr><td>Email</td><td><?= Security::e($donateur['email']) ?></td></tr>
      <?php if ($donateur['adresse']): ?><tr><td>Adresse</td><td><?= Security::e($donateur['adresse']) ?></td></tr><?php endif; ?>
      <?php if ($donateur['pays']): ?><tr><td>Pays</td><td><?= Security::e($donateur['pays']) ?></td></tr><?php endif; ?>
      <?php elseif (!empty($don['email_anonyme'])): ?>
      <tr><td>Email</td><td><?= Security::e($don['email_anonyme']) ?></td></tr>
      <tr><td>Don anonyme</td><td>Oui</td></tr>
      <?php else: ?>
      <tr><td colspan="2" style="color:#6b7280;text-align:center">Don effectué anonymement</td></tr>
      <?php endif; ?>
    </table>

    <!-- Informations de l'organisation -->
    <div class="recu-section-title">Organisation bénéficiaire</div>
    <table class="recu-table">
      <tr><td>Nom</td><td><?= Security::e($siteName) ?></td></tr>
      <tr><td>Adresse</td><td><?= Security::e(Helpers::getSetting('site_adresse', '')) ?></td></tr>
      <tr><td>Reconnaissance</td><td>Organisation reconnue d'utilité publique</td></tr>
      <tr><td>Année de fondation</td><td><?= Security::e(Helpers::getSetting('fondation_annee', '2010')) ?></td></tr>
    </table>

    <!-- Certification RGPD & déductibilité -->
    <div class="recu-certification">
      <p>✅ <strong>Déductibilité fiscale :</strong> Conformément aux articles 200 et 238 bis du Code Général des Impôts, ce don ouvre droit à une réduction d'impôt de <strong>66%</strong> du montant versé, dans la limite de 20% du revenu imposable.</p>
      <p>🛡️ <strong>Conformité RGPD :</strong> Vos données personnelles sont traitées dans le strict respect du Règlement Général sur la Protection des Données. Ce reçu est établi conformément à la législation en vigueur.</p>
      <p>📋 <strong>Conservation :</strong> Nous vous recommandons de conserver ce document pendant 3 ans minimum pour votre déclaration fiscale.</p>
    </div>

    <!-- Signature -->
    <div class="recu-signature">
      <div class="recu-stamp">
        <?= Security::e($siteName) ?><br>
        ✓<br>
        CERTIFIÉ<br>CONFORME
      </div>
      <div class="recu-sign-text">
        Émis automatiquement le <?= date('d/m/Y à H:i') ?><br>
        <div class="recu-sign-name"><?= Security::e($siteName) ?></div>
        <div style="font-size:11px;color:#9ca3af">Direction Générale</div>
      </div>
    </div>
  </div>

  <!-- Pied de page -->
  <div class="recu-footer">
    <p><?= Security::e($siteName) ?> · <?= Security::e(Helpers::getSetting('site_adresse', '')) ?></p>
    <p>
      📞 <?= Security::e(Helpers::getSetting('site_telephone', '')) ?> &nbsp;·&nbsp;
      ✉️ <?= Security::e(Helpers::getSetting('site_email', '')) ?> &nbsp;·&nbsp;
      🌐 <?= BASE_URL ?>
    </p>
    <p style="margin-top:8px">Ce reçu fiscal a été généré automatiquement et est valable sans signature manuscrite.</p>
  </div>
</div>

<script>
// Auto-print si paramètre ?print=1
if (new URLSearchParams(window.location.search).get('print') === '1') {
  window.addEventListener('load', () => setTimeout(() => window.print(), 500));
}
</script>

</body>
</html>
