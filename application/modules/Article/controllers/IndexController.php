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
 
 
class Article_IndexController extends Core_Controller_Action_Standard
{
  protected $_navigation;

  public function init()
  {
    if( !$this->_helper->requireAuth()->setAuthParams('article', null, 'view')->isValid() ) return;
    
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($article_id = (int) $this->_getParam('article_id')) &&
          null !== ($article = Engine_Api::_()->getItem('article', $article_id)) )
      {
        Engine_Api::_()->core()->setSubject($article);
      }
    }
    
    $this->_helper->requireUser->addActionRequires(array(
      'create',
      'delete',
      'edit',
      'manage',
      'success',
      'publish'
    ));

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'delete' => 'article',
      'edit' => 'article',
      'success' => 'article',
      'publish' => 'article',
      'view' => 'article',
    ));
    
  }
  
  public function indexAction()
  {
    $this->_helper->content->setNoRender()->setEnabled();
  }
  
  // NONE USER SPECIFIC METHODS
  public function browseAction()
  {    
    $this->_helper->content->setNoRender()->setEnabled();
    return;
  }

  
  public function manageAction()
  {
    $this->_helper->content->setNoRender()->setEnabled();
    return;    
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->_loadNavigations();
    
    
    
    $this->view->allowed_upload = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'article', 'photo');

    $this->view->approval = $approval = (int) Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'article', 'approval');
    
    
    
    $this->view->form = $form = new Article_Form_Search();
    $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(),'article_manage',true));
    
    $values = array();
    // Populate form data
    if( $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();
    }

    $values = Engine_Api::_()->getApi('filter','radcodes')->removeKeyEmptyValues($values);
    $this->view->formValues = $values;
    
    $this->view->assign($values);
    
    // items needed to show what is being filtered in browse page
    if( !empty($values['category']) )
    {
      $this->view->categoryObject = Engine_Api::_()->article()->getCategory($values['category']);
    }
    if (!empty($values['tag']))
    {
      $this->view->tagObject = Engine_Api::_()->getItem('core_tag', $values['tag']);
    }
    
    $values['user'] = $viewer;
    $values['limit'] = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('article.page', 10);
    
    // Get paginator
    $this->view->paginator = $paginator = Engine_Api::_()->article()->getArticlesPaginator($values);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));    

    $this->view->can_create = Engine_Api::_()->authorization()->isAllowed('article', $viewer, 'create');
  }


  
  
  public function viewAction()
  {
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->article = $article = Engine_Api::_()->core()->getSubject('article');

    //return $this->_forward('requireauth', 'error', 'core');
    // require log in --- and -- not logged in => show log in screen
    if ( !Engine_Api::_()->getApi('settings', 'core')->getSetting('article.public', 1) && !$this->_helper->requireUser()->isValid() ) { 
      return;
    }
    
    // logged in && no view permission => show no permission
    if ( $this->_helper->requireUser()->checkRequire() && !$this->_helper->requireAuth()->setAuthParams($article, null, 'view')->isValid()) {
      return;
    }
    else if (!$this->_helper->requireUser()->checkRequire()) {
      if (!$this->_helper->requireAuth()->setAuthParams($article, null, 'view')->checkRequire()) {
        return $this->_forward('requireuser', 'error', 'core');
      }
    }    
    //if (!$this->_helper->requireAuth()->setAuthParams($article, null, 'view')->checkRequire()) {
    //  return $this->_forward('requireauth', 'error', 'core');
   // }
    
    
    $this->view->owner = $owner = Engine_Api::_()->user()->getUser($article->owner_id);
    
    $this->view->canEdit = $this->_helper->requireAuth()->setAuthParams($article, null, 'edit')->checkRequire();
    $this->view->canUpload = $this->_helper->requireAuth()->setAuthParams($article, null, 'photo')->checkRequire();
    $this->view->canDelete = $this->_helper->requireAuth()->setAuthParams($article, null, 'delete')->checkRequire();
    $this->view->canPublish = $article->isOwner($viewer) && !$article->isPublished();
    
    $this->view->approval = 0;
    if ($viewer->getIdentity()) {
      $this->view->approval = (int) Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'article', 'approval');
    }
    $archiveList = Engine_Api::_()->article()->getArchiveList(array('user_id'=>$article->owner_id,'published'=>1));
    
    $article->view_count++;
    $article->save();
    
    $this->view->article = $article;
    if ($article->photo_id)
    {
      $this->view->main_photo = $article->getPhoto($article->photo_id);
    }
    // get tags
    $this->view->articleTags = $article->tags()->getTagMaps();
    $this->view->userTags = $article->tags()->getTagsByTagger($article->getOwner());
    
    // get archive list
    $this->view->archive_list = $this->handleArchiveList($archiveList);
    
    $view = $this->view;
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    $this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($article);
    
    // album material
    $this->view->album = $album = $article->getSingletonAlbum();
    $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $paginator->setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('article.gallery', 4));
    
    if($article->category_id !=0) $this->view->category = Engine_Api::_()->article()->getCategory($article->category_id);
    $this->view->userCategories = Engine_Api::_()->article()->getUserCategories($this->view->article->owner_id);
        
    // related articles
    $this->view->relatedArticles = Engine_Api::_()->article()->getRelatedArticles($article);
  }

    
  
  
  public function createAction()
  {
    //if( !$this->_helper->requireUser()->isValid() ) return;
    if( !$this->_helper->requireAuth()->setAuthParams('article', null, 'create')->isValid()) return;
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->_loadNavigations();
    $this->view->form = $form = new Article_Form_Create();
    // set up data needed to check quota
    $values['user'] = $viewer->getIdentity();
    $paginator = $this->_helper->api()->getApi('core', 'article')->getArticlesPaginator($values);


    $this->view->quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'article', 'max');
    $this->view->current_count = $paginator->getTotalItemCount();


    // If not post or form not valid, return
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
      $table = Engine_Api::_()->getItemTable('article');
      $db = $table->getAdapter();
      $db->beginTransaction();

      try
      {
      	$featured = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'article', 'featured') ? 1 : 0;
        $sponsored = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'article', 'sponsored') ? 1 : 0;
      	
        // Create article
        $values = array_merge($form->getValues(), array(
          'owner_type' => $viewer->getType(),
          'owner_id' => $viewer->getIdentity(),
          'featured' => $featured,
          'sponsored' => $sponsored,
        ));

        $article = $table->createRow();
        $article->setFromArray($values);
        $article->save();

        // Set photo
        if( !empty($values['photo']) ) {
          $article->setPhoto($form->photo);
        }

        // Add tags
        $tags = preg_split('/[,]+/', $values['tags']);
        $tags = array_filter(array_map("trim", $tags));
        $article->tags()->addTagMaps($viewer, $tags);


        $customfieldform = $form->getSubForm('customField');
        $customfieldform->setItem($article);
        $customfieldform->saveValues();

        // CREATE AUTH STUFF HERE
        $auth = Engine_Api::_()->authorization()->context;  
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
  
        $auth_keys = array(
         'view' => 'everyone',
         'comment' => 'registered',
        );
        
        foreach ($auth_keys as $auth_key => $auth_default)
        {
          $auth_value = isset($values['auth_'.$auth_key]) ? $values['auth_'.$auth_key] : $auth_default;
          $authMax = array_search($auth_value, $roles);
          
          foreach( $roles as $i => $role )
          {
            $auth->setAllowed($article, $role, $auth_key, ($i <= $authMax));
          }
        }

        
        // Add activity only if article is published
        if ($article->isPublished()) {
          $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $article, 'article_new');
          if($action!=null){
            Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $article);
          }
        }

        
        
        // Commit
        $db->commit();
        
        return $this->_helper->redirector->gotoRoute(array('article_id'=>$article->article_id), 'article_success', true);
        
        /*
        // Redirect
        $allowed_upload = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'article', 'photo');

        if( $allowed_upload )
        {
          return $this->_helper->redirector->gotoRoute(array('article_id'=>$article->article_id), 'article_success', true);
        }
        else
        {
        	return $this->_helper->redirector->gotoRoute(array(), 'article_manage', true); 
        }
        */
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
    }
  }

  public function editAction()
  {
    //if( !$this->_helper->requireUser()->isValid() ) return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->article = $article = Engine_Api::_()->core()->getSubject('article');
    
    if( !$this->_helper->requireAuth()->setAuthParams($article, null, 'edit')->isValid())
    {
      return $this->_forward('requireauth', 'error', 'core');
    }

    // Get navigation
    $navigation = $this->getNavigation();
    $this->view->navigation = $navigation;

    $this->view->form = $form = new Article_Form_Edit(array(
      'item' => $article
    ));
    
    // only for create
    $form->removeElement('photo');

    $form->populate($article->toArray());

    $auth = Engine_Api::_()->authorization()->context;
    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
    $auth_keys = array(
     'view' => 'everyone',
     'comment' => 'registered',
    );
    
    // Save article entry
    if( !$this->getRequest()->isPost() )
    {

      // prepare tags
      $articleTags = $article->tags()->getTagMaps();
      
      $tagString = '';
      foreach( $articleTags as $tagmap )
      {
        if( $tagString !== '' ) $tagString .= ', ';
        $tagString .= $tagmap->getTag()->getTitle();
      }

      $this->view->tagNamePrepared = $tagString;
      $form->tags->setValue($tagString);
      
      foreach ($auth_keys as $auth_key => $auth_default)
      {
        $auth_field = 'auth_'.$auth_key;
        
        foreach( $roles as $i => $role )
        {
          if (isset($form->$auth_field->options[$role]) && 1 === $auth->isAllowed($article, $role, $auth_key))
          {
            $form->$auth_field->setValue($role);
          }
        }
      }

      if ($article->isPublished()) $form->removeElement('published');
      
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }


    // Process

    // handle save for tags
    $values = $form->getValues();
    $tags = preg_split('/[,]+/', $values['tags']);
    $tags = array_filter(array_map("trim", $tags));

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try
    {
      $article->setFromArray($values);
      $article->modified_date = date('Y-m-d H:i:s');

      $article->tags()->setTagMaps($viewer, $tags);
      $article->save();

      // Save custom fields
      $customfieldform = $form->getSubForm('customField');
      $customfieldform->setItem($article);
      $customfieldform->saveValues();

      // CREATE AUTH STUFF HERE
      $values = $form->getValues();
      
      // CREATE AUTH STUFF HERE
      foreach ($auth_keys as $auth_key => $auth_default)
      {
        $auth_value = isset($values['auth_'.$auth_key]) ? $values['auth_'.$auth_key] : $auth_default;
        $authMax = array_search($auth_value, $roles);
          
        foreach( $roles as $i => $role )
        {
          $auth->setAllowed($article, $role, $auth_key, ($i <= $authMax));
        }
      }
      
      // Add activity only if article is published
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionsByObject($article);
      if (count($action->toArray())<=0 && $article->isPublished()){
      	
      	if( $viewer->getIdentity() != $article->owner_id)
      	{
      		$owner = Engine_Api::_()->user()->getUser($article->owner_id);
      	}
      	else {
      		$owner = $viewer;
      	}
      	
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $article, 'article_new');
        if($action!=null){
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $article);
        }
      }
    
      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach( $actionTable->getActionsByObject($article) as $action ) {
        $actionTable->resetActivityBindings($action);
      }
      $db->commit();


      $savedChangesNotice = Zend_Registry::get('Zend_Translate')->_("Your changes were saved.");
      $form->addNotice($savedChangesNotice);
      $customfieldform->removeElement('submit');
      
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
  }
  


  public function deleteAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->article = $article = Engine_Api::_()->core()->getSubject('article');
    
    //if( $viewer->getIdentity() != $article->owner_id && !$this->_helper->requireAuth()->setAuthParams($article, null, 'edit')->isValid())
    if( !$this->_helper->requireAuth()->setAuthParams($article, null, 'delete')->isValid()) return;

    // Get navigation
    $navigation = $this->getNavigation();
    $this->view->navigation = $navigation;
    
    if( $this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true )
    {
      $this->view->article->delete();
      return $this->_helper->redirector->gotoRoute(array(), 'article_manage', true);
    }
  }
  
  
  public function publishAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
  	$this->view->article = $article = Engine_Api::_()->core()->getSubject('article');
  	
  	// only owner can publish
    if( $viewer->getIdentity() != $article->owner_id)
    {
      return $this->_forward('requireauth', 'error', 'core');
    }
  	
    $approval = (int) Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'article', 'approval');
    if ($approval)
    {
      return $this->_forward('requireauth', 'error', 'core');
    }
    
    if ($article->isPublished())
    {
      return $this->_helper->redirector->gotoRoute(array(), 'article_manage', true);
    }
    
    
    $table = $article->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $article->published = 1;
      $article->save();
      
      // Add activity only if article is published
      if ($article->isPublished()) 
      {
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $article, 'article_new');
        if($action!=null){
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $article);
        }
      }
      
      $db->commit();
      
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    return $this->_redirectCustom($article);
    //return $this->_helper->redirector->gotoRoute(array(), 'article_manage', true);
  }
  
  public function successAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->article = $article = Engine_Api::_()->core()->getSubject('article');
    
    if( $viewer->getIdentity() != $article->owner_id )
    {
      return $this->_forward('requireauth', 'error', 'core');
    }    
    
    $this->_loadNavigations();

    if( $this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true )
    {
    	return $this->_helper->redirector->gotoRoute(array('controller'=>'photo','action'=>'upload','subject'=>$article->getGuid()), 'article_extended', true);
    }
  }


  public function tagsAction()
  {
    $this->_helper->content->setNoRender()->setEnabled();
    return;  	
  	/*
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->form = $form = new Article_Form_Search();
               
    if( !$viewer->getIdentity() )
    {
      $form->removeElement('show');
    }
    
    $this->_loadNavigations();
    $this->view->can_create = $this->_helper->requireAuth()->setAuthParams('article', null, 'create')->checkRequire();
    
    $this->view->tags = $tags = Engine_Api::_()->article()->getPopularTags(array('limit' => 999, 'order' => 'text'));
  	*/
  }
  
  public function getNavigation($active = false)
  {
    if( is_null($this->_navigation) )
    {
      $this->_navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('article_main');      
    }
    return $this->_navigation;
  }

  protected function _loadNavigations()
  {
    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('article_main');

    // Get quick navigation
    $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('article_quick');    
  }
  
  public function handleArchiveList($results)
  {
    $archive_list = array();
    
    foreach ($results as $result)
    {
    	$article_date = strtotime($result['period']);
    	
      $date_info = Radcodes_Lib_Helper_Date::archive($article_date);
      $date_start = $date_info['date_start'];
      
      if( !isset($archive_list[$date_start]) )
      {
        $archive_list[$date_start] = $date_info;
        $archive_list[$date_start]['count'] = $result['total'];
      }
      else
      {
        $archive_list[$date_start]['count'] += $result['total'];
      }
    }

    return $archive_list;
  }
 
  
  public function uploadPhotoAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->_helper->layout->disableLayout();

    if( !Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create') ) {
      return false;
    }

    if( !$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid() ) return;

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
    if( !isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name']) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

    $db = Engine_Api::_()->getDbtable('photos', 'album')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = Engine_Api::_()->user()->getViewer();

      $album = Engine_Api::_()->article()->getSpecialAlbum($viewer, 'article');
      
      $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
      $photo = $photoTable->createRow();
      $photo->setFromArray(array(
        'owner_type' => 'user',
        'owner_id' => $viewer->getIdentity(),
        'collection_id' => $album->album_id, // for SE <= 4.1.6 .. (this column was removed since v4.1.7
        'album_id' => $album->album_id, // for SE >= v4.1.7
      ));
      $photo->save();

      $photo->setPhoto($_FILES['Filedata']);

      $this->view->status = true;
      $this->view->name = $_FILES['Filedata']['name'];
      $this->view->photo_id = $photo->photo_id;
      $this->view->photo_url = $photo->getPhotoUrl();

      if( !$album->photo_id )
      {
        $album->photo_id = $photo->getIdentity();
        $album->save();
      }

      $auth      = Engine_Api::_()->authorization()->context;
      $auth->setAllowed($photo, 'everyone', 'view',    true);
      $auth->setAllowed($photo, 'everyone', 'comment', true);
      $auth->setAllowed($album, 'everyone', 'view',    true);
      $auth->setAllowed($album, 'everyone', 'comment', true);


      $db->commit();

    } catch( Album_Model_Exception $e ) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = $this->view->translate($e->getMessage());
      //throw $e;
      return;

    } catch( Exception $e ) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
      throw $e;
      return;
    }
  }
    
}

