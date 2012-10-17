--
-- Update module Inviter
--

UPDATE `engine4_core_modules` SET `version` = '4.0.3'  WHERE `name` = 'inviter';
UPDATE `engine4_core_menuitems` SET `label` = 'Inviter' WHERE `module`='inviter' && `name`='core_main_inviter' && `label`='Friends Inviter';