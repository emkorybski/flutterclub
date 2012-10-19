INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('article_main_browse', 'article', 'Browse Articles', 'Article_Plugin_Menus::canViewArticles', '{"route":"article_browse","module":"article","controller":"index","action":"browse"}', 'article_main', '', 1),
('article_main_manage', 'article', 'My Articles', 'Article_Plugin_Menus::canCreateArticles', '{"route":"article_manage","module":"article","controller":"index","action":"manage"}', 'article_main', '', 2),
('article_main_create', 'article', 'Post New Article', 'Article_Plugin_Menus::canCreateArticles', '{"route":"article_create","module":"article","controller":"index","action":"create"}', 'article_main', '', 3)
;

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('article_main', 'standard', 'Article Main Navigation Menu'),
('article_admin_main', 'standard', 'Article Admin Main Navigation Menu')
;
