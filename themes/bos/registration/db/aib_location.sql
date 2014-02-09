CREATE TABLE `aib_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` tinytext,
  `title` varchar(255) DEFAULT NULL,
  `address` text,
  `zip` varchar(5) DEFAULT NULL,
  `lat` float DEFAULT NULL,
  `lng` float DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
