
ALTER TABLE `users` ADD COLUMN `optin_friends` TINYINT(0) default 1;
ALTER TABLE `users` ADD COLUMN `optin_nudges` TINYINT(0) default 1;
ALTER TABLE `users` CHANGE COLUMN `optin` `optin_updates` TINYINT(0) default 0;

INSERT INTO `reservedwords` (`text`) VALUES ('notices');

DROP TABLE IF EXISTS `nudgestates`;
CREATE TABLE IF NOT EXISTS `nudgestates` (
	`id` INT unsigned NOT NULL auto_increment,
	`name` VARCHAR(32) default NULL,
	PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `nudgestates` VALUES (1, 'NEW');
INSERT INTO `nudgestates` VALUES (2, 'SENT');
INSERT INTO `nudgestates` VALUES (3, 'TRASH');

DROP TABLE IF EXISTS `nudges`;
CREATE TABLE IF NOT EXISTS `nudges` (
	`id` INT unsigned NOT NULL auto_increment,
	`nudger` INT unsigned NOT NULL,
	`nudgee` INT unsigned NOT NULL,
	`state` INT unsigned NOT NULL default 1,
	`created` TIMESTAMP default CURRENT_TIMESTAMP,
	FOREIGN KEY (`nudger`) REFERENCES `users` (`id`),
	FOREIGN KEY (`nudgee`) REFERENCES `users` (`id`),
	FOREIGN KEY (`state`) REFERENCES `nudgestates` (`id`),
	PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `friends` ADD COLUMN `sent` TINYINT(0) default 0;
UPDATE `friends` SET `sent` = 1 WHERE `state` IN (2, 3);

delimiter $$
CREATE TRIGGER `rmnudges` AFTER INSERT ON `runs`
FOR EACH ROW
BEGIN
	UPDATE `nudges` SET `state` = 3 WHERE `nudgee` = NEW.user AND `state` != 3;
END;
$$
delimiter ;
