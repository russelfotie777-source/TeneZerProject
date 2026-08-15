-- Tenezer Immobilier — schéma de base de données
-- Remplace l'ancien schéma "MF Appartements / MBoa Market" (apartments, apartment_amenities,
-- amenities, photo2, photos, reservations, customers, blocked_dates, whatsapp_tracking).
-- Table `admins` conservée telle quelle (comptes existants).

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Structure de la table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('super_admin','moderateur','editeur') DEFAULT 'moderateur',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admins` (`id`, `username`, `password`, `full_name`, `role`, `is_active`, `created_at`) VALUES
(1, 'admin', '$2y$10$RUiP8M7kPw8LuQswTt2pe.bUt4EPt03URuexmK/AZDdyYE3g6INWG', 'Esssome Paul', 'moderateur', 1, '2026-04-11 03:20:25'),
(3, 'modérateur', '$2y$10$/rkbASEzNCtTRonF4IahP.iHCoB6HcRz0XVucMWlQAGpQwsQ0Z9iG', 'Daniel steve', 'editeur', 1, '2026-04-12 19:37:48');

-- --------------------------------------------------------

--
-- Structure de la table `owners` (espace propriétaire — module 1)
--

CREATE TABLE `owners` (
  `id` int(11) NOT NULL,
  `agency_name` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `cni_photo_url` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `rejection_reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `properties` (logements à louer + terrains — module 1)
--

CREATE TABLE `properties` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `created_by_admin_id` int(11) DEFAULT NULL,
  `category` enum('logement','terrain') NOT NULL DEFAULT 'logement',
  `type` varchar(30) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `quartier` varchar(100) DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `is_new` tinyint(1) DEFAULT NULL,
  `distance_route` varchar(100) DEFAULT NULL,
  `bedrooms` int(11) DEFAULT NULL,
  `bathrooms` int(11) DEFAULT NULL,
  `surface_chambre` int(11) DEFAULT NULL,
  `surface_salon` int(11) DEFAULT NULL,
  `surface_cuisine` int(11) DEFAULT NULL,
  `superficie_terrain` int(11) DEFAULT NULL,
  `has_parking` tinyint(1) DEFAULT 0,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Contenu de démonstration : biens réalistes publiés par Tenezer (owner_id NULL)
--

INSERT INTO `properties`
(`id`, `owner_id`, `created_by_admin_id`, `category`, `type`, `title`, `description`, `city`, `quartier`, `price`, `is_new`, `distance_route`, `bedrooms`, `bathrooms`, `surface_chambre`, `surface_salon`, `surface_cuisine`, `superficie_terrain`, `has_parking`, `features`, `status`)
VALUES
(1, NULL, 1, 'logement', 'appartement', 'Appartement 2 chambres climatisé à Bonapriso',
 'Bel appartement de 2 chambres au cœur de Bonapriso, quartier calme et recherché de Douala. Climatisation, cuisine équipée et sécurité 24h/24. Idéal pour famille ou professionnel.',
 'Douala', 'Bonapriso', 85000.00, 1, '200m du goudron', 2, 1, 16, 22, 8, NULL, 1,
 '["water","electricity","security","parking","ac","kitchen"]', 'active'),

(2, NULL, 1, 'logement', 'studio', 'Studio meublé en plein centre d\'Akwa',
 'Studio entièrement meublé à deux pas des commerces et bureaux d\'Akwa. Parfait pour une personne seule ou un séjour professionnel de courte à moyenne durée.',
 'Douala', 'Akwa', 45000.00, 0, NULL, 1, 1, NULL, NULL, NULL, NULL, 0,
 '["water","electricity","security","furnished","wifi","tv"]', 'active'),

(3, NULL, 1, 'logement', 'chambre', 'Chambre moderne dans résidence sécurisée à Bali',
 'Chambre spacieuse dans une résidence gardée du quartier Bali. Accès facile, eau et électricité stables, environnement calme.',
 'Douala', 'Bali', 20000.00, 0, NULL, 1, 1, NULL, NULL, NULL, NULL, 0,
 '["water","electricity","security"]', 'active'),

