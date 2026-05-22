<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<div class="dashboard-page-header">
  <h1>Mon dossier social</h1>
  <p>Complétez votre dossier pour accélérer le traitement de votre demande</p>
</div>

<!-- Statut si dossier existant -->
<?php if ($dossier): ?>
<div style="background:<?= STATUTS_BENEF_COLORS[$dossier['statut_dossier']] ?>15;border:1px solid <?= STATUTS_BENEF_COLORS[$dossier['statut_dossier']] ?>40;border-radius:var(--radius-xl);padding:var(--space-4) var(--space-5);margin-bottom:var(--space-6);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:var(--space-3)">
  <div>
    <span style="font-weight:700;color:<?= STATUTS_BENEF_COLORS[$dossier['statut_dossier']] ?>">
      Statut : <?= Security::e(STATUTS_BENEF_LABELS[$dossier['statut_dossier']] ?? $dossier['statut_dossier']) ?>
    </span>
    <span style="font-size:var(--text-xs);color:var(--color-gray-500);margin-left:var(--space-3)">Dossier n° <?= Security::e($dossier['numero_dossier']) ?></span>
  </div>
  <div style="font-size:var(--text-xs);color:var(--color-gray-500)">
    Dernière mise à jour : <?= Helpers::formatDate($dossier['modifie_le'],false,true) ?>
  </div>
</div>
<?php if ($dossier['motif_refus']): ?>
<div class="alert alert-danger" style="margin-bottom:var(--space-5)">
  <span class="alert-icon">✕</span>
  <div><strong>Motif du refus :</strong> <?= Security::e($dossier['motif_refus']) ?></div>
