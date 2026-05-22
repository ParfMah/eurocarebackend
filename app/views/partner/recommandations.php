<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<div class="dashboard-page-header"><h1>Recommandations de bénéficiaires</h1><p>Soumettez et suivez vos recommandations</p></div>
<?php if (!$profil || $profil['statut']!=='valide'): ?>
<div class="alert alert-warning"><span class="alert-icon">⚠</span><div>Votre profil doit être <strong>validé</strong> pour soumettre des recommandations.</div></div>
<?php else: ?>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-6)">
  <div>
    <h3 style="font-weight:700;margin-bottom:var(--space-4)">Nouvelle recommandation</h3>
    <div style="background:white;border-radius:var(--radius-xl);border:1px solid var(--color-gray-100);padding:var(--space-5);box-shadow:var(--shadow-xs)">
      <form method="POST" action="<?= BASE_URL ?>/partenaire/recommandations" data-validate novalidate>
        <input type="hidden" name="_csrf_token" value="<?= Security::e(Session::generateCsrfToken()) ?>">
        <div class="form-group"><label class="form-label">N° de dossier <span class="required">*</span></label><input type="text" name="numero_dossier" class="form-control" placeholder="EC-2024-XXXXXX" required></div>
        <div class="form-group"><label class="form-label">Niveau d'urgence</label>
          <select name="niveau_urgence" class="form-control">
            <?php foreach (URGENCE_LABELS as $v=>$l): ?><option value="<?= $v ?>"><?= Security::e($l) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="form-group"><label class="form-label">Recommandation <span class="required">*</span></label><textarea name="recommandation" class="form-control" rows="5" required minlength="20" data-maxlength="3000" placeholder="Décrivez la situation de la personne et votre recommandation d'aide..."></textarea></div>
        <button type="submit" class="btn btn-primary btn-block">📤 Soumettre la recommandation</button>
      </form>
    </div>
  </div>
  <div>
    <h3 style="font-weight:700;margin-bottom:var(--space-4)">Recommandations soumises</h3>
    <?php if (!empty($recos)): ?>
    <div style="display:flex;flex-direction:column;gap:var(--space-3)">
      <?php foreach ($recos as $r): ?>
      <div style="background:white;border:1px solid var(--color-gray-100);border-radius:var(--radius-xl);padding:var(--space-4)">
        <div style="display:flex;justify-content:space-between;margin-bottom:var(--space-2)">
          <strong style="font-size:var(--text-sm)"><?= Security::e($r['benef_nom']) ?> — <?= Security::e($r['numero_dossier']) ?></strong>
          <span class="badge badge-<?= $r['statut']==='traitee'?'green':($r['statut']==='lue'?'blue':'yellow') ?>"><?= Security::e(ucfirst($r['statut'])) ?></span>
        </div>
        <p style="font-size:var(--text-xs);color:var(--color-gray-600)"><?= Security::e(Helpers::truncate($r['recommandation'],100)) ?></p>
        <div style="font-size:11px;color:var(--color-gray-400);margin-top:var(--space-2)"><?= Helpers::formatDate($r['cree_le'],false,true) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div style="text-align:center;padding:var(--space-8);background:white;border-radius:var(--radius-xl);border:1px solid var(--color-gray-100)"><div style="font-size:2.5rem;margin-bottom:var(--space-3)">📋</div><p style="color:var(--color-gray-500);font-size:var(--text-sm)">Aucune recommandation soumise.</p></div>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>
