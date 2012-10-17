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

 
class Article_PhotoController extends Core_Controller_Action_Standard
{
  public function init()
  {
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
          null !== ($photo = Engine_Api::_()->getItem('article_photo', $photo_id)) )
      {
        Engine_Api::_()->core()->setSubject($photo);
      }

      else if( 0 !== ($article_id = (int) $this->_getParam('article_id')) &&
          null !== ($article = Engine_Api::_()->getItem('article', $article_id)) )
      {
        Engine_Api::_()->core()->setSubject($article);
      }
    }

    $this->_helper->requireUser->addActionRequires(array(
      'upload',
      'upload-photo',
      'edit',
      'delete',
      'manage'
    ));

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'list' => 'article',
      'upload' => 'article',
      'view' => 'article_photo',
      'edit' => 'article_photo',
      'delete' => 'article_photo',
      'manage' => 'article',
    ));
  }

  public function listAction()
  {
    $this->view->article = $article = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $article->getSingletonAlbum();

    $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->owner = $owner = Engine_Api::_()->getItem('user', $article->owner_id);
    
    $this->view->canUpload = $this->_helper->requireAuth()->setAuthParams($article, null, 'photo')->checkRequire();

    $album->view_count++;
    $album->save();  
  }

  

  public function manageAction()
  {
    $this->view->article = $article = Engine_Api::_()->core()->getSubject();
    
    if( !$this->_helper->requireAuth()->setAuthParams($article, null, 'photo')->isValid() ) return;
    
    //echo 'stupid';
    //if( !$this->_helper->requireUser()->isValid() ) return;
    //if( !$this->_helper->requireAuth()->setAuthParams($article, null, 'edit')->isValid() ) return;
    
    $viewer = Engine_Api::_()->user()->getViewer();
    //$this->view->canUpload = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'article', 'photo');
    
    
    // Prepare data
    $this->view->article = $article = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $article->getSingletonAlbum();
    $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    
    $paginator->setCurrentPageNumber($this->_getParam('page',1));
    $paginator->setItemCountPerPage($paginator->getTotalItemCount());

    // Make form
    $this->view->form = $form = new Article_Form_Photo_Manage();
    
    //$form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes were saved.'));
    
    foreach( $paginator as $photo )
    {
      $subform = new Article_Form_Photo_Manage_Edit(array('elementsBelongTo' => $photo->getGuid()));
      $subform->populate($photo->toArray());
      $form->addSubForm($subform, $photo->getGuid());
      $form->cover->addMultiOption($photo->file_id, $photo->file_id);
    }

    if( !$this->getRequest()->isPost() )
    {
      return;
    }
    
    if( !$form->isValid($this->getRequest()->getPost()))
    {
      return;
    }

    $table = $article->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $values = $form->getValues();
      if( !empty($values['cover']) ) {
        $article->photo_id = $values['cover'];
        $article->save();
      }


      // Process
      foreach( $paginator as $photo )
      {
        $subform = $form->getSubForm($photo->getGuid());
        $values = $subform->getValues();

        $values = $values[$photo->getGuid()];
        unset($values['photo_id']);
        if( isset($values['delete']) && $values['delete'] == '1' )
        {
          $photo->delete();
        }
        else
        {
          $photo->setFromArray($values);
          $photo->save();
        }
      }

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    
    return $this->_helper->redirector->gotoRoute(array('controller'=>'photo', 'action' => 'list', 'subject' => $article->getGuid()), 'article_extended', true);
    
  }  
  

  public function uploadAction()
  {
    if( isset($_GET['ul']) || isset($_FILES['Filedata']) ) {
      return $this->_forward('upload-photo', null, null, array('format' => 'json'));
    }    
    
    $this->view->article = $article = Engine_Api::_()->core()->getSubject();
    if( !$this->_helper->requireAuth()->setAuthParams($article, null, 'photo')->isValid() ) {
      return;
    }
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $album = $article->getSingletonAlbum();

    $this->view->article_id = $article->article_id;
    $this->view->form = $form = new Article_Form_Photo_Upload();
    $form->file->setAttrib('data', array('article_id' => $article->getIdentity()));

    if( !$this->getRequest()->isPost() )
    {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process
    $table = Engine_Api::_()->getItemTable('article_photo');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $values = $form->getValues();
      $params = array(
        'article_id' => $article->getIdentity(),
        'user_id' => $viewer->getIdentity(),
      );

      // Add action and attachments
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $article, 'article_photo_upload', null, array(
      	'count' => count($values['file'])
      ));

      // Do other stuff
      $count = 0;
      foreach( $values['file'] as $photo_id )
      {
        $photo = Engine_Api::_()->getItem("article_photo", $photo_id);
        if( !($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity() ) continue;

        $photo->collection_id = $album->album_id;
        $photo->album_id = $album->album_id;
        $photo->save();
        
        
        if ($article->photo_id == 0) {
          $article->photo_id = $photo->file_id;
          $article->save();
        }
				
        
        if( $action instanceof Activity_Model_Action && $count < 8 )
        {
          $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
        }
        $count++;
      }

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }


    $this->_redirectCustom($article);
  }

  public function uploadPhotoAction()
  {
    $article = Engine_Api::_()->getItem('article', (int) $this->_getParam('article_id'));

    if( !$this->_helper->requireUser()->checkRequire() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    // @todo check auth
    //$article

    $values = $this->getRequest()->getPost();
    if( empty($values['Filename']) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
      return;
    }

    if( !isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name']) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

    $db = Engine_Api::_()->getDbtable('photos', 'article')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = Engine_Api::_()->user()->getViewer();
      $album = $article->getSingletonAlbum();

      $params = array(
        // We can set them now since only one album is allowed
        'collection_id' => $album->getIdentity(),
        'album_id' => $album->getIdentity(),

        'article_id' => $article->getIdentity(),
        'user_id' => $viewer->getIdentity(),
      );
      
      $photoTable = Engine_Api::_()->getItemTable('article_photo');
      $photo = $photoTable->createRow();
      $photo->setFromArray($params);
      $photo->save();
      
      $photo->setPhoto($_FILES['Filedata']);
      
      //$photo_id = Engine_Api::_()->article()->createPhoto($params, $_FILES['Filedata'])->photo_id;

      $this->view->status = true;
      $this->view->name = $_FILES['Filedata']['name'];
      $this->view->photo_id = $photo->getIdentity();

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
      // throw $e;
      return;
    }
  }

  
  
  public function viewAction()
  {
    $this->view->photo = $photo = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $photo->getCollection();
    $this->view->article = $article = $photo->getArticle();
    
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    
    $this->view->canUpload = $this->_helper->requireAuth()->setAuthParams($article, null, 'photo')->checkRequire();
    
    $photo->view_count++;
    $photo->save();
  }
  
  
  
  public function editAction()
  {
  	
    $photo = Engine_Api::_()->core()->getSubject();

    $this->view->article = $article = $photo->getArticle();
    if( !$this->_helper->requireAuth()->setAuthParams($article, null, 'photo')->isValid() ) return;
    
    $this->view->form = $form = new Article_Form_Photo_Edit();

    if( !$this->getRequest()->isPost() )
    {
      $form->populate($photo->toArray());
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('photos', 'article')->getAdapter();
    $db->beginTransaction();

    try
    {
      $photo->setFromArray($form->getValues())->save();

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changes saved')),
      'layout' => 'default-simple',
      'parentRefresh' => true,
      'closeSmoothbox' => true,
    ));
  }


  
  public function deleteAction()
  { 
    $photo = Engine_Api::_()->core()->getSubject();
    $this->view->article = $article = $photo->getParent('article');

    if( !$this->_helper->requireAuth()->setAuthParams($article, null, 'photo')->isValid() ) return;

    $this->view->form = $form = new Article_Form_Photo_Delete();

    if( !$this->getRequest()->isPost() )
    {
      $form->populate($photo->toArray());
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('photos', 'article')->getAdapter();
    $db->beginTransaction();

    try
    {
      $photo->delete();

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $parentRedirect = Zend_Controller_Front::getInstance()->getRouter()
      ->assemble(array('controller'=>'photo', 'action' => 'list', 'subject' => $article->getGuid()), 'article_extended', true);
    
    return $this->_forward('success', 'utility', 'core', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Photo deleted')),
      'layout' => 'default-simple',
      'parentRedirect' => $parentRedirect,
      'closeSmoothbox' => true,
    ));  
    
  }
    

  
}