</div>
<?php endif; ?>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 320px;gap:var(--space-6);align-items:start">

  <!-- Formulaire dossier -->
  <form method="POST" action="<?= BASE_URL ?>/beneficiaire/mon-dossier" data-validate novalidate>
    <input type="hidden" name="_csrf_token" value="<?= Security::e(Session::generateCsrfToken()) ?>">

    <div style="background:white;border-radius:var(--radius-xl);border:1px solid var(--color-gray-100);overflow:hidden;box-shadow:var(--shadow-xs)">
      <!-- Situation personnelle -->
      <div style="padding:var(--space-5) var(--space-6);border-bottom:1px solid var(--color-gray-100)">
        <h3 style="font-weight:700;font-size:var(--text-md);margin-bottom:var(--space-5)">📋 Votre situation</h3>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4)">
          <div class="form-group">
            <label class="form-label">Type de situation <span class="required">*</span></label>
            <select name="type_beneficiaire" class="form-control" required>
              <option value="">Choisir...</option>
              <?php foreach (TYPES_BENEFICIAIRE as $v=>$l): ?>
              <option value="<?= $v ?>" <?= ($dossier['type_beneficiaire']??'')===$v?'selected':'' ?>><?= Security::e($l) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Situation familiale</label>
            <select name="situation_familiale" class="form-control">
              <option value="">Choisir...</option>
              <?php foreach (['celibataire'=>'Célibataire','marie'=>'Marié(e)','divorce'=>'Divorcé(e)','veuf'=>'Veuf/Veuve','concubinage'=>'Concubinage'] as $v=>$l): ?>
              <option value="<?= $v ?>" <?= ($dossier['situation_familiale']??'')===$v?'selected':'' ?>><?= $l ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Nombre d'enfants à charge</label>
            <input type="number" name="nombre_enfants" class="form-control" min="0" max="20" value="<?= (int)($dossier['nombre_enfants']??0) ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Revenus mensuels (€)</label>
            <input type="number" name="revenus_mensuels" class="form-control" min="0" step="10" placeholder="0 si sans revenus" value="<?= htmlspecialchars($dossier['revenus_mensuels']??'') ?>">
          </div>
          <div class="form-group" style="grid-column:span 2">
            <label class="form-label">Situation de logement</label>
            <select name="situation_logement" class="form-control">
              <option value="">Choisir...</option>
              <?php foreach (['proprietaire'=>'Propriétaire','locataire'=>'Locataire','heberge'=>'Hébergé(e)','sans_abri'=>'Sans domicile fixe','autre'=>'Autre'] as $v=>$l): ?>
              <option value="<?= $v ?>" <?= ($dossier['situation_logement']??'')===$v?'selected':'' ?>><?= $l ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>

      <!-- Description -->
      <div style="padding:var(--space-5) var(--space-6);border-bottom:1px solid var(--color-gray-100)">
        <h3 style="font-weight:700;font-size:var(--text-md);margin-bottom:var(--space-4)">📝 Description de votre situation</h3>
        <div class="form-group">
          <label class="form-label">Besoins principaux <span class="required">*</span></label>
          <textarea name="besoins_principaux" class="form-control" rows="3" required minlength="20" placeholder="Décrivez vos besoins principaux (alimentation, logement, médical, scolaire...)" data-maxlength="2000"><?= Security::e($dossier['besoins_principaux']??'') ?></textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Description détaillée de votre situation <span class="required">*</span></label>
          <textarea name="description_situation" class="form-control" rows="5" required minlength="50" placeholder="Expliquez en détail votre situation actuelle, les événements qui ont conduit à cette situation..." data-maxlength="5000"><?= Security::e($dossier['description_situation']??'') ?></textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Niveau d'urgence estimé <span class="required">*</span></label>
          <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:var(--space-3)">
            <?php foreach (URGENCE_LABELS as $v=>$l): ?>
            <label style="cursor:pointer">
              <input type="radio" name="niveau_urgence" value="<?= $v ?>" <?= ($dossier['niveau_urgence']??'modere')===$v?'checked':'' ?> style="position:absolute;opacity:0;width:0;height:0" required>
              <div class="role-option-label" style="flex-direction:row;gap:var(--space-2);padding:var(--space-3)">
                <span style="width:12px;height:12px;border-radius:50%;background:<?= URGENCE_COLORS[$v] ?>;flex-shrink:0"></span>
                <span style="font-size:var(--text-xs);font-weight:600"><?= $l ?></span>
              </div>
            </label>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- Submit -->
      <div style="padding:var(--space-4) var(--space-6);background:var(--color-gray-50);display:flex;gap:var(--space-3)">
        <button type="submit" class="btn btn-primary">💾 Enregistrer le dossier</button>
        <a href="<?= BASE_URL ?>/beneficiaire/tableau-de-bord" class="btn btn-ghost">Retour</a>
      </div>
    </div>
  </form>

  <!-- Sidebar upload docs -->
  <div style="display:flex;flex-direction:column;gap:var(--space-4)">

    <!-- Upload document -->
    <div style="background:white;border-radius:var(--radius-xl);border:1px solid var(--color-gray-100);padding:var(--space-5);box-shadow:var(--shadow-xs)">
      <h4 style="font-weight:700;font-size:var(--text-sm);margin-bottom:var(--space-4)">📎 Joindre un document</h4>
      <form id="uploadDocForm">
        <div class="form-group">
          <label class="form-label">Type de document</label>
          <select id="typeDocument" class="form-control">
            <?php foreach (TYPES_DOCUMENT as $v=>$l): ?><option value="<?= $v ?>"><?= Security::e($l) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="drop-zone" id="docDropZone">
          <div class="drop-zone-icon">📄</div>
          <div class="drop-zone-text">Cliquez ou déposez un fichier</div>
          <div class="drop-zone-sub">PDF, JPG, PNG, DOC — max 10 Mo</div>
          <input type="file" id="docFile" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="display:none">
        </div>
        <button type="button" class="btn btn-outline btn-block btn-sm" style="margin-top:var(--space-3)" id="uploadDocBtn">📤 Envoyer</button>
      </form>
    </div>

    <!-- Liste documents -->
    <?php if (!empty($docs)): ?>
    <div style="background:white;border-radius:var(--radius-xl);border:1px solid var(--color-gray-100);padding:var(--space-5);box-shadow:var(--shadow-xs)">
      <h4 style="font-weight:700;font-size:var(--text-sm);margin-bottom:var(--space-4)">📁 Documents joints (<?= count($docs) ?>)</h4>
      <div class="document-list">
        <?php foreach ($docs as $doc):
          $icons=['application/pdf'=>'📕','image/jpeg'=>'🖼️','image/png'=>'🖼️'];
          $ico=$icons[$doc['mime_type']??'']??'📄';
          $statColors=['en_attente'=>'yellow','valide'=>'green','rejete'=>'red'];
        ?>
        <div class="document-item">
          <div class="document-icon"><?= $ico ?></div>
          <div style="flex:1;min-width:0">
            <div class="document-name truncate"><?= Security::e(Helpers::truncate($doc['nom_original'],30)) ?></div>
            <div class="document-meta"><?= Security::e(TYPES_DOCUMENT[$doc['type_document']]??$doc['type_document']) ?> · <?= Helpers::formatFileSize((int)$doc['taille']) ?></div>
          </div>
          <span class="badge badge-<?= $statColors[$doc['statut']]??'gray' ?>" style="font-size:10px;flex-shrink:0"><?= ucfirst($doc['statut']) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Aide -->
    <div style="background:var(--color-blue-pale);border:1px solid var(--color-blue-border);border-radius:var(--radius-xl);padding:var(--space-4)">
      <h4 style="font-weight:600;font-size:var(--text-sm);color:var(--color-blue-deep);margin-bottom:var(--space-2)">💡 Documents recommandés</h4>
      <ul style="list-style:disc;padding-left:var(--space-4);font-size:var(--text-xs);color:var(--color-gray-600);display:flex;flex-direction:column;gap:var(--space-1)">
        <li>Pièce d'identité</li><li>Justificatif de domicile</li><li>Justificatif de revenus</li>
        <li>Documents médicaux si nécessaire</li><li>Actes de naissance (enfants)</li>
      </ul>
    </div>
  </div>
</div>

<script>
// Upload AJAX document
document.getElementById('uploadDocBtn')?.addEventListener('click', async () => {
  const file    = document.getElementById('docFile').files[0];
  const type    = document.getElementById('typeDocument').value;
  const btn     = document.getElementById('uploadDocBtn');
  if (!file) { Toast.error('Veuillez sélectionner un fichier.'); return; }
  btn.disabled = true; btn.textContent = 'Envoi...';
  const fd = new FormData();
  fd.append('document', file);
  fd.append('type_document', type);
  fd.append('_csrf_token', CSRF_TOKEN);
  try {
    const res  = await fetch('<?= BASE_URL ?>/beneficiaire/document', { method:'POST', body:fd });
    const data = await res.json();
    if (data.success) { Toast.success('Document envoyé ! Il sera vérifié sous peu.'); location.reload(); }
    else Toast.error(data.message || 'Erreur lors de l\'envoi.');
  } catch { Toast.error('Erreur réseau.'); }
  finally { btn.disabled=false; btn.textContent='📤 Envoyer'; }
});
</script>
