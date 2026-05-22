<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>

<section class="page-hero">
  <div class="container">
    <div class="page-hero-content reveal">
      <span class="section-eyebrow" style="color:var(--color-gold-light);display:inline-block;margin-bottom:var(--space-3)">Vous avez besoin d'aide ?</span>
      <h1 class="page-hero-title">Demander une aide sociale</h1>
      <p class="page-hero-subtitle">Créez votre dossier en quelques minutes. Notre équipe sociale l'étudiera dans les plus brefs délais.</p>
    </div>
  </div>
</section>

<div class="breadcrumb-section">
  <div class="container">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Accueil</a></li>
      <li class="breadcrumb-separator">›</li>
      <li class="breadcrumb-item active">Demander une aide</li>
    </ol>
  </div>
</div>

<section class="section">
  <div class="container" style="max-width:900px">

    <!-- Étapes -->
    <div class="steps-grid" style="margin-bottom:var(--space-12)">
      <?php foreach ([
        ['1','📋','Créez votre compte','Inscrivez-vous gratuitement en tant que bénéficiaire en 2 minutes.'],
        ['2','📝','Remplissez votre dossier','Décrivez votre situation et joignez les documents justificatifs.'],
        ['3','🔍','Étude de votre dossier','Notre équipe sociale étudie votre demande sous 5-15 jours ouvrables.'],
        ['4','🎁','Aide accordée','Si validé, vous recevez l\'aide adaptée à votre situation.'],
      ] as $i=>$s): ?>
      <div class="step-card reveal delay-<?= $i*100 ?>">
        <div class="step-number-wrap"><span class="step-number"><?= $s[0] ?></span></div>
        <div style="font-size:2rem;margin-bottom:var(--space-2)"><?= $s[1] ?></div>
        <h3 class="step-title"><?= $s[2] ?></h3>
        <p class="step-desc"><?= $s[3] ?></p>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- CTA principal -->
    <div style="background:linear-gradient(135deg,#0d2b6e,#1a56db);border-radius:var(--radius-2xl);padding:var(--space-10);text-align:center;color:white;margin-bottom:var(--space-10)" class="reveal">
      <div style="font-size:3rem;margin-bottom:var(--space-4)">🤝</div>
      <h2 style="font-size:var(--text-2xl);font-weight:800;color:white;margin-bottom:var(--space-3)">Commencer ma demande d'aide</h2>
      <p style="color:rgba(255,255,255,.8);max-width:500px;margin:0 auto var(--space-6);line-height:var(--line-relaxed)">
        Inscription gratuite, confidentielle et sécurisée. Vos données personnelles sont protégées conformément au RGPD.
      </p>
      <div style="display:flex;gap:var(--space-4);justify-content:center;flex-wrap:wrap">
        <a href="<?= BASE_URL ?>/inscription?type=beneficiaire" class="btn btn-gold btn-xl">
          🤝 Créer mon compte bénéficiaire
        </a>
        <a href="<?= BASE_URL ?>/connexion" class="btn btn-outline-white btn-xl">
          J'ai déjà un compte
        </a>
      </div>
    </div>

    <!-- Types d'aides -->
    <div class="section-header reveal" style="margin-bottom:var(--space-8)">
      <h2 class="section-title">Types d'aides disponibles</h2>
      <div class="divider-gold"></div>
    </div>
    <div class="grid grid-cols-3 gap-5" style="margin-bottom:var(--space-10)">
      <?php foreach ([
        ['💰','Aide financière','Soutien financier direct pour faire face aux dépenses essentielles.'],
        ['🍎','Aide alimentaire','Bons d\'achat et colis alimentaires pour les familles en difficulté.'],
        ['🏥','Aide médicale','Accès aux soins et aide aux médicaments non remboursés.'],
        ['📚','Aide scolaire','Matériel scolaire, activités périscolaires, soutien éducatif.'],
        ['🏠','Aide logement','Solutions d\'hébergement d\'urgence et aide au maintien dans le logement.'],
        ['🧠','Soutien psychologique','Accompagnement psychologique par des professionnels qualifiés.'],
      ] as $i=>[$ico,$titre,$desc]): ?>
      <div class="card reveal delay-<?= ($i%3)*100 ?>" style="padding:var(--space-5);text-align:center">
        <div style="font-size:2rem;margin-bottom:var(--space-3)"><?= $ico ?></div>
        <h3 style="font-size:var(--text-sm);font-weight:700;margin-bottom:var(--space-2)"><?= $titre ?></h3>
        <p style="font-size:var(--text-xs);color:var(--color-gray-600);line-height:var(--line-relaxed)"><?= $desc ?></p>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- FAQ rapide -->
    <div class="reveal" style="background:var(--color-gray-50);border-radius:var(--radius-2xl);padding:var(--space-8)">
      <h3 style="font-weight:700;font-size:var(--text-lg);margin-bottom:var(--space-5)">Questions fréquentes</h3>
      <div class="faq-accordion">
        <?php foreach ([
          ['Qui peut faire une demande d\'aide ?',
           'Toute personne résidant en Europe et se trouvant dans une situation de vulnérabilité sociale, économique ou médicale peut soumettre une demande. Il n\'y a aucune discrimination d\'origine, de religion ou de nationalité.'],
          ['Quels documents dois-je fournir ?',
           'Pièce d\'identité, justificatif de domicile, justificatif de revenus (ou attestation de non-revenus), et tout document médical ou administratif pertinent. Ces documents peuvent être uploadés directement depuis votre espace.'],
          ['Mon dossier est-il confidentiel ?',
           'Absolument. Toutes vos informations sont strictement confidentielles, chiffrées et accessibles uniquement par notre équipe sociale. Nous respectons scrupuleusement le RGPD.'],
        ] as $i=>[$q,$r]): ?>
        <div class="faq-item reveal delay-<?= $i*100 ?>">
          <div class="faq-question" role="button" tabindex="0">
            <span><?= Security::e($q) ?></span>
            <span class="faq-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span>
          </div>
          <div class="faq-answer"><div class="faq-answer-inner"><?= Security::e($r) ?></div></div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="text-center mt-6">
        <a href="<?= BASE_URL ?>/faq" class="btn btn-outline">Voir toutes les questions →</a>
      </div>
    </div>
  </div>
</section>
