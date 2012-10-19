
/**
 * SocialEngine - SocialEngineMods
 *
 */


CREATE TABLE IF NOT EXISTS `engine4_friendsinviter_teasersettings` (
  `user_id` int(11) unsigned NOT NULL,
  `enabled` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `engine4_semods_invite_unsubscribe` (
  `unsubscribe_id` int(11) NOT NULL AUTO_INCREMENT,
  `unsubscribe_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `unsubscribe_user_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `unsubscribe_type` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`unsubscribe_id`),
  KEY `unsubscribe_user_email` (`unsubscribe_user_email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `engine4_invites_stats_user` (
  `user_id` int(9) unsigned NOT NULL DEFAULT '0',
  `invites_sent` int(11) NOT NULL DEFAULT '0',
  `invites_converted` int(11) NOT NULL DEFAULT '0',
  `invites_sent_counter` int(11) NOT NULL DEFAULT '0',
  `invites_sent_last` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`) VALUES
('core_main_invite', 'core', 'Invite', '', '{"route":"invite"}', 'core_main', '', 0, 1),
('core_admin_main_plugins_friendsinviter', 'friendsinviter', 'Friends Inviter', '', '{"route":"admin_default","module":"friendsinviter","controller":"settings","action":"index"}', 'core_admin_main_plugins', '', 0, 999),
('friendsinviter_admin_main_settings', 'friendsinviter', 'General Settings', '', '{"route":"admin_default","module":"friendsinviter","controller":"settings"}', 'friendsinviter_admin_main', '', 0, 1),
('friendsinviter_admin_main_tracker', 'friendsinviter', 'Invitations Tracker', '', '{"route":"admin_default","module":"friendsinviter","controller":"tracker"}', 'friendsinviter_admin_main', '', 0, 2),
('friendsinviter_admin_main_quickstats', 'friendsinviter', 'Quick Stats', '', '{"route":"admin_default","module":"friendsinviter","controller":"quickstats"}', 'friendsinviter_admin_main', '', 0, 3),
('friendsinviter_admin_main_stats', 'friendsinviter', 'Statistics', '', '{"route":"admin_default","module":"friendsinviter","controller":"stats"}', 'friendsinviter_admin_main', '', 0, 4),
('friendsinviter_admin_main_leaderboard', 'friendsinviter', 'Leaderboard', '', '{"route":"admin_default","module":"friendsinviter","controller":"leaderboard"}', 'friendsinviter_admin_main', '', 0, 5),
('friendsinviter_admin_main_help', 'friendsinviter', 'Help', '', '{"route":"admin_default","module":"friendsinviter","controller":"help"}', 'friendsinviter_admin_main', '', 0, 5);


INSERT INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('friendsinviter', 'Friends Inviter', 'Friends Inviter plugin.', '4.0.0', 1, 'extra');


UPDATE `engine4_user_signup` SET `class` = 'Friendsinviter_Plugin_Signup_Invite' WHERE `class` = 'User_Plugin_Signup_Invite';


INSERT INTO `engine4_core_settings` (`name`, `value`) VALUES
('friendsinviter.invite.filteremails', '^support@.*,^info@.*,adsense-support@google.com'),
('friendsinviter.invite.topdomains', 'gmail.com,hotmail.com,live.com,yahoo.com,aol.com,mail.com,mac.com,fastmail.fm,inbox.com'),
('friendsinviter.invite.topnetworks.0.d', 'MSN Messenger'),
('friendsinviter.invite.topnetworks.0.e', '1'),
('friendsinviter.invite.topnetworks.0.l', ''),
('friendsinviter.invite.topnetworks.0.n', 'messenger'),
('friendsinviter.top.domains', 'gmail.com,yahoo.com,fast.fm');

