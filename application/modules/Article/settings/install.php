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
 
 
 
class Article_Installer extends Engine_Package_Installer_Module
{
  
  public function removeCustomPages()
  {
    $db     = $this->getDb();
    
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_menus')
      ->where('name = ?', 'article_quick')
      ->limit(1);
      ;
    $info = $select->query()->fetch();
    
    if (empty($info))
    {
      //echo 'not exist'; // pre v4.1.0
      
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_core_pages')
        ->where('name = ?', 'article_index_index')
        ->limit(1);
        ;
      $info = $select->query()->fetch();
      
      if (!empty($info))
      {
        $page_id = $info['page_id'];
        
        $where = $db->quoteInto('page_id = ?', $page_id);
        
        $db->delete('engine4_core_content', $where);
        $db->delete('engine4_core_pages', $where);
      }
      
      $removed_widgets_where = $db->quoteInto('name IN (?)', array('article.list-recent','article.list-most-commented','article.list-most-liked','article.list-most-viewed'));
      
      //echo $removed_widgets_where;
      $db->delete('engine4_core_content', $removed_widgets_where);
    }

  }
  
  public function addBrowsePage()
  {
    // article Home page
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);
    
      // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'article_index_browse')
      ->limit(1);
      ;
    $info = $select->query()->fetch();

