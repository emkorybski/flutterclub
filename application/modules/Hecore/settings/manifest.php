<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'hecore',
    'version' => '4.2.0p4',
    'path' => 'application/modules/Hecore',
    'title' => 'Hire-Experts Core Module',
    'description' => 'Hire-Experts Core Module',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    'meta' =>
    array (
      'title' => 'Hire-Experts Core Module',
      'description' => 'Hire-Experts Core Module',
      'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    ),
    'callback' => array (
      'class' => 'Engine_Package_Installer_Module',
    ),
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' => 
    array (
      0 => 'application/modules/Hecore',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/hecore.csv',
    ),
  ),
  'items' => array(
    'featureds'
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onRenderLayoutDefault',
      'resource' => 'Hecore_Plugin_Core',
    ),
    array(
      'event' => 'onRenderLayoutAdmin',
      'resource' => 'Hecore_Plugin_Core',
    ),
    array(
      'event' => 'onRenderLayoutAdminSimple',
      'resource' => 'Hecore_Plugin_Core',
    ),
    array(
      'event' => 'onRenderLayoutDefaultSimple',
      'resource' => 'Hecore_Plugin_Core',
    )
  ),
  // Routes ---------------------------------------------------------------------
  'routes' => array(
      
    'hecore_module' => array(
      'route' => 'hecore-module/:action/*',
      'defaults' => array(
        'module' => 'hecore',
        'controller' => 'module',
        'action' => 'index',
      )
    ),

    'hecore_index' => array(
      'route' => 'hecore/:controller/:action/*',
      'defaults' => array(
        'module' => 'hecore',
        'controller' => 'index',
        'action' => 'index',
      )
    ),

    'hecore_friend' => array(
      'route' => 'hecore-friend/:action/*',
      'defaults' => array(
        'module' => 'hecore',
        'controller' => 'friend',
        'action' => 'index',
      )
    ),
  )
);