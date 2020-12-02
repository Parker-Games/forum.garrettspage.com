CREATE TABLE IF NOT EXISTS `tiki_encryption_keys` (
  `keyId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `description` text NULL,
  `algo` varchar(50) NULL,
  `shares` int(11) NOT NULL,
  `users` text NULL,
  `secret` varchar(191) NOT NULL,
  PRIMARY KEY  (`keyId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;
