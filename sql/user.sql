CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `password` char(60) COLLATE utf8_general_ci NOT NULL,
  `role` enum('user','admin') COLLATE utf8_general_ci NOT NULL DEFAULT 'user',
  `firstname` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `surname` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `emailConfirmed` tinyint(1) NOT NULL DEFAULT '1',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `forgotPassHash` char(64) COLLATE utf8_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `forgotPassHash` (`forgotPassHash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;