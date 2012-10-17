INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('younet-core', 'YouNet Core Module', 'YouNet Core Module', '4.01', 1, 'extra') ;


INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_plugins_younet_core', 'younet-core', 'YouNet Core', '', '{"route":"admin_default","module":"younet-core","controller":"settings","action":"yours"}', 'core_admin_main_plugins', '',1 );

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('younet_core_admin_main_yours', 'younet-core', 'Your Plugins', '', '{"route":"admin_default","module":"younet-core","controller":"settings","action":"yours"}', 'younet_core_admin_main', '', 2);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('younet_core_admin_main_younet', 'younet-core', 'YouNet Plugins', '', '{"route":"admin_default","module":"younet-core","controller":"settings","action":"younet"}', 'younet_core_admin_main', '',1 );
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('younet_core_admin_main_info', 'younet-core', 'License Term', '', '{"route":"admin_default","module":"younet-core","controller":"settings","action":"information"}', 'younet_core_admin_main', '',3 );

CREATE TABLE IF NOT EXISTS `engine4_younetcore_apisettings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  `value` text NOT NULL,
  `params` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `engine4_younetcore_license` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `title` text NOT NULL,
  `descriptions` text,
  `type` varchar(50) NOT NULL,
  `current_version` varchar(50) NOT NULL,
  `lasted_version` varchar(50) NOT NULL,
  `is_active` int(1) DEFAULT '0',
  `date_active` int(11) DEFAULT NULL,
  `params` text,
  `download_link` varchar(500) DEFAULT NULL,
  `demo_link` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `engine4_younetcore_install` (
  `token` text NOT NULL,
  `params` text NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



