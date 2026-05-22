<?php defined('BASEPATH') or die('Accès direct interdit.'); ?>
<section class="page-hero" style="padding:var(--space-10) 0"><div class="container"><div class="page-hero-content reveal">
  <h1 class="page-hero-title">Conditions d'utilisation</h1>
  <p class="page-hero-subtitle">Dernière mise à jour : <?= date('d/m/Y') ?></p>
</div></div></section>
<section class="section"><div class="container" style="max-width:850px">
  <div style="background:white;border-radius:var(--radius-2xl);padding:var(--space-10);box-shadow:var(--shadow-sm);border:1px solid var(--color-gray-100)" class="article-content">
    <h2>1. Objet</h2>
    <p>Les présentes conditions régissent l'utilisation de la plateforme <?= Security::e(Helpers::getSetting('site_nom','EuroCare Humanitaire')) ?> accessible à l'adresse <?= BASE_URL ?>.</p>
    <h2>2. Accès à la plateforme</h2>
    <p>L'accès à certaines fonctionnalités nécessite la création d'un compte. L'inscription est gratuite et ouverte à toute personne majeure résidant en Europe.</p>
    <h2>3. Utilisation des services</h2>
    <p>Les utilisateurs s'engagent à fournir des informations exactes, à ne pas usurper l'identité d'un tiers, à ne pas utiliser la plateforme à des fins frauduleuses.</p>
    <h2>4. Dons et paiements</h2>
    <p>Tous les dons sont définitifs sauf erreur manifeste. Les reçus fiscaux sont émis conformément à la législation française. Les dons récurrents sont résiliables à tout moment depuis l'espace donateur.</p>
    <h2>5. Propriété intellectuelle</h2>
    <p>Tous les contenus de la plateforme (textes, images, logos, code) sont protégés par le droit de la propriété intellectuelle et appartiennent à <?= Security::e(Helpers::getSetting('site_nom','EuroCare Humanitaire')) ?>.</p>
    <h2>6. Limitation de responsabilité</h2>
    <p>Nous mettons tout en œuvre pour assurer la disponibilité de la plateforme mais ne pouvons garantir une disponibilité ininterrompue.</p>
    <h2>7. Droit applicable</h2>
    <p>Les présentes conditions sont régies par le droit français. Tout litige sera soumis aux tribunaux compétents de Paris.</p>
  </div>
</div></section>
<style>.article-content h2{font-size:var(--text-xl);font-weight:700;margin:var(--space-8) 0 var(--space-3);color:var(--color-blue-deep)}.article-content p{margin-bottom:var(--space-4);color:var(--color-gray-700);line-height:var(--line-relaxed)}</style>
