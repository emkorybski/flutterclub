<?php

class News_Widget_LastedNewsController extends News_Content_Widget_Base {

  protected function _getItems() {
    $table = Engine_Api::_()->getDbtable('Contents', 'News');
    $limit = $this->_getParam('max', 5);
    if ($limit <= 0) {
      $limit = 5;
    }

    $select = $table->select('engine4_news_contents')->setIntegrityCheck(false)
            ->joinLeft("engine4_news_categories", "engine4_news_categories.category_id= engine4_news_contents.category_id", array('logo' => 'engine4_news_categories.category_logo', 'logo_icon' => 'engine4_news_categories.logo', 'display_logo' => 'engine4_news_categories.display_logo', 'mini_logo' => 'engine4_news_categories.mini_logo'))
            ->order('engine4_news_contents.pubDate DESC')
            ->where('engine4_news_categories.is_active= ? ', 1)
            ->limit($limit);

    return $table->fetchAll($select);
  }

}