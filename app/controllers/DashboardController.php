<?php
/**
 * =====================================================
 * EUROCARE HUMANITAIRE - Contrôleur Dashboard (dispatch)
 * =====================================================
 */
defined('BASEPATH') or die('Accès direct interdit.');

class DashboardController
{
    public function index(array $params = []): void
    {
        $role = Auth::role();
        switch ($role) {
            case ROLE_ADMIN:
            case ROLE_MODERATEUR:
                Helpers::redirect('/admin');
            case ROLE_DONATEUR:
                Helpers::redirect('/donateur/tableau-de-bord');
            case ROLE_BENEFICIAIRE:
                Helpers::redirect('/beneficiaire/tableau-de-bord');
            case ROLE_PARTENAIRE:
                Helpers::redirect('/partenaire/tableau-de-bord');
            default:
                Helpers::redirect('/');
        }
    }
}
