<?php return array (
  'package' => 
  array (
    'type' => 'theme',
    'name' => 'pink_and_black',
    'version' => NULL,
    'revision' => '$Revision: 9378 $',
    'path' => 'application/themes/pink_and_black',
    'repository' => 'socialengine.net',
    'title' => 'pink_and_black',
    'thumb' => 'theme.jpg',
    'author' => 'Flutterclub',
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'remove',
    ),
    'callback' => 
    array (
      'class' => 'Engine_Package_Installer_Theme',
    ),
    'directories' => 
    array (
      0 => 'application/themes/clean',
    ),
    'description' => '',
  ),
  'files' => 
  array (
    0 => 'theme.css',
    1 => 'constants.css',
  ),
); ?>