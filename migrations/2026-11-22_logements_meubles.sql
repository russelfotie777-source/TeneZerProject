-- Rubrique « Logements meublés » : les meublés deviennent une catégorie de `properties`
-- publiée par l'administration, avec leurs informations de sécurité et d'accès.
-- À exécuter une fois sur la base existante (phpMyAdmin > SQL) avant de déployer le code.

ALTER TABLE `properties`
  MODIFY `category` enum('logement','terrain','meuble') NOT NULL DEFAULT 'logement',
  ADD COLUMN `price_period` enum('jour','mois') DEFAULT NULL AFTER `price`,
  ADD COLUMN `has_guard` tinyint(1) NOT NULL DEFAULT 0 AFTER `has_parking`,
  ADD COLUMN `has_secure_gate` tinyint(1) NOT NULL DEFAULT 0 AFTER `has_guard`,
  ADD COLUMN `open_24h` tinyint(1) NOT NULL DEFAULT 0 AFTER `has_secure_gate`;