(4, NULL, 1, 'logement', 'bureau', 'Bureau climatisé idéal pour siège d\'entreprise',
 'Espace de bureau climatisé à Ndogbong, zone commerciale dynamique. Parking disponible et groupe électrogène pour une activité sans coupure.',
 'Douala', 'Ndogbong', 120000.00, 1, '100m du goudron', NULL, NULL, NULL, NULL, NULL, NULL, 1,
 '["water","electricity","security","parking","ac","generator"]', 'active'),

(5, NULL, 1, 'logement', 'appartement', 'Appartement de standing 3 chambres à Bastos',
 'Grand appartement 3 chambres dans le quartier huppé de Bastos à Yaoundé. Salon spacieux, cuisine équipée, parking privé et groupe électrogène.',
 'Yaoundé', 'Bastos', 150000.00, 1, NULL, 3, 2, 18, 30, 12, NULL, 1,
 '["water","electricity","security","parking","ac","generator","kitchen","tv"]', 'active'),

(6, NULL, 1, 'logement', 'studio', 'Studio étudiant proche des grandes écoles',
 'Studio pratique et abordable à Biyem-Assi, à proximité des grandes écoles et universités de Yaoundé. Idéal pour étudiants.',
 'Yaoundé', 'Biyem-Assi', 35000.00, 0, NULL, 1, 1, NULL, NULL, NULL, NULL, 0,
 '["water","electricity","wifi"]', 'active'),

(7, NULL, 1, 'logement', 'magasin', 'Local commercial passant à Odza',
 'Magasin bien situé sur un axe passant du quartier Odza à Yaoundé. Idéal pour commerce de détail ou point de vente.',
 'Yaoundé', 'Odza', 100000.00, 0, '10m du goudron', NULL, NULL, NULL, NULL, NULL, NULL, 1,
 '["water","electricity","security","parking"]', 'active'),

(8, NULL, 1, 'logement', 'appartement', 'Appartement 2 chambres au centre-ville de Bafoussam',
 'Appartement confortable de 2 chambres au centre-ville de Bafoussam, proche de toutes commodités. Quartier sécurisé avec parking.',
 'Bafoussam', 'Centre-ville', 60000.00, 0, NULL, 2, 1, 14, 18, 8, NULL, 1,
 '["water","electricity","security","parking"]', 'active'),

(9, NULL, 1, 'logement', 'chambre', 'Chambre moderne proche de l\'université de Buea',
 'Chambre moderne à Molyko, à quelques minutes de l\'université de Buea. Wifi disponible, environnement calme et sécurisé.',
 'Buea', 'Molyko', 25000.00, 0, NULL, 1, 1, NULL, NULL, NULL, NULL, 0,
 '["water","electricity","wifi","security"]', 'active'),

(10, NULL, 1, 'terrain', 'terrain', 'Terrain viabilisé de 400 m² à Ndogpassi',
 'Terrain plat et viabilisé de 400 m² à Ndogpassi, Douala. Eau et électricité déjà disponibles sur place, idéal pour construction résidentielle.',
 'Douala', 'Ndogpassi', 12000000.00, NULL, '50m du goudron', NULL, NULL, NULL, NULL, NULL, 400, 0,
 '["water","electricity","fenced"]', 'active'),

(11, NULL, 1, 'terrain', 'terrain', 'Terrain de 600 m² à Nkolbisson',
 'Beau terrain clôturé de 600 m² à Nkolbisson, Yaoundé. Quartier en plein développement, accès facile.',
 'Yaoundé', 'Nkolbisson', 18000000.00, NULL, '300m du goudron', NULL, NULL, NULL, NULL, NULL, 600, 0,
 '["electricity","borehole","fenced"]', 'active'),

