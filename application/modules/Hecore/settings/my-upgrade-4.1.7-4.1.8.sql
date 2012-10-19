--
-- Update module Hecore
--

UPDATE `engine4_core_modules` SET `version` = '4.1.8'  WHERE `name` = 'hecore';

UPDATE `engine4_core_menuitems` SET `label`='Hire-Experts', `order` = '887' WHERE `name`='core_admin_main_plugins_hecore';