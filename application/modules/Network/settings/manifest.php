<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Network
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 9688 2012-04-18 23:32:12Z richard $
 * @author     Sami
 */
return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'network',
    'version' => '4.2.3',
    'revision' => '$Revision: 9688 $',
    'path' => 'application/modules/Network',
    'repository' => 'socialengine.net',
    'title' => 'Networks',
    'description' => 'Networks',
    'author' => 'Webligo Developments',
    'changeLog' => 'settings/changelog.php',
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.2.0',
      ),
      array(
        'type' => 'module',
        'name' => 'fields',
        'minVersion' => '4.2.0',
      ),
    ),
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       //'enable',
       //'disable',
     ),
    'callback' => array(
      'class' => 'Engine_Package_Installer_Module',
      'priority' => 4000,
    ),
    'directories' => array(
      'application/modules/Network',
    ),
    'files' => array(
      'application/languages/en/network.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onFieldsValuesSave',
      'resource' => 'Network_Plugin_User',
    ),
    array(
      'event' => 'onUserCreateAfter',
      'resource' => 'Network_Plugin_User',
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Network_Plugin_User',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'network'
  ),
  // Routes --------------------------------------------------------------------
);