
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: my.sql 9688 2012-04-18 23:32:12Z richard $
 * @author     John
 */


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_actiontypes`
--

/*
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('fields_change_generic', 'fields', '{item:$subject} changed their {translate:$label} to "{translate:$value}".', 0, 3, 1, 1, 1, 1),
('fields_change_long', 'fields', '{item:$subject} changed their {translate:$label}".', 0, 3, 1, 1, 1, 1)
;
*/


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('fields', 'Fields', 'Fields', '4.2.3', 1, 'core');
