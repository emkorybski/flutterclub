<?php

return array(
  'package' => array(
    'type' => 'module',
    'name' => 'news',
	'title' => 'News',
    'description' => 'Get RSS feeds from remote servers',
    'author' => 'YouNet Company',
    'version' => '4.06p3',
    'path' => 'application/modules/News',
    'repository' => 'younetco.com',
    'meta' => array(
      'title' => 'News',
      'description' => 'Get RSS feeds from remote servers',
      'author' => 'YouNet Company',
    ),
	'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.0',
      ),
      array(
         'type' => 'module',
         'name' => 'younet-core',
         'minVersion' => '4.01',
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
      'class' => 'Engine_Package_Installer_Module',
      'priority' => 4000,
    ),
    'directories' => array(
      'application/modules/News',
    ),
    'files' => array(
      'application/languages/en/news.csv',
	  'application/modules/Core/View/Helper/FeedDescription.php',
    ),
  ),
  // Content -------------------------------------------------------------------
  
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
	
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'news',  	
    'news_category',
    'news_content',
  	'news_nusers',
  	'Categories',
  	'categories',
    'Categoryparents',
    'categoryparents',
  	'Contents',
  	'contents',
  	'news_contents',
  	'news_param'
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array( 	
    'news_specific' => array(
      'route' => 'news/:id/:title',
      'defaults' => array(
    	'module' => 'news',
    	'controller' => 'index',
    	'action' => 'detail'
     ),
     'reqs' => array(
    	'action' => '(detail)',
     	'id' => '\d+',
        'title',
     )
    ), 
    'news_category' => array(
      'route' => 'news/list',
      'defaults' => array(
    	'module' => 'news',
    	'controller' => 'index',
    	'action' => 'list'
     ),
     'reqs' => array(
    	'action' => '(list)',
     	'id' => '\d+',
     	'title' => '\d+'
     )
    ),    
    'news_general' => array(
      'route' => 'news/:action/:page',
      'defaults' => array(
    	'module' => 'news',
    	'controller' => 'index',
    	'action' => 'index',
        'page' => 1,
     ),
     'reqs' => array(
    	'page' => '\d+',
        'action' => '(index|manage|featured|lists)',
     )
    ),  
    'news_xml' => array(
      'route' => 'news/readxml',
      'defaults' => array(
    	'module' => 'news',
    	'controller' => 'index',
    	'action' => 'readxml'
     ),
     'reqs' => array(
    	'action' => '(readxml)',
     )
    ),  
      'news_edit_news' => array(
      'route' => 'news/edit/*',
      'defaults' => array(
        'module' => 'news',
        'controller' => 'index',
        'action' => 'edit',
      ),
    ),

       'news_loadFeed' => array(
      'route' => 'news/loadfeed/*',
      'defaults' => array(
        'module' => 'news',
        'controller' => 'index',
        'action' => 'loadfeed',
      ),
    ),
  )
) ?>