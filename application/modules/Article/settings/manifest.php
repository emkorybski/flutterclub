<?php 

/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Article
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */

return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'article',
    'version' => '4.1.6',
    'path' => 'application/modules/Article',
    'repository' => 'radcodes.com',
    'title' => 'Articles',
    'description' => 'This plugin allows your social network users to post and share articles, attach photos, comments.',
    'author' => 'Radcodes LLC',
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),
    'callback' => array(
      'path' => 'application/modules/Article/settings/install.php',
      'class' => 'Article_Installer',
    ),
    'dependencies' => array(
      'radcodes' => array(
        'type' => 'module',
        'name' => 'radcodes',
        'minVersion' => '4.0.7'
      )
    ),
    'directories' => array(
      'application/modules/Article',
    ),
    'files' => array(
      'application/languages/en/article.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onStatistics',
      'resource' => 'Article_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Article_Plugin_Core',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'article',
    'article_category',
    'article_album',
    'article_photo'
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'article_extended' => array(
      'route' => 'articles/:controller/:action/*',
      'defaults' => array(
        'module' => 'article',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'controller' => '\D+',
        'action' => '\D+',
      )
    ),
    'article_general' => array(
      'route' => 'articles/:action/*',
      'defaults' => array(
        'module' => 'article',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'action' => '(browse|create|manage|tags|upload-photo)',
      ),
    ),    
    // Public
    'article_home' => array(
      'route' => 'articles',
      'defaults' => array(
        'module' => 'article',
        'controller' => 'index',
        'action' => 'index'
      )
    ),
    'article_browse' => array(
      'route' => 'articles/browse/*',
      'defaults' => array(
        'module' => 'article',
        'controller' => 'index',
        'action' => 'browse',
      )
    ),  
    'article_tags' => array(
      'route' => 'articles/tags/*',
      'defaults' => array(
        'module' => 'article',
        'controller' => 'index',
        'action' => 'tags',
      )
    ),       
    'article_entry_view_old' => array(
      'route' => 'articles/:user_id/:article_id/:slug',
      'defaults' => array(
        'module' => 'article',
        'controller' => 'index',
        'action' => 'view',
    	  'slug' => ''
      ),
      'reqs' => array(
        'user_id' => '\d+',
        'article_id' => '\d+'
      )
    ),
    'article_entry_view' => array(
      'route' => 'articles/:article_id/:slug/*',
      'defaults' => array(
        'module' => 'article',
        'controller' => 'profile',
        'action' => 'index',
        'slug' => ''
      ),
      'reqs' => array(
        'article_id' => '\d+'
      )
    ),    
    // User
    'article_create' => array(
      'route' => 'articles/create',
      'defaults' => array(
        'module' => 'article',
        'controller' => 'index',
        'action' => 'create'
      )
    ),
    'article_manage' => array(
      'route' => 'articles/manage/*',
      'defaults' => array(
        'module' => 'article',
        'controller' => 'index',
        'action' => 'manage',
      )
    ),
    'article_specific' => array(
      'route' => 'articles/:article_id/:action/*',
      'defaults' => array(
        'module' => 'article',
        'controller' => 'index',
      ),
      'reqs' => array(
        'action' => '(edit|publish|delete|success)',
        'article_id' => '\d+',
      )
    ),    
    
    'article_delete' => array(
      'route' => 'articles/delete/:article_id',
      'defaults' => array(
        'module' => 'article',
        'controller' => 'index',
        'action' => 'delete'
      )
    ),   
    'article_publish' => array(
      'route' => 'articles/publish/:article_id',
      'defaults' => array(
        'module' => 'article',
        'controller' => 'index',
        'action' => 'publish'
      )
    ),
    
    'article_success' => array(
      'route' => 'articles/success/:article_id',
      'defaults' => array(
        'module' => 'article',
        'controller' => 'index',
        'action' => 'success'
      )
    ),

    // Admin
    'article_admin_manage_level' => array(
      'route' => 'admin/article/level/:level_id',
      'defaults' => array(
        'module' => 'article',
        'controller' => 'admin-level',
        'action' => 'index',
        'level_id' => 1
      )
    ),
  ),
);
