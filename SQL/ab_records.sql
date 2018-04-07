#
# Table structure for table `ab_records`
#

CREATE TABLE `ab_records` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `CreationDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `ModifyDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `ModifiedBy` varchar(15) NOT NULL default '',
  `FirstName` varchar(100) NOT NULL default '',
  `LastName` varchar(100) NOT NULL default '',
  `Address1` varchar(100) NOT NULL default '',
  `Address2` varchar(100) NOT NULL default '',
  `City` varchar(100) NOT NULL default '',
  `State` varchar(100) NOT NULL default '',
  `PostalCode` varchar(10) NOT NULL default '',
  `Country` varchar(50) NOT NULL default '',
  `HomePh` varchar(15) NOT NULL default '',
  `WorkPh` varchar(15) NOT NULL default '',
  `MobilePh` varchar(15) NOT NULL default '',
  `OtherPh` varchar(15) NOT NULL default '',
  `WebSite` varchar(100) NOT NULL default 'http://',
  `ICQ` varchar(15) NOT NULL default '',
  `AIM` varchar(20) NOT NULL default '',
  `Yahoo` varchar(20) NOT NULL default '',
  `Email1` varchar(100) NOT NULL default '',
  `Email2` varchar(100) NOT NULL default '',
  `Comments` text NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM;

