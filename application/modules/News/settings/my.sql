INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES ('news', 'News', 'Get data from remote servers', '4.06p2', 1, 'extra');

CREATE TABLE `engine4_news_categoryparents` (
  `category_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_name` varchar(200) DEFAULT NULL,
  `category_description` text ,
  `is_active` smallint(1) DEFAULT 1,
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM COLLATE='utf8_unicode_ci';

--
-- Table structure for table `engine4_news_category`
--

CREATE TABLE `engine4_news_categories` (
  `category_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_name` varchar(200),
  `category_parent_id` int(11) NOT NULL DEFAULT '0',
  `url_resource` varchar(500),
  `posted_date` DATETIME DEFAULT NULL,
  `category_logo` TEXT,
  `logo` TEXT,
  `is_active` smallint(1) DEFAULT '1',
  `mini_logo` tinyint(1) NOT NULL DEFAULT 1,
  `display_logo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM COLLATE='utf8_unicode_ci';


--
-- Table structure for table `engine4_news_content`
--

CREATE TABLE `engine4_news_contents` (
  `content_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `owner_type` varchar(50) DEFAULT 'user',
  `owner_id` int(11) UNSIGNED DEFAULT 1,
  `title` varchar(300) DEFAULT NULL,
  `description` TEXT,
  `content` TEXT,
  `image` varchar(300) DEFAULT NULL,
  `link_detail` varchar(300) DEFAULT NULL,
  `author` varchar(200) DEFAULT NULL,
  `pubDate` varchar(100) DEFAULT NULL,
  `pubDate_parse` varchar(255) DEFAULT NULL,
  `posted_date` datetime DEFAULT NULL,
  `is_active` smallint(6) DEFAULT 1,
  `is_featured` smallint(6) DEFAULT 0,
  `count_view` int(11) UNSIGNED DEFAULT 0,
  PRIMARY KEY (`content_id`)
) ENGINE=MyISAM COLLATE='utf8_unicode_ci';

--
-- Table structure for table `engine4_news_timeframe`
--

CREATE TABLE `engine4_news_timeframe` (
  `timeframe_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `minutes` varchar(2) DEFAULT '*',
  `hour` varchar(2) DEFAULT '*',
  `month` varchar(2) DEFAULT '*',
  `day` varchar(2) DEFAULT '*',
  `weekday` varchar(10) DEFAULT '*',
  PRIMARY KEY (`timeframe_id`)
) ENGINE=MyISAM  COLLATE='utf8_unicode_ci';

--
-- Table structure for table `engine4_news_timeframe`
--
CREATE TABLE `engine4_news_params` (
  `param_id` int(11) UNSIGNED NOT NULL DEFAULT '1',
  `search` varchar(256) DEFAULT NULL,
  `orderby` varchar(50) NOT NULL,
  `category` int(11) UNSIGNED NOT NULL,
  `page` int(11) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`param_id`)
) ENGINE=MyISAM COLLATE='utf8_unicode_ci';


CREATE TABLE `engine4_news_nusers` (
  `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM  COLLATE='utf8_unicode_ci';

--
-- Dumping data for table `engine4_core_modules`
--
INSERT IGNORE  INTO `engine4_news_params`(param_id,`search`,`orderby`,`category`,`page`) values (1,'','0',0,1);



INSERT IGNORE  INTO `engine4_activity_actiontypes`(`type`,`module`,`body`,`enabled`,displayable,`attachable`,commentable,shareable,is_generated) VALUES ('news_content','news','{item:$subject} commented on:',1, 5, 0, 1, 4, 0);
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ('comment_news_content', 'news', '{item:$subject} commented on {item:$object:News}: {body:$body}', 1, 5, 0, 1, 4, 0);
INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES ('liked_news', 'news', '{item:$subject} has commented on a {item:$object:News} you liked.', 0, ''), ('like_news', 'news', '{item:$subject} likes your {item:$object:News}.', 0, '');



--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_news', 'news', 'News', '', '{"route":"default","module":"news"}', 'core_main', '', 10),
('core_admin_main_plugins_news', 'news', 'News', '', '{"route":"admin_default","module":"news","controller":"manage"}', 'core_admin_main_plugins', '', 999),
('news_admin_main_manage', 'news', 'News Management', '', '{"route":"admin_default","module":"news","controller":"manage"}', 'news_admin_main', '', 1),
('news_admin_main_category', 'news', 'RSS Management', '', '{"route":"admin_default","module":"news","controller":"manage","action":"category"}', 'news_admin_main', '', 2),
('news_admin_main_categories', 'news', 'Category Management', '', '{"route":"admin_default","module":"news","controller":"manage","action":"categories"}', 'news_admin_main', '', 4),
('news_admin_main_create', 'news', 'Add RSS', '', '{"route":"admin_default","module":"news","controller":"manage","action":"create"}', 'news_admin_main', '', 3),
('news_admin_main_users', 'news', 'User Settings', '', '{"route":"admin_default","module":"news","controller":"manage","action":"users"}', 'news_admin_main', '', 5);
--
-- Dumping data for table `engine4_core_pages`

INSERT IGNORE  INTO `engine4_core_pages`(`name`,displayname,`url`,`title`,`description`,`keywords`,`custom`,fragment,layout,view_count) 
VALUES ('news_index_list','Listing News',NULL,'Listing News','This is the page news','',0,0,'',0);


--
-- Dumping data for table `engine4_core_content`
--
INSERT IGNORE INTO `engine4_core_content`(`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, attribs)
VALUES ((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='news_index_list'), 'container', 'top', NULL, '1', '[]', NULL);

INSERT IGNORE INTO `engine4_core_content`(`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, attribs)
VALUES  ((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='news_index_list'), 'container', 'middle', (SELECT LAST_INSERT_ID()) , '6', '[]', NULL),
        ((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='news_index_list'),'widget','news.menu-news',(SELECT LAST_INSERT_ID() + 1),3,'[]',NULL);

INSERT IGNORE INTO `engine4_core_content`(`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, attribs)
VALUES ((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='news_index_list'), 'container', 'main', NULL, '2', '[""]', NULL);

INSERT IGNORE INTO `engine4_core_content`(`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, attribs)
VALUES ((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='news_index_list'), 'container', 'left', (SELECT LAST_INSERT_ID()) , '4', '[""]', NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='news_index_list'), 'container', 'middle', (SELECT LAST_INSERT_ID()) , '6', '[""]', NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='news_index_list'), 'container', 'right', (SELECT LAST_INSERT_ID()) , '5', '[""]', NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='news_index_list'),'widget','news.lasted-news',(SELECT LAST_INSERT_ID() + 1),3,'{\"title\":\"Recent News\"}',NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='news_index_list'),'widget','news.featured-news',(SELECT LAST_INSERT_ID() + 2),4,'{\"title\":\"Featured News\"}',NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='news_index_list'),'widget','news.list-news',(SELECT LAST_INSERT_ID() + 2),5,'{\"title\":\"Listing News\"}',NULL),  
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='news_index_list'),'widget','news.search-news',(SELECT LAST_INSERT_ID() + 3),7,'{\"title\":\"Search News\"}',NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='news_index_list'),'widget','news.top-news',(SELECT LAST_INSERT_ID() + 3),8,'{\"title\":\"Top News\"}',NULL);

