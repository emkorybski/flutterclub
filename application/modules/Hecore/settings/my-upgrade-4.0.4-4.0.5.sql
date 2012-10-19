--
-- Update module Hecore
--

UPDATE `engine4_core_modules` SET `version` = '4.0.5'  WHERE `name` = 'hecore';

CREATE TABLE IF NOT EXISTS `engine4_hecore_featureds` (
  `featured_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  PRIMARY KEY  (`featured_id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `engine4_hecore_user_settings` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`user_id` INT(10) NOT NULL DEFAULT '0',
	`setting` VARCHAR(255) NULL DEFAULT '',
	`value` TEXT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB ROW_FORMAT=DEFAULT;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('core_admin_main_plugins_hecore', 'hecore', 'Hire-Experts Core', '', '{"route":"admin_default","module":"hecore","controller":"index"}', 'core_admin_main_plugins', '', 1, 0, 999),
('hecore_admin_main_settings', 'hecore', 'hecore_Global Settings', '', '{"route":"admin_default","module":"hecore","controller":"settings"}', 'hecore_admin_main', '', 1, 0, 2),
('hecore_admin_main_plugins', 'hecore', 'hecore_Plugins', '', '{"route":"admin_default","module":"hecore","controller":"index"}', 'hecore_admin_main', '', 1, 0, 3),
('hecore_admin_main_featureds', 'hecore', 'hecore_Featured Members', '', '{"route":"admin_default","module":"hecore","controller":"featureds"}', 'hecore_admin_main', '', 1, 0, 1);

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ('hecore.featured.count', '9');