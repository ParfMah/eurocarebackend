<?php
/**
 * =====================================================
 * EUROCARE HUMANITAIRE - Constantes Globales
 * =====================================================
 * Fichier : app/config/constants.php
 * Description : Toutes les constantes métier du projet
 * =====================================================
 */

defined('BASEPATH') or die('Accès direct interdit.');

// =====================================================
// RÔLES UTILISATEURS
// =====================================================
define('ROLE_ADMIN',       'admin');
define('ROLE_MODERATEUR',  'moderateur');
define('ROLE_DONATEUR',    'donateur');
define('ROLE_BENEFICIAIRE','beneficiaire');
define('ROLE_PARTENAIRE',  'partenaire');

// Labels des rôles (pour affichage)
define('ROLES_LABELS', [
    ROLE_ADMIN        => 'Administrateur',
    ROLE_MODERATEUR   => 'Modérateur Social',
    ROLE_DONATEUR     => 'Donateur',
    ROLE_BENEFICIAIRE => 'Bénéficiaire',
    ROLE_PARTENAIRE   => 'Institution Partenaire',
]);

// Couleurs des badges rôles
define('ROLES_COLORS', [
    ROLE_ADMIN        => '#e02424',
    ROLE_MODERATEUR   => '#9061f9',
    ROLE_DONATEUR     => '#0e9f6e',
    ROLE_BENEFICIAIRE => '#1a56db',
    ROLE_PARTENAIRE   => '#c27803',
]);

// =====================================================
// STATUTS UTILISATEURS
// =====================================================
define('STATUT_USER_ACTIF',      'actif');
define('STATUT_USER_INACTIF',    'inactif');
define('STATUT_USER_SUSPENDU',   'suspendu');
define('STATUT_USER_EN_ATTENTE', 'en_attente');

define('STATUTS_USER_LABELS', [
    STATUT_USER_ACTIF      => 'Actif',
    STATUT_USER_INACTIF    => 'Inactif',
    STATUT_USER_SUSPENDU   => 'Suspendu',
    STATUT_USER_EN_ATTENTE => 'En attente de vérification',
]);

// =====================================================
// STATUTS DES DOSSIERS BÉNÉFICIAIRES
// =====================================================
define('STATUT_BENEF_EN_ATTENTE', 'en_attente');
define('STATUT_BENEF_EN_ETUDE',   'en_etude');
define('STATUT_BENEF_VERIFIE',    'verifie');
define('STATUT_BENEF_PRIORITAIRE','prioritaire');
define('STATUT_BENEF_REJETE',     'rejete');
define('STATUT_BENEF_AIDE',       'aide');

define('STATUTS_BENEF_LABELS', [
    STATUT_BENEF_EN_ATTENTE  => 'En attente',
    STATUT_BENEF_EN_ETUDE    => 'En cours d\'étude',
    STATUT_BENEF_VERIFIE     => 'Vérifié',
    STATUT_BENEF_PRIORITAIRE => 'Prioritaire',
    STATUT_BENEF_REJETE      => 'Rejeté',
    STATUT_BENEF_AIDE        => 'Aidé',
]);

define('STATUTS_BENEF_COLORS', [
    STATUT_BENEF_EN_ATTENTE  => '#6b7280',
    STATUT_BENEF_EN_ETUDE    => '#d97706',
    STATUT_BENEF_VERIFIE     => '#059669',
    STATUT_BENEF_PRIORITAIRE => '#7c3aed',
    STATUT_BENEF_REJETE      => '#dc2626',
    STATUT_BENEF_AIDE        => '#1d4ed8',
]);

// =====================================================
// NIVEAUX D'URGENCE
// =====================================================
define('URGENCE_FAIBLE',   'faible');
define('URGENCE_MODERE',   'modere');
define('URGENCE_ELEVE',    'eleve');
define('URGENCE_CRITIQUE', 'critique');

define('URGENCE_LABELS', [
    URGENCE_FAIBLE   => 'Faible',
    URGENCE_MODERE   => 'Modéré',
    URGENCE_ELEVE    => 'Élevé',
    URGENCE_CRITIQUE => 'Critique',
]);

define('URGENCE_COLORS', [
    URGENCE_FAIBLE   => '#22c55e',
    URGENCE_MODERE   => '#f59e0b',
    URGENCE_ELEVE    => '#f97316',
    URGENCE_CRITIQUE => '#ef4444',
]);

// =====================================================
// STATUTS DES DONS
// =====================================================
define('STATUT_DON_EN_ATTENTE', 'en_attente');
define('STATUT_DON_VALIDE',     'valide');
define('STATUT_DON_ECHOUE',     'echoue');
define('STATUT_DON_REMBOURSE',  'rembourse');
define('STATUT_DON_ANNULE',     'annule');

define('STATUTS_DON_LABELS', [
    STATUT_DON_EN_ATTENTE => 'En attente',
    STATUT_DON_VALIDE     => 'Validé',
    STATUT_DON_ECHOUE     => 'Échoué',
    STATUT_DON_REMBOURSE  => 'Remboursé',
    STATUT_DON_ANNULE     => 'Annulé',
]);

// =====================================================
// TYPES D'AIDES
// =====================================================
define('TYPES_AIDE', [
    'financiere'     => 'Aide financière directe',
    'alimentaire'    => 'Aide alimentaire',
    'medicale'       => 'Accompagnement médical',
    'scolaire'       => 'Soutien scolaire',
    'logement'       => 'Aide au logement',
    'materiel'       => 'Fourniture de matériel',
    'psychologique'  => 'Soutien psychologique',
    'juridique'      => 'Accompagnement juridique',
    'autre'          => 'Autre',
]);

