CREATE TABLE `aib_token` (
  `token` varchar(255) NOT NULL DEFAULT '',
  `available` int(11) DEFAULT '1',
  `user_id` int(11) DEFAULT NULL,
  `site_id` int(11) DEFAULT '9',
  `distro` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