--
-- Dumping data for table `engine4_authorization_permissions`
--

INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES
(1, 'news_content', 'create', 1, NULL),
(1, 'news_content', 'delete', 1, NULL),
(1, 'news_content', 'edit', 1, NULL),
(1, 'news_content', 'view', 1, NULL),
(1, 'news_content', 'comment', 2, NULL),
(1, 'news_content', 'css', 1, NULL),
(1, 'news_content', 'max', 3, '20'),
(1, 'news_content', 'photo', 1, NULL),
(1, 'news_content', 'auth_comment', 5, '["everyone","owner_network","owner_member_member","owner_member","owner"]'),
(1, 'news_content', 'auth_html', 3, 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr'),
(1, 'news_content', 'auth_view', 5, '["everyone","owner_network","owner_member_member","owner_member","owner"]'),

(2, 'news_content', 'create', 1, NULL),
(2, 'news_content', 'delete', 1, NULL),
(2, 'news_content', 'edit', 1, NULL),
(2, 'news_content', 'view', 1, NULL),
(2, 'news_content', 'comment', 2, NULL),
(2, 'news_content', 'css', 1, NULL),
(2, 'news_content', 'max', 3, '20'),
(2, 'news_content', 'photo', 1, NULL),
(2, 'news_content', 'auth_comment', 5, '["everyone","owner_network","owner_member_member","owner_member","owner"]'),
(2, 'news_content', 'auth_html', 3, 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr'),
(2, 'news_content', 'auth_view', 5, '["everyone","owner_network","owner_member_member","owner_member","owner"]'),

(3, 'news_content', 'create', 1, NULL),
(3, 'news_content', 'delete', 1, NULL),
(3, 'news_content', 'edit', 1, NULL),
(3, 'news_content', 'view', 1, NULL),
(3, 'news_content', 'comment', 2, NULL),
(3, 'news_content', 'css', 1, NULL),
(3, 'news_content', 'max', 3, '20'),
(3, 'news_content', 'photo', 1, NULL),
(3, 'news_content', 'auth_comment', 5, '["everyone","owner_network","owner_member_member","owner_member","owner"]'),
(3, 'news_content', 'auth_html', 3, 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr'),
(3, 'news_content', 'auth_view', 5, '["everyone","owner_network","owner_member_member","owner_member","owner"]'),

(4, 'news_content', 'create', 1, NULL),
(4, 'news_content', 'delete', 1, NULL),
(4, 'news_content', 'edit', 1, NULL),
(4, 'news_content', 'view', 1, NULL),
(4, 'news_content', 'comment', 2, NULL),
(4, 'news_content', 'css', 1, NULL),
(4, 'news_content', 'max', 3, '20'),
(4, 'news_content', 'photo', 1, NULL),
(4, 'news_content', 'auth_comment', 5, '["everyone","owner_network","owner_member_member","owner_member","owner"]'),
(4, 'news_content', 'auth_html', 3, 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr'),
(4, 'news_content', 'auth_view', 5, '["everyone","owner_network","owner_member_member","owner_member","owner"]'),

(5, 'news_content', 'view', 1, NULL),
(5, 'news_content', 'css', 1, NULL),
(5, 'news_content', 'max', 3, '20'),
(5, 'news_content', 'photo', 1, NULL),
(5, 'news_content', 'auth_comment', 3, '[]'),
(5, 'news_content', 'auth_html', 3, 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr'),
(5, 'news_content', 'auth_view', 5, '["everyone","owner_network","owner_member_member","owner_member","owner"]');
