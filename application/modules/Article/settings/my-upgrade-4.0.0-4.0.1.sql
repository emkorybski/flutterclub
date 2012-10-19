
ALTER TABLE `engine4_article_photos` ADD COLUMN
  `view_count` int(11) unsigned NOT NULL default '0';

ALTER TABLE `engine4_article_photos` ADD COLUMN
  `comment_count` int(11) unsigned NOT NULL default '0';

UPDATE `engine4_article_photos` SET `comment_count` =
  (SELECT COUNT(*) FROM `engine4_core_comments` WHERE `resource_type` = 'article_photo' && `resource_id` = `engine4_article_photos`.`photo_id`) ;
