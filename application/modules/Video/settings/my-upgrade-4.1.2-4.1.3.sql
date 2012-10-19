ALTER TABLE `engine4_video_videos`
  ADD COLUMN `parent_type` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci default NULL,
  ADD COLUMN `parent_id` int(11) unsigned default NULL;

