#
# Table structure for table `ab_auth_user`
#

CREATE TABLE `ab_auth_user` (
  `uid` tinyint(3) unsigned NOT NULL auto_increment,
  `username` varchar(20) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `admin` enum('YES','NO') NOT NULL default 'NO',
  UNIQUE KEY `id` (`uid`)
) ENGINE=MyISAM;

#
# Insert the initial user
#

INSERT INTO ab_auth_user VALUES (1, 'admin', '934161ec95c5ed977ec88f1d0d40d1a6', 'YES');