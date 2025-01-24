DROP TABLE IF EXISTS `lyfd_announcements`;
CREATE TABLE `lyfd_announcements` (
  `id` int(11) NOT NULL,
  `callsign` varchar(12) NOT NULL,
  `loc` varchar(6) NOT NULL,
  `band_50` tinyint(1) NOT NULL,
  `band_144` tinyint(1) NOT NULL,
  `band_432` tinyint(1) NOT NULL,
  `band_shf` tinyint(1) NOT NULL,
  `mode_cw` tinyint(1) NOT NULL,
  `mode_ph` tinyint(1) NOT NULL,
  `mode_digi` tinyint(1) NOT NULL,
  `year` int(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

LOCK TABLES `lyfd_announcements` WRITE;
INSERT INTO `lyfd_announcements` VALUES (31,'LY1YZ/P','KO25to',0,1,1,0,0,1,0,2024),(33,'LY5AT/P','KO24jq',0,1,1,1,1,1,0,2024),(32,'LY3UE','KO25xh',0,1,1,1,1,1,0,2024);
UNLOCK TABLES;
