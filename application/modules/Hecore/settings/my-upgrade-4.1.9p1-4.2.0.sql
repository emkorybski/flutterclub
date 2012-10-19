--
-- Update module Hecore
--

UPDATE `engine4_core_modules` SET `version` = '4.2.0'  WHERE `name` = 'hecore';

ALTER TABLE `engine4_hecore_modules` ADD `modified_stamp` INT NOT NULL DEFAULT '0';