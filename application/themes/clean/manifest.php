<?php
/**
 * SocialEngine
 *
 * @category   Application_Theme
 * @package    Default
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 9378 2011-10-13 22:50:30Z john $
 * @author     Alex
 */
return array(
  'package' => array(
    'type' => 'theme',
    'name' => 'clean',
    'version' => '4.2.0',
    'revision' => '$Revision: 9378 $',
    'path' => 'application/themes/clean',
    'repository' => 'socialengine.net',
    'title' => 'Clean',
    'thumb' => 'theme.jpg',
    'author' => 'Webligo Developments',
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
      'application/themes/clean',
    ),
  ),
  'files' => array(
    'theme.css',
    'constants.css',
  ),
) ?>