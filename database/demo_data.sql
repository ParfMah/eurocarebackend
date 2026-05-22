-- =====================================================
-- EUROCARE HUMANITAIRE — Données de démonstration
-- Fichier : database/demo_data.sql
-- Usage : importer APRÈS schema.sql pour tester
-- =====================================================

-- Désactiver les contraintes temporairement
SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- UTILISATEURS DE DÉMONSTRATION
-- =====================================================
-- Mot de passe commun pour tous : Demo@2024!
-- Hash bcrypt : $2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- (c'est le hash de "password" avec cost=12 — pour démo uniquement)

INSERT INTO `users` (`uuid`, `email`, `password`, `role`, `statut`, `prenom`, `nom`, `email_verifie`, `pays`, `ville`) VALUES
-- Modérateur
(UUID(), 'moderateur@eurocare.eu', '$2y$12$LKVEGsPgMzG7oABQNXCiNOJyU0MVl.GRl8K3u.sRJvtYNJPOTXnOW', 'moderateur', 'actif', 'Marie', 'Laurent', 1, 'France', 'Lyon'),
-- Donateur 1
(UUID(), 'donateur1@example.com', '$2y$12$LKVEGsPgMzG7oABQNXCiNOJyU0MVl.GRl8K3u.sRJvtYNJPOTXnOW', 'donateur', 'actif', 'Thomas', 'Renault', 1, 'France', 'Paris'),
-- Donateur 2
(UUID(), 'donateur2@example.com', '$2y$12$LKVEGsPgMzG7oABQNXCiNOJyU0MVl.GRl8K3u.sRJvtYNJPOTXnOW', 'donateur', 'actif', 'Sophie', 'Bernard', 1, 'Belgique', 'Bruxelles'),
-- Bénéficiaire 1
(UUID(), 'beneficiaire1@example.com', '$2y$12$LKVEGsPgMzG7oABQNXCiNOJyU0MVl.GRl8K3u.sRJvtYNJPOTXnOW', 'beneficiaire', 'actif', 'Marie', 'Dupont', 1, 'France', 'Marseille'),
-- Bénéficiaire 2
(UUID(), 'beneficiaire2@example.com', '$2y$12$LKVEGsPgMzG7oABQNXCiNOJyU0MVl.GRl8K3u.sRJvtYNJPOTXnOW', 'beneficiaire', 'actif', 'Ahmed', 'Karim', 1, 'France', 'Lille'),
-- Partenaire
(UUID(), 'partenaire@ong-exemple.eu', '$2y$12$LKVEGsPgMzG7oABQNXCiNOJyU0MVl.GRl8K3u.sRJvtYNJPOTXnOW', 'partenaire', 'actif', 'Jean', 'Martin', 1, 'France', 'Bordeaux');

-- Récupérer les IDs (utiliser des variables pour la cohérence)
SET @id_moderateur   = (SELECT id FROM users WHERE email = 'moderateur@eurocare.eu');
SET @id_donateur1    = (SELECT id FROM users WHERE email = 'donateur1@example.com');
SET @id_donateur2    = (SELECT id FROM users WHERE email = 'donateur2@example.com');
SET @id_benef1       = (SELECT id FROM users WHERE email = 'beneficiaire1@example.com');
SET @id_benef2       = (SELECT id FROM users WHERE email = 'beneficiaire2@example.com');
SET @id_partenaire   = (SELECT id FROM users WHERE email = 'partenaire@ong-exemple.eu');
SET @id_admin        = (SELECT id FROM users WHERE role = 'admin' LIMIT 1);

-- =====================================================
-- DONS DE DÉMONSTRATION
-- =====================================================
INSERT INTO `dons` (`uuid`, `user_id`, `montant`, `devise`, `type`, `cause`, `projet_id`, `statut`, `valide_le`, `cree_le`) VALUES
(UUID(), @id_donateur1, 50.00, 'EUR', 'ponctuel', 'Aide orphelins', 1, 'valide', NOW() - INTERVAL 5 DAY, NOW() - INTERVAL 5 DAY),
(UUID(), @id_donateur1, 100.00,'EUR', 'mensuel',  'Aide d\'urgence',2, 'valide', NOW() - INTERVAL 35 DAY,NOW() - INTERVAL 35 DAY),
(UUID(), @id_donateur1, 100.00,'EUR', 'mensuel',  'Aide d\'urgence',2, 'valide', NOW() - INTERVAL 5 DAY, NOW() - INTERVAL 5 DAY),
(UUID(), @id_donateur2, 25.00, 'EUR', 'ponctuel', NULL, 1, 'valide',  NOW() - INTERVAL 10 DAY,NOW() - INTERVAL 10 DAY),
(UUID(), @id_donateur2, 250.00,'EUR', 'ponctuel', 'Accès soins',  3, 'valide', NOW() - INTERVAL 20 DAY,NOW() - INTERVAL 20 DAY),
(UUID(), NULL, 30.00, 'EUR', 'ponctuel', NULL, NULL, 'valide', NOW() - INTERVAL 2 DAY, NOW() - INTERVAL 2 DAY),
(UUID(), @id_donateur1, 75.00, 'EUR', 'ponctuel', NULL, NULL, 'en_attente', NULL, NOW());

-- Mettre à jour les montants collectés des projets
UPDATE projets SET montant_collecte = 75.00  WHERE id = 1;
UPDATE projets SET montant_collecte = 200.00 WHERE id = 2;
UPDATE projets SET montant_collecte = 250.00 WHERE id = 3;

-- =====================================================
-- DOSSIERS BÉNÉFICIAIRES
-- =====================================================
INSERT INTO `beneficiaires_profils`
(`user_id`, `numero_dossier`, `type_beneficiaire`, `situation_familiale`, `nombre_enfants`,
 `revenus_mensuels`, `situation_logement`, `besoins_principaux`, `description_situation`,
 `niveau_urgence`, `statut_dossier`, `valide_par`, `valide_le`, `cree_le`) VALUES
(
  @id_benef1, 'EC-2024-A1B2C3', 'famille_en_difficulte', 'marie', 2,
  650.00, 'locataire',
  'Aide alimentaire, aide scolaire pour les enfants',
  'Suite à la perte d\'emploi de mon mari il y a 6 mois, nos revenus ont drastiquement chuté. Nous avons deux enfants scolarisés et peinons à couvrir les dépenses de base. Les factures s\'accumulent et nous risquons l\'expulsion.',
  'eleve', 'prioritaire', @id_admin, NOW() - INTERVAL 3 DAY, NOW() - INTERVAL 10 DAY
),
(
  @id_benef2, 'EC-2024-D4E5F6', 'personne_handicapee', 'celibataire', 0,
  800.00, 'locataire',
  'Aide médicale, aide pour l\'adaptation du logement',
  'Accident de travail il y a 8 mois. Partiellement invalide, je ne peux plus exercer mon métier. Ma couverture médicale ne prend pas en charge tous les soins nécessaires à ma rééducation.',
  'critique', 'en_etude', NULL, NULL, NOW() - INTERVAL 2 DAY
);

SET @id_dossier1 = (SELECT id FROM beneficiaires_profils WHERE user_id = @id_benef1);
SET @id_dossier2 = (SELECT id FROM beneficiaires_profils WHERE user_id = @id_benef2);

-- =====================================================
-- AIDES ACCORDÉES
-- =====================================================
INSERT INTO `aides_octroyees`
(`beneficiaire_id`, `projet_id`, `type_aide`, `montant`, `description`, `statut`, `accorde_par`, `date_attribution`, `cree_le`) VALUES
(
  @id_dossier1, 2, 'financiere', 200.00,
  'Aide financière d\'urgence pour couvrir les charges du mois de novembre. Virement effectué sur le compte bancaire du bénéficiaire.',
  'complete', @id_admin, CURDATE() - INTERVAL 5 DAY, NOW() - INTERVAL 5 DAY
),
(
  @id_dossier1, NULL, 'alimentaire', NULL,
  'Bon d\'achat alimentaire de 80€ valable chez Carrefour, émis pour la semaine du 18 au 25 novembre.',
  'complete', @id_moderateur, CURDATE() - INTERVAL 2 DAY, NOW() - INTERVAL 2 DAY
);

-- =====================================================
-- PARTENAIRE ORGANISATION
-- =====================================================
INSERT INTO `partenaires_profils`
(`user_id`, `nom_organisation`, `type_organisation`, `numero_enregistrement`,
 `description`, `domaines_action`, `pays`, `ville`, `telephone`, `email_contact`,
 `statut`, `valide_par`, `valide_le`, `featured`, `cree_le`) VALUES
(
  @id_partenaire, 'ONG Solidarité Europe', 'ong', 'W-2024-06789',
  'ONG dédiée à l\'insertion professionnelle des personnes en situation précaire en Europe de l\'Ouest.',
  'Emploi, formation professionnelle, accompagnement social',
  'France', 'Bordeaux', '+33 5 56 00 00 00', 'contact@solidarite-europe.org',
  'valide', @id_admin, NOW() - INTERVAL 15 DAY, 1, NOW() - INTERVAL 20 DAY
);

-- =====================================================
-- ARTICLE DE BLOG
-- =====================================================
INSERT INTO `articles`
(`titre`, `slug`, `contenu`, `extrait`, `auteur_id`, `categorie_id`, `statut`, `featured`, `vues`, `publie_le`, `cree_le`) VALUES
(
  'Notre bilan annuel 2024 : 1 240 personnes aidées',
  'bilan-annuel-2024',
  '<h2>Une année de mobilisation sans précédent</h2>
  <p>En 2024, EuroCare Humanitaire a atteint des records dans son action humanitaire. Grâce à la générosité de nos donateurs et à l\'engagement de nos équipes, nous avons pu apporter une aide concrète à 1 240 personnes en situation de vulnérabilité à travers l\'Europe.</p>
  <h2>Répartition des aides accordées</h2>
  <p>Parmi les bénéficiaires accompagnés cette année, 38% étaient des familles monoparentales, 22% des personnes âgées isolées, 18% des orphelins et mineurs vulnérables, et 22% des adultes en situation de précarité.</p>
  <h2>Vos dons en action</h2>
  <p>92% des fonds collectés ont été directement redistribués sous forme d\'aides financières, alimentaires, médicales et éducatives. Nous remercions chacun de nos 850 donateurs pour leur confiance renouvelée.</p>',
  'En 2024, EuroCare Humanitaire a accompagné 1 240 personnes vulnérables à travers l\'Europe. Retour sur une année de mobilisation exceptionnelle.',
  @id_admin, 5, 'publie', 1, 234, NOW() - INTERVAL 7 DAY, NOW() - INTERVAL 7 DAY
),
(
  'Comment nous sélectionnons les bénéficiaires ?',
  'selection-beneficiaires-criteres',
  '<h2>Un processus rigoureux et équitable</h2>
  <p>La sélection des bénéficiaires est au cœur de notre engagement de transparence. Chaque dossier est étudié selon des critères objectifs : niveau d\'urgence, situation familiale, revenus, besoins spécifiques et disponibilité des ressources.</p>
  <h2>Les étapes du traitement</h2>
  <p>1. Dépôt du dossier en ligne avec justificatifs<br>2. Étude par notre équipe de travailleurs sociaux<br>3. Vérification des informations<br>4. Attribution selon le niveau de priorité<br>5. Notification et attribution de l\'aide</p>',
  'Découvrez comment nous traitons les demandes d\'aide avec équité et rigueur, dans le respect de la dignité de chaque bénéficiaire.',
  @id_moderateur, 1, 'publie', 0, 89, NOW() - INTERVAL 14 DAY, NOW() - INTERVAL 14 DAY
);

-- =====================================================
-- REMETTRE LES CONTRAINTES
-- =====================================================
SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- RÉSUMÉ DES COMPTES DE TEST
-- =====================================================
-- admin@eurocare-humanitaire.eu  / Admin@2024!   → Administrateur
-- moderateur@eurocare.eu         / Admin@2024!   → Modérateur
-- donateur1@example.com          / Admin@2024!   → Donateur (250€ de dons)
-- donateur2@example.com          / Admin@2024!   → Donateur (275€ de dons)
-- beneficiaire1@example.com      / Admin@2024!   → Bénéficiaire (dossier prioritaire)
-- beneficiaire2@example.com      / Admin@2024!   → Bénéficiaire (dossier en étude)
-- partenaire@ong-exemple.eu      / Admin@2024!   → Partenaire ONG validé
