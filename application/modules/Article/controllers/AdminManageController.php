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
 
 
class Article_AdminManageController extends Core_Controller_Action_Admin
{
  public function init()
  {
    if (!Engine_Api::_()->article()->checkLicense()) {
      return $this->_redirectCustom(array('route'=>'admin_default', 'module'=>'article', 'controller'=>'settings', 'notice' => 'license'));
    }   

    parent::init();
  }   
  
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('article_admin_main', array(), 'article_admin_main_manage');

      
    $this->view->formFilter = $formFilter = new Article_Form_Admin_Manage_Filter();

    // Process form
    $values = array();
    if($formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    }
    $values = Engine_Api::_()->article()->filterEmptyParams($values);
    
    $this->view->formValues = $values;

    $this->view->assign($values);
   
    $this->view->paginator = Engine_Api::_()->article()->getArticlesPaginator($values);
    $this->view->paginator->setItemCountPerPage((int) Engine_Api::_()->getApi('settings', 'core')->getSetting('article.page', 10));
    $this->view->paginator->setCurrentPageNumber($this->_getParam('page',1));
    $this->view->params = $values;
    
  }

  public function deleteselectedAction()
  {
    $this->view->ids = $ids = $this->_getParam('ids', null);
    $confirm = $this->_getParam('confirm', false);
    $this->view->count = count(explode(",", $ids));

    // Save values
    if( $this->getRequest()->isPost() && $confirm == true )
    {
      $ids_array = explode(",", $ids);
      foreach( $ids_array as $id ){
        $article = Engine_Api::_()->getItem('article', $id);
        if( $article ) $article->delete();
      }

      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

  }  
  
  public function featuredAction()
  {
    // In smoothbox
    $id = $this->_getParam('id');
    $this->view->article_id=$id;
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $article = Engine_Api::_()->getItem('article', $id);
        
        $article->featured = $this->_getParam('featured') == 'yes' ? 1 : 0;
        $article->save();
        
        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
          //'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changes saved'))
      ));
    }
  }
  
  public function sponsoredAction()
  {
    // In smoothbox
    $id = $this->_getParam('id');
    $this->view->article_id=$id;
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $article = Engine_Api::_()->getItem('article', $id);
        
        $article->sponsored = $this->_getParam('sponsored') == 'yes' ? 1 : 0;
        $article->save();
        
        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
          //'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changes saved'))
      ));
    }

  }  
  
  public function publishedAction()
  {
    // In smoothbox
    $id = $this->_getParam('id');
    $this->view->article_id=$id;
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $article = Engine_Api::_()->getItem('article', $id);
        
        $article->published = $this->_getParam('published') == 'yes' ? 1 : 0;
        $article->save();
        
        if ($article->isPublished()) {
          // Add activity only if article is published
          $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionsByObject($article);
          if (count($action->toArray())<=0){
          	
          	$owner = Engine_Api::_()->user()->getUser($article->owner_id);

            $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $article, 'article_new');
            if($action!=null){
              Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $article);
            }
            
            // Rebuild privacy
            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
            foreach( $actionTable->getActionsByObject($article) as $action ) {
              $actionTable->resetActivityBindings($action);
            }
          }
        }

        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
          //'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changes saved'))
      ));
    }
  }  
  
  public function deleteAction()
  {
    // In smoothbox
    $id = $this->_getParam('id');
    $this->view->article_id=$id;
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();


      try
      {
        $article = Engine_Api::_()->getItem('article', $id);
        $article->delete();
        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }

  }

}