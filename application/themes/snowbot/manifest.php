<?php
/**
 * SocialEngine
 *
 * @category   Application_Theme
 * @package    Bamboo
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 9579 2012-01-06 00:00:44Z john $
 * @author     Bryan
 */

return array(
  'package' => array(
    'type' => 'theme',
    'name' => 'snowbot',
    'version' => '4.2.0',
    'revision' => '$Revision: 9579 $',
    'path' => 'application/themes/snowbot',
    'repository' => 'socialengine.net',
    'title' => 'Snowbot',
    'thumb' => 'snowbot_theme.jpg',
    'author' => 'Webligo Developments',
    'changeLog' => array(
      '4.2.0' => array(
        'manifest.php' => 'Incremented version',
        'theme.css' => 'Fixed issue with feed comment option list',
      ),
      '4.1.8p1' => array(
        'manifest.php' => 'Incremented version',
        'theme.css' => 'Fixed issue with new pages in the layout editor',
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
      'application/themes/snowbot',
    ),
  ),
  'files' => array(
    'theme.css',
    'constants.css',
  )
) ?>