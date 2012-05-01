
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('user_profile_admin', 'user', 'Admin Settings', 'User_Plugin_Menus', '', 'user_profile', '', 9);

DROP TABLE IF EXISTS `engine4_user_janrain`;
CREATE TABLE IF NOT EXISTS `engine4_user_janrain` (
  `user_id` int(11) unsigned NOT NULL,
  `identifier` varchar(255) NOT NULL,
  `provider` varchar(255) NOT NULL default '',
  `token` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `identifier` (`identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_janrain', 'user', 'Janrain Integration', '', '{"route":"admin_default", "action":"janrain", "controller":"settings", "module":"user"}', 'core_admin_main_settings', '', 4)
;
