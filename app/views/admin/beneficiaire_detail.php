<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<div class="admin-page-header">
  <div class="admin-page-header-left">
    <h1>Dossier — <?= Security::e($benef['numero_dossier']) ?></h1>
    <p><?= Security::e($benef['prenom'].' '.$benef['nom']) ?> · <?= Security::e($benef['email']) ?></p>
  </div>
  <div style="display:flex;gap:var(--space-2)">
    <a href="<?= BASE_URL ?>/admin/beneficiaires" class="btn btn-ghost btn-sm">← Retour</a>
    <span class="badge" style="font-size:var(--text-sm);padding:var(--space-2) var(--space-4);background:<?= STATUTS_BENEF_COLORS[$benef['statut_dossier']] ?>20;color:<?= STATUTS_BENEF_COLORS[$benef['statut_dossier']] ?>"><?= Security::e(STATUTS_BENEF_LABELS[$benef['statut_dossier']]??'') ?></span>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 340px;gap:var(--space-5);align-items:start">

  <!-- Infos dossier -->
  <div style="display:flex;flex-direction:column;gap:var(--space-5)">

    <!-- Infos personnelles -->
    <div class="admin-widget">
      <div class="admin-widget-header"><div class="admin-widget-title">👤 Informations personnelles</div></div>
      <div class="admin-widget-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-3)">
          <?php foreach ([
            ['Prénom',  $benef['prenom']],['Nom',$benef['nom']],['Email',$benef['email']],
            ['Téléphone',$benef['telephone']??'—'],['Pays',$benef['pays']??'—'],['Ville',$benef['ville']??'—'],
            ['Type',(TYPES_BENEFICIAIRE[$benef['type_beneficiaire']]??$benef['type_beneficiaire'])],
            ['Situation familiale',ucfirst($benef['situation_familiale']??'—')],
            ['Enfants à charge',(int)$benef['nombre_enfants']],
            ['Revenus mensuels',$benef['revenus_mensuels']?Helpers::formatAmount((float)$benef['revenus_mensuels']):'—'],
            ['Logement',ucfirst(str_replace('_',' ',$benef['situation_logement']??'—'))],
            ['Inscription',Helpers::formatDate($benef['user_cree_le'],false,true)],
          ] as [$l,$v]): ?>
          <div style="padding:var(--space-2) 0;border-bottom:1px solid var(--color-gray-50)">
            <div style="font-size:10px;text-transform:uppercase;letter-spacing:.05em;color:var(--color-gray-400)"><?= $l ?></div>
            <div style="font-size:var(--text-sm);font-weight:500;margin-top:2px"><?= Security::e((string)$v) ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Description situation -->
    <div class="admin-widget">
      <div class="admin-widget-header"><div class="admin-widget-title">📋 Description de la situation</div></div>
      <div class="admin-widget-body">
        <div style="margin-bottom:var(--space-4)">
          <div style="font-size:var(--text-xs);text-transform:uppercase;color:var(--color-gray-400);margin-bottom:var(--space-2)">Besoins principaux</div>
          <p style="font-size:var(--text-sm);color:var(--color-gray-700);line-height:var(--line-relaxed)"><?= nl2br(Security::e($benef['besoins_principaux']??'')) ?></p>
        </div>
        <div>
          <div style="font-size:var(--text-xs);text-transform:uppercase;color:var(--color-gray-400);margin-bottom:var(--space-2)">Description détaillée</div>
          <p style="font-size:var(--text-sm);color:var(--color-gray-700);line-height:var(--line-relaxed)"><?= nl2br(Security::e($benef['description_situation']??'')) ?></p>
        </div>
      </div>
    </div>

    <!-- Documents -->
    <?php if (!empty($documents)): ?>
    <div class="admin-widget">
      <div class="admin-widget-header"><div class="admin-widget-title">📎 Documents (<?= count($documents) ?>)</div></div>
      <div class="admin-widget-body" style="padding:0">
        <div class="document-list" style="padding:var(--space-4)">
          <?php foreach ($documents as $d):
            $docIcos=['application/pdf'=>'📕','image/jpeg'=>'🖼️','image/png'=>'🖼️'];
          ?>
          <div class="document-item">
            <div class="document-icon"><?= $docIcos[$d['mime_type']??'']??'📄' ?></div>
            <div style="flex:1;min-width:0">
              <div class="document-name truncate"><?= Security::e(Helpers::truncate($d['nom_original'],35)) ?></div>
              <div class="document-meta"><?= Security::e(TYPES_DOCUMENT[$d['type_document']]??$d['type_document']) ?> · <?= Helpers::formatFileSize((int)$d['taille']) ?></div>
            </div>
            <span class="badge badge-<?= $d['statut']==='valide'?'green':($d['statut']==='rejete'?'red':'yellow') ?>" style="font-size:10px"><?= ucfirst($d['statut']) ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Aides accordées -->
    <?php if (!empty($aides)): ?>
    <div class="admin-widget">
      <div class="admin-widget-header"><div class="admin-widget-title">🎁 Aides accordées (<?= count($aides) ?>)</div></div>
      <div class="table-wrapper" style="border:none;box-shadow:none;border-radius:0">
        <table class="admin-data-table">
          <thead><tr><th>Type</th><th>Montant</th><th>Date</th><th>Par</th><th>Statut</th></tr></thead>
          <tbody>
            <?php foreach ($aides as $a): ?>
            <tr>
              <td><?= Security::e(TYPES_AIDE[$a['type_aide']]??$a['type_aide']) ?></td>
              <td><?= $a['montant']?Helpers::formatAmount((float)$a['montant']):'—' ?></td>
              <td style="color:var(--color-gray-500)"><?= Helpers::formatDate($a['date_attribution']) ?></td>
              <td style="font-size:var(--text-xs)"><?= Security::e($a['accordeur_nom']??'') ?></td>
              <td><span class="badge badge-<?= $a['statut']==='complete'?'green':($a['statut']==='approuve'?'blue':'yellow') ?>"><?= ucfirst($a['statut']) ?></span></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>

    <!-- Recommandations partenaires -->
    <?php if (!empty($recommandations)): ?>
    <div class="admin-widget">
      <div class="admin-widget-header"><div class="admin-widget-title">🏛️ Recommandations partenaires (<?= count($recommandations) ?>)</div></div>
      <div class="admin-widget-body">
        <?php foreach ($recommandations as $r): ?>
        <div style="padding:var(--space-3) 0;border-bottom:1px solid var(--color-gray-100)">
          <div style="display:flex;justify-content:space-between;margin-bottom:var(--space-1)">
            <strong style="font-size:var(--text-sm)"><?= Security::e($r['nom_organisation']) ?></strong>
            <span class="badge" style="background:<?= URGENCE_COLORS[$r['niveau_urgence']] ?>20;color:<?= URGENCE_COLORS[$r['niveau_urgence']] ?>;font-size:10px"><?= Security::e(URGENCE_LABELS[$r['niveau_urgence']]??'') ?></span>
          </div>
          <p style="font-size:var(--text-xs);color:var(--color-gray-600)"><?= Security::e(Helpers::truncate($r['recommandation'],120)) ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <!-- Sidebar actions -->
  <div style="display:flex;flex-direction:column;gap:var(--space-4);position:sticky;top:80px">

    <!-- Changer statut dossier -->
    <div class="admin-widget">
      <div class="admin-widget-header"><div class="admin-widget-title">⚙️ Gérer le dossier</div></div>
      <div class="admin-widget-body">
        <form id="statutForm">
          <div class="form-group">
            <label class="form-label">Nouveau statut</label>
            <select id="nouveauStatut" name="statut_dossier" class="form-control">
              <?php foreach (STATUTS_BENEF_LABELS as $v=>$l): ?>
              <option value="<?= $v ?>" <?= $benef['statut_dossier']===$v?'selected':'' ?>><?= Security::e($l) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Note interne</label>
            <textarea class="form-control" rows="3" id="noteInterne" placeholder="Commentaire visible uniquement par l'équipe..."><?= Security::e($benef['note_interne']??'') ?></textarea>
          </div>
          <button type="button" class="btn btn-primary btn-block"
            data-action="changer-statut"
            data-url="<?= BASE_URL ?>/admin/beneficiaires/<?= (int)$benef['id'] ?>/statut"
            onclick="changerStatutDossier()"
          >💾 Mettre à jour</button>
        </form>
      </div>
    </div>

    <!-- Accorder une aide -->
    <div class="admin-widget">
      <div class="admin-widget-header"><div class="admin-widget-title">🎁 Accorder une aide</div></div>
      <div class="admin-widget-body">
        <form method="POST" action="<?= BASE_URL ?>/admin/beneficiaires/<?= (int)$benef['id'] ?>/aide" data-validate novalidate>
          <input type="hidden" name="_csrf_token" value="<?= Security::e(Session::generateCsrfToken()) ?>">
          <div class="form-group">
            <label class="form-label">Type d'aide <span class="required">*</span></label>
            <select name="type_aide" class="form-control" required>
              <option value="">Choisir...</option>
              <?php foreach (TYPES_AIDE as $v=>$l): ?><option value="<?= $v ?>"><?= Security::e($l) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Montant (€)</label>
            <input type="number" name="montant" class="form-control" min="0" step="0.01" placeholder="0 si aide en nature">
          </div>
          <div class="form-group">
            <label class="form-label">Description <span class="required">*</span></label>
            <textarea name="description" class="form-control" rows="3" required minlength="5" placeholder="Détails de l'aide accordée..."></textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Date d'attribution</label>
            <input type="date" name="date_attribution" class="form-control" value="<?= date('Y-m-d') ?>">
          </div>
          <button type="submit" class="btn btn-success btn-block">✅ Accorder l'aide</button>
        </form>
      </div>
    </div>

    <!-- Niveau d'urgence actuel -->
    <div style="background:<?= URGENCE_COLORS[$benef['niveau_urgence']] ?>15;border:1px solid <?= URGENCE_COLORS[$benef['niveau_urgence']] ?>40;border-radius:var(--radius-xl);padding:var(--space-4);text-align:center">
      <div style="font-size:var(--text-xs);color:var(--color-gray-500);text-transform:uppercase;letter-spacing:.05em">Urgence déclarée</div>
      <div style="font-size:var(--text-xl);font-weight:800;color:<?= URGENCE_COLORS[$benef['niveau_urgence']] ?>;margin-top:4px"><?= Security::e(URGENCE_LABELS[$benef['niveau_urgence']]??'') ?></div>
    </div>
  </div>
</div>

<script>
async function changerStatutDossier() {
  const statut = document.getElementById('nouveauStatut').value;
  const note   = document.getElementById('noteInterne').value;
  const fd = new FormData();
  fd.append('statut_dossier', statut);
  fd.append('note_interne', note);
  fd.append('_csrf_token', CSRF_TOKEN);
  try {
    const res  = await fetch('<?= BASE_URL ?>/admin/beneficiaires/<?= (int)$benef['id'] ?>/statut', { method:'POST', body:fd });
    const data = await res.json();
    if (data.success) { Toast.success(data.message); setTimeout(()=>location.reload(),900); }
    else Toast.error(data.message);
  } catch { Toast.error('Erreur réseau.'); }
}
</script>
