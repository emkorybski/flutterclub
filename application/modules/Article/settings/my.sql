
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Article
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */


-- --------------------------------------------------------

--
-- Table structure for table `engine4_article_articles`
--

DROP TABLE IF EXISTS `engine4_article_articles`;
CREATE TABLE `engine4_article_articles` (
  `article_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `body` longtext NOT NULL,
  `owner_id` int(11) unsigned NOT NULL,
  `category_id` int(11) unsigned NOT NULL,
  `photo_id` int(10) unsigned NOT NULL default '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `like_count` int(11) unsigned NOT NULL default '0',
  `search` tinyint(1) NOT NULL default '1',
  `published`  tinyint(4) NOT NULL default '0',
  `featured` tinyint(4) NOT NULL default '0',
  `sponsored` tinyint(4) NOT NULL default '0',
  PRIMARY KEY (`article_id`),
  KEY `owner_id` (`owner_id`),
  KEY `search` (`search`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_article_albums`
--

DROP TABLE IF EXISTS `engine4_article_albums`;
CREATE TABLE `engine4_article_albums` (
  `album_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` int(11) unsigned NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` mediumtext NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `search` tinyint(1) NOT NULL default '1',
  `photo_id` int(11) unsigned NOT NULL default '0',
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `collectible_count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (`album_id`),
  KEY `article_id` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_article_photos`
--

DROP TABLE IF EXISTS `engine4_article_photos`;
CREATE TABLE `engine4_article_photos` (
  `photo_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `album_id` int(11) unsigned NOT NULL,
  `article_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` varchar(255) NOT NULL,
  `collection_id` int(11) unsigned NOT NULL,
  `file_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',  
  PRIMARY KEY (`photo_id`),
  KEY `album_id` (`album_id`),
  KEY `article_id` (`article_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_article_categories`
--

DROP TABLE IF EXISTS `engine4_article_categories`;
CREATE TABLE `engine4_article_categories` (
  `category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `category_name` varchar(128) NOT NULL,
  `description` TEXT,
  `photo_id` int(10) unsigned NOT NULL default '0',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `order` smallint(3) NOT NULL DEFAULT '999',
  PRIMARY KEY (`category_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Dumping data for table `engine4_article_categories`
--

INSERT IGNORE INTO `engine4_article_categories` (`category_id`, `order`, `user_id`, `category_name`, `description`) VALUES
(1, 1, 0, 'Arts & Culture', ''),
(2, 2, 0, 'Business', ''),
(3, 3, 0, 'Entertainment', ''),
(4, 4, 0, 'Lifestyle', ''),
(5, 5, 0, 'Family & Home', ''),
(6, 6, 0, 'Health', ''),
(7, 7, 0, 'Recreation', ''),
(8, 8, 0, 'Personal', ''),
(9, 9, 0, 'Shopping', ''),
(10, 10, 0, 'Society', ''),
(11, 11, 0, 'Sports', ''),
(12, 12, 0, 'Technology', ''),
(13, 13, 0, 'Other', '');


-- --------------------------------------------------------

--
-- Table structure for table `engine4_article_fields_maps`
--

DROP TABLE IF EXISTS `engine4_article_fields_maps`;
CREATE TABLE `engine4_article_fields_maps` (
  `field_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `order` smallint(6) NOT NULL,
  PRIMARY KEY  (`field_id`,`option_id`,`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ;

--
-- Dumping data for table `engine4_article_fields_maps`
--


-- --------------------------------------------------------

--
-- Table structure for table `engine4_article_fields_meta`
--

DROP TABLE IF EXISTS `engine4_article_fields_meta`;
CREATE TABLE `engine4_article_fields_meta` (
  `field_id` int(11) NOT NULL auto_increment,

  `type` varchar(24) collate latin1_general_ci NOT NULL,
  `label` varchar(64) NOT NULL,
  `description` varchar(255) NOT NULL default '',
  `alias` varchar(32) NOT NULL default '',
  `required` tinyint(1) NOT NULL default '0',
  `display` tinyint(1) unsigned NOT NULL,
  `publish` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `search` tinyint(1) unsigned NOT NULL default '0',
  `order` smallint(3) unsigned NOT NULL default '999',

  `config` text NOT NULL,
  `validators` text NULL,
  `filters` text NULL,

  `style` text NULL,
  `error` text NULL,

  PRIMARY KEY  (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;



-- --------------------------------------------------------

--
-- Table structure for table `engine4_article_fields_options`
--

DROP TABLE IF EXISTS `engine4_article_fields_options`;
CREATE TABLE `engine4_article_fields_options` (
  `option_id` int(11) NOT NULL auto_increment,
  `field_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `order` smallint(6) NOT NULL default '999',
  PRIMARY KEY  (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_article_fields_values`
--

DROP TABLE IF EXISTS `engine4_article_fields_values`;
CREATE TABLE `engine4_article_fields_values` (
  `item_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `index` smallint(3) NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_article_fields_search`
--

DROP TABLE IF EXISTS `engine4_article_fields_search`;
CREATE TABLE IF NOT EXISTS `engine4_article_fields_search` (
  `item_id` int(11) NOT NULL,
  PRIMARY KEY  (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menus`
--

DELETE FROM engine4_core_menus WHERE name LIKE 'article_%';

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('article_main', 'standard', 'Article Main Navigation Menu'),
('article_admin_main', 'standard', 'Article Admin Main Navigation Menu'),
('article_quick', 'standard', 'Article Post New Navigation Menu'),
('article_gutter', 'standard', 'Article Gutter Navigation Menu'),
('article_photos', 'standard', 'Article Photos Navigation Menu')
;


--
-- Dumping data for table `engine4_core_menuitems`
--
DELETE FROM `engine4_core_menuitems` WHERE module = 'article';

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_article', 'article', 'Articles', '', '{"route":"article_home"}', 'core_main', '', 4),
('core_sitemap_article', 'article', 'Articles', '', '{"route":"article_home"}', 'core_sitemap', '', 4),

('core_admin_main_plugins_article', 'article', 'Articles', '', '{"route":"admin_default","module":"article","controller":"settings"}', 'core_admin_main_plugins', '', 999),

('article_main_browse', 'article', 'Browse Articles', 'Article_Plugin_Menus::canViewArticles', '{"route":"article_browse","module":"article","controller":"index","action":"browse"}', 'article_main', '', 1),
('article_main_manage', 'article', 'My Articles', 'Article_Plugin_Menus::canCreateArticles', '{"route":"article_manage","module":"article","controller":"index","action":"manage"}', 'article_main', '', 2),
('article_main_create', 'article', 'Post New Article', 'Article_Plugin_Menus::canCreateArticles', '{"route":"article_create","module":"article","controller":"index","action":"create"}', 'article_main', '', 3),

('article_admin_main_manage', 'article', 'View Articles', '', '{"route":"admin_default","module":"article","controller":"manage"}', 'article_admin_main', '', 1),
('article_admin_main_settings', 'article', 'Global Settings', '', '{"route":"admin_default","module":"article","controller":"settings"}', 'article_admin_main', '', 2),
('article_admin_main_level', 'article', 'Member Level Settings', '', '{"route":"admin_default","module":"article","controller":"level"}', 'article_admin_main', '', 3),
('article_admin_main_fields', 'article', 'Article Questions', '', '{"route":"admin_default","module":"article","controller":"fields"}', 'article_admin_main', '', 4),
('article_admin_main_categories', 'article', 'Categories', '', '{"route":"admin_default","module":"article","controller":"categories"}', 'article_admin_main', '', 5),

('article_quick_create', 'article', 'Post New Article', 'Article_Plugin_Menus::canCreateArticles', '{"route":"article_general","action":"create","class":"buttonlink icon_article_new"}', 'article_quick', '', 1),

('article_gutter_list', 'article', 'All Submitter Articles', 'Article_Plugin_Menus', '{"route":"article_general","action":"browse","class":"buttonlink icon_article_viewall"}', 'article_gutter', '', 1),
('article_gutter_create', 'article', 'Post New Article', 'Article_Plugin_Menus', '{"route":"article_general","action":"create","class":"buttonlink icon_article_create"}', 'article_gutter', '', 2),
('article_gutter_edit', 'article', 'Edit This Article', 'Article_Plugin_Menus', '{"route":"article_specific","action":"edit","class":"buttonlink icon_article_edit"}', 'article_gutter', '', 3),
('article_gutter_delete', 'article', 'Delete This Article', 'Article_Plugin_Menus', '{"route":"article_specific","action":"delete","class":"buttonlink icon_article_delete"}', 'article_gutter', '', 4),

('article_photos_list', 'article', 'View Photos', 'Article_Plugin_Menus', '{"route":"article_extended","controller":"photo","action":"list","class":"buttonlink icon_article_photos_list"}', 'article_photos', '', 1),
('article_photos_manage', 'article', 'Manage Photos', 'Article_Plugin_Menus', '{"route":"article_extended","controller":"photo","action":"manage","class":"buttonlink icon_article_photos_manage"}', 'article_photos', '', 2),
('article_photos_upload', 'article', 'Upload Photos', 'Article_Plugin_Menus', '{"route":"article_extended","controller":"photo","action":"upload","class":"buttonlink icon_article_photos_upload"}', 'article_photos', '', 3)

;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--
DELETE FROM `engine4_core_modules` WHERE name = 'article';

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('article', 'Articles', 'This plugin allows your social network users to post and share articles, attach photos, comments.', '4.1.6', 1, 'extra');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_settings`
--
DELETE FROM `engine4_core_settings` WHERE name LIKE 'article.%';

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('article.license','XXXX-XXXX-XXXX-XXXX'),
('article.sorting','0'),
('article.page','10');


-- --------------------------------------------------------

DELETE FROM `engine4_activity_actiontypes` WHERE module = 'article';

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('article_new', 'article', '{item:$subject} posted a new article:', 1, 5, 1, 3, 1, 1),
('comment_article', 'article', '{item:$subject} commented on {item:$owner}''s {item:$object:article}: {body:$body}', 1, 1, 1, 1, 1, 0);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_notificationtypes`
--
DELETE FROM `engine4_activity_notificationtypes` WHERE module = 'article';

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('comment_article', 'article', '{item:$subject} has commented on your {item:$object:article}.', 0, ''),
('like_article', 'article', '{item:$subject} likes your {item:$object:article}.', 0, ''),
('commented_article', 'article', '{item:$subject} has commented on a {item:$object:article} you commented on.', 0, ''),
('liked_article', 'article', '{item:$subject} has commented on a {item:$object:article} you liked.', 0, '')
;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_authorization_permissions`
--


DELETE FROM `engine4_authorization_permissions` WHERE `type` = 'article';

-- ALL - except PUBLIC
-- auth_view, auth_comment, auth_html, auth_htmlattrs
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'article' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'article' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'article' as `type`,
    'auth_html' as `name`,
    3 as `value`,
    'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, object, param, embed, br, hr, blockquote' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'article' as `type`,
    'auth_htmlattrs' as `name`,
    3 as `value`,
    'href, src, alt, border, align, width, height, vspace, hspace, target, style, name, value, id, title, class, colspan, type, allowscriptaccess, allowfullscreen, rows, cols, size, language' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
-- featured, sponsored, approval
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'article' as `type`,
    'featured' as `name`,
    0 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'article' as `type`,
    'sponsored' as `name`,
    0 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');  
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'article' as `type`,
    'approval' as `name`,
    0 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public'); 
-- max

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'article' as `type`,
    'max' as `name`,
    3 as `value`,
    1000 as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');   

-- create
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'article' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');   
  
-- ADMIN, MODERATOR
-- view, delete, edit, photo, comment
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'article' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'article' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'article' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'article' as `type`,
    'photo' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'article' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
-- view, delete, edit, photo, comment, discuss
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'article' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'article' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'article' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'article' as `type`,
    'photo' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'article' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'article' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');


