CREATE TABLE `user` (
  `user_id` bigint(20) unsigned NOT NULL,
  `ctime` datetime NOT NULL,
  PRIMARY KEY `user_id` (`user_id`),
  KEY `ctime` (`ctime`)
);

CREATE TABLE `score` (
  `user_id` bigint(20) unsigned NOT NULL,
  `value` int(11) NOT NULL COMMENT 'Maybe scores can be negative. Who knows?',
  `ctime` datetime NOT NULL,
  PRIMARY KEY `user_id` (`user_id`, `ctime`)
) ENGINE=InnoDB;

CREATE TABLE `score_gain_weekly` (
  `user_id` bigint(20) unsigned NOT NULL,
  `value` int(11) NOT NULL,
  `ctime` datetime NOT NULL,
  PRIMARY KEY(`user_id`)
) ENGINE=InnoDB;
