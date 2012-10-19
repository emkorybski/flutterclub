<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    News
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AdminManageController.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    News
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class News_IndexController extends Core_Controller_Action_Standard
{
   
	public function indexAction()
	{
        
       $_SESSION['start_date'] ='';
       $_SESSION['end_date']='';
       $array = $this->getRequest()->getPost();
       $subject = Engine_Api::_()->getItem('news_param', 1);
      
       if($array)
       {
       		if(isset($array['search']))
           		$subject->search = $array['search'];
           	else
           		$subject->search = "";

           	if(isset($array['category']))
           		$subject->category = $array['category'];           	
           	else
           		$subject->category = "0";
           		
           	if(isset($array['page']))
           		$subject->page = $array['page'];
           	elseif(isset($array['nextpage']))
           		$subject->page = $array['nextpage'];
           	else
           		$subject->page = "1";
           		
       }
       Engine_Api::_()->core()->setSubject($subject);
	   //$this->_helper->content->render();
       //$this->_redirect("news/list"); 
       //$_SESSION['keysearch'] = null;  
       
       $this->_forward("list","index","news");
	}
	
	public function detailAction()
	{

       $this->view->navigation = $this->getNavigation();
		$this->view->form = $form = new News_Form_Commonsearch();
                
	    // Populate form
	    $this->view->categories = $categories = Engine_Api::_()->news()->getAllCategories();
	     
	    foreach( $categories as $category )
	    {
	     	$form->category->addMultiOption($category['category_id'], $category['category_name']);
	    }

		$content_id = $this->_getParam('id');
          

            //inactive news If I click to a inactive news link on activity feed
             $table = Engine_Api::_()->getDbtable('Contents', 'News');
             $select = $table->select('engine4_news_contents')->setIntegrityCheck(false)
            ->joinLeft("engine4_news_categories", "engine4_news_categories.category_id= engine4_news_contents.category_id")
            ->where('engine4_news_contents.content_id= ? ', $content_id)
            ->where('engine4_news_categories.is_active= ? ', 1)
            ->limit(1);

            $items = $table->fetchAll($select);

            if (count($items) <= 0) {
                $news_content = Engine_Api::_()->getItem('contents', -1);
                if( $news_content) {
                    Engine_Api::_()->core()->setSubject($news_content);
                }
                if( !$this->_helper->requireSubject()->isValid() ) return;
            }else{

                $news_content = Engine_Api::_()->getItem('contents', $content_id);
                if( $news_content) {
                    Engine_Api::_()->core()->setSubject($news_content);
                }
                if( !$this->_helper->requireSubject()->isValid() ) return;

                $news_content->count_view = $news_content->count_view+1;

                $news_content->save();

                $this->view->content = $news_content;
                $this->view->is_commment  = $this->_getParam('commentdetail');

                //get category detail
                $category = Engine_Api::_()->news()->getAllCategories(array(
                    'category_id'=>$news_content['category_id'],
                ));

                $this->view->category = $category;
            }
        //print_r($this->view->category);  die();
	    /*
	    //get all category
	    $this->view->categories = Engine_Api::_()->news()->getAllCategories(array(
	    	'orderby'=> 'category_id',
	    ));
	    */

	}
	public function listAction()
	{
        
            $this->view->form = $form = new News_Form_Commonsearch();
	    // Populate form
	    $this->view->categories = $categories = Engine_Api::_()->news()->getAllCategories(array('is_active'=>1));
	    $news_search_query = "";
	    foreach( $categories as $category )
	    {	    	
	     	$form->category->addMultiOption($category['category_id'], $category['category_name']);
	    }	    
	    	    
		if(isset($_POST['category']) && !empty($_POST['category']))
		{
			$category_id = $_POST['category'];			
		}		
		else{			
			$category_id = 0;
		}
		
		if(!isset($_POST['search']) && empty($_POST['search']))
		{
			$searchText = "";
		}
		else
		{			
			$searchText = $_POST['search'];
			
			$news_search_arr = explode(" ", $searchText);
		    $news_searchs = array();
		    foreach ($news_search_arr as $item)
		    {
		  	  if($item != "")
		  	  {
		  	      $news_searchs[] = $item;
		  	  }
		    }
		    $news_search_query = implode("%", $news_searchs);
		    $news_search_query = "%" . $news_search_query . "%";	
		}
        if(isset($_POST['nextpage']) && !(empty($_POST['nextpage'])))
	    {	    	
	    	$page = $_POST['nextpage'];	
	    }
	    else
	    {
	    	$page = 1;
	    }
       
        $_SESSION['keysearch'] = array('nextpage'=>$page,'category'=>$category_id,'searchText'=>$news_search_query,'keyword'=>$searchText);
        $apiNews = new News_Api_Core();
        if($apiNews->checkVersionSE())//version 4.1.x
        {
            $this->_helper->content->setNoRender()->setEnabled();
        }
        else//version 4.0.x
        {
            $this->_helper->content->render();    
        }
        
         
	}
	public function manageAction()
   {
       $username  = Engine_Api::_()->user()->getViewer()->username;
        $users = Engine_Api::_()->news()->getAllUsers();
        $flag = false;
        foreach ($users as $user)
        {
           if ($user['username'] == $username)
           {
               $flag = true; 
           }
       }
        if (Engine_Api::_()->user()->getViewer()->level_id == 1 || Engine_Api::_()->user()->getViewer()->level_id == 2)
       {
           $flag = true; 
       } 
       if (!$this->_helper->requireUser()->isValid() || $flag == false )
       {
           $this->view->error   = 'You must be logged in.';
            return;
       }
       $this->view->navigation = $this->getNavigation();
        $page = $this->_getParam('page',1);    
        $_SESSION['result'] = null;
        $this->view->paginator = Engine_Api::_()->news()->getContentsPaginator(array(
          'order' => 'content_id DESC','order_feature'=>'manage',
        ));
        $this->view->paginator->setItemCountPerPage(25);
        $this->view->paginator->setCurrentPageNumber($page);
    
        if ($this->getRequest()->isPost())
        {
          $values = $this->getRequest()->getPost();
          try{
          foreach ($values as $key=>$value) {
            if ($key == 'delete_' . $value)
            {
              $content = Engine_Api::_()->getItem('contents', $value);
              $content->delete();
            }
          }
          }catch(Exception $ex){
              $_SESSION['result'] = 0;
          }
          $_SESSION['result'] = 1;
        } 
           
  }  
 public function featuredAction()
   {
      if($this->getRequest()->isPost())
      {
            $value = $this->getRequest()->getPost();
            $content_ids = explode(',',$value['news_featured']);
             $content = Engine_Api::_()->getDbTable('contents', 'news');                                                      
             try{
            foreach ($content_ids as $content_id){
                if (is_numeric($content_id))
                {
                    $where_content = $content->getAdapter()->quoteInto('content_id = ?', $content_id);
                    $content->update(array('is_featured'=>$value['is_set_featured']), $where_content);  
                }
            }
            }catch(Exception $e){
                $this->view->result = 0;
                 $_SESSION['result']=0;
            }
          $this->view->result = 2;       
      }
     $_SESSION['result'] = $this->view->result;
     if ($this->getRequest()->page >1 && !empty($this->getRequest()->page))
        $this->_redirect("news/manage/".$this->getRequest()->page,array('result'=>$this->view->result));    
     else
        $this->_redirect("news/manage",array('result'=>$this->view->result));    
   }
   public function editAction()
  {
    $translate = Zend_Registry::get('Zend_Translate');
    $content_id =  $this->view->content_id = $this->getRequest()->getParam('content_id');
    $this->view->form = $form = new News_Form_Edit();
    if ( $this->getRequest()->isPost() && $this->view->form->isValid($this->getRequest()->getPost()) ) {
      $db = Engine_Api::_()->getDbTable('contents', 'news')->getAdapter();
      $db->beginTransaction();
      try {
        $this->view->form->saveValues();
        $db->commit();
        $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => true,
              'parentRefresh' => true,
              'format'=> 'smoothbox',
              'messages' => array('Your changes have been saved.')
              ));
      } catch (Exception $e) {
        $db->rollback();
        $this->view->success = false;
      }
    }                                       
  }
	public function viewcommentsAction()
	{
		$contentId = $this->_getParam('id');
		$subject = Engine_Api::_()->getItem('contents', $contentId);
		
		$viewer = Engine_Api::_()->user()->getViewer();
	    
	
	    // Perms
	    $this->view->canDelete = $subject->authorization()->isAllowed($viewer, 'edit');
	
	    // Likes
	    $this->view->viewAllLikes = $this->_getParam('viewAllLikes', false);
	    $this->view->likes = $likes = $subject->likes()->getLikePaginator();
	
	    // Comments
	
	    // If has a page, display oldest to newest
	    if( null !== ( $page = $this->_getParam('page')) )
	    {
	      $commentSelect = $subject->comments()->getCommentSelect();
	      $commentSelect->order('comment_id ASC');
	      $comments = Zend_Paginator::factory($commentSelect);
	      $comments->setCurrentPageNumber($page);
	      $comments->setItemCountPerPage(10);
	      $this->view->comments = $comments;
	      $this->view->page = $page;
	    }
	
	    // If not has a page, show the
	    else
	    {
	      $commentSelect = $subject->comments()->getCommentSelect();
	      $commentSelect->order('comment_id DESC');
	      $comments = Zend_Paginator::factory($commentSelect);
	      $comments->setCurrentPageNumber(1);
	      $comments->setItemCountPerPage(4);
	      $this->view->comments = $comments;
	      $this->view->page = $page;
	    }

	    $this->view->subject = $subject;
	    $this->view->contentid = $contentId;
	    
	}
	
	public function loaddataAction()
	{
		$page = "1";
		$category = "0";
		$limit = 5;
  		if(isset($_POST['nextpage']) && !(empty($_POST['nextpage'])))
	    {	    	
	    	$page = $_POST['nextpage'];	
	    }
	    
		if(isset($_POST['category']) && !(empty($_POST['category'])))
	    {	    	
	    	$category = $_POST['category'];	
	    }
	    
		if(isset($_POST['limit']) && !(empty($_POST['limit'])))
	    {	    	
	    	$limit = $_POST['limit'];	
	    }
	    	    
	    	    
	    $this->view->paginator = Engine_Api::_()->news()->getContentsPaginator(array(
	      'category_id'=>$category, 'checkcomment'=>'yes', 'limit'=>$limit, 'order' => 'content_id DESC',
	    ));
	    $this->view->paginator->setItemCountPerPage(10);
	    $this->view->paginator->setCurrentPageNumber($page);
	    $this->view->categoryId = $category;	
	    $this->view->limit = $limit;	
	}
    protected $_navigation;
      public function getNavigation()
      {
        $tabs   = array();
        $tabs[] = array(
              'label'      => 'Browse News',
              'route'      => 'news_general',
              'action'     => 'index',
              'controller' => 'index',
              'module'     => 'news'
            );
       $tabs[] = array(
              'label'      => 'News Management',
              'route'      => 'news_general',
              'action'     => 'manage',
              'controller' => 'index',
              'module'     => 'news'
            );
        if( is_null($this->_navigation) ) {
          $this->_navigation = new Zend_Navigation();
          $this->_navigation->addPages($tabs);
        }
        return $this->_navigation;
      }
      public function loadfeedAction(){
          
        $categoryparent_id = $this->_getParam('categoryparent');
        $categories = Engine_Api::_()->news()->getAllCategories(array('category_active'=>1,'category_parent'=>$categoryparent_id));
        $html = '';
        if($categoryparent_id==-1)
        {
            $html .='<option value="0" label="Feed" selected= "selected">All Feeds</option>';
        }
        foreach($categories as $category)
        {
            $html.= '<option value="'.$category['category_id'] .'" label="'.$category['category_name'] .'" >'.$category['category_name'] .'</option>';
        }
        $this->view->html = $html;
        return;
      }
      public function deleteallAction(){
       $table = Engine_Api::_()->getDbtable('Contents','News');
        $select = $table->select();


        $contents = $table->fetchAll($select);
        // echo count($contents);die();
        if(count($contents) > 0 )
            foreach($contents as $content){
                $content->delete();
            }
      }
    public function listsAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    // Perms
    $this->view->canComment = $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
    $this->view->canDelete = $subject->authorization()->isAllowed($viewer, 'edit');

    // Likes
    $this->view->viewAllLikes = $this->_getParam('viewAllLikes', false);
    $this->view->likes = $likes = $subject->likes()->getLikePaginator();

    // Comments

    // If has a page, display oldest to newest
    if( null !== ( $page = $this->_getParam('page')) )
    {
      $commentSelect = $subject->comments()->getCommentSelect();
      $commentSelect->order('comment_id ASC');
      $comments = Zend_Paginator::factory($commentSelect);
      $comments->setCurrentPageNumber($page);
      $comments->setItemCountPerPage(10);
      $this->view->comments = $comments;
      $this->view->page = $page;
    }

    // If not has a page, show the
    else
    {
      $commentSelect = $subject->comments()->getCommentSelect();
      $commentSelect->order('comment_id DESC');
      $comments = Zend_Paginator::factory($commentSelect);
      $comments->setCurrentPageNumber(1);
      $comments->setItemCountPerPage(4);
      $this->view->comments = $comments;
      $this->view->page = $page;
    }

    if( $viewer->getIdentity() && $canComment ) {
      $this->view->form = $form = new Core_Form_Comment_Create();
      $form->populate(array(
        'identity' => $subject->getIdentity(),
        'type' => $subject->getType(),
      ));
    }


  }

}
?>