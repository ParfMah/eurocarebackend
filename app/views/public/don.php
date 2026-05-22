<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>

<section class="page-hero" style="padding:var(--space-12) 0">
  <div class="container">
    <div class="page-hero-content reveal" style="text-align:center">
      <span class="section-eyebrow" style="color:var(--color-gold-light);display:inline-block;margin-bottom:var(--space-3)">Agir maintenant</span>
      <h1 class="page-hero-title">Faire un don</h1>
      <p class="page-hero-subtitle">Chaque euro compte. 92% de vos dons sont redistribués directement aux bénéficiaires.</p>
      <div style="display:flex;justify-content:center;gap:var(--space-6);margin-top:var(--space-6);flex-wrap:wrap">
        <?php foreach (['🛡️ Sécurisé','📄 Reçu fiscal','✅ RGPD','🏆 Don en confiance'] as $b): ?>
        <span style="display:flex;align-items:center;gap:var(--space-2);font-size:var(--text-sm);color:rgba(255,255,255,.8)"><?= $b ?></span>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 1.2fr;gap:var(--space-12);align-items:start">

      <!-- Informations gauche -->
      <div class="reveal">
        <h2 style="font-size:var(--text-2xl);font-weight:700;margin-bottom:var(--space-6)">Pourquoi donner ?</h2>
        <div style="display:flex;flex-direction:column;gap:var(--space-4);margin-bottom:var(--space-8)">
          <?php foreach ([
            ['💰','92% redistribués directement','Seulement 8% couvrent les frais opérationnels. C\'est un des meilleurs taux du secteur.'],
            ['📊','Traçabilité totale','Chaque don est tracé et publié en temps réel dans notre espace transparence.'],
            ['📄','Reçu fiscal automatique','Déduction d\'impôts à 66%. Votre reçu vous est envoyé automatiquement par email.'],
            ['🔄','Don récurrent possible','Mettez en place un prélèvement mensuel ou annuel, résiliable à tout moment.'],
          ] as $r): ?>
          <div style="display:flex;gap:var(--space-4);align-items:flex-start">
            <div style="width:3rem;height:3rem;background:var(--color-blue-pale);border-radius:var(--radius-xl);display:flex;align-items:center;justify-content:center;font-size:1.25rem;flex-shrink:0"><?= $r[0] ?></div>
            <div>
              <div style="font-weight:600;margin-bottom:4px"><?= $r[1] ?></div>
              <div style="font-size:var(--text-sm);color:var(--color-gray-600);line-height:var(--line-relaxed)"><?= $r[2] ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- Compteur en temps réel -->
        <?php $gs = Helpers::getGlobalStats(); ?>
        <div style="background:linear-gradient(135deg,#0d2b6e,#1a56db);border-radius:var(--radius-2xl);padding:var(--space-6);color:white">
          <div style="font-size:var(--text-sm);color:rgba(255,255,255,.7);margin-bottom:var(--space-4)">📊 Impact en temps réel</div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4)">
            <div><div style="font-size:var(--text-2xl);font-weight:800"><?= number_format($gs['nombre_beneficiaires']?:1240,0,',',' ') ?></div><div style="font-size:var(--text-xs);color:rgba(255,255,255,.6)">Personnes aidées</div></div>
            <div><div style="font-size:var(--text-2xl);font-weight:800"><?= $gs['taux_redistribution']?:92 ?>%</div><div style="font-size:var(--text-xs);color:rgba(255,255,255,.6)">Redistribués</div></div>
            <div><div style="font-size:var(--text-2xl);font-weight:800"><?= $gs['projets_actifs']?:4 ?></div><div style="font-size:var(--text-xs);color:rgba(255,255,255,.6)">Projets actifs</div></div>
            <div><div style="font-size:var(--text-2xl);font-weight:800"><?= $gs['nombre_partenaires']?:48 ?>+</div><div style="font-size:var(--text-xs);color:rgba(255,255,255,.6)">Partenaires</div></div>
          </div>
        </div>
      </div>

      <!-- Formulaire don -->
      <div class="reveal delay-100">
        <div class="card" style="box-shadow:var(--shadow-xl)">
          <div class="card-header" style="background:linear-gradient(135deg,#f9fafb,white)">
            <h3 style="font-size:var(--text-lg);font-weight:700">Votre don</h3>
          </div>
          <div class="card-body">
            <form method="POST" action="<?= BASE_URL ?>/faire-un-don" data-validate novalidate id="donForm">
              <input type="hidden" name="_csrf_token" value="<?= Security::e(Session::generateCsrfToken()) ?>">

              <!-- Montants preset -->
              <div class="form-group">
                <label class="form-label">Choisissez un montant <span class="required">*</span></label>
                <div class="amount-presets">
                  <?php foreach ([10=>'Un repas',25=>'Aide d\'urgence',50=>'Matériel scolaire',100=>'Aide médicale',250=>'Aide logement',500=>'Aide complète'] as $amt=>$sub): ?>
                  <button type="button" class="amount-btn <?= $amt===25?'selected':'' ?>" data-amount="<?= $amt ?>">
                    <?= $amt ?>€<span class="sub"><?= $sub ?></span>
                  </button>
                  <?php endforeach; ?>
                </div>
                <div style="position:relative;margin-top:var(--space-3)">
                  <span style="position:absolute;left:var(--space-4);top:50%;transform:translateY(-50%);color:var(--color-gray-500);font-weight:600">€</span>
                  <input type="number" name="montant" id="montantInput" class="form-control" placeholder="Autre montant" min="1" step="1" required style="padding-left:2.5rem" value="25">
                </div>
              </div>

              <!-- Fréquence -->
              <div class="form-group">
                <label class="form-label">Fréquence du don</label>
                <div class="payment-methods">
                  <?php foreach (['ponctuel'=>['💳','Ponctuel','Une fois'],'mensuel'=>['🔄','Mensuel','Chaque mois'],'annuel'=>['📅','Annuel','Chaque année']] as $v=>[$ico,$lab,$sub]): ?>
                  <button type="button" class="payment-method-btn <?= $v==='ponctuel'?'active':'' ?>" style="flex-direction:column;gap:2px;padding:var(--space-3)" onclick="document.querySelectorAll('.payment-method-btn').forEach(b=>b.classList.remove('active'));this.classList.add('active');document.querySelector('[name=type]').value='<?= $v ?>'">
                    <span style="font-size:1.1rem"><?= $ico ?></span>
                    <span style="font-size:var(--text-xs);font-weight:600"><?= $lab ?></span>
                    <span style="font-size:10px;color:var(--color-gray-500)"><?= $sub ?></span>
                  </button>
                  <?php endforeach; ?>
                </div>
                <input type="hidden" name="type" value="ponctuel">
              </div>

              <!-- Cause -->
              <?php if (!empty($projets)): ?>
              <div class="form-group">
                <label class="form-label" for="projet_id">Flécher vers un projet (optionnel)</label>
                <select id="projet_id" name="projet_id" class="form-control">
                  <option value="">Fonds général — là où le besoin est le plus urgent</option>
                  <?php foreach ($projets as $pr): ?>
                  <option value="<?= (int)$pr['id'] ?>" <?= ($projet['id']??0)===(int)$pr['id']?'selected':'' ?>><?= Security::e($pr['titre']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <?php endif; ?>

              <!-- Anonymat -->
              <div class="form-group">
                <label class="form-check">
                  <input type="checkbox" class="form-check-input" name="anonyme" id="cbAnonyme" value="1">
                  <span class="form-check-label">Je souhaite faire un don anonyme (non publié)</span>
                </label>
              </div>

              <!-- Infos anonyme si non connecté -->
              <?php if (!Auth::check()): ?>
              <div id="anonFields" style="display:none;background:var(--color-gray-50);border-radius:var(--radius-lg);padding:var(--space-4);border:1px solid var(--color-gray-200)">
                <p style="font-size:var(--text-xs);color:var(--color-gray-600);margin-bottom:var(--space-3)">Pour recevoir votre reçu fiscal, renseignez votre email :</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-3)">
                  <div class="form-group" style="margin-bottom:0"><input type="text" name="prenom_anonyme" class="form-control" placeholder="Prénom (optionnel)"></div>
                  <div class="form-group" style="margin-bottom:0"><input type="email" name="email_anonyme" class="form-control" placeholder="Email pour le reçu"></div>
                </div>
              </div>
              <?php endif; ?>

              <!-- Message -->
              <div class="form-group">
                <label class="form-label">Message d'encouragement (optionnel)</label>
                <textarea name="message" class="form-control" rows="2" placeholder="Un mot pour l'équipe ou les bénéficiaires..."></textarea>
              </div>

              <!-- Résumé -->
              <div id="donSummary" style="background:var(--color-blue-pale);border:1px solid var(--color-blue-border);border-radius:var(--radius-xl);padding:var(--space-4);margin-bottom:var(--space-4)">
                <div style="display:flex;justify-content:space-between;align-items:center">
                  <span style="font-size:var(--text-sm);color:var(--color-gray-700)">Votre don :</span>
                  <span id="summaryAmount" style="font-size:var(--text-xl);font-weight:800;color:var(--color-blue-deep)">25 €</span>
                </div>
                <div style="font-size:var(--text-xs);color:var(--color-gray-500);margin-top:4px">
                  Déduction fiscale estimée : <strong id="summaryDeduction" style="color:var(--color-success)">16,50 €</strong>
                </div>
              </div>

              <button type="submit" class="btn btn-gold btn-block btn-xl">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                Confirmer mon don
              </button>

              <p style="text-align:center;font-size:var(--text-xs);color:var(--color-gray-500);margin-top:var(--space-3)">
                🔒 Paiement 100% sécurisé · Reçu fiscal par email · Résiliable à tout moment
              </p>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
// Mise à jour du résumé en temps réel
const montantInput = document.getElementById('montantInput');
const summaryAmount = document.getElementById('summaryAmount');
const summaryDeduction = document.getElementById('summaryDeduction');
const cbAnonyme = document.getElementById('cbAnonyme');
const anonFields = document.getElementById('anonFields');

function updateSummary() {
  const v = parseFloat(montantInput?.value) || 0;
  if (summaryAmount) summaryAmount.textContent = v.toLocaleString('fr-FR', {minimumFractionDigits:0}) + ' €';
  if (summaryDeduction) summaryDeduction.textContent = (v * 0.66).toLocaleString('fr-FR', {minimumFractionDigits:2}) + ' €';
}

montantInput?.addEventListener('input', updateSummary);
document.querySelectorAll('.amount-btn').forEach(btn => {
  btn.addEventListener('click', updateSummary);
});

cbAnonyme?.addEventListener('change', () => {
  if (anonFields) anonFields.style.display = cbAnonyme.checked ? 'block' : 'none';
});
</script>

<style>
@media (max-width:768px) {
  .container > div[style*="grid-template-columns:1fr 1.2fr"] {
    grid-template-columns: 1fr !important;
  }
}
</style>
