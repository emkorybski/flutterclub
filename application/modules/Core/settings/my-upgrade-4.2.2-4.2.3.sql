
INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('user.support.links', 1);

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_admin_user_signup', 'core', '[host],[email],[date],[recipient_title],[object_title],[object_link]');
