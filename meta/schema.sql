
DROP DATABASE IF EXISTS `runnersmedium`;
CREATE DATABASE `runnersmedium` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
GRANT SELECT,INSERT,UPDATE,DELETE,CREATE ON runnersmedium.* TO 'runnersmedium'@'localhost' IDENTIFIED BY '**';
USE `runnersmedium`;

DROP TABLE IF EXISTS `timezones`;
CREATE TABLE IF NOT EXISTS `timezones` (
	`id` INT unsigned NOT NULL auto_increment,
	`name` VARCHAR(45) default NULL,
	`offset` VARCHAR(10) default '00:00',
	PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `timezones` (`name`, `offset`) VALUES
	('Eastern Time (US & Canada)', '-05:00'),
	('Central Time (US & Canada)', '-06:00'),
	('Mountain Time (US & Canada)', '-07:00'),
	('Pacific Time (US & Canada)', '-08:00'),
	('Alaskan Time', '-09:00'),
	('Hawaii-Aleutians Time', '-10:00'),
	('International Date Line East', '12:00'),
	('Magadan Time Russia', '11:00'),
	('East Australian Time', '10:00'),
	('Central Australian Time', '09:30'),
	('Japan Time', '09:00'),
	('West Australian Time', '08:00'),
	('China Coast Time', '08:00'),
	('North Sumatra', '06:30'),
	('Russian Federation Zone 5', '06:00'),
	('Indian', '05:30'),
	('Russian Federation Zone 4', '05:00'),
	('Russian Federation Zone 3', '04:00'),
	('Iran', '03:30'),
	('Baghdad Time/Moscow Time', '03:00'),
	('Eastern Europe Time', '02:00'),
	('Central European Time', '01:00'),
	('Universal Time Coordinated', '00:00'),
	('West Africa Time', '-01:00'),
	('Azores Time', '-02:00'),
	('Atlantic Time', '-03:00'),
	('Newfoundland', '-03:30');

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
	`id` INT unsigned NOT NULL auto_increment,
	`username` VARCHAR(20) NOT NULL,
	`password` VARCHAR(45) NOT NULL,
	`email` VARCHAR(45) NOT NULL,
	`name` VARCHAR(45) default NULL,
	`units` INT default 0,
	`timezone` INT default 1,
	`ispublic` TINYINT(0) default 0,
	`about` VARCHAR(140) default NULL,
	`location` VARCHAR(45) default NULL,
	`url` VARCHAR(90) default NULL,
	`why` VARCHAR(140) default NULL,
	`birthday` DATE default NULL,
	`height` VARCHAR(20) default NULL,
	`weight` SMALLINT default NULL,
	`gender` VARCHAR(10) default NULL,
	`picture` VARCHAR(90) default NULL,
	`cookie` VARCHAR(32) default NULL,
	`token` VARCHAR(32) default NULL,
	`invites` INT default 100,
	`created` TIMESTAMP default CURRENT_TIMESTAMP,
	`lastlogin` TIMESTAMP default 0,
	`lastfail` TIMESTAMP default 0,
	`optin` TINYINT(0) default 0,
	`isadmin` TINYINT(0) default 0,
	FOREIGN KEY (`units`) REFERENCES `units` (`id`),
	FOREIGN KEY (`timezone`) REFERENCES `timezones` (`id`),
	PRIMARY KEY  (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `friendstates`;
CREATE TABLE IF NOT EXISTS `friendstates` (
	`id` INT unsigned NOT NULL auto_increment,
	`name` VARCHAR(32) default NULL,
	PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `friendstates` VALUES (1, 'REQUEST');
INSERT INTO `friendstates` VALUES (2, 'ACTIVE');
INSERT INTO `friendstates` VALUES (3, 'DENIED');

DROP TABLE IF EXISTS `friends`;
CREATE TABLE IF NOT EXISTS `friends` (
	`id` INT unsigned NOT NULL auto_increment,
	`user` INT unsigned NOT NULL,
	`friend` INT unsigned NOT NULL,
	`state` INT unsigned NOT NULL,
	`created` TIMESTAMP default CURRENT_TIMESTAMP,
	FOREIGN KEY (`user`) REFERENCES `users` (`id`),
	FOREIGN KEY (`friend`) REFERENCES `users` (`id`),
	FOREIGN KEY (`state`) REFERENCES `friendstates` (`id`),
	PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `requests`;
CREATE TABLE IF NOT EXISTS `requests` (
	`id` INT unsigned NOT NULL auto_increment,
	`email` VARCHAR(45) NOT NULL,
	`name` VARCHAR(45) default NULL,
	`optin` TINYINT(0) default 0,
	`invite` INT unsigned default NULL,
	FOREIGN KEY (`invite`) REFERENCES `invites` (`id`),
	PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tickets`;
CREATE TABLE `tickets` (
	`id` INT unsigned NOT NULL auto_increment,
	`hash` VARCHAR(255) default NULL,
	`data` VARCHAR(255) default NULL,
	`created` DATETIME default NULL,
	`modified` DATETIME default NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY hashs (`hash`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `quits`;
CREATE TABLE IF NOT EXISTS `quits` (
	`id` INT unsigned NOT NULL auto_increment,
	`username` VARCHAR(20) NOT NULL,
	`reasoncode` INT default NULL,
	`feedback` VARCHAR(140) NOT NULL,
	`created` TIMESTAMP default CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- run engine

DROP TABLE IF EXISTS `shoes`;
CREATE TABLE IF NOT EXISTS `shoes` (
	`id` INT unsigned NOT NULL auto_increment,
	`user` INT unsigned default NULL,
	`brand` VARCHAR(32) NOT NULL,
	`model` VARCHAR(32) default NULL,
	`price` FLOAT default NULL,
	`comments` VARCHAR(90) default NULL,
	`retired` BOOLEAN default 0,
	`rating` INT unsigned default NULL,
	`purchased` DATETIME default NULL,
	`created` TIMESTAMP default CURRENT_TIMESTAMP,
	FOREIGN KEY (`user`) REFERENCES `users` (`id`),
	PRIMARY KEY  (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `courses`;
CREATE TABLE IF NOT EXISTS `courses` (
	`id` INT unsigned NOT NULL auto_increment,
	`user` INT unsigned default NULL,
	`name` VARCHAR(32) NOT NULL,
	`distance` FLOAT default NULL,
	`city` VARCHAR(55) default NULL,
	`comments` VARCHAR(90) default NULL,
	`params` TEXT default NULL,
	`rating` INT unsigned default NULL,
	`created` TIMESTAMP default CURRENT_TIMESTAMP,
	FOREIGN KEY (`user`) REFERENCES `users` (`id`),
	PRIMARY KEY  (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `runtypes`;
CREATE TABLE IF NOT EXISTS `runtypes` (
	`id` INT unsigned NOT NULL auto_increment,
	`user` INT unsigned default NULL,
	`name` VARCHAR(32) NOT NULL,
	`created` TIMESTAMP default CURRENT_TIMESTAMP,
	FOREIGN KEY (`user`) REFERENCES `users` (`id`),
	PRIMARY KEY  (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `runtypes` (`name`) VALUES ('tempo');
INSERT INTO `runtypes` (`name`) VALUES ('easy');
INSERT INTO `runtypes` (`name`) VALUES ('fartlek');
INSERT INTO `runtypes` (`name`) VALUES ('hill');
INSERT INTO `runtypes` (`name`) VALUES ('interval');
INSERT INTO `runtypes` (`name`) VALUES ('long');
INSERT INTO `runtypes` (`name`) VALUES ('race');
INSERT INTO `runtypes` (`name`) VALUES ('treadmill');
INSERT INTO `runtypes` (`name`) VALUES ('none');

DROP TABLE IF EXISTS `runs`;
CREATE TABLE IF NOT EXISTS `runs` (
	`id` INT unsigned NOT NULL auto_increment,
	`user` INT unsigned NOT NULL,
	`type` INT unsigned NOT NULL,
	`course` INT unsigned default NULL,
	`shoe` INT unsigned default NULL,
	`laps` FLOAT default 1,
	`name` VARCHAR(30) default NULL,
	`distance` FLOAT default NULL,
	`duration` TIME default NULL,
	`weight` FLOAT default NULL,
	`comments` VARCHAR(90) default NULL,
	`date` DATETIME NOT NULL,
	`created` TIMESTAMP default CURRENT_TIMESTAMP,
	FOREIGN KEY (`user`) REFERENCES `users` (`id`),
	FOREIGN KEY (`type`) REFERENCES `runtypes` (`id`),
	FOREIGN KEY (`course`) REFERENCES `courses` (`id`),
	FOREIGN KEY (`shoe`) REFERENCES `shoes` (`id`),
	PRIMARY KEY  (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `likes`;
CREATE TABLE IF NOT EXISTS `likes` (
	`id` INT unsigned NOT NULL auto_increment,
	`user` INT unsigned NOT NULL,
	`run` INT unsigned NOT NULL,
	`created` TIMESTAMP default CURRENT_TIMESTAMP,
	FOREIGN KEY (`user`) REFERENCES `users` (`id`),
	FOREIGN KEY (`run`) REFERENCES `runs` (`id`),
	PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `reservedwords`;
CREATE TABLE IF NOT EXISTS `reservedwords` (
	`id` INT unsigned NOT NULL auto_increment,
	`text` VARCHAR(20),
	PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

delimiter $$
CREATE TRIGGER `rmuser` AFTER DELETE ON `users`
FOR EACH ROW
BEGIN
	DELETE FROM `runs` WHERE `user` = OLD.id;
	DELETE FROM `courses` WHERE `user` = OLD.id;
	DELETE FROM `shoes` WHERE `user` = OLD.id;
	DELETE FROM `friends` WHERE `user` = OLD.id OR `friend` = OLD.id;
	DELETE FROM `likes` WHERE `user` = OLD.id;
END;
$$
delimiter ;

-- reserved words

source /Users/markb/Sites/runnersmedium/meta/reserved.sql;

-- test data

source /Users/markb/Sites/runnersmedium/meta/test.sql;

-- updates

source /Users/markb/Sites/runnersmedium/meta/updates.sql;