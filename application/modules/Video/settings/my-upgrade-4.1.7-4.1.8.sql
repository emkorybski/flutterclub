
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('video_quick_create', 'video', 'Post New Video', 'Video_Plugin_Menus::canCreateVideos', '{"route":"video_general","action":"create","class":"buttonlink icon_video_new"}', 'video_quick', '', 1)
;

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('video_quick', 'standard', 'Video Quick Navigation Menu')
;
