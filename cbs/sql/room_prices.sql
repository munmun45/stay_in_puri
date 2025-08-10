CREATE TABLE IF NOT EXISTS `room_prices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `main_price` decimal(10,2) NOT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `cp` decimal(10,2) DEFAULT NULL COMMENT 'Cost Price',
  `map` decimal(10,2) DEFAULT NULL COMMENT 'Minimum Advertised Price',
  `mvp` decimal(10,2) DEFAULT NULL COMMENT 'Minimum Viable Price',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `room_id` (`room_id`),
  CONSTRAINT `room_prices_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
