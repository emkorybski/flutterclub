--
-- Update module Inviter
--

UPDATE `engine4_core_modules` SET `version` = '4.0.1'  WHERE `name` = 'inviter';
UPDATE `engine4_inviter_providers` SET `provider_default` = '1'  WHERE `provider_title` = 'AOL';