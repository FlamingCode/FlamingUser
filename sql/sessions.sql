CREATE TABLE `sessions` (
  `id` char(32) CHARACTER SET utf8 COLLATE utf8_danish_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_danish_ci NOT NULL,
  `modified` int(11) DEFAULT NULL,
  `lifetime` int(11) DEFAULT NULL,
  `data` text CHARACTER SET utf8 COLLATE utf8_danish_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;