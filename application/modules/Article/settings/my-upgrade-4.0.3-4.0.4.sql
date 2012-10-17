DELETE FROM `engine4_core_search` WHERE `type` = 'article_category';

INSERT IGNORE INTO `engine4_core_search`
  SELECT
    'article' as `type`,
    article_id as `id`,
    title as `title`,
    '' as `description`,
    '' as `keywords`,
    '' as `hidden`
  FROM `engine4_article_articles`;  