(12, NULL, 1, 'terrain', 'terrain', 'Terrain de 1000 m² vue mer à Kribi',
 'Magnifique terrain de 1000 m² avec vue sur mer à Kribi. Emplacement idéal pour résidence secondaire ou projet touristique.',
 'Kribi', 'Bord de mer', 35000000.00, NULL, '150m du goudron', NULL, NULL, NULL, NULL, NULL, 1000, 0,
 '["water","electricity","fenced"]', 'active');

-- --------------------------------------------------------

--
-- Structure de la table `media` (photos/vidéos, table polymorphe)
--

CREATE TABLE `media` (
  `id` int(11) NOT NULL,
  `entity_type` varchar(50) NOT NULL DEFAULT 'property',
  `entity_id` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `type` enum('photo','video') NOT NULL DEFAULT 'photo',
  `is_main` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `whatsapp_clicks` (tracking des clics WhatsApp, tous modules)
--

CREATE TABLE `whatsapp_clicks` (
  `id` int(11) NOT NULL,
  `entity_type` varchar(50) NOT NULL DEFAULT 'property',
  `entity_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Photos d'illustration temporaires pour les biens de démonstration (à remplacer par les vraies photos)
--

INSERT INTO `media` (`id`, `entity_type`, `entity_id`, `url`, `type`, `is_main`, `sort_order`) VALUES
(1, 'property', 1, 'tnz_property_1_demo.jpg', 'photo', 1, 0),
(2, 'property', 2, 'tnz_property_2_demo.jpg', 'photo', 1, 0),
(3, 'property', 3, 'tnz_property_3_demo.jpg', 'photo', 1, 0),
(4, 'property', 4, 'tnz_property_4_demo.jpg', 'photo', 1, 0),
(5, 'property', 5, 'tnz_property_5_demo.jpg', 'photo', 1, 0),
(6, 'property', 6, 'tnz_property_6_demo.jpg', 'photo', 1, 0),
(7, 'property', 7, 'tnz_property_7_demo.jpg', 'photo', 1, 0),
(8, 'property', 8, 'tnz_property_8_demo.jpg', 'photo', 1, 0),
(9, 'property', 9, 'tnz_property_9_demo.jpg', 'photo', 1, 0),
(10, 'property', 10, 'tnz_property_10_demo.jpg', 'photo', 1, 0),
(11, 'property', 11, 'tnz_property_11_demo.jpg', 'photo', 1, 0),
(12, 'property', 12, 'tnz_property_12_demo.jpg', 'photo', 1, 0);

-- --------------------------------------------------------

--
-- Structure de la table `requests` (demandes des modules 1 à 5 — lead/contact)
--

CREATE TABLE `requests` (
  `id` int(11) NOT NULL,
  `module` enum('location','achat_vente','meuble','service_menager','objet_perdu') NOT NULL,
  `sub_type` varchar(50) DEFAULT NULL,
  `property_id` int(11) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `status` enum('pending','processing','closed') NOT NULL DEFAULT 'pending',
  `handled_by` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `audit_logs` (généralisée à toutes les entités)
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `owners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`);

ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`),
  ADD KEY `created_by_admin_id` (`created_by_admin_id`),
  ADD KEY `category` (`category`),
  ADD KEY `status` (`status`);

ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entity_type_id` (`entity_type`,`entity_id`);

ALTER TABLE `whatsapp_clicks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entity_type_id` (`entity_type`,`entity_id`);

ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`),
  ADD KEY `handled_by` (`handled_by`),
  ADD KEY `module` (`module`),
  ADD KEY `status` (`status`);

ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `entity_type_id` (`entity_type`,`entity_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `owners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

ALTER TABLE `media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

ALTER TABLE `whatsapp_clicks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

ALTER TABLE `properties`
  ADD CONSTRAINT `properties_ibfk_owner` FOREIGN KEY (`owner_id`) REFERENCES `owners` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `properties_ibfk_admin` FOREIGN KEY (`created_by_admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `requests_ibfk_admin` FOREIGN KEY (`handled_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
