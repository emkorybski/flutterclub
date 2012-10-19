<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'auto-friender',
    'version' => '4.1.6.2',
    'path' => 'application/modules/AutoFriender',
    'title' => 'Auto Friender',
    'description' => 'Auto Friender',
    'author' => 'Technobd',
    'callback' => 
    array (
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
      0 => 'application/modules/AutoFriender',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/auto-friender.csv',
    ),
  ),
  'hooks' => array(
      array(
          'event' => 'onUserCreateAfter',
          'resource' => 'AutoFriender_Plugin_Signup',
      ),
  ),
); ?>