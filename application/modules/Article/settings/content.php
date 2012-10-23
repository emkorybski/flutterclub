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

  // ------- on user profile tab

  array(
    'title' => 'Profile Articles',
    'description' => 'Displays a member\'s articles on their profile. It also supports displaying articles that are created by specific page/subject owner, example: when use this widget on Group Profile page, and config User=OWNER mode, it would shows articles created by the group owner. If you set User=VIEWER mode, then the widget will displays articles created by current logged in member.',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.profile-articles',
    'defaultParams' => array(
      'title' => 'Articles',
      'titleCount' => true,
      'max' => 5,
      'user_type' => 'owner',
      'order' => 'recent',
      'display_style' => 'wide',
    ),
    'adminForm' => array(
      'description' => 'Displays a member\'s articles on their profile. It also supports displaying articles that are created by specific page/subject owner, example: when use this widget on Group Profile page, and config User=OWNER mode, it would shows articles created by the group owner. If you set User=VIEWER mode, then the widget will displays articles created by current logged in member.',
      'attribs' => array(
        'class' => 'article_widget_form'
      ),
      'elements' => array(
      
        Article_Form_Helper::getContentField('title', array('value' => 'Articles')),
        Article_Form_Helper::getContentField('max', array('value' => 5)),
        Article_Form_Helper::getContentField('user_type', array('value' => 'owner')),
        Article_Form_Helper::getContentField('order'),
        Article_Form_Helper::getContentField('period'),
        
        Article_Form_Helper::getContentField('keyword'),
        Article_Form_Helper::getContentField('company'),
        Article_Form_Helper::getContentField('category'),
        Article_Form_Helper::getContentField('featured'),
        Article_Form_Helper::getContentField('sponsored'),
        Article_Form_Helper::getContentField('display_style'),
        Article_Form_Helper::getContentField('showphoto'),
        Article_Form_Helper::getContentField('showdescription'),
        Article_Form_Helper::getContentField('showmeta'),
        Article_Form_Helper::getContentField('showmemberitemlist'),        
               
      ),
    ),     
  ),
  
  // ------- list articles
  
  array(
    'title' => 'List Articles',
    'description' => 'Displays a list of posted articles with different filtering options (can be used to build variety of article listings such as Recent Articles, Most Commented by XYZ user with specified category etc..)',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.list-articles',
    'defaultParams' => array(
      'title' => 'Articles',
      'titleCount' => true,
      'max' => 5,
      'display_style' => 'wide',
      'order' => 'recent'
    ),  
    'adminForm' => array(
      'attribs' => array(
        'class' => 'article_widget_form'
      ),
      'elements' => array(
        Article_Form_Helper::getContentField('title', array('value' => 'Articles')),
        Article_Form_Helper::getContentField('max'),
        Article_Form_Helper::getContentField('order'),
        Article_Form_Helper::getContentField('period'),
        Article_Form_Helper::getContentField('user'),
        Article_Form_Helper::getContentField('keyword'),
        Article_Form_Helper::getContentField('category'),
        Article_Form_Helper::getContentField('featured'),
        Article_Form_Helper::getContentField('sponsored'),
        Article_Form_Helper::getContentField('display_style'),
        Article_Form_Helper::getContentField('showphoto'),
        Article_Form_Helper::getContentField('showdescription'),
        Article_Form_Helper::getContentField('showmeta'),
        Article_Form_Helper::getContentField('showemptyresult'),
      ),
    ),    
  ),  

  
  // ------- article categories
  
  array(
    'title' => 'Article Categories',
    'description' => 'Displays a list of article categories.',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.list-categories',
    'defaultParams' => array(
      'title' => '',
      'showphoto' => 1,
      'showdetails' => 0,
      'descriptionlength' => 68,
    ), 

    'adminForm' => array(
      'attribs' => array(
        'class' => 'article_widget_form'
      ),    
      'elements' => array(
        Article_Form_Helper::getContentField('title', array('value' => '')),
        Article_Form_Helper::getContentField('showphoto', array('value' => 1)),
        Article_Form_Helper::getContentField('showdetails', array('value' => 0)),
        array(
          'Text',
          'descriptionlength',
          array(
            'label' => 'Max Description Characters',
            'value' => 68,
          ),
        ),
      ),   
    ),    
  ),   
  
 
  
  
  // ------- featured articles
  
  array(
    'title' => 'Featured Articles',
    'description' => 'Displays slideshow of featured articles with different filtering options (wide mode)',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.list-featured',
    'defaultParams' => array(
      'title' => 'Featured Articles',
    ),   
    'adminForm' => array(
      'attribs' => array(
        'class' => 'article_widget_form'
      ),    
      'elements' => array(
        Article_Form_Helper::getContentField('title', array('value' => 'Featured Articles')),
        Article_Form_Helper::getContentField('max', array('value' => 5)),
        Article_Form_Helper::getContentField('order', array('value' => 'random')),
        Article_Form_Helper::getContentField('period'),
        Article_Form_Helper::getContentField('user'),
        Article_Form_Helper::getContentField('keyword'),
        Article_Form_Helper::getContentField('category'),
        Article_Form_Helper::getContentField('showphoto'),
        Article_Form_Helper::getContentField('showdescription'),
        Article_Form_Helper::getContentField('showmeta'),
      ),
    ),    
  ),   
  
  // ------- sponsored articles
  
  array(
    'title' => 'Sponsored Articles',
    'description' => 'Displays a list of sponsored articles.',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.list-sponsored',
    'defaultParams' => array(
      'title' => 'Sponsored Articles',
      'max' => 5,
    ),   
    'adminForm' => array(
      'attribs' => array(
        'class' => 'article_widget_form'
      ),    
      'elements' => array(
        Article_Form_Helper::getContentField('title', array('value' => 'Sponsored Articles')),
        Article_Form_Helper::getContentField('max', array('value' => 5)),
        Article_Form_Helper::getContentField('order', array('value' => 'random')),
        Article_Form_Helper::getContentField('period'),
        Article_Form_Helper::getContentField('user'),
        Article_Form_Helper::getContentField('keyword'),
        Article_Form_Helper::getContentField('category'),
        Article_Form_Helper::getContentField('showphoto'),
        Article_Form_Helper::getContentField('showdescription'),
        Article_Form_Helper::getContentField('showmeta'),
      ),
    ),  
  ),   

  // ------- top menu nav
  array(
    'title' => 'Menu Articles',
    'description' => 'Displays a menu navigation (Browse Article, My Articles, Post New Article) on article home page.',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.list-menu',
  ), 
  
  // ------- search form
  
  array(
    'title' => 'Search Articles',
    'description' => 'Displays search form on article home page.',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.list-search',
  ), 
  
  // ------- create new article
  array(
    'title' => 'Post New Article',
    'description' => 'Displays a quick navigation article to post new article',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.create-new',
  ),    
  
  // ------- popular tags
  
  array(
    'title' => 'Popular Tags',
    'description' => 'Displays article popular tags.',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.list-tags',
    'defaultParams' => array(
      'title' => 'Popular Tags',
      'max' => 100,
      'order' => 'text',
    ),
    'adminForm' => array(
      'elements' => array(
        Article_Form_Helper::getContentField('title', array('value' => 'Popular Tags')),
        Article_Form_Helper::getContentField('max', array('label' => 'Max Tags', 'value' => 100)),
        Article_Form_Helper::getContentField('order', array('value' => 'text', 'multiOptions' => array('text' => 'Tag Name','total' => 'Total Count'))), 
        Article_Form_Helper::getContentField('showlinkall'),              
      ),
    ),     
  ),


  // ------- browse articles
  
  array(
    'title' => 'Browse Articles - List',
    'description' => 'Displays articles on Browse Articles page',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.browse-articles',
  ),  
  
  array(
    'title' => 'Browse Articles - Member',
    'description' => 'Displays member info of current listing member\'s articles on Browse Articles page',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.browse-articles-member',
  ),  
  
  // ------- manage articles
  
  array(
    'title' => 'My Articles - List',
    'description' => 'Displays member articles on (Manage) My Articles page',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.manage-articles',
  ), 

  // ------- manage search
  
  array(
    'title' => 'My Articles - Search',
    'description' => 'Displays search form  on (Manage) My Articles page',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.manage-search',
  ),   
  
  // ------- top submitters
  array(
    'title' => 'Top Article Submitters',
    'description' => 'Displays list of top article\'s submitters',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.top-submitters',
    'defaultParams' => array(
      'title' => 'Top Posters',
      'max' => 5,
    ),  
    'adminForm' => array(
      'elements' => array(
        Article_Form_Helper::getContentField('title', array('value' => 'Top Posters')),
        Article_Form_Helper::getContentField('max', array('label' => 'Max Items')),
        Article_Form_Helper::getContentField('period'),
      ),
    ),    
  ), 
  
  
  // ========================= ARTICLE PROFILE WIDGETS (article view page) ===========================
  // ------- article profile body
  array(
    'title' => 'Article - Profile Body',
    'description' => 'Displays an article\'s body on its profile.',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.profile-body',      
  ), 
  
  // ------- article profile breadcrumb
  array(
    'title' => 'Article - Profile Breadcrumb',
    'description' => 'Displays an article\'s breadcrumb on its profile.',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.profile-breadcrumb',      
  ), 
  
  // ------- article profile comments
  array(
    'title' => 'Article - Profile Comments',
    'description' => 'Displays an article\'s comments on its profile.',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.profile-comments',    
    'defaultParams' => array(
      'title' => 'Comments',
    ),  
    'adminForm' => array(
      'elements' => array(
        Article_Form_Helper::getContentField('title', array('value' => 'Comments')),
      ),
    ),    
  ),
  
  // ------- article profile description
  array(
    'title' => 'Article - Profile Description',
    'description' => 'Displays an article\'s description on its profile.',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.profile-description',        
  ), 
  
  // ------- article profile details
  array(
    'title' => 'Article - Profile Details',
    'description' => 'Displays an article\'s details (customized questions/fields data) on its profile.',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.profile-details',    
    'defaultParams' => array(
      'title' => 'Details',
    ),  
    'adminForm' => array(
      'elements' => array(
        Article_Form_Helper::getContentField('title', array('value' => 'Details')),
      ),
    ),    
  ), 
  
  // ------- article profile icon featured
  array(
    'title' => 'Article - Profile Icon Featured',
    'description' => 'Displays a icon for featured article on its profile.',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.profile-icon-featured',    
    'defaultParams' => array(
      'title' => '',
      'text' => 'FEATURED ARTICLE',
    ),  
    'adminForm' => array(
      'elements' => array(
        Article_Form_Helper::getContentField('title', array('value' => '')),
        Article_Form_Helper::getContentField('text', array('label'=>'Icon Text', 'value' => 'FEATURED ARTICLE')),
        Article_Form_Helper::getContentField('image', array('label' => 'Icon Image URL', 'value' => '')),
      ),
    ),    
  ),
  
  // ------- article profile icon sponsored
  array(
    'title' => 'Article - Profile Icon Sponsored',
    'description' => 'Displays a icon for sponsored article on its profile.',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.profile-icon-sponsored',    
    'defaultParams' => array(
      'title' => '',
      'text' => 'SPONSORED ARTICLE',
    ),  
    'adminForm' => array(
      'elements' => array(
        Article_Form_Helper::getContentField('title', array('value' => '')),
        Article_Form_Helper::getContentField('text', array('label'=>'Icon Text', 'value' => 'SPONSORED ARTICLE')),
        Article_Form_Helper::getContentField('image', array('label' => 'Icon Image URL', 'value' => '')),
      ),
    ),    
  ),  
  
  // ------- article profile info
  array(
    'title' => 'Article - Profile Info',
    'description' => 'Displays an article\'s info (owner, category, views, comments etc..) on its profile.',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.profile-info',      
  ),  
  
  // ------- article profile notice
  array(
    'title' => 'Article - Profile Notice',
    'description' => 'Displays an article\'s system notice such as approval / publish status etc..',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.profile-notice', 
  ),
 
  
  // ------- article profile options
  array(
    'title' => 'Article - Profile Options',
    'description' => 'Displays an article\'s options (sidebar navigation: All Submitter Articles | Post New Article | Edit This Article | Delete This Article) on its profile.',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.profile-options',    
    'defaultParams' => array(
      'title' => '',
    ),  
    'adminForm' => array(
      'elements' => array(
        Article_Form_Helper::getContentField('title', array('value' => '')),
      ),
    ),    
  ),
  
  // ------- article profile photo
  array(
    'title' => 'Article - Profile Photo',
    'description' => 'Displays an article\'s main photo.',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.profile-photo',    
    'defaultParams' => array(
      'title' => '',
    ),  
    'adminForm' => array(
      'elements' => array(
        Article_Form_Helper::getContentField('title', array('value' => '')),
      ),
    ),    
  ), 
  
  // ------- article profile photos
  array(
    'title' => 'Article - Profile Photos',
    'description' => 'Displays an article\'s photo album / gallery.',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.profile-photos',    
    'defaultParams' => array(
      'title' => 'Photos',
    ),  
    'adminForm' => array(
      'elements' => array(
        Article_Form_Helper::getContentField('title', array('value' => 'Photos')),
      ),
    ),    
  ),  
  
  // ------- article profile related articles
  array(
    'title' => 'Article - Profile Related Articles',
    'description' => 'Displays an article\'s related articles (by tags) on its profile.',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.profile-related-articles',    
    'defaultParams' => array(
  	  'titleCount' => true,
      'title' => 'Related Articles',
      'max' => 5,
      'order' => 'random',
    ),  
    'adminForm' => array(
      'attribs' => array(
        'class' => 'article_widget_form'
      ),
      'elements' => array(
      
        Article_Form_Helper::getContentField('title', array('value' => 'Related Articles')),
        Article_Form_Helper::getContentField('max'),
        Article_Form_Helper::getContentField('order', array('value' => 'recent')),
        Article_Form_Helper::getContentField('period'),
        Article_Form_Helper::getContentField('user'),
        Article_Form_Helper::getContentField('keyword'),
        Article_Form_Helper::getContentField('category'),
        Article_Form_Helper::getContentField('featured'),
        Article_Form_Helper::getContentField('sponsored'),        
        Article_Form_Helper::getContentField('showphoto'),
      ),
    ),    
  ),  

  // ------- article profile social shares
  array(
    'title' => 'Article - Profile Social Shares',
    'description' => 'Displays an article\'s social shares such as Facebook, Twitter, Digg using AddThis service on its profile.',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.profile-social-shares',       
  ),

  // ------- article profile submitter
  array(
    'title' => 'Article - Profile Submitter',
    'description' => 'Displays an article\'s submitter on its profile.',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.profile-submitter',    
    'defaultParams' => array(
      'title' => 'Submitter',
    ),  
    'adminForm' => array(
      'elements' => array(
        Article_Form_Helper::getContentField('title', array('value' => 'Submitter')),
      ),
    ),    
  ),
    
  // ------- article profile tags
  array(
    'title' => 'Article - Profile Tags',
    'description' => 'Displays an article\'s tags on its profile.',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.profile-tags',    
    'defaultParams' => array(
      'title' => '',
    ),  
    'adminForm' => array(
      'elements' => array(
        Article_Form_Helper::getContentField('title', array('value' => '')),
      ),
    ),    
  ),   
  
  // ------- article profile title
  array(
    'title' => 'Article - Profile Title',
    'description' => 'Displays an article\'s title on its profile.',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.profile-title',       
  ),
  
  // ------- article profile tools
  array(
    'title' => 'Article - Profile Tools',
    'description' => 'Displays an article\'s tools (Share | Report) on its profile.',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.profile-tools',       
  ),
    
     array(
    'title' => 'Flutterclub TV',
    'description' => 'Flutterclub promotional videos.',
    'category' => 'Articles',
    'type' => 'widget',
    'name' => 'article.flutterclub-tv',

    ),
  
) ?>