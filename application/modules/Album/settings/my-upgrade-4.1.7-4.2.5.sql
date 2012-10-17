INSERT INTO engine4_album_categories VALUES (2147483647, 1, 'All Categories');
UPDATE `engine4_album_categories` SET `category_id` = '0', `user_id` = '1', `category_name` = 'All Categories' WHERE `category_id` = '2147483647' COLLATE utf8_bin LIMIT 1;
