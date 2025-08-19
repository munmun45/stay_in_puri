-- Create contact_info table
CREATE TABLE IF NOT EXISTS `contact_info` (
  `id` INT NOT NULL,
  `phone1` VARCHAR(50) DEFAULT NULL,
  `phone2` VARCHAR(50) DEFAULT NULL,
  `email1` VARCHAR(100) DEFAULT NULL,
  `email2` VARCHAR(100) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `google_map` TEXT DEFAULT NULL,
  `facebook` VARCHAR(255) DEFAULT NULL,
  `twitter` VARCHAR(255) DEFAULT NULL,
  `instagram` VARCHAR(255) DEFAULT NULL,
  `youtube` VARCHAR(255) DEFAULT NULL,
  `whatsapp` VARCHAR(255) DEFAULT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed a default row (id=1) if not exists
INSERT INTO `contact_info` (`id`, `phone1`, `phone2`, `email1`, `email2`, `address`, `google_map`, `facebook`, `twitter`, `instagram`, `youtube`, `whatsapp`)
SELECT 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL
WHERE NOT EXISTS (SELECT 1 FROM `contact_info` WHERE `id` = 1);
