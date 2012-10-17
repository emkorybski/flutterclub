
ALTER TABLE `engine4_album_albums` ADD KEY `category_id` (`category_id`) ;

ALTER TABLE `engine4_album_photos` CHANGE COLUMN `collection_id` `album_id` int(11) unsigned NOT NULL AFTER `photo_id`;
