<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>

<section class="page-hero">
  <div class="container">
    <div class="page-hero-content reveal">
      <h1 class="page-hero-title">Nous contacter</h1>
      <p class="page-hero-subtitle">Notre équipe est disponible du lundi au vendredi pour répondre à toutes vos questions.</p>
    </div>
  </div>
</section>

<div class="breadcrumb-section">
  <div class="container">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Accueil</a></li>
      <li class="breadcrumb-separator">›</li>
      <li class="breadcrumb-item active">Contact</li>
    </ol>
  </div>
</div>

<section class="section">
  <div class="container">
    <div class="contact-grid">

      <!-- Infos contact -->
      <div class="reveal">
        <h2 style="font-size:var(--text-2xl);font-weight:700;margin-bottom:var(--space-6)">Parlons ensemble</h2>
        <p style="color:var(--color-gray-600);line-height:var(--line-relaxed);margin-bottom:var(--space-8)">Que vous souhaitiez faire un don, demander une aide, devenir partenaire ou simplement nous poser une question, nous sommes à votre écoute.</p>

        <div class="contact-info-list">
          <?php foreach ([
            ['📍','Adresse',Helpers::getSetting('site_adresse','12 Rue de la Solidarité, 75001 Paris')],
            ['📞','Téléphone',Helpers::getSetting('site_telephone','+33 1 23 45 67 89')],
            ['✉️','Email',Helpers::getSetting('site_email','contact@eurocare-humanitaire.eu')],
            ['🕐','Horaires',Helpers::getSetting('site_horaires','Lun–Ven : 9h00 – 18h00')],
          ] as $item): ?>
          <div class="contact-info-item">
            <div class="contact-info-icon"><span style="font-size:1.25rem"><?= $item[0] ?></span></div>
            <div>
              <div class="contact-info-label"><?= Security::e($item[1]) ?></div>
              <div class="contact-info-value"><?= Security::e($item[2]) ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- Liens rapides -->
        <div style="margin-top:var(--space-8);padding:var(--space-6);background:var(--color-blue-pale);border-radius:var(--radius-xl);border:1px solid var(--color-blue-border)">
          <h4 style="font-weight:600;margin-bottom:var(--space-4);color:var(--color-blue-deep)">Accès rapide</h4>
          <div style="display:flex;flex-direction:column;gap:var(--space-2)">
            <a href="<?= BASE_URL ?>/faire-un-don" style="font-size:var(--text-sm);color:var(--color-blue-mid);text-decoration:none;display:flex;align-items:center;gap:var(--space-2)">💝 Faire un don maintenant</a>
            <a href="<?= BASE_URL ?>/inscription?type=beneficiaire" style="font-size:var(--text-sm);color:var(--color-blue-mid);text-decoration:none;display:flex;align-items:center;gap:var(--space-2)">🤝 Demander une aide sociale</a>
            <a href="<?= BASE_URL ?>/inscription?type=partenaire" style="font-size:var(--text-sm);color:var(--color-blue-mid);text-decoration:none;display:flex;align-items:center;gap:var(--space-2)">🏛️ Devenir partenaire institutionnel</a>
            <a href="<?= BASE_URL ?>/faq" style="font-size:var(--text-sm);color:var(--color-blue-mid);text-decoration:none;display:flex;align-items:center;gap:var(--space-2)">❓ Consulter la FAQ</a>
          </div>
        </div>
      </div>

      <!-- Formulaire -->
      <div class="reveal delay-200">
        <div class="card">
          <div class="card-header">
            <h3 style="font-size:var(--text-lg);font-weight:700">Envoyer un message</h3>
          </div>
          <div class="card-body">
            <form method="POST" action="<?= BASE_URL ?>/contact" data-validate novalidate>
              <input type="hidden" name="_csrf_token" value="<?= Security::e(Session::generateCsrfToken()) ?>">

              <div class="form-group" style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-3)">
                <div>
                  <label class="form-label" for="nom">Nom complet <span class="required">*</span></label>
                  <input type="text" id="nom" name="nom" class="form-control" placeholder="Votre nom" required minlength="2" value="<?= Security::e($_POST['nom']??'') ?>">
                </div>
                <div>
                  <label class="form-label" for="telephone">Téléphone</label>
                  <input type="tel" id="telephone" name="telephone" class="form-control" placeholder="+33 6 00 00 00 00" value="<?= Security::e($_POST['telephone']??'') ?>">
                </div>
              </div>

              <div class="form-group">
                <label class="form-label" for="email">Email <span class="required">*</span></label>
                <input type="email" id="email" name="email" class="form-control" placeholder="votre@email.com" required value="<?= Security::e($_POST['email']??'') ?>">
              </div>

              <div class="form-group">
                <label class="form-label" for="sujet">Sujet <span class="required">*</span></label>
                <select id="sujet" name="sujet" class="form-control" required>
                  <option value="">Choisissez un sujet...</option>
                  <option value="Information générale">Information générale</option>
                  <option value="Demande d'aide">Demande d'aide sociale</option>
                  <option value="Don et financement">Don et financement</option>
                  <option value="Partenariat">Partenariat institutionnel</option>
                  <option value="Bénévolat">Bénévolat et engagement</option>
                  <option value="Presse et médias">Presse et médias</option>
                  <option value="Signalement">Signalement et réclamation</option>
                  <option value="Autre">Autre</option>
                </select>
              </div>

              <div class="form-group">
                <label class="form-label" for="message">Message <span class="required">*</span></label>
                <textarea id="message" name="message" class="form-control" rows="5" placeholder="Décrivez votre demande ou question..." required minlength="10" data-maxlength="2000"><?= Security::e($_POST['message']??'') ?></textarea>
              </div>

              <!-- Anti-honeypot -->
              <div style="position:absolute;left:-9999px;opacity:0" aria-hidden="true">
                <input type="text" name="website" tabindex="-1" autocomplete="off">
              </div>

              <button type="submit" class="btn btn-primary btn-block">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                Envoyer le message
              </button>

              <p style="font-size:var(--text-xs);color:var(--color-gray-500);text-align:center;margin-top:var(--space-3)">
                🔒 Vos données sont protégées conformément au RGPD. Réponse sous 24-48h ouvrées.
              </p>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FAQ rapide -->
