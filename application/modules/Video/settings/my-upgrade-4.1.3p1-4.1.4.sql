
INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('video.embeds', 1);


INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('mobi_browse_video', 'video', 'Videos', '', '{"route":"video_general"}', 'mobi_browse', '', 9);