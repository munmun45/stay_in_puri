CREATE TABLE IF NOT EXISTS `room_tariffs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `day_type` enum('weekday','weekend','holiday') NOT NULL DEFAULT 'weekday',
  `price` decimal(10,2) NOT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `cp` decimal(10,2) DEFAULT NULL COMMENT 'Cost Price',
  `map` decimal(10,2) DEFAULT NULL COMMENT 'Minimum Advertised Price',
  `mvp` decimal(10,2) DEFAULT NULL COMMENT 'Minimum Viable Price',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `room_id` (`room_id`),
  KEY `date_range` (`start_date`, `end_date`),
  KEY `day_type` (`day_type`),
  CONSTRAINT `room_tariffs_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
