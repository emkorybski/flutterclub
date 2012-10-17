ALTER TABLE `engine4_article_articles` ADD COLUMN
  `description` text NOT NULL AFTER `title`;
  
INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('article_quick', 'standard', 'Article Post New Navigation Menu'),
('article_gutter', 'standard', 'Article Gutter Navigation Menu'),
('article_photos', 'standard', 'Article Photos Navigation Menu')
;


INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES

('article_quick_create', 'article', 'Post New Article', 'Article_Plugin_Menus::canCreateArticles', '{"route":"article_general","action":"create","class":"buttonlink icon_article_new"}', 'article_quick', '', 1),

('article_gutter_list', 'article', 'All Submitter Articles', 'Article_Plugin_Menus', '{"route":"article_general","action":"browse","class":"buttonlink icon_article_viewall"}', 'article_gutter', '', 1),
('article_gutter_create', 'article', 'Post New Article', 'Article_Plugin_Menus', '{"route":"article_general","action":"create","class":"buttonlink icon_article_create"}', 'article_gutter', '', 2),
('article_gutter_edit', 'article', 'Edit This Article', 'Article_Plugin_Menus', '{"route":"article_specific","action":"edit","class":"buttonlink icon_article_edit"}', 'article_gutter', '', 3),
('article_gutter_delete', 'article', 'Delete This Article', 'Article_Plugin_Menus', '{"route":"article_specific","action":"delete","class":"buttonlink icon_article_delete"}', 'article_gutter', '', 4),

('article_photos_list', 'article', 'View Photos', 'Article_Plugin_Menus', '{"route":"article_extended","controller":"photo","action":"list","class":"buttonlink icon_article_photos_list"}', 'article_photos', '', 1),
('article_photos_manage', 'article', 'Manage Photos', 'Article_Plugin_Menus', '{"route":"article_extended","controller":"photo","action":"manage","class":"buttonlink icon_article_photos_manage"}', 'article_photos', '', 2),
('article_photos_upload', 'article', 'Upload Photos', 'Article_Plugin_Menus', '{"route":"article_extended","controller":"photo","action":"upload","class":"buttonlink icon_article_photos_upload"}', 'article_photos', '', 3)
;
