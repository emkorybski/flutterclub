<?php

return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'friendsinviter',
    'version' => '4.0.4',
    'path' => 'application/modules/Friendsinviter',
    'repository' => 'socialenginemods.net',
    'meta' => array(
      'title' => 'Friends Inviter Basic',
      'description' => 'Friends Inviter Basic',
      'author' => 'SocialEngineMods',
    ),
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion'  => '4.0.4',
        'required' => true
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
      'path' => 'application/modules/Friendsinviter/settings/install.php',
      'class' => 'Friendsinviter_Installer',
    ),
    'directories' => array(
      'application/modules/Friendsinviter',
    ),
    'files' => array(
      'application/languages/en/friendsinviter.csv',
    ),
  ),
  // Content -------------------------------------------------------------------
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onUserCreateAfter',
      'resource' => 'Friendsinviter_Plugin_Signup',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'friendsinviter'
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    // Public
    // User
    'invite' => array(
      'route' => 'invite',
      'defaults' => array(
        'module' => 'friendsinviter',
        'controller' => 'index',
        'action' => 'index'
      )
    ),

    'friendsinviter_pending' => array(
      'route' => 'invite/pending',
      'defaults' => array(
        'module' => 'friendsinviter',
        'controller' => 'index',
        'action' => 'pending'
      )
    ),
    'friendsinviter_stats' => array(
      'route' => 'invite/stats',
      'defaults' => array(
        'module' => 'friendsinviter',
        'controller' => 'index',
        'action' => 'stats'
      )
    ),
    'friendsinviter_reflink' => array(
      'route' => 'invite/reflink',
      'defaults' => array(
        'module' => 'friendsinviter',
        'controller' => 'index',
        'action' => 'reflink'
      )
    ),
    'friendsinviter_unsubscribe' => array(
      'route' => 'invite/unsubscribe',
      'defaults' => array(
        'module' => 'friendsinviter',
        'controller' => 'index',
        'action' => 'unsubscribe'
      )
    ),

    // Admin
    //'friendsinviter_admin_settings' => array(
    //  'route' => 'admin/settings/friendsinviter',
    //  'defaults' => array(
    //    'module' => 'friendsinviter',
    //    'controller' => 'admin',
    //    'action' => 'index'
    //  )
    //),
    //'friendsinviter_admin_settings' => array(
    //  'route' => 'admin/friendsinviter/settings',
    //  'defaults' => array(
    //    'module' => 'friendsinviter',
    //    'controller' => 'settings',
    //    'action' => 'index'
    //  )
    //),
    //'friendsinviter_admin_settings' => array(
    //  'route' => 'admin/friendsinviter/settings',
    //  'defaults' => array(
    //    'module' => 'friendsinviter',
    //    'controller' => 'admin',
    //    'action' => 'settings'
    //  )
    //),
    //'friendsinviter_admin_stats' => array(
    //  'route' => 'admin/friendsinviter/stats',
    //  'defaults' => array(
    //    'module' => 'friendsinviter',
    //    'controller' => 'admin',
    //    'action' => 'stats'
    //  )
    //),
  // end routes
  ),
);