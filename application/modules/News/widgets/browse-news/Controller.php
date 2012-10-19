<?php
class News_Widget_BrowseNewsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {  	
	
        $_SESSION['start_date'] ='';
        $_SESSION['end_date']='';
        $this->view->form = $form = new News_Form_Search();
	    // Populate form
        $this->view->categories = $categories = Engine_Api::_()->news()->getAllCategories();

        foreach( $categories as $category )
        {
            $form->category->addMultiOption($category['category_id'], $category['category_name']);
        }

        $_paginate_params = array();
        $obj_param = Engine_Api::_()->core()->getSubject('news_param');
        $_paginate_params['search']  =  $obj_param->search;
        $_paginate_params['orderby']   =   $obj_param->orderby;      
        $_paginate_params['category'] =   $obj_param->category;
        $_paginate_params['page'] =  $obj_param->page;
       	$params = array_merge($_paginate_params, array());
    	// Process form
    	$form->isValid($params);
	    $values = $form->getValues();
	    $this->view->categoryId = $values['category'];
	    
	    $news_search_query = "";
	    if($values['search'] != "")
	    {
	    	$news_search_arr = explode(" ", $values['search']);
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
      	
	    $this->view->paginator = Engine_Api::_()->news()->getContentsPaginator(array(
	      'category_id' => $values['category'], 'search' =>$news_search_query, 'order' => 'content_id DESC','is_active'=>1
	    ));
	    $this->view->paginator->setItemCountPerPage(25);
	    $this->view->paginator->setCurrentPageNumber($values['page']);
	    
  }
}