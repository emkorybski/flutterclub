--
-- Update module Inviter
--

UPDATE `engine4_core_modules` SET `version` = '4.0.2'  WHERE `name` = 'inviter';
UPDATE `engine4_core_menuitems` SET `label` = 'Friends Inviter' WHERE `module`='inviter' && `name` IN ('core_main_inviter', 'core_sitemap_inviter', 'core_admin_main_plugins_inviter');