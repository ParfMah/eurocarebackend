-- =====================================================
-- EUROCARE HUMANITAIRE - BASE DE DONNÉES COMPLÈTE
-- Version : 1.0.0
-- Encodage : UTF-8 (utf8mb4)
-- Description : Schéma complet de la plateforme humanitaire
-- =====================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- Suppression des tables existantes (ordre inverse des dépendances)
DROP TABLE IF EXISTS `statistiques_quotidiennes`;
DROP TABLE IF EXISTS `contacts`;
DROP TABLE IF EXISTS `faq`;
DROP TABLE IF EXISTS `temoignages`;
DROP TABLE IF EXISTS `parametres`;
DROP TABLE IF EXISTS `pages_cms`;
DROP TABLE IF EXISTS `journal_audit`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `messages`;
DROP TABLE IF EXISTS `commentaires`;
DROP TABLE IF EXISTS `articles`;
DROP TABLE IF EXISTS `categories_articles`;
DROP TABLE IF EXISTS `recommandations_partenaires`;
DROP TABLE IF EXISTS `partenaires_profils`;
DROP TABLE IF EXISTS `aides_octroyees`;
DROP TABLE IF EXISTS `documents`;
DROP TABLE IF EXISTS `beneficiaires_profils`;
DROP TABLE IF EXISTS `dons_recurrents`;
DROP TABLE IF EXISTS `dons`;
DROP TABLE IF EXISTS `projets`;
DROP TABLE IF EXISTS `verification_emails`;
DROP TABLE IF EXISTS `reinitialisation_mdp`;
DROP TABLE IF EXISTS `sessions_utilisateurs`;
DROP TABLE IF EXISTS `users`;

