CREATE TABLE IF NOT EXISTS `Wunschplan_delete` (
  `VK` tinyint(11) NOT NULL,
  `Wochentag` tinyint(4) NOT NULL,
  `Dienstbeginn` varchar(64) CHARACTER SET latin1 DEFAULT NULL,
  `Dienstende` varchar(64) CHARACTER SET latin1 DEFAULT NULL,
  `Mittagsbeginn` varchar(64) CHARACTER SET latin1 DEFAULT NULL,
  `Mittagsende` varchar(64) CHARACTER SET latin1 DEFAULT NULL,
  `Kommentar` text CHARACTER SET latin1,
  `Stunden` varchar(64) CHARACTER SET latin1 DEFAULT NULL,
  `Mandant` varchar(64) CHARACTER SET latin1 NOT NULL DEFAULT '1',
  PRIMARY KEY (`VK`,`Wochentag`,`Mandant`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci