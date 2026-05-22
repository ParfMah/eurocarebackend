<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<div class="dashboard-page-header"><h1>Espace partenaire</h1><p>Tableau de bord de votre organisation</p></div>
<?php if ($profil && $profil['statut']==='valide'): ?>
<div class="alert alert-success mb-6"><span class="alert-icon">✅</span><div>Votre organisation <strong><?= Security::e($profil['nom_organisation']) ?></strong> est validée et active.</div></div>
<?php elseif ($profil && $profil['statut']==='en_attente'): ?>
<div class="alert alert-warning" style="margin-bottom:var(--space-6)"><span class="alert-icon">⏳</span><div>Votre dossier est en cours de validation. Délai habituel : 2-5 jours ouvrables.</div></div>
<?php else: ?>
<div class="alert alert-info" style="margin-bottom:var(--space-6)"><span class="alert-icon">ℹ</span><div>Complétez votre <a href="<?= BASE_URL ?>/partenaire/mon-profil">profil organisation</a> pour soumettre des recommandations.</div></div>
<?php endif; ?>
<div style="display:flex;flex-wrap:wrap;gap:var(--space-3);margin-bottom:var(--space-6)">
  <a href="<?= BASE_URL ?>/partenaire/mon-profil"       class="btn btn-primary">🏛️ Mon profil</a>
  <a href="<?= BASE_URL ?>/partenaire/recommandations"  class="btn btn-outline">📋 Recommandations</a>
</div>
<?php if (!empty($recos)): ?>
<div style="background:white;border-radius:var(--radius-xl);border:1px solid var(--color-gray-100);overflow:hidden;box-shadow:var(--shadow-xs)">
  <div style="padding:var(--space-4) var(--space-5);border-bottom:1px solid var(--color-gray-100)"><h3 style="font-weight:700">Dernières recommandations</h3></div>
  <table class="table"><thead><tr><th>Bénéficiaire</th><th>Urgence</th><th>Statut</th><th>Date</th></tr></thead><tbody>
  <?php foreach ($recos as $r): ?><tr>
    <td><?= Security::e($r['benef_nom']) ?></td>
    <td><span class="badge" style="background:<?= URGENCE_COLORS[$r['niveau_urgence']] ?>20;color:<?= URGENCE_COLORS[$r['niveau_urgence']] ?>"><?= Security::e(URGENCE_LABELS[$r['niveau_urgence']]??'') ?></span></td>
    <td><span class="badge badge-<?= $r['statut']==='traitee'?'green':($r['statut']==='lue'?'blue':'yellow') ?>"><?= Security::e(ucfirst($r['statut'])) ?></span></td>
    <td style="color:var(--color-gray-500)"><?= Helpers::formatDate($r['cree_le']) ?></td>
  </tr><?php endforeach; ?>
  </tbody></table>
</div>
<?php endif; ?>
