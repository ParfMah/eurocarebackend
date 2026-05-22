<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<section class="page-hero" style="padding:var(--space-10) 0"><div class="container"><div class="page-hero-content reveal">
  <h1 class="page-hero-title">Politique de confidentialité</h1>
  <p class="page-hero-subtitle">Dernière mise à jour : <?= date('d/m/Y') ?> — Conformité RGPD totale</p>
</div></div></section>
<section class="section"><div class="container" style="max-width:850px">
  <?php if (!empty($page['contenu'])): echo $page['contenu']; else: ?>
  <div style="background:white;border-radius:var(--radius-2xl);padding:var(--space-10);box-shadow:var(--shadow-sm);border:1px solid var(--color-gray-100)" class="article-content">
    <h2>1. Responsable du traitement</h2>
    <p><?= Security::e(Helpers::getSetting('site_nom','EuroCare Humanitaire')) ?>, organisation humanitaire européenne.<br>Email : <?= Security::e(Helpers::getSetting('site_email','contact@eurocare-humanitaire.eu')) ?></p>
    <h2>2. Données collectées</h2>
    <p>Nous collectons uniquement les données nécessaires au fonctionnement de nos services : nom, prénom, email, téléphone, données de situation sociale (pour les bénéficiaires), données de paiement (pour les donateurs).</p>
    <h2>3. Finalités du traitement</h2>
    <p>Les données sont utilisées pour : la gestion des demandes d'aide, le traitement des dons et émission de reçus fiscaux, la communication sur nos actions (avec consentement), la conformité légale et comptable.</p>
    <h2>4. Durée de conservation</h2>
    <p>Les données sont conservées 5 ans après la dernière interaction, sauf obligation légale contraire (reçus fiscaux : 10 ans).</p>
    <h2>5. Vos droits RGPD</h2>
    <p>Vous disposez des droits d'accès, rectification, effacement, portabilité et opposition. Pour les exercer : <a href="<?= BASE_URL ?>/contact">contactez-nous</a>.</p>
    <h2>6. Cookies</h2>
    <p>Nous utilisons uniquement des cookies de session nécessaires au fonctionnement du site. Aucun cookie publicitaire ou de tracking n'est utilisé.</p>
    <h2>7. Sécurité</h2>
    <p>Toutes les données sont chiffrées en transit (HTTPS) et au repos. Nous appliquons les meilleures pratiques de sécurité informatique.</p>
    <h2>8. Contact DPO</h2>
    <p>Pour toute question relative à la protection de vos données : <a href="mailto:<?= Security::e(Helpers::getSetting('site_email','contact@eurocare-humanitaire.eu')) ?>"><?= Security::e(Helpers::getSetting('site_email','contact@eurocare-humanitaire.eu')) ?></a></p>
  </div>
  <?php endif; ?>
</div></section>
<style>.article-content h2{font-size:var(--text-xl);font-weight:700;margin:var(--space-8) 0 var(--space-3);color:var(--color-blue-deep)}.article-content p{margin-bottom:var(--space-4);color:var(--color-gray-700);line-height:var(--line-relaxed)}</style>
