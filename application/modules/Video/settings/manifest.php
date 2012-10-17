<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 9688 2012-04-18 23:32:12Z richard $
 * @author     Jung
 */
return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'video',
    'version' => '4.2.3',
    'revision' => '$Revision: 9688 $',
    'path' => 'application/modules/Video',
    'repository' => 'socialengine.net',
    'title' => 'Videos',
    'description' => 'Videos',
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
      'path' => 'application/modules/Video/settings/install.php',
      'class' => 'Video_Installer',
    ),
    'directories' => array(
      'application/modules/Video',
    ),
    'files' => array(
      'application/languages/en/video.csv',
    ),
  ),
  // Compose
  'composer' => array(
    'video' => array(
      'script' => array('_composeVideo.tpl', 'video'),
      'plugin' => 'Video_Plugin_Composer',
      'auth' => array('video', 'create'),
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'video',
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onStatistics',
      'resource' => 'Video_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Video_Plugin_Core',
    ),
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'video_general' => array(
      'route' => 'videos/:action/*',
      'defaults' => array(
        'module' => 'video',
        'controller' => 'index',
        'action' => 'browse',
      ),
      'reqs' => array(
        'action' => '(index|browse|create|list|manage)',
      )
    ),
    'video_view' => array(
      'route' => 'videos/:user_id/:video_id/:slug/*',
      'defaults' => array(
        'module' => 'video',
        'controller' => 'index',
        'action' => 'view',
        'slug' => '',
      ),
      'reqs' => array(
        'user_id' => '\d+'
      )
    ),
  )
) ?>