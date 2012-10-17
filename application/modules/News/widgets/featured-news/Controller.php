<?php
class News_Widget_FeaturedNewsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  { 
    
    $table = Engine_Api::_()->getDbtable('Contents','News');
    $limit =$this->_getParam('max');
   
    if (!isset($limit) || $limit <=0)
        $limit = 5;
    $limit = 20;
    $selectFeatured = $table->select('engine4_news_contents')->setIntegrityCheck(false)    
          ->joinLeft("engine4_news_categories","engine4_news_categories.category_id= engine4_news_contents.category_id",array('logo'=>'engine4_news_categories.category_logo','logo_icon'=>'engine4_news_categories.logo','display_logo'=>'engine4_news_categories.display_logo','mini_logo'=>'engine4_news_categories.mini_logo'))
          ->where('engine4_news_categories.is_active= ? ',1)
         ->where('engine4_news_contents.is_featured = ? ',1)
         ->order('engine4_news_contents.pubDate DESC')
         ->limit($limit);
    
    $featuredNews = $table->fetchAll($selectFeatured);
    
    if( count($featuredNews) <= 0 ) {
      return $this->setNoRender();
    }
    $this->view->featuredNews = $featuredNews;
    $this->view->totalItem = count($featuredNews);
        
  }
}
