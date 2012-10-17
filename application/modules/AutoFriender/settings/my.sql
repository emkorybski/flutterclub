INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('auto-friender', 'Auto Friender', 'Auto Friender', '4.1.6.2', 1, 'extra') ;

INSERT IGNORE INTO `engine4_core_menuitems`
	(`id`, `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`)
	VALUES (NULL, 'core_admin_main_plugins_auto_friender', 'auto-friender', 'Auto Friendship', NULL, '{"route":"admin_default","module":"auto-friender","controller":"manage"}', 'core_admin_main_plugins', NULL, '0', '999');


INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('auto.friender.friend_id', ''),
('auto.friender.enable', 0),
('auto.friender.applyAll', 0);