-- =====================================================
-- TABLE : users (tous les utilisateurs de la plateforme)
-- =====================================================
CREATE TABLE `users` (
    `id`                    INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    `uuid`                  CHAR(36)          NOT NULL,
    `email`                 VARCHAR(255)      NOT NULL,
    `password`              VARCHAR(255)      NOT NULL,
    `role`                  ENUM('admin','moderateur','donateur','beneficiaire','partenaire') NOT NULL DEFAULT 'donateur',
    `statut`                ENUM('actif','inactif','suspendu','en_attente') NOT NULL DEFAULT 'en_attente',
    `prenom`                VARCHAR(100)      NOT NULL,
    `nom`                   VARCHAR(100)      NOT NULL,
    `telephone`             VARCHAR(20)       DEFAULT NULL,
    `date_naissance`        DATE              DEFAULT NULL,
    `pays`                  VARCHAR(100)      DEFAULT NULL,
    `ville`                 VARCHAR(100)      DEFAULT NULL,
    `adresse`               TEXT              DEFAULT NULL,
    `code_postal`           VARCHAR(20)       DEFAULT NULL,
    `photo_profil`          VARCHAR(255)      DEFAULT NULL,
    `bio`                   TEXT              DEFAULT NULL,
    `email_verifie`         TINYINT(1)        NOT NULL DEFAULT 0,
    `deux_facteurs`         TINYINT(1)        NOT NULL DEFAULT 0,
    `langue`                VARCHAR(10)       NOT NULL DEFAULT 'fr',
    `newsletter`            TINYINT(1)        NOT NULL DEFAULT 1,
    `notifications_email`   TINYINT(1)        NOT NULL DEFAULT 1,
    `derniere_connexion`    DATETIME          DEFAULT NULL,
    `ip_inscription`        VARCHAR(45)       DEFAULT NULL,
    `tentatives_connexion`  INT               NOT NULL DEFAULT 0,
    `bloque_jusqu`          DATETIME          DEFAULT NULL,
    `token_api`             VARCHAR(255)      DEFAULT NULL,
    `cree_le`               DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modifie_le`            DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `supprime_le`           DATETIME          DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_uuid`    (`uuid`),
    UNIQUE KEY `uk_email`   (`email`),
    INDEX `idx_role`        (`role`),
    INDEX `idx_statut`      (`statut`),
    INDEX `idx_supprime_le` (`supprime_le`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Table principale de tous les utilisateurs de la plateforme';

-- =====================================================
-- TABLE : sessions_utilisateurs (sessions sécurisées)
-- =====================================================
CREATE TABLE `sessions_utilisateurs` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`    INT UNSIGNED NOT NULL,
    `token`      VARCHAR(255) NOT NULL,
    `ip`         VARCHAR(45)  DEFAULT NULL,
    `user_agent` TEXT         DEFAULT NULL,
    `expire_le`  DATETIME     NOT NULL,
    `cree_le`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_token` (`token`),
    INDEX `idx_user_id`  (`user_id`),
    INDEX `idx_expire_le`(`expire_le`),
    CONSTRAINT `fk_sessions_user` FOREIGN KEY (`user_id`)
        REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Sessions utilisateurs sécurisées avec expiration automatique';

-- =====================================================
-- TABLE : reinitialisation_mdp (réinitialisation de mot de passe)
-- =====================================================
CREATE TABLE `reinitialisation_mdp` (
    `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`   INT UNSIGNED NOT NULL,
    `token`     VARCHAR(255) NOT NULL,
    `expire_le` DATETIME     NOT NULL,
    `utilise`   TINYINT(1)   NOT NULL DEFAULT 0,
    `ip`        VARCHAR(45)  DEFAULT NULL,
    `cree_le`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_token` (`token`),
    INDEX `idx_user_id`  (`user_id`),
    CONSTRAINT `fk_reset_user` FOREIGN KEY (`user_id`)
        REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Tokens de réinitialisation de mot de passe';

-- =====================================================
-- TABLE : verification_emails (vérification d'email)
-- =====================================================
CREATE TABLE `verification_emails` (
    `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`   INT UNSIGNED NOT NULL,
    `token`     VARCHAR(255) NOT NULL,
    `expire_le` DATETIME     NOT NULL,
    `utilise`   TINYINT(1)   NOT NULL DEFAULT 0,
    `cree_le`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_token` (`token`),
    INDEX `idx_user_id` (`user_id`),
    CONSTRAINT `fk_verify_user` FOREIGN KEY (`user_id`)
        REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Tokens de vérification d\'adresse email';

-- =====================================================
-- TABLE : projets (causes et projets humanitaires)
-- =====================================================
CREATE TABLE `projets` (
    `id`                INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `titre`             VARCHAR(255)     NOT NULL,
    `slug`              VARCHAR(255)     NOT NULL,
    `description`       LONGTEXT         NOT NULL,
    `description_courte`VARCHAR(500)     DEFAULT NULL,
    `objectif_montant`  DECIMAL(10,2)    NOT NULL DEFAULT 0.00,
    `montant_collecte`  DECIMAL(10,2)    NOT NULL DEFAULT 0.00,
    `image`             VARCHAR(255)     DEFAULT NULL,
    `categorie`         VARCHAR(100)     DEFAULT NULL,
    `statut`            ENUM('actif','complete','suspendu','termine') NOT NULL DEFAULT 'actif',
    `date_debut`        DATE             DEFAULT NULL,
    `date_fin`          DATE             DEFAULT NULL,
    `featured`          TINYINT(1)       NOT NULL DEFAULT 0,
    `ordre`             INT              NOT NULL DEFAULT 0,
    `beneficiaires_aides`INT             NOT NULL DEFAULT 0,
    `cree_par`          INT UNSIGNED     DEFAULT NULL,
    `cree_le`           DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modifie_le`        DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_slug` (`slug`),
    INDEX `idx_statut`  (`statut`),
    INDEX `idx_featured`(`featured`),
    CONSTRAINT `fk_projets_user` FOREIGN KEY (`cree_par`)
        REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Projets et causes humanitaires de la plateforme';

-- =====================================================
-- TABLE : dons (donations reçues)
-- =====================================================
CREATE TABLE `dons` (
    `id`                    INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `uuid`                  CHAR(36)         NOT NULL,
    `user_id`               INT UNSIGNED     DEFAULT NULL,
    `donateur_anonyme`      TINYINT(1)       NOT NULL DEFAULT 0,
    `prenom_anonyme`        VARCHAR(100)     DEFAULT NULL,
    `email_anonyme`         VARCHAR(255)     DEFAULT NULL,
    `montant`               DECIMAL(10,2)    NOT NULL,
    `devise`                VARCHAR(10)      NOT NULL DEFAULT 'EUR',
    `type`                  ENUM('ponctuel','mensuel','annuel') NOT NULL DEFAULT 'ponctuel',
    `cause`                 VARCHAR(255)     DEFAULT NULL,
    `projet_id`             INT UNSIGNED     DEFAULT NULL,
    `statut`                ENUM('en_attente','valide','echoue','rembourse','annule') NOT NULL DEFAULT 'en_attente',
    `methode_paiement`      VARCHAR(100)     DEFAULT NULL,
    `reference_transaction` VARCHAR(255)     DEFAULT NULL,
    `recu_envoye`           TINYINT(1)       NOT NULL DEFAULT 0,
    `recu_pdf`              VARCHAR(255)     DEFAULT NULL,
    `message`               TEXT             DEFAULT NULL,
    `deductible_impot`      TINYINT(1)       NOT NULL DEFAULT 1,
    `ip_donateur`           VARCHAR(45)      DEFAULT NULL,
    `cree_le`               DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `valide_le`             DATETIME         DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_uuid` (`uuid`),
    INDEX `idx_user_id`  (`user_id`),
    INDEX `idx_statut`   (`statut`),
    INDEX `idx_projet_id`(`projet_id`),
    INDEX `idx_cree_le`  (`cree_le`),
    CONSTRAINT `fk_dons_user`   FOREIGN KEY (`user_id`)   REFERENCES `users`(`id`)   ON DELETE SET NULL,
    CONSTRAINT `fk_dons_projet` FOREIGN KEY (`projet_id`) REFERENCES `projets`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Historique complet de tous les dons reçus';

-- =====================================================
-- TABLE : dons_recurrents (abonnements de dons réguliers)
-- =====================================================
CREATE TABLE `dons_recurrents` (
    `id`                    INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `user_id`               INT UNSIGNED     NOT NULL,
    `montant`               DECIMAL(10,2)    NOT NULL,
    `devise`                VARCHAR(10)      NOT NULL DEFAULT 'EUR',
    `frequence`             ENUM('mensuel','trimestriel','annuel') NOT NULL DEFAULT 'mensuel',
    `cause`                 VARCHAR(255)     DEFAULT NULL,
    `projet_id`             INT UNSIGNED     DEFAULT NULL,
    `statut`                ENUM('actif','pause','annule') NOT NULL DEFAULT 'actif',
    `prochaine_echeance`    DATE             DEFAULT NULL,
    `reference_abonnement`  VARCHAR(255)     DEFAULT NULL,
    `total_preleve`         DECIMAL(10,2)    NOT NULL DEFAULT 0.00,
    `nombre_prelevements`   INT              NOT NULL DEFAULT 0,
    `cree_le`               DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modifie_le`            DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_user_id`(`user_id`),
    INDEX `idx_statut`  (`statut`),
    CONSTRAINT `fk_dons_rec_user` FOREIGN KEY (`user_id`)
        REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Dons récurrents et abonnements de soutien';

-- =====================================================
-- TABLE : beneficiaires_profils (profils des bénéficiaires)
-- =====================================================
CREATE TABLE `beneficiaires_profils` (
    `id`                    INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `user_id`               INT UNSIGNED     NOT NULL,
    `numero_dossier`        VARCHAR(50)      NOT NULL,
    `type_beneficiaire`     ENUM('orphelin','enfant_vulnerable','personne_agee','famille_en_difficulte','sans_abri','personne_handicapee','autre') NOT NULL,
    `situation_familiale`   ENUM('celibataire','marie','divorce','veuf','concubinage') DEFAULT NULL,
    `nombre_enfants`        INT              NOT NULL DEFAULT 0,
    `revenus_mensuels`      DECIMAL(10,2)    DEFAULT NULL,
    `source_revenus`        VARCHAR(255)     DEFAULT NULL,
    `situation_logement`    ENUM('proprietaire','locataire','heberge','sans_abri','autre') DEFAULT NULL,
    `besoins_principaux`    TEXT             DEFAULT NULL,
    `description_situation` TEXT             DEFAULT NULL,
    `antecedents_medicaux`  TEXT             DEFAULT NULL,
    `niveau_urgence`        ENUM('faible','modere','eleve','critique') NOT NULL DEFAULT 'modere',
    `statut_dossier`        ENUM('en_attente','en_etude','verifie','prioritaire','rejete','aide') NOT NULL DEFAULT 'en_attente',
    `motif_refus`           TEXT             DEFAULT NULL,
    `note_interne`          TEXT             DEFAULT NULL,
    `score_priorite`        INT              NOT NULL DEFAULT 0,
    `valide_par`            INT UNSIGNED     DEFAULT NULL,
    `valide_le`             DATETIME         DEFAULT NULL,
    `cree_le`               DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modifie_le`            DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_user_id`       (`user_id`),
    UNIQUE KEY `uk_numero_dossier`(`numero_dossier`),
    INDEX `idx_statut_dossier` (`statut_dossier`),
    INDEX `idx_niveau_urgence` (`niveau_urgence`),
    CONSTRAINT `fk_benef_user`    FOREIGN KEY (`user_id`)    REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_benef_valideur`FOREIGN KEY (`valide_par`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Dossiers sociaux détaillés des bénéficiaires';

-- =====================================================
-- TABLE : documents (pièces justificatives)
-- =====================================================
CREATE TABLE `documents` (
    `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`        INT UNSIGNED NOT NULL,
    `beneficiaire_id`INT UNSIGNED DEFAULT NULL,
    `type_document`  ENUM('identite','justificatif_domicile','justificatif_revenus',
                          'certificat_naissance','certificat_medical','photo','preuve_aide','autre') NOT NULL,
    `nom_original`   VARCHAR(255) NOT NULL,
    `nom_stockage`   VARCHAR(255) NOT NULL,
    `chemin`         VARCHAR(500) NOT NULL,
    `taille`         INT UNSIGNED NOT NULL,
    `mime_type`      VARCHAR(100) NOT NULL,
    `statut`         ENUM('en_attente','valide','rejete') NOT NULL DEFAULT 'en_attente',
    `note`           TEXT         DEFAULT NULL,
    `verifie_par`    INT UNSIGNED DEFAULT NULL,
    `verifie_le`     DATETIME     DEFAULT NULL,
    `cree_le`        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_user_id`         (`user_id`),
    INDEX `idx_beneficiaire_id` (`beneficiaire_id`),
    INDEX `idx_statut`          (`statut`),
    CONSTRAINT `fk_docs_user`  FOREIGN KEY (`user_id`)  REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Documents et pièces justificatives uploadés';

-- =====================================================
-- TABLE : aides_octroyees (aides accordées aux bénéficiaires)
-- =====================================================
CREATE TABLE `aides_octroyees` (
    `id`               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `beneficiaire_id`  INT UNSIGNED  NOT NULL,
    `projet_id`        INT UNSIGNED  DEFAULT NULL,
    `type_aide`        ENUM('financiere','alimentaire','medicale','scolaire','logement','materiel','psychologique','juridique','autre') NOT NULL,
    `montant`          DECIMAL(10,2) DEFAULT NULL,
    `description`      TEXT          NOT NULL,
    `statut`           ENUM('approuve','en_cours','complete','annule') NOT NULL DEFAULT 'approuve',
    `accorde_par`      INT UNSIGNED  NOT NULL,
    `date_attribution` DATE          NOT NULL,
    `date_completion`  DATE          DEFAULT NULL,
    `preuve_aide`      VARCHAR(255)  DEFAULT NULL,
    `note_interne`     TEXT          DEFAULT NULL,
    `satisfaction`     TINYINT(1)    DEFAULT NULL COMMENT 'Note satisfaction 1-5',
    `cree_le`          DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modifie_le`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_beneficiaire_id`(`beneficiaire_id`),
    INDEX `idx_statut`         (`statut`),
    INDEX `idx_type_aide`      (`type_aide`),
    CONSTRAINT `fk_aides_benef`   FOREIGN KEY (`beneficiaire_id`) REFERENCES `beneficiaires_profils`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_aides_accordeur`FOREIGN KEY (`accorde_par`)    REFERENCES `users`(`id`)                ON DELETE RESTRICT,
    CONSTRAINT `fk_aides_projet`  FOREIGN KEY (`projet_id`)       REFERENCES `projets`(`id`)              ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Historique de toutes les aides accordées aux bénéficiaires';

-- =====================================================
-- TABLE : partenaires_profils (profils des organisations partenaires)
-- =====================================================
CREATE TABLE `partenaires_profils` (
    `id`                    INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`               INT UNSIGNED NOT NULL,
    `nom_organisation`      VARCHAR(255) NOT NULL,
    `type_organisation`     ENUM('ong','hopital','ecole','association','service_social','entreprise_mecene','fondation','autre') NOT NULL,
    `numero_enregistrement` VARCHAR(100) DEFAULT NULL,
    `site_web`              VARCHAR(255) DEFAULT NULL,
    `logo`                  VARCHAR(255) DEFAULT NULL,
    `description`           TEXT         DEFAULT NULL,
    `domaines_action`       TEXT         DEFAULT NULL,
    `pays`                  VARCHAR(100) DEFAULT NULL,
    `ville`                 VARCHAR(100) DEFAULT NULL,
    `adresse`               TEXT         DEFAULT NULL,
    `telephone`             VARCHAR(20)  DEFAULT NULL,
    `contact_principal`     VARCHAR(255) DEFAULT NULL,
    `email_contact`         VARCHAR(255) DEFAULT NULL,
    `statut`                ENUM('en_attente','valide','suspendu','rejete') NOT NULL DEFAULT 'en_attente',
    `valide_par`            INT UNSIGNED DEFAULT NULL,
    `valide_le`             DATETIME     DEFAULT NULL,
    `featured`              TINYINT(1)   NOT NULL DEFAULT 0,
    `ordre_affichage`       INT          NOT NULL DEFAULT 0,
    `cree_le`               DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modifie_le`            DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_user_id` (`user_id`),
    INDEX `idx_statut`  (`statut`),
    INDEX `idx_featured`(`featured`),
    CONSTRAINT `fk_part_user`    FOREIGN KEY (`user_id`)    REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_part_valideur`FOREIGN KEY (`valide_par`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Profils des organisations partenaires institutionnels';

-- =====================================================
-- TABLE : recommandations_partenaires
-- =====================================================
CREATE TABLE `recommandations_partenaires` (
    `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `partenaire_id`   INT UNSIGNED NOT NULL,
    `beneficiaire_id` INT UNSIGNED NOT NULL,
    `recommandation`  TEXT         NOT NULL,
    `niveau_urgence`  ENUM('faible','modere','eleve','critique') NOT NULL DEFAULT 'modere',
    `statut`          ENUM('soumise','lue','traitee','rejetee') NOT NULL DEFAULT 'soumise',
    `traitee_par`     INT UNSIGNED DEFAULT NULL,
    `cree_le`         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_partenaire_id`  (`partenaire_id`),
    INDEX `idx_beneficiaire_id`(`beneficiaire_id`),
    CONSTRAINT `fk_reco_partenaire`   FOREIGN KEY (`partenaire_id`)   REFERENCES `partenaires_profils`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_reco_beneficiaire` FOREIGN KEY (`beneficiaire_id`) REFERENCES `beneficiaires_profils`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Recommandations émises par les partenaires pour des bénéficiaires';

-- =====================================================
-- TABLE : categories_articles (catégories du blog)
-- =====================================================
CREATE TABLE `categories_articles` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nom`         VARCHAR(100) NOT NULL,
    `slug`        VARCHAR(100) NOT NULL,
    `description` TEXT         DEFAULT NULL,
    `image`       VARCHAR(255) DEFAULT NULL,
    `couleur`     VARCHAR(20)  DEFAULT '#1a56db',
    `ordre`       INT          NOT NULL DEFAULT 0,
    `actif`       TINYINT(1)   NOT NULL DEFAULT 1,
    `cree_le`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Catégories du blog et des actualités';

-- =====================================================
-- TABLE : articles (blog et actualités)
-- =====================================================
CREATE TABLE `articles` (
    `id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `titre`              VARCHAR(255) NOT NULL,
    `slug`               VARCHAR(255) NOT NULL,
    `contenu`            LONGTEXT     NOT NULL,
    `extrait`            TEXT         DEFAULT NULL,
    `image_principale`   VARCHAR(255) DEFAULT NULL,
    `auteur_id`          INT UNSIGNED DEFAULT NULL,
    `categorie_id`       INT UNSIGNED DEFAULT NULL,
    `statut`             ENUM('brouillon','publie','archive') NOT NULL DEFAULT 'brouillon',
    `featured`           TINYINT(1)   NOT NULL DEFAULT 0,
    `vues`               INT UNSIGNED NOT NULL DEFAULT 0,
    `commentaires_actifs`TINYINT(1)   NOT NULL DEFAULT 1,
    `meta_titre`         VARCHAR(255) DEFAULT NULL,
    `meta_description`   TEXT         DEFAULT NULL,
    `tags`               TEXT         DEFAULT NULL,
    `temps_lecture`      INT          DEFAULT NULL COMMENT 'En minutes',
    `publie_le`          DATETIME     DEFAULT NULL,
    `cree_le`            DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modifie_le`         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_slug`  (`slug`),
    INDEX `idx_statut`    (`statut`),
    INDEX `idx_featured`  (`featured`),
    INDEX `idx_publie_le` (`publie_le`),
    CONSTRAINT `fk_articles_auteur`   FOREIGN KEY (`auteur_id`)    REFERENCES `users`(`id`)              ON DELETE SET NULL,
    CONSTRAINT `fk_articles_categorie`FOREIGN KEY (`categorie_id`) REFERENCES `categories_articles`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Articles du blog et actualités de l\'organisation';

-- =====================================================
-- TABLE : commentaires (commentaires des articles)
-- =====================================================
CREATE TABLE `commentaires` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `article_id`   INT UNSIGNED NOT NULL,
    `user_id`      INT UNSIGNED DEFAULT NULL,
    `auteur_nom`   VARCHAR(100) DEFAULT NULL,
    `auteur_email` VARCHAR(255) DEFAULT NULL,
    `contenu`      TEXT         NOT NULL,
    `statut`       ENUM('en_attente','approuve','rejete','spam') NOT NULL DEFAULT 'en_attente',
    `parent_id`    INT UNSIGNED DEFAULT NULL,
    `ip`           VARCHAR(45)  DEFAULT NULL,
    `cree_le`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_article_id`(`article_id`),
    INDEX `idx_statut`     (`statut`),
    CONSTRAINT `fk_comm_article` FOREIGN KEY (`article_id`) REFERENCES `articles`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_comm_user`    FOREIGN KEY (`user_id`)    REFERENCES `users`(`id`)    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Commentaires des articles du blog';

-- =====================================================
-- TABLE : messages (messagerie interne sécurisée)
-- =====================================================
CREATE TABLE `messages` (
    `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `expediteur_id`    INT UNSIGNED DEFAULT NULL,
    `destinataire_id`  INT UNSIGNED NOT NULL,
    `sujet`            VARCHAR(255) NOT NULL,
    `contenu`          TEXT         NOT NULL,
    `lu`               TINYINT(1)   NOT NULL DEFAULT 0,
    `lu_le`            DATETIME     DEFAULT NULL,
    `archive_exp`      TINYINT(1)   NOT NULL DEFAULT 0,
    `archive_dest`     TINYINT(1)   NOT NULL DEFAULT 0,
    `parent_id`        INT UNSIGNED DEFAULT NULL,
    `cree_le`          DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_expediteur_id`  (`expediteur_id`),
    INDEX `idx_destinataire_id`(`destinataire_id`),
    INDEX `idx_lu`             (`lu`),
    CONSTRAINT `fk_msg_exp`  FOREIGN KEY (`expediteur_id`)   REFERENCES `users`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_msg_dest` FOREIGN KEY (`destinataire_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Messagerie interne sécurisée entre utilisateurs';

-- =====================================================
-- TABLE : notifications (notifications in-app)
-- =====================================================
CREATE TABLE `notifications` (
    `id`       INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`  INT UNSIGNED NOT NULL,
    `titre`    VARCHAR(255) NOT NULL,
    `message`  TEXT         NOT NULL,
    `type`     ENUM('info','succes','avertissement','erreur','don','aide','message','systeme','validation') NOT NULL DEFAULT 'info',
    `lien`     VARCHAR(500) DEFAULT NULL,
    `icone`    VARCHAR(50)  DEFAULT NULL,
    `lu`       TINYINT(1)   NOT NULL DEFAULT 0,
    `lu_le`    DATETIME     DEFAULT NULL,
    `cree_le`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_lu`      (`lu`),
    INDEX `idx_cree_le` (`cree_le`),
    CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Notifications en temps réel pour tous les utilisateurs';

-- =====================================================
-- TABLE : journal_audit (journal complet des actions)
-- =====================================================
CREATE TABLE `journal_audit` (
    `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`           INT UNSIGNED DEFAULT NULL,
    `action`            VARCHAR(100) NOT NULL,
    `module`            VARCHAR(100) DEFAULT NULL,
    `table_concernee`   VARCHAR(100) DEFAULT NULL,
    `enregistrement_id` INT UNSIGNED DEFAULT NULL,
    `anciennes_valeurs` JSON         DEFAULT NULL,
    `nouvelles_valeurs` JSON         DEFAULT NULL,
    `ip`                VARCHAR(45)  DEFAULT NULL,
    `user_agent`        TEXT         DEFAULT NULL,
    `details`           TEXT         DEFAULT NULL,
    `severite`          ENUM('info','attention','critique') NOT NULL DEFAULT 'info',
    `cree_le`           DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_user_id`  (`user_id`),
    INDEX `idx_action`   (`action`),
    INDEX `idx_module`   (`module`),
    INDEX `idx_severite` (`severite`),
    INDEX `idx_cree_le`  (`cree_le`),
    CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Journal d\'audit complet de toutes les actions importantes';

-- =====================================================
-- TABLE : pages_cms (pages gérées par le CMS)
-- =====================================================
CREATE TABLE `pages_cms` (
    `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `titre`            VARCHAR(255) NOT NULL,
    `slug`             VARCHAR(255) NOT NULL,
    `contenu`          LONGTEXT     DEFAULT NULL,
    `contenu_json`     JSON         DEFAULT NULL COMMENT 'Contenu structuré par sections',
    `meta_titre`       VARCHAR(255) DEFAULT NULL,
    `meta_description` TEXT         DEFAULT NULL,
    `statut`           ENUM('publie','brouillon') NOT NULL DEFAULT 'publie',
    `modifiable`       TINYINT(1)   NOT NULL DEFAULT 1,
    `template`         VARCHAR(100) DEFAULT 'default',
    `modifie_par`      INT UNSIGNED DEFAULT NULL,
    `cree_le`          DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modifie_le`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Pages du site gérées via le CMS administrateur';

-- =====================================================
-- TABLE : parametres (paramètres globaux du site)
-- =====================================================
CREATE TABLE `parametres` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `cle`         VARCHAR(100) NOT NULL,
    `valeur`      TEXT         DEFAULT NULL,
    `type`        ENUM('texte','nombre','booleen','json','image','email','url','couleur') NOT NULL DEFAULT 'texte',
    `groupe`      VARCHAR(100) DEFAULT 'general',
    `label`       VARCHAR(255) DEFAULT NULL,
    `description` TEXT         DEFAULT NULL,
    `modifiable`  TINYINT(1)   NOT NULL DEFAULT 1,
    `cree_le`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modifie_le`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_cle`  (`cle`),
    INDEX `idx_groupe` (`groupe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Paramètres globaux configurables de la plateforme';

-- =====================================================
-- TABLE : temoignages (témoignages de bénéficiaires et donateurs)
-- =====================================================
CREATE TABLE `temoignages` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`      INT UNSIGNED DEFAULT NULL,
    `nom_affiche`  VARCHAR(100) NOT NULL COMMENT 'Nom anonymisé ou réel selon choix',
    `role`         VARCHAR(100) DEFAULT NULL COMMENT 'Ex: Bénéficiaire, Donateur...',
    `photo`        VARCHAR(255) DEFAULT NULL,
    `contenu`      TEXT         NOT NULL,
    `note`         TINYINT(1)   DEFAULT 5,
    `pays`         VARCHAR(100) DEFAULT NULL,
    `statut`       ENUM('en_attente','approuve','rejete') NOT NULL DEFAULT 'en_attente',
    `featured`     TINYINT(1)   NOT NULL DEFAULT 0,
    `ordre`        INT          NOT NULL DEFAULT 0,
    `cree_le`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_statut` (`statut`),
    CONSTRAINT `fk_temoignage_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Témoignages approuvés des bénéficiaires et donateurs';

-- =====================================================
-- TABLE : faq (questions fréquentes)
-- =====================================================
CREATE TABLE `faq` (
    `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `question`  TEXT         NOT NULL,
    `reponse`   TEXT         NOT NULL,
    `categorie` VARCHAR(100) NOT NULL DEFAULT 'general',
    `ordre`     INT          NOT NULL DEFAULT 0,
    `actif`     TINYINT(1)   NOT NULL DEFAULT 1,
    `vues`      INT UNSIGNED NOT NULL DEFAULT 0,
    `cree_le`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modifie_le`DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_categorie`(`categorie`),
    INDEX `idx_actif`    (`actif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Foire aux questions gérée par l\'administrateur';

-- =====================================================
-- TABLE : contacts (messages du formulaire public)
-- =====================================================
CREATE TABLE `contacts` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nom`          VARCHAR(200) NOT NULL,
    `email`        VARCHAR(255) NOT NULL,
    `telephone`    VARCHAR(20)  DEFAULT NULL,
    `sujet`        VARCHAR(255) NOT NULL,
    `message`      TEXT         NOT NULL,
    `statut`       ENUM('nouveau','lu','repondu','archive','spam') NOT NULL DEFAULT 'nouveau',
    `ip`           VARCHAR(45)  DEFAULT NULL,
    `reponse`      TEXT         DEFAULT NULL,
    `repondu_par`  INT UNSIGNED DEFAULT NULL,
    `repondu_le`   DATETIME     DEFAULT NULL,
    `cree_le`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_statut`  (`statut`),
    INDEX `idx_cree_le` (`cree_le`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Messages reçus via le formulaire de contact public';

-- =====================================================
-- TABLE : statistiques_quotidiennes (agrégats journaliers)
-- =====================================================
CREATE TABLE `statistiques_quotidiennes` (
    `id`                   INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `date`                 DATE          NOT NULL,
    `total_dons`           DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `nombre_dons`          INT UNSIGNED  NOT NULL DEFAULT 0,
    `nouveaux_beneficiaires`INT UNSIGNED NOT NULL DEFAULT 0,
    `aides_accordees`      INT UNSIGNED  NOT NULL DEFAULT 0,
    `montant_aides`        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `nouveaux_donateurs`   INT UNSIGNED  NOT NULL DEFAULT 0,
    `nouveaux_partenaires` INT UNSIGNED  NOT NULL DEFAULT 0,
    `visites`              INT UNSIGNED  NOT NULL DEFAULT 0,
    `cree_le`              DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Statistiques agrégées par jour pour les graphiques';

-- =====================================================
-- INSERTION DES DONNÉES INITIALES
-- =====================================================

-- ---- Paramètres globaux du site ----
INSERT INTO `parametres` (`cle`, `valeur`, `type`, `groupe`, `label`, `description`) VALUES
-- Groupe : Général
('site_nom',           'EuroCare Humanitaire',                                 'texte',   'general',  'Nom du site',          'Nom principal de l\'organisation'),
('site_slogan',        'Ensemble pour un monde plus juste',                    'texte',   'general',  'Slogan principal',     'Slogan affiché sur la page d\'accueil'),
('site_description',   'Organisation humanitaire européenne d\'assistance sociale dédiée aux personnes vulnérables', 'texte', 'general', 'Description', 'Description courte du site'),
('site_logo',          '',                                                      'image',   'general',  'Logo',                 'Logo principal de l\'organisation'),
('site_favicon',       '',                                                      'image',   'general',  'Favicon',              'Icône du navigateur'),
('fondation_annee',    '2010',                                                  'nombre',  'general',  'Année de fondation',   'Année de création de l\'organisation'),
-- Groupe : Contact
('site_email',         'contact@eurocare-humanitaire.eu',                      'email',   'contact',  'Email principal',      'Adresse email principale'),
('site_email_dons',    'dons@eurocare-humanitaire.eu',                         'email',   'contact',  'Email dons',           'Email pour les questions de dons'),
('site_telephone',     '+33 1 23 45 67 89',                                    'texte',   'contact',  'Téléphone',            'Numéro de téléphone principal'),
('site_adresse',       '12 Rue de la Solidarité, 75001 Paris, France',         'texte',   'contact',  'Adresse',              'Adresse physique du siège'),
('site_horaires',      'Lundi - Vendredi : 9h00 - 18h00',                      'texte',   'contact',  'Horaires',             'Horaires d\'ouverture'),
-- Groupe : Statistiques (transparence)
('total_dons_cumul',   '0',                                                    'nombre',  'stats',    'Total dons cumulés',   'Montant total des dons reçus depuis le début'),
('total_beneficiaires','0',                                                    'nombre',  'stats',    'Total bénéficiaires',  'Nombre total de bénéficiaires aidés'),
('total_partenaires',  '0',                                                    'nombre',  'stats',    'Total partenaires',    'Nombre de partenaires actifs'),
('taux_redistribution','92',                                                   'nombre',  'stats',    'Taux de redistribution','% des dons redistribués directement'),
-- Groupe : Système
('maintenance_mode',   '0',                                                    'booleen', 'systeme',  'Mode maintenance',     '1 pour activer le mode maintenance'),
('inscription_ouverte','1',                                                    'booleen', 'systeme',  'Inscriptions ouvertes','0 pour fermer les inscriptions'),
('commentaires_actifs','1',                                                    'booleen', 'systeme',  'Commentaires actifs',  'Activer les commentaires sur le blog'),
('don_minimum',        '5',                                                    'nombre',  'systeme',  'Don minimum (€)',       'Montant minimum d\'un don'),
-- Groupe : Email SMTP
('smtp_host',          '',                                                     'texte',   'email',    'Serveur SMTP',         'Adresse du serveur SMTP'),
('smtp_port',          '587',                                                  'nombre',  'email',    'Port SMTP',            'Port du serveur SMTP'),
('smtp_user',          '',                                                     'texte',   'email',    'Utilisateur SMTP',     'Identifiant SMTP'),
('smtp_password',      '',                                                     'texte',   'email',    'Mot de passe SMTP',    'Mot de passe SMTP (chiffré)'),
('smtp_from_name',     'EuroCare Humanitaire',                                 'texte',   'email',    'Nom expéditeur',       'Nom affiché dans les emails'),
-- Groupe : Réseaux sociaux
('facebook_url',       '',                                                     'url',     'reseaux',  'Facebook',             'URL de la page Facebook'),
('twitter_url',        '',                                                     'url',     'reseaux',  'Twitter/X',            'URL du profil Twitter/X'),
('instagram_url',      '',                                                     'url',     'reseaux',  'Instagram',            'URL du profil Instagram'),
('linkedin_url',       '',                                                     'url',     'reseaux',  'LinkedIn',             'URL de la page LinkedIn'),
('youtube_url',        '',                                                     'url',     'reseaux',  'YouTube',              'URL de la chaîne YouTube');

-- ---- Catégories d'articles ----
INSERT INTO `categories_articles` (`nom`, `slug`, `description`, `couleur`, `ordre`) VALUES
('Actualités',          'actualites',       'Dernières nouvelles de l\'organisation',         '#1a56db', 1),
('Actions de terrain',  'actions-terrain',  'Nos interventions et missions humanitaires',     '#057a55', 2),
('Témoignages',         'temoignages',      'Histoires inspirantes de nos bénéficiaires',     '#c27803', 3),
('Partenariats',        'partenariats',     'Nouvelles de nos partenaires institutionnels',   '#5850ec', 4),
('Transparence',        'transparence',     'Rapports financiers et d\'activités',            '#0e9f6e', 5),
('Événements',          'evenements',       'Événements et mobilisations à venir',            '#e02424', 6);

-- ---- FAQ par défaut ----
INSERT INTO `faq` (`question`, `reponse`, `categorie`, `ordre`) VALUES
('Comment faire un don ?',
 'Vous pouvez faire un don via notre page "Faire un don". Choisissez le montant souhaité, sélectionnez une cause à soutenir et effectuez votre paiement de manière sécurisée. Vous pouvez également faire des dons récurrents (mensuels ou annuels).',
 'dons', 1),
('Mon don est-il déductible des impôts ?',
 'Oui ! Vos dons sont déductibles à hauteur de 66% de votre impôt sur le revenu, dans la limite de 20% de votre revenu imposable. Un reçu fiscal officiel vous sera envoyé automatiquement par email après chaque don validé.',
 'dons', 2),
('Comment puis-je suivre l\'utilisation de mes dons ?',
 'Notre section Transparence financière vous permet de suivre en temps réel l\'utilisation des fonds collectés. Vous y trouverez le total redistribué, les projets financés et les dépenses détaillées. Depuis votre espace donateur, vous pouvez également voir l\'impact direct de vos contributions.',
 'dons', 3),
('Comment demander une aide ?',
 'Créez un compte bénéficiaire sur notre plateforme, puis remplissez votre dossier social en décrivant votre situation et en joignant les documents justificatifs nécessaires. Notre équipe sociale étudiera votre demande dans les meilleurs délais.',
 'aide', 4),
('Combien de temps prend l\'étude d\'un dossier ?',
 'L\'étude d\'un dossier prend généralement entre 5 et 15 jours ouvrables selon la complexité de la situation et le niveau d\'urgence. Les dossiers classés "prioritaires" ou "critiques" sont traités sous 48h.',
 'aide', 5),
('Quels types d\'aides proposez-vous ?',
 'Nous proposons plusieurs types d\'aides : aide financière directe, aide alimentaire, accompagnement médical, soutien scolaire, aide au logement, fourniture de matériel et accompagnement psychologique et juridique.',
 'aide', 6),
('Comment devenir partenaire institutionnel ?',
 'Inscrivez-vous via notre formulaire dédié aux partenaires. Renseignez les informations de votre organisation (type, domaines d\'action, coordonnées). Après validation par notre équipe, vous pourrez collaborer avec nous et soumettre des recommandations pour des bénéficiaires.',
 'partenariat', 7),
('L\'organisation est-elle certifiée ?',
 'Oui, EuroCare Humanitaire est reconnue d\'utilité publique et dispose de l\'agrément "Don en confiance". Nous sommes également certifiés ISO 9001 pour la qualité de nos processus et respectons strictement le RGPD pour la protection des données.',
 'general', 8),
('Comment protégez-vous mes données personnelles ?',
 'Nous respectons strictement le Règlement Général sur la Protection des Données (RGPD). Vos données sont chiffrées, anonymisées pour les rapports publics, et ne sont jamais vendues à des tiers. Vous pouvez exercer vos droits d\'accès, rectification et suppression depuis votre espace personnel.',
 'general', 9),
('Puis-je faire un don anonyme ?',
 'Oui, lors du processus de don, vous pouvez cocher l\'option "Don anonyme". Votre identité ne sera pas associée au don dans les rapports publics. Vous recevrez quand même votre reçu fiscal si vous avez renseigné votre email.',
 'dons', 10);

-- ---- Témoignages par défaut ----
INSERT INTO `temoignages` (`nom_affiche`, `role`, `contenu`, `note`, `pays`, `statut`, `featured`, `ordre`) VALUES
('Marie L.', 'Bénéficiaire - Mère de famille', 'Grâce à EuroCare, mes deux enfants ont pu continuer leur scolarité. L\'aide reçue a changé notre vie du tout au tout. Je ne sais pas ce que nous aurions fait sans ce soutien précieux.', 5, 'France', 'approuve', 1, 1),
('Thomas R.', 'Donateur régulier', 'Je contribue chaque mois depuis 3 ans. La transparence totale sur l\'utilisation des fonds me donne entière confiance. Je vois concrètement où va mon argent et l\'impact qu\'il a.', 5, 'Belgique', 'approuve', 1, 2),
('Dr. Sophie M.', 'Partenaire - Responsable ONG', 'Notre collaboration avec EuroCare est exemplaire. Les processus sont rigoureux, les équipes professionnelles et l\'impact sur le terrain est réel et mesurable.', 5, 'Suisse', 'approuve', 1, 3),
('Ahmed K.', 'Bénéficiaire', 'Après mon accident, je n\'avais plus de revenus. EuroCare m\'a soutenu pendant ma convalescence, m\'a aidé à retrouver un logement. Aujourd\'hui je suis de nouveau autonome. Merci infiniment.', 5, 'France', 'approuve', 0, 4);

-- ---- Projets par défaut ----
INSERT INTO `projets` (`titre`, `slug`, `description`, `description_courte`, `objectif_montant`, `categorie`, `statut`, `featured`, `ordre`) VALUES
('Soutien aux orphelins d\'Europe', 'soutien-orphelins-europe',
 'Ce programme vise à assurer un avenir digne aux enfants orphelins et vulnérables en Europe. Nous finançons leur scolarité, leur alimentation, leur accès aux soins médicaux et leur accompagnement psychologique.',
 'Assurer un avenir digne aux enfants les plus vulnérables d\'Europe.',
 50000.00, 'enfance', 'actif', 1, 1),
('Aide d\'urgence aux familles en détresse', 'aide-urgence-familles',
 'Un fonds d\'urgence permettant d\'intervenir rapidement auprès des familles confrontées à des situations de crise aiguë : perte d\'emploi soudaine, expulsion, catastrophe personnelle.',
 'Intervention rapide auprès des familles en situation de crise.',
 30000.00, 'urgence', 'actif', 1, 2),
('Accès aux soins pour les plus démunis', 'acces-soins-demunis',
 'Programme permettant aux personnes sans couverture médicale suffisante d\'accéder à des soins essentiels : consultations médicales, médicaments, hospitalisations d\'urgence.',
 'Garantir l\'accès aux soins pour ceux qui ne peuvent pas se soigner.',
 25000.00, 'sante', 'actif', 1, 3),
('Réinsertion professionnelle', 'reinsertion-professionnelle',
 'Accompagnement et financement de formations professionnelles pour les personnes éloignées du marché de l\'emploi, avec un suivi personnalisé jusqu\'à la reprise d\'activité.',
 'Aider les personnes à retrouver une autonomie économique.',
 20000.00, 'emploi', 'actif', 0, 4);

-- ---- Administrateur principal (mot de passe : Admin@2024!) ----
-- Hash bcrypt généré avec cost=12 pour 'Admin@2024!'
INSERT INTO `users`
    (`uuid`, `email`, `password`, `role`, `statut`, `prenom`, `nom`, `email_verifie`, `pays`, `ville`)
VALUES
    (UUID(), 'admin@eurocare-humanitaire.eu',
     '$2y$12$LKVEGsPgMzG7oABQNXCiNOJyU0MVl.GRl8K3u.sRJvtYNJPOTXnOW',
     'admin', 'actif', 'Administrateur', 'Principal', 1, 'France', 'Paris');

SET FOREIGN_KEY_CHECKS = 1;
