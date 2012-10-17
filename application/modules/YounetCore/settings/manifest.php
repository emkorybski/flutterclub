<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'younet-core',
    'version' => '4.02p4',
    'path' => 'application/modules/YounetCore',
    'title' => 'YouNet Core Module',
    'description' => 'YouNet Core Module',
    'author' => 'YouNet Company',
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
      0 => 'application/modules/YounetCore',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/younet-core.csv',
    ),
  ),
); ?>