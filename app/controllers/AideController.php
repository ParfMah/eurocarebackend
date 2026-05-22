<?php
/**
 * AideController.php
 * Gestion de la demande d'aide publique
 */
defined('BASEPATH') or die('Accès direct interdit.');

class AideController
{
    public function formulairePublic(array $p = []): void
    {
        // Si connecté et bénéficiaire → rediriger vers son dossier
        if (Auth::check() && Auth::role() === ROLE_BENEFICIAIRE) {
            Helpers::redirect('/beneficiaire/mon-dossier');
        }

        // Si connecté avec un autre rôle
        if (Auth::check()) {
            Helpers::redirectWithFlash('/tableau-de-bord', 'info',
                'Pour demander une aide, créez un compte bénéficiaire.');
        }

        // Non connecté → page d'information + lien inscription
        Helpers::view('public/demander_aide', [
            'pageTitle' => 'Demander une aide',
            'metaDesc'  => 'Créez votre dossier de demande d\'aide sociale sur EuroCare Humanitaire.',
            'bodyClass' => 'page-aide',
        ]);
    }
}
