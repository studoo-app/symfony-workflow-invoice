

CREATE TABLE `audit` (
  `id` int(11) NOT NULL,
  `date` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `message` longtext COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour la table `audit`
--
ALTER TABLE `audit`
    ADD PRIMARY KEY (`id`);

--
-- Déclencheurs `invoice`
--
DELIMITER $$
CREATE TRIGGER `tracker_audit_insert` AFTER INSERT ON `invoice` FOR EACH ROW BEGIN

INSERT INTO audit VALUES(NULL,NOW(),CONCAT('Une facture a été créee pour le client ',NEW.client));

END
$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER `tracker_audit_maj` AFTER UPDATE ON `invoice` FOR EACH ROW BEGIN

INSERT INTO audit VALUES(NULL,NOW(),CONCAT('Une facture a été modifier pour le client ',NEW.client));

END
$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER `tracker_datemaj` BEFORE UPDATE ON `invoice` FOR EACH ROW BEGIN

set NEW.udpated_at = CURRENT_TIME();

END
$$
DELIMITER ;





