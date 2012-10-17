
ALTER TABLE engine4_article_articles ADD COLUMN
  `like_count` int(11) unsigned NOT NULL default '0'
  AFTER `comment_count`;

UPDATE `engine4_article_articles` SET `like_count` =
  (SELECT COUNT(*) FROM `engine4_core_likes` WHERE `resource_type` = 'article' && `resource_id` = `engine4_article_articles`.`article_id`) ;



ALTER TABLE `engine4_article_categories`
CHANGE COLUMN `user_id` `user_id` int(11) unsigned NOT NULL DEFAULT '0';

ALTER TABLE `engine4_article_categories`
ADD COLUMN `description` TEXT,
ADD COLUMN `photo_id` int(10) unsigned NOT NULL default '0',
ADD COLUMN `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
ADD COLUMN `order` smallint(3) NOT NULL DEFAULT '999';

UPDATE `engine4_article_categories` SET `user_id` = '0', `order` = category_id , description = '';

-- UPDATE MENU ITEMS
DELETE FROM `engine4_core_menuitems` WHERE module = 'article' AND name = 'article_admin_main_widget';

UPDATE `engine4_core_menuitems` 
  SET params = '{"route":"admin_default","module":"article","controller":"categories"}'
  WHERE module = 'article' AND name = 'article_admin_main_categories';

-- FIX comment privacy from "everyone" (removed in this version) to "registered"
UPDATE `engine4_authorization_allow` 
  SET `role` = 'registered'
  WHERE `resource_type` = 'article' AND `action` = 'comment' AND `role` = 'everyone';
  
-- CLEAN UP ALL OLD VALUES
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
  
-- featured, sponsored
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
-- view, delete, edit, photo, comment
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
  
  