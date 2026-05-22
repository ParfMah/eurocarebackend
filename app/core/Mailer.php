<?php
/**
 * =====================================================
 * EUROCARE HUMANITAIRE - Classe Mailer
 * =====================================================
 * Fichier : app/core/Mailer.php
 * Description : Envoi d'emails transactionnels HTML.
 *   Utilise mail() PHP natif (remplacer par PHPMailer
 *   en production avec configuration SMTP).
 * =====================================================
 */

defined('BASEPATH') or die('Accès direct interdit.');

class Mailer
{
    /**
     * Envoie un email HTML avec template branded.
     *
     * @param  string $to       Destinataire
     * @param  string $subject  Sujet
     * @param  string $body     Corps HTML (sans le layout)
     * @param  string $name     Nom du destinataire (optionnel)
     * @return bool
     */
    public static function send(
        string $to,
        string $subject,
        string $body,
        string $name = ''
    ): bool {
        $siteName  = Helpers::getSetting('site_nom', APP_NAME);
        $siteEmail = Helpers::getSetting('site_email', MAIL_FROM_ADDRESS);
        $fromName  = MAIL_FROM_NAME;
        $fromEmail = MAIL_FROM_ADDRESS;
        $year      = date('Y');

        $html = "<!DOCTYPE html>
<html lang='fr'>
<head>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width,initial-scale=1'>
  <title>{$subject}</title>
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#f3f4f6;color:#374151}
    .wrapper{max-width:600px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08)}
    .header{background:linear-gradient(135deg,#0d2b6e,#1a56db);padding:32px;text-align:center}
    .header img{height:48px}
    .header h1{color:#fff;font-size:20px;font-weight:700;margin-top:12px}
    .header p{color:rgba(255,255,255,.75);font-size:13px;margin-top:4px}
    .body{padding:32px}
    .body h2{font-size:20px;color:#111827;margin-bottom:12px}
    .body p{font-size:15px;color:#4b5563;line-height:1.6;margin-bottom:16px}
    .btn{display:inline-block;background:linear-gradient(135deg,#1a56db,#0d2b6e);color:#fff;padding:14px 32px;border-radius:8px;text-decoration:none;font-weight:600;font-size:15px;margin:8px 0}
    .info-box{background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:16px;margin:16px 0;font-size:14px;color:#1e40af}
    .success-box{background:#ecfdf5;border:1px solid #a7f3d0;border-radius:8px;padding:16px;margin:16px 0;font-size:14px;color:#065f46}
    .divider{border:none;border-top:1px solid #e5e7eb;margin:24px 0}
    .footer{background:#f9fafb;padding:24px;text-align:center;border-top:1px solid #e5e7eb}
    .footer p{font-size:12px;color:#9ca3af;margin-bottom:4px}
    .footer a{color:#6b7280;text-decoration:none}
    table.details{width:100%;border-collapse:collapse;margin:16px 0}
    table.details td{padding:8px 12px;font-size:14px;border-bottom:1px solid #f3f4f6}
    table.details td:first-child{color:#6b7280;width:45%}
    table.details td:last-child{font-weight:600;color:#111827}
  </style>
</head>
<body>
  <div class='wrapper'>
    <div class='header'>
      <h1>🛡️ {$siteName}</h1>
      <p>Organisation Humanitaire Européenne</p>
    </div>
    <div class='body'>
      {$body}
    </div>
    <div class='footer'>
      <p>© {$year} {$siteName} · <a href='".BASE_URL."'>Visiter le site</a></p>
      <p>Ce message est envoyé automatiquement, merci de ne pas y répondre directement.</p>
      <p>Pour nous contacter : <a href='mailto:{$siteEmail}'>{$siteEmail}</a></p>
    </div>
  </div>
</body>
</html>";

        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: {$fromName} <{$fromEmail}>\r\n";
        $headers .= "Reply-To: {$siteEmail}\r\n";
        $headers .= "X-Mailer: EuroCare-PHP\r\n";

        $toFormatted = $name ? "{$name} <{$to}>" : $to;

        $result = @mail($toFormatted, '=?UTF-8?B?' . base64_encode($subject) . '?=', $html, $headers);

        Logger::logToFile('INFO', "Email envoyé à {$to} : {$subject} — " . ($result ? 'OK' : 'ECHEC'));

        return $result;
    }

    // =====================================================
    // TEMPLATES D'EMAILS PRÉDÉFINIS
    // =====================================================

    /**
     * Email de bienvenue après inscription
     */
    public static function sendWelcome(string $email, string $prenom, string $role): bool
    {
        $roleLabel = ROLES_LABELS[$role] ?? 'Membre';
        $body = "
        <h2>Bienvenue, {$prenom} ! 🎉</h2>
        <p>Votre compte <strong>{$roleLabel}</strong> a été créé avec succès sur la plateforme EuroCare Humanitaire.</p>
        <div class='success-box'>
            ✅ Votre email a été vérifié. Vous pouvez maintenant accéder à tous nos services.
        </div>
        <p>Découvrez votre espace personnel :</p>
        <a href='" . BASE_URL . "/tableau-de-bord' class='btn'>Accéder à mon espace →</a>
        <hr class='divider'>
        <p style='font-size:13px;color:#9ca3af'>Si vous n'avez pas créé ce compte, contactez-nous immédiatement.</p>";

        return self::send($email, 'Bienvenue sur EuroCare Humanitaire !', $body, $prenom);
    }

    /**
     * Email de confirmation de don avec reçu
     */
    public static function sendDonConfirmation(
        string $email,
        string $prenom,
        float  $montant,
        string $uuid,
        string $cause = ''
    ): bool {
        $montantFormaté = Helpers::formatAmount($montant);
        $deduction      = Helpers::formatAmount($montant * 0.66);
        $date           = date('d/m/Y H:i');
        $causeText      = $cause ?: 'Fonds général';
        $recuUrl        = BASE_URL . '/don/recu/' . $uuid;

        $body = "
        <h2>Merci pour votre don ! 💝</h2>
        <p>Bonjour {$prenom}, votre don a bien été reçu et validé. Voici votre reçu :</p>
        <table class='details'>
            <tr><td>Référence</td><td><code>{$uuid}</code></td></tr>
            <tr><td>Montant du don</td><td><strong style='color:#1a56db'>{$montantFormaté}</strong></td></tr>
            <tr><td>Cause soutenue</td><td>{$causeText}</td></tr>
            <tr><td>Date</td><td>{$date}</td></tr>
            <tr><td>Déductible à 66%</td><td><strong style='color:#059669'>{$deduction}</strong></td></tr>
        </table>
        <div class='success-box'>
            ✅ Ce document fait office de reçu fiscal. Conservez-le pour votre déclaration d'impôts.
        </div>
        <a href='{$recuUrl}' class='btn'>📄 Télécharger mon reçu PDF</a>
        <hr class='divider'>
        <p>Grâce à votre générosité, nous pouvons continuer à aider des milliers de personnes en difficulté à travers l'Europe.</p>
        <p><a href='" . BASE_URL . "/transparence' style='color:#1a56db'>Voir comment votre don est utilisé →</a></p>";

        return self::send($email, "Confirmation de votre don de {$montantFormaté}", $body, $prenom);
    }

    /**
     * Email de notification de changement de statut dossier
     */
    public static function sendStatutDossier(
        string $email,
        string $prenom,
        string $statut,
        string $numeroDossier,
        string $note = ''
    ): bool {
        $statutLabel = STATUTS_BENEF_LABELS[$statut] ?? $statut;
        $couleur     = match($statut) {
            'verifie', 'prioritaire', 'aide' => '#059669',
            'rejete'                          => '#dc2626',
            default                           => '#1a56db',
        };

        $emoji = match($statut) {
            'en_etude'    => '🔍',
            'verifie'     => '✅',
            'prioritaire' => '⭐',
            'aide'        => '🎁',
            'rejete'      => '❌',
            default       => 'ℹ️',
        };

        $body = "
        <h2>{$emoji} Mise à jour de votre dossier</h2>
        <p>Bonjour {$prenom},</p>
        <p>Le statut de votre dossier <strong>{$numeroDossier}</strong> vient d'être mis à jour :</p>
        <div style='background:" . $couleur . "15;border:1px solid " . $couleur . "40;border-radius:8px;padding:16px;text-align:center;margin:16px 0'>
            <div style='font-size:24px;margin-bottom:8px'>{$emoji}</div>
            <div style='font-size:18px;font-weight:700;color:{$couleur}'>{$statutLabel}</div>
        </div>";

        if ($note) {
            $body .= "<div class='info-box'><strong>Note de l'équipe :</strong><br>" . nl2br(htmlspecialchars($note)) . "</div>";
        }

        $body .= "
        <a href='" . BASE_URL . "/beneficiaire/mon-dossier' class='btn'>Voir mon dossier →</a>
        <hr class='divider'>
        <p style='font-size:13px;color:#9ca3af'>Pour toute question, contactez notre équipe sociale.</p>";

        return self::send($email, "{$emoji} Dossier {$numeroDossier} — Statut : {$statutLabel}", $body, $prenom);
    }

    /**
     * Email de validation partenaire
     */
    public static function sendPartenaireStatut(
        string $email,
        string $prenom,
        string $nomOrg,
        string $statut
    ): bool {
        $ok    = ($statut === 'valide');
        $emoji = $ok ? '✅' : '❌';
        $txt   = $ok ? 'validée' : 'rejetée';

        $body = "
        <h2>{$emoji} Décision sur votre dossier partenaire</h2>
        <p>Bonjour {$prenom},</p>
        <p>Nous avons étudié la candidature de <strong>{$nomOrg}</strong> en tant que partenaire institutionnel d'EuroCare Humanitaire.</p>
        <div class='" . ($ok ? 'success' : 'info') . "-box'>
            {$emoji} Votre candidature a été <strong>{$txt}</strong>.
        </div>";

        if ($ok) {
            $body .= "
            <p>Vous pouvez dès maintenant accéder à votre espace partenaire et soumettre des recommandations de bénéficiaires.</p>
            <a href='" . BASE_URL . "/partenaire/tableau-de-bord' class='btn'>Accéder à mon espace →</a>";
        } else {
            $body .= "<p>Pour toute question ou pour soumettre une nouvelle candidature, n'hésitez pas à <a href='" . BASE_URL . "/contact'>nous contacter</a>.</p>";
        }

        return self::send($email, "{$emoji} Candidature partenaire — {$nomOrg}", $body, $prenom);
    }
}