    if( empty($info) ) {

      $db->insert('engine4_core_pages', array(
        'name' => 'article_index_browse',
        'displayname' => 'Article Browse Page',
        'title' => 'Article Browse Page',
        'description' => 'This is the browse page for articles.',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      $i = 1;
      
      // CONTAINERS (TOP / MAIN)
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'top',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $top_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $main_id = $db->lastInsertId('engine4_core_content');

      // ---------- CONTAINER TOP & WIDGET MENU -----------
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $top_id,
        'order' => $i++,
        'params' => '',
      ));
      $top_middle_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.list-menu',
        'parent_content_id' => $top_middle_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
      $top_middle_menu_id = $db->lastInsertId('engine4_core_content');
      
      // ---------- CONTAINER MAIN -----------      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_right_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_middle_id = $db->lastInsertId('engine4_core_content');
      
    
      // ------ MAIN :: RIGHT WIDGETS

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.browse-articles-member',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
    
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.list-search',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
    
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.create-new',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));

      // ------ MAIN :: MIDDLE WIDGETS   
      /*
      // tab
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.container-tabs',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '{"max":"6"}',
      ));
      // tab items
      $tab_id = $db->lastInsertId('engine4_core_content');
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.browse-articles',
        'parent_content_id' => $tab_id,
        'order' => $i++,
        'params' => '{"title":"Browse Articles"}',
      ));    
      */
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.browse-articles',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '{"title":"Browse Articles"}',
      ));
    
      
    }
  }
  // addBrowsePage
  
  
  public function addManagePage()
  {
    // article Home page
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);
    
      // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'article_index_manage')
      ->limit(1);
      ;
    $info = $select->query()->fetch();

    if( empty($info) ) {

      $db->insert('engine4_core_pages', array(
        'name' => 'article_index_manage',
        'displayname' => 'Article Manage Page',
        'title' => 'Article Manage Page',
        'description' => 'This is the manage page for articles.',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      $i = 1;
      
      // CONTAINERS (TOP / MAIN)
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'top',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $top_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $main_id = $db->lastInsertId('engine4_core_content');

      // ---------- CONTAINER TOP & WIDGET MENU -----------
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $top_id,
        'order' => $i++,
        'params' => '',
      ));
      $top_middle_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.list-menu',
        'parent_content_id' => $top_middle_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
      $top_middle_menu_id = $db->lastInsertId('engine4_core_content');
      
      // ---------- CONTAINER MAIN -----------      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_right_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_middle_id = $db->lastInsertId('engine4_core_content');
      
    
      // ------ MAIN :: RIGHT WIDGETS
    
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.manage-search',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
    
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.create-new',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));

      // ------ MAIN :: MIDDLE WIDGETS   
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.manage-articles',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '{"title":"My Articles"}',
      ));
    
      
    }
  }
  // addManagePage  
  
  public function addPopularTagsPage()
  {
    // article Home page
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);
    
      // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'article_index_tags')
      ->limit(1);
      ;
    $info = $select->query()->fetch();

    if( empty($info) ) {

      $db->insert('engine4_core_pages', array(
        'name' => 'article_index_tags',
        'displayname' => 'Article Popular Tags Page',
        'title' => 'Article Popular Tags Page',
        'description' => 'This is the popular tags page for articles.',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      $i = 1;
      
      // CONTAINERS (TOP / MAIN)
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'top',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $top_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $main_id = $db->lastInsertId('engine4_core_content');

      // ---------- CONTAINER TOP & WIDGET MENU -----------
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $top_id,
        'order' => $i++,
        'params' => '',
      ));
      $top_middle_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.list-menu',
        'parent_content_id' => $top_middle_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
      $top_middle_menu_id = $db->lastInsertId('engine4_core_content');
      
      // ---------- CONTAINER MAIN -----------      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_right_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_middle_id = $db->lastInsertId('engine4_core_content');
      
    
      // ------ MAIN :: RIGHT WIDGETS     
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.list-search',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
    
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.create-new',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));

      // ------ MAIN :: MIDDLE WIDGETS   
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.list-tags',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '{"title":"Popular Tags","max":"999","order":"text","showlinkall":"0"}',
      ));
      
    }
  }
  // addPopularTagsPage  
  
  public function addUserProfileTab()
  {
      //
    // install content areas
    //
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);

    // profile page
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'user_profile_index')
      ->limit(1);
    $page_id = $select->query()->fetchObject()->page_id;


    // article.profile-articles
    
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_content')
      ->where('page_id = ?', $page_id)
      ->where('type = ?', 'widget')
      ->where('name = ?', 'article.profile-articles')
      ;
    $info = $select->query()->fetch();
    if( empty($info) ) {
    
      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_core_content')
        ->where('page_id = ?', $page_id)
        ->where('type = ?', 'container')
        ->limit(1);
      $container_id = $select->query()->fetchObject()->content_id;

      // middle_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_core_content')
        ->where('parent_content_id = ?', $container_id)
        ->where('type = ?', 'container')
        ->where('name = ?', 'middle')
        ->limit(1);
      $middle_id = $select->query()->fetchObject()->content_id;

      // tab_id (tab container) may not always be there
      $select
        ->reset('where')
        ->where('type = ?', 'widget')
        ->where('name = ?', 'core.container-tabs')
        ->where('page_id = ?', $page_id)
        ->limit(1);
      $tab_id = $select->query()->fetchObject();
      if( $tab_id && @$tab_id->content_id ) {
          $tab_id = $tab_id->content_id;
      } else {
        $tab_id = null;
      }

      // tab on profile
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type'    => 'widget',
        'name'    => 'article.profile-articles',
        'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
        'order'   => 6,
        'params'  => '{"title":"Articles","titleCount":true}',
      ));

    }
  }
  
  
  public function addHomePage()
  {
    // Article Home page
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);
    
      // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'article_index_index')
      ->limit(1);
      ;
    $info = $select->query()->fetch();

    if( empty($info) ) {

      $db->insert('engine4_core_pages', array(
        'name' => 'article_index_index',
        'displayname' => 'Article Home Page',
        'title' => 'Article Home Page',
        'description' => 'This is the home page for articles.',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      $i = 1;
      
      // CONTAINERS (TOP / MAIN)
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'top',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $top_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $main_id = $db->lastInsertId('engine4_core_content');

      // ---------- CONTAINER TOP & WIDGET MENU -----------
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $top_id,
        'order' => $i++,
        'params' => '',
      ));
      $top_middle_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.list-menu',
        'parent_content_id' => $top_middle_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
      $top_middle_menu_id = $db->lastInsertId('engine4_core_content');
      
      // ---------- CONTAINER MAIN -----------
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'left',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_left_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_right_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_middle_id = $db->lastInsertId('engine4_core_content');
      
      // ------ MAIN :: LEFT WIDGETS
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.list-categories',
        'parent_content_id' => $main_left_id,
        'order' => $i++,
        'params' => '{"title":"","showdetails":"0","showphoto":"1","descriptionlength":"68"}',
      ));    
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.top-submitters',
        'parent_content_id' => $main_left_id,
        'order' => $i++,
        'params' => '{"title":"Top Posters"}',
      )); 

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.list-articles',
        'parent_content_id' => $main_left_id,
        'order' => $i++,
        'params' => '{"title":"Most Commented","max":"3","order":"mostcommented","period":"month","display_style":"narrow","showdescription":"0"}',
      ));      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.list-articles',
        'parent_content_id' => $main_left_id,
        'order' => $i++,
        'params' => '{"title":"Most Viewed","max":"3","order":"mostviewed","period":"month","display_style":"narrow","showdescription":"0"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.list-articles',
        'parent_content_id' => $main_left_id,
        'order' => $i++,
        'params' => '{"title":"Most Liked","max":"3","order":"mostliked","period":"month","display_style":"narrow","showdescription":"0"}',
      ));         

      // ------ MAIN :: RIGHT WIDGETS
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.list-search',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.create-new',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.list-sponsored',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":"Sponsored Articles","max":"3"}',
      ));
      // ------ MAIN :: MIDDLE WIDGETS
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.list-featured',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '{"title":"Featured Articles","max":"5"}',
      ));    
      
      // tab
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.container-tabs',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '{"max":"6"}',
      ));
      // tab items
      $tab_id = $db->lastInsertId('engine4_core_content');      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.list-articles',
        'parent_content_id' => $tab_id,
        'order' => $i++,
        'params' => '{"title":"Recent Articles","max":"10","order":"recent","showemptyresult":"1"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.list-tags',
        'parent_content_id' => $tab_id,
        'order' => $i++,
        'params' => '{"title":"Popular Tags","max":"100","order":"text","showlinkall":"1"}',
      ));
      
    }
  }  
  // addHomePage
  
  
  
  public function addProfilePage()
  {
    $db = $this->getDb();
    $select = new Zend_Db_Select($db);

    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $hasWidget = $select
      ->from('engine4_core_pages', new Zend_Db_Expr('TRUE'))
      ->where('name = ?', 'article_profile_index')
      ->limit(1)
      ->query()
      ->fetchColumn()
      ;

    // Add it
    if( empty($hasWidget) ) {

      $db->insert('engine4_core_pages', array(
        'name' => 'article_profile_index',
        'displayname' => 'Article Profile',
        'title' => 'Article Profile',
        'description' => 'This is the profile for an article.',
        'custom' => 0,
        //'provides' => 'subject=article', // requires SE v4.1.2
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      // containers
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
      ));
      $container_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $container_id,
        'order' => 3,
        'params' => '',
      ));
      $middle_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $container_id,
        'order' => 1,
        'params' => '',
      ));
      $right_id = $db->lastInsertId('engine4_core_content');

      // middle column
      $m = 0;
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.profile-notice',
        'parent_content_id' => $middle_id,
        'order' => ++$m,
        'params' => '',
      ));      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.profile-breadcrumb',
        'parent_content_id' => $middle_id,
        'order' => ++$m,
        'params' => '',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.profile-title',
        'parent_content_id' => $middle_id,
        'order' => ++$m,
        'params' => '',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.profile-info',
        'parent_content_id' => $middle_id,
        'order' => ++$m,
        'params' => '{"title":""}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.profile-description',
        'parent_content_id' => $middle_id,
        'order' => ++$m,
        'params' => '',
      ));      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.profile-body',
        'parent_content_id' => $middle_id,
        'order' => ++$m,
        'params' => '',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.profile-tags',
        'parent_content_id' => $middle_id,
        'order' => ++$m,
        'params' => '{"title":""}',
      ));      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.container-tabs',
        'parent_content_id' => $middle_id,
        'order' => ++$m,
        'params' => '{"max":"6"}',
      ));
      $tab_id = $db->lastInsertId('engine4_core_content');

      // right column
      $r = 0;
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.profile-submitter',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"title":""}',
      ));
       $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.profile-photo',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"title":""}',
      ));     
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.profile-icon-sponsored',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"title":"","text":"SPONSORED ARTICLE"}',
      ));      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.profile-icon-featured',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"title":"","text":"FEATURED ARTICLE"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.profile-options',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"title":""}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.profile-related-articles',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"titleCount":true,"title":"Related Articles","max":5,"order":"random"}',
      ));      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.profile-social-shares',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"title":""}',
      ));
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.profile-tools',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"title":""}',
      ));
      
      
      // tabs
      $t = 0;
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.profile-comments',
        'parent_content_id' => $tab_id,
        'order' => ++$t,
        'params' => '{"title":"Comments"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.profile-details',
        'parent_content_id' => $tab_id,
        'order' => ++$t,
        'params' => '{"title":"Details"}',
      )); 
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'article.profile-photos',
        'parent_content_id' => $tab_id,
        'order' => ++$t,
        'params' => '{"titleCount":true,"title":"Photos","max":12}',
      ));             


    }
  }
  // addProfilePage
  

  
  public function onInstall()
  {
    $this->removeCustomPages();
    
    $this->addUserProfileTab();
    $this->addHomePage();
    $this->addBrowsePage();
    $this->addManagePage();
    $this->addPopularTagsPage();
    $this->addProfilePage();
    
    parent::onInstall();
  }
}
