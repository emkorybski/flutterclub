<?php
/**
 * SocialEngine
 *
 * @category   Application_Theme
 * @package    Midnight
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 9579 2012-01-06 00:00:44Z john $
 * @author     Alex
 */

return array(
  'package' => array(
    'type' => 'theme',
    'name' => 'midnight',
    'version' => '4.2.0',
    'revision' => '$Revision: 9579 $',
    'path' => 'application/themes/midnight',
    'repository' => 'socialengine.net',
    'title' => 'Midnight',
    'thumb' => 'midnight_theme.jpg',
    'author' => 'Webligo Developments',
    'changeLog' => array(
      '4.2.0' => array(
        'manifest.php' => 'Incremented version',
        'theme.css' => 'Fixed issue with feed comment option list',
      ),
      '4.1.8' => array(
        'manifest.php' => 'Incremented version',
        'mobile.css' => 'Added styles for HTML5 input elements',
        'theme.css' => 'Added styles for HTML5 input elements',
      ),
      '4.1.4' => array(
        'mainfest.php' => 'Incremented version',
        'mobile.css' => 'Added new type of stylesheet',
      ),
      '4.0.1' => array(
        'manifest.php' => 'Incremented version; removed deprecated meta key',
      ),
    ),
    'actions' => array(
      'install',
      'upgrade',
      'refresh',
      'remove',
    ),
    'callback' => array(
      'class' => 'Engine_Package_Installer_Theme',
    ),
    'directories' => array(
      'application/themes/midnight',
    ),
  ),
  'files' => array(
    'theme.css',
    'constants.css',
  ),
  'nophoto' => array(
    'user' => array(
      'thumb_icon' => 'application/themes/midnight/images/nophoto_user_thumb_icon.png',
      'thumb_profile' => 'application/themes/midnight/images/nophoto_user_thumb_profile.png',
    ),
    'group' => array(
      'thumb_normal' => 'application/themes/midnight/images/nophoto_event_thumb_normal.jpg',
      'thumb_profile' => 'application/themes/midnight/images/nophoto_event_thumb_profile.jpg',
    ),
    'event' => array(
      'thumb_normal' => 'application/themes/midnight/images/nophoto_event_thumb_normal.jpg',
      'thumb_profile' => 'application/themes/midnight/images/nophoto_event_thumb_profile.jpg',
    ),
  ),
) ?>