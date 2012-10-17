INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('radcodes_admin_main_plugins', 'radcodes', 'Plugins', '', '{"route":"admin_default","module":"radcodes","controller":"plugins"}', 'radcodes_admin_main', '', 3);

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('radcodes.mapcache','1'),
('radcodes.mapdebug','0')
;
