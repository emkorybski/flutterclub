<?php
class News_Widget_ListingNewsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {  	
      if(isset($_SESSION['keysearch']))
         {
//            print_r($_SESSION['keysearch']);
//            die();
             $category =  $_SESSION['keysearch']['category'];
             $news_search_query = $_SESSION['keysearch']['searchText'];
             $page = $_SESSION['keysearch']['nextpage'];
             $start_date =  $_SESSION['start_date'];
             $end_date =  $_SESSION['end_date'];

      }else{
  		    $category = $this->_getParam('category');
            $page = "1";
              if(isset($_POST['nextpage']) && !(empty($_POST['nextpage'])))
              {            
                    $page = $_POST['nextpage'];    
              }
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
                         echo $news_search_query;
            $_SESSION['category'] = $category;
            $_SESSION['searchText'] = $news_search_query ;
            

         }

        $limit = $this->_getParam('max');
        if(!isset($limit) || $limit<=0)
            $limit = 10;
	    $this->view->paginator =Engine_Api::_()->news()->getContentsPaginator(array(
          'category_id' => $category, 'search'=>$news_search_query, 'order' => 'pubDate DESC','limit'=>$limit,'is_active'=>1,'getcommment'=>true,'new2'=>'listing2',
                'start_date'=>$start_date,'end_date'=>$end_date,
        ));
        $tableComment = Engine_Api::_()->getDbtable('comments','Core');
//        foreach($this->view->paginator as $key=>$itemnews)
//        {
//
//        }
	    $this->view->paginator->setItemCountPerPage( $limit );
	    $this->view->paginator->setCurrentPageNumber($page);

  }
}