<section class="section-sm bg-gray-50">
  <div class="container" style="max-width:700px">
    <div class="section-header reveal" style="margin-bottom:var(--space-8)">
      <h3 style="font-size:var(--text-xl);font-weight:700">Questions fréquentes</h3>
    </div>
    <div class="faq-accordion">
      <?php foreach ([
        ['Quel délai pour obtenir une aide ?','Les dossiers standard sont traités sous 5 à 15 jours ouvrables. Les situations critiques sont prises en charge sous 48h.'],
        ['Comment faire un don défiscalisé ?','Chaque don validé génère automatiquement un reçu fiscal téléchargeable depuis votre espace donateur. La déduction est de 66% de votre impôt.'],
        ['Puis-je visiter vos locaux ?','Oui, sur rendez-vous uniquement du lundi au vendredi. Contactez-nous au préalable pour organiser votre visite.'],
      ] as $i=>$q): ?>
      <div class="faq-item reveal delay-<?= $i*100 ?>">
        <div class="faq-question" role="button" tabindex="0" aria-expanded="false">
          <span><?= Security::e($q[0]) ?></span>
          <span class="faq-icon">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          </span>
        </div>
        <div class="faq-answer"><div class="faq-answer-inner"><?= Security::e($q[1]) ?></div></div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-8 reveal">
      <a href="<?= BASE_URL ?>/faq" class="btn btn-outline">Voir toutes les questions →</a>
    </div>
  </div>
</section>
