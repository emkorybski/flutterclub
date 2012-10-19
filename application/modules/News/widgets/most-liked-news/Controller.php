<?php
class News_Widget_MostLikedNewsController extends News_Content_Widget_Base
{
  public function indexAction()
  { 
	
	$table = Engine_Api::_()->getDbtable('Contents','News');
        $limit =$this->_getParam('max');
	
	if (!isset($limit) || $limit <=0)
		$limit = 5;
    $selectTop = $table->select('engine4_news_contents')->setIntegrityCheck(false)    
          ->joinLeft("engine4_news_categories","engine4_news_categories.category_id= engine4_news_contents.category_id",array('logo'=>'engine4_news_categories.category_logo','logo_icon'=>'engine4_news_categories.logo','display_logo'=>'engine4_news_categories.display_logo','display_logo'=>'engine4_news_categories.display_logo'))
          ->joinLeft("engine4_core_likes","engine4_core_likes.resource_id = engine4_news_contents.content_id",array('count_like'=>'Count(resource_id)'))
          ->where('engine4_core_likes.resource_type = ?','news_content')
          ->where('engine4_news_categories.is_active= ? ',1)
          ->group("resource_id")  
          ->order("Count(resource_id) DESC")
		  ->limit($limit);
	
    $topNews = $table->fetchAll($selectTop);
	
	if( count($topNews) <= 0 ) {
      return $this->setNoRender();
    }
    $this->view->topNews =  $this->_prepareContent($topNews);
	    
  }
}