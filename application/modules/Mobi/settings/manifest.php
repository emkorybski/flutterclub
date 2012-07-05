<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 9652 2012-03-16 01:21:32Z john $
 * @author     Charlotte
 */
return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'mobi',
    'version' => '4.2.2',
    'revision' => '$Revision: 9652 $',
    'path' => 'application/modules/Mobi',
    'repository' => 'socialengine.net',
    'title' => 'Mobi',
    'description' => 'Mobile',
    'author' => 'Webligo Developments',
    'changeLog' => 'settings/changelog.php',
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.2.0',
      ),
    ),
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),
    'callback' => array(
      'path' => 'application/modules/Mobi/settings/install.php',
      'class' => 'Mobi_Installer',
    ),
    'directories' => array(
      'application/modules/Mobi',
    ),
    'files' => array(
      'application/languages/en/mobi.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  // Items ---------------------------------------------------------------------
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'mobi_general' => array(
      'route' => 'mobi/:action/*',
      'defaults' => array(
        'module' => 'mobi',
        'controller' => 'browse',
        'action' => '(browse)',
      ),
      'reqs' => array(
        'action' => '\D+',
      )
    ),
  )
) ?>