// =====================================================
// TYPES DE BÉNÉFICIAIRES
// =====================================================
define('TYPES_BENEFICIAIRE', [
    'orphelin'              => 'Orphelin',
    'enfant_vulnerable'     => 'Enfant vulnérable',
    'personne_agee'         => 'Personne âgée en difficulté',
    'famille_en_difficulte' => 'Famille en difficulté',
    'sans_abri'             => 'Personne sans-abri',
    'personne_handicapee'   => 'Personne en situation de handicap',
    'autre'                 => 'Autre situation',
]);

// =====================================================
// TYPES D'ORGANISATIONS PARTENAIRES
// =====================================================
define('TYPES_PARTENAIRE', [
    'ong'            => 'Organisation Non Gouvernementale (ONG)',
    'hopital'        => 'Hôpital / Structure médicale',
    'ecole'          => 'École / Établissement scolaire',
    'association'    => 'Association locale',
    'service_social' => 'Service social public',
    'entreprise_mecene' => 'Entreprise mécène',
    'fondation'      => 'Fondation',
    'autre'          => 'Autre organisation',
]);

// =====================================================
// TYPES DE DOCUMENTS
// =====================================================
define('TYPES_DOCUMENT', [
    'identite'              => 'Pièce d\'identité',
    'justificatif_domicile' => 'Justificatif de domicile',
    'justificatif_revenus'  => 'Justificatif de revenus',
    'certificat_naissance'  => 'Acte / Certificat de naissance',
    'certificat_medical'    => 'Certificat médical',
    'photo'                 => 'Photo',
    'preuve_aide'           => 'Preuve d\'aide accordée',
    'autre'                 => 'Autre document',
]);

// =====================================================
// ACTIONS DU JOURNAL D'AUDIT
// =====================================================
define('ACTION_LOGIN',           'connexion');
define('ACTION_LOGOUT',          'deconnexion');
define('ACTION_REGISTER',        'inscription');
define('ACTION_PASSWORD_RESET',  'reinitialisation_mdp');
define('ACTION_EMAIL_VERIFY',    'verification_email');
define('ACTION_DON_CREATE',      'creation_don');
define('ACTION_DON_VALIDATE',    'validation_don');
define('ACTION_BENEF_CREATE',    'creation_beneficiaire');
define('ACTION_BENEF_UPDATE',    'modification_beneficiaire');
define('ACTION_BENEF_VALIDATE',  'validation_dossier');
define('ACTION_AIDE_CREATE',     'creation_aide');
define('ACTION_USER_CREATE',     'creation_utilisateur');
define('ACTION_USER_UPDATE',     'modification_utilisateur');
define('ACTION_USER_DELETE',     'suppression_utilisateur');
define('ACTION_USER_SUSPEND',    'suspension_utilisateur');
define('ACTION_ARTICLE_CREATE',  'creation_article');
define('ACTION_ARTICLE_UPDATE',  'modification_article');
define('ACTION_SETTING_UPDATE',  'modification_parametre');
define('ACTION_PARTNER_VALIDATE','validation_partenaire');
define('ACTION_UPLOAD_FILE',     'telechargement_fichier');
define('ACTION_FAILED_LOGIN',    'echec_connexion');

// =====================================================
// TYPES DE NOTIFICATIONS
// =====================================================
define('NOTIF_INFO',        'info');
define('NOTIF_SUCCES',      'succes');
define('NOTIF_ATTENTION',   'avertissement');
define('NOTIF_ERREUR',      'erreur');
define('NOTIF_DON',         'don');
define('NOTIF_AIDE',        'aide');
define('NOTIF_MESSAGE',     'message');
define('NOTIF_SYSTEME',     'systeme');
define('NOTIF_VALIDATION',  'validation');

// =====================================================
// PAYS EUROPEENS (pour les formulaires)
// =====================================================
define('PAYS_EUROPEENS', [
    'FR' => 'France',
    'BE' => 'Belgique',
    'CH' => 'Suisse',
    'LU' => 'Luxembourg',
    'MC' => 'Monaco',
    'DE' => 'Allemagne',
    'IT' => 'Italie',
    'ES' => 'Espagne',
    'PT' => 'Portugal',
    'NL' => 'Pays-Bas',
    'GB' => 'Royaume-Uni',
    'AT' => 'Autriche',
    'SE' => 'Suède',
    'NO' => 'Norvège',
    'DK' => 'Danemark',
    'FI' => 'Finlande',
    'PL' => 'Pologne',
    'CZ' => 'République Tchèque',
    'HU' => 'Hongrie',
    'RO' => 'Roumanie',
    'BG' => 'Bulgarie',
    'GR' => 'Grèce',
    'HR' => 'Croatie',
    'SK' => 'Slovaquie',
    'SI' => 'Slovénie',
    'EE' => 'Estonie',
    'LV' => 'Lettonie',
    'LT' => 'Lituanie',
    'IE' => 'Irlande',
    'MT' => 'Malte',
    'CY' => 'Chypre',
    'Autre' => 'Autre pays',
]);

// =====================================================
// DEVISES ACCEPTÉES
// =====================================================
define('DEVISES', [
    'EUR' => '€ Euro',
    'CHF' => 'CHF Franc Suisse',
    'GBP' => '£ Livre Sterling',
    'USD' => '$ Dollar US',
]);

// =====================================================
// MONTANTS DE DONS SUGGÉRÉS (en euros)
// =====================================================
define('MONTANTS_DON_SUGGERES', [10, 25, 50, 100, 250, 500]);

// =====================================================
// FRÉQUENCES DE DONS RÉCURRENTS
// =====================================================
define('FREQUENCES_DON', [
    'mensuel'      => 'Mensuel',
    'trimestriel'  => 'Trimestriel',
    'annuel'       => 'Annuel',
]);
