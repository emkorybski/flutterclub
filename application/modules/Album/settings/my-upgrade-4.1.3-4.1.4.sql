INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('mobi_browse_album', 'album', 'Albums', '', '{"route":"album_general","action":"browse"}', 'mobi_browse', '', 2);
ALTER TABLE `engine4_album_albums` CHANGE `type` `type` ENUM( 'wall', 'profile', 'message', 'blog' ) NULL DEFAULT NULL;