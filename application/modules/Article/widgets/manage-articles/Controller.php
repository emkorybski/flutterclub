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
 
 
 
class Article_Widget_ManageArticlesController extends Engine_Content_Widget_Abstract
{

  protected $_childCount;
  
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    $this->view->paginator = $paginator = $this->loadArticlePaginator();
    $this->view->formValues = $params = $this->getQueryParams();
    
    $this->view->assign($params);
    
    // Add count to title if configured
    $this->_childCount = $paginator->getTotalItemCount();    
    
    $this->view->showphoto = $this->_getParam('showphoto', 1);
    $this->view->showdetails = $this->_getParam('showdetails', 1); 
    $this->view->showmeta = $this->_getParam('showmeta', 1); 
    $this->view->showdescription = $this->_getParam('showdescription', 1);     
    
    if (isset($params['category']))
    {
      $this->view->categoryObject = $category = Engine_Api::_()->article()->getCategory($params['category']);
      if ($category instanceof Article_Model_Category) 
      {
        $title = $this->view->translate('My %s Articles', $this->view->translate($category->getTitle()));
        $this->getElement()->setTitle($title);
      }
    }
    
    if (!empty($params['tag']))
    {
      $this->view->tagObject = Engine_Api::_()->getItem('core_tag', $params['tag']);
    } 
    
    $this->view->can_create = Engine_Api::_()->authorization()->isAllowed('article', $viewer, 'create');
    $this->view->allowed_upload = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'article', 'photo');
    $this->view->approval = $approval = (int) Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'article', 'approval');
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }  
  
  protected function loadArticlePaginator()
  {    
    $queryParams = $this->getQueryParams();
    $forcedParams = $this->getForcedParams();

    $params = array_merge($queryParams, $forcedParams);

    $paginataor = Engine_Api::_()->article()->getArticlesPaginator($params);

    return $paginataor;

  }

  protected function getForcedParams()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    $force_params = array(
      'limit' => $this->_getParam('max', 10),
      //'preorder' => (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('article.sorting', 0),
      'user' => $viewer->getIdentity(),
    );
    
    return $force_params;
  }
  
  protected function getQueryParams()
  {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();
    
    foreach (array('action','module','controller','rewrite') as $key) {
      unset($params[$key]);
    }
    
    $params = Engine_Api::_()->getApi('filter','radcodes')->removeKeyEmptyValues($params);
    
    return $params;
  }  
  
}