-- Create offers table for mapping discounts to hotels and rooms
CREATE TABLE IF NOT EXISTS `offers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hotel_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `discount_percentage` decimal(5,2) NOT NULL,
  `min_amount` decimal(10,2) DEFAULT NULL COMMENT 'Minimum amount up to which discount applies',
  `valid_from` date NOT NULL,
  `valid_to` date NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Status: 1=Active, 0=Inactive',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `hotel_id` (`hotel_id`),
  KEY `room_id` (`room_id`),
  CONSTRAINT `offers_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `offers_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `check_hotel_or_room` CHECK ((`hotel_id` IS NOT NULL OR `room_id` IS NOT NULL))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
