<?php

return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'friendsinvitersocial',
    'version' => '4.0.0',
    'path' => 'application/modules/Friendsinvitersocial',
    'repository' => 'socialenginemods.net',
    'title' => 'Friends Inviter Social Networks Extension',
    'description' => 'Friends Inviter Social Networks Extension',
    'author' => 'SocialEngineMods',
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),
    'callback' => array(
      'path' => 'application/modules/Friendsinvitersocial/settings/install.php',
      'class' => 'Friendsinvitersocial_Installer',
    ),
    'directories' => array(
      'application/modules/Friendsinvitersocial',
    ),
    'files' => array(
      'application/languages/en/friendsinvitersocial.csv',
    ),
  // Items ---------------------------------------------------------------------
    'items' => array(
      'friendsinvitersocial'
    ),
    'routes' => array(
      // Public
      // User
      'friendsinvitersocial' => array(
        'route' => 'friendsinvitersocial',
        'defaults' => array(
          'module' => 'friendsinvitersocial',
          'controller' => 'index',
          'action' => 'index'
        )
      ),
    ),
  ),
);