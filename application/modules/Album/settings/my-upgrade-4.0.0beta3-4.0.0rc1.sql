/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: my-upgrade-4.0.0beta3-4.0.0rc1.sql 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */
ALTER TABLE  `engine4_album_albums` ADD  `category_id` INT( 11 ) UNSIGNED NOT NULL DEFAULT  '0'
