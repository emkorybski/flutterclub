<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class News_Content_Widget_Base extends Engine_Content_Widget_Abstract {

   public function indexAction() {

    $items = $this->_getItems();

    if (count($items) <= 0) {
      return $this->setNoRender();
    }
    $this->view->items = $this->_prepareContent($items);
    $this->setScriptPath('application/modules/News/widgets/top-news');
  }
  
  protected function _prepareContent($items) {

    foreach ($items as $key => $item) {
      $items[$key]->author = $item->author ? $item->author : 'Unknown';
      $items[$key]->pubDate = $this->_getShortime($item);
      $items[$key]->image = $this->_getShortImage($item);
      $items[$key]->description = $this->_getShortDescription($item);
    }
    return $items;
  }

  protected function _getShortDescription($item) {
	return $item->description;

    $str = $item->description;
	$str =  preg_replace('/<br\s*\/>/',' - ',$str);
	$str = preg_replace('/<a\s+[^>]+>(.*?)<\/a>/im','',$str);
	$str = strip_tags($str);
	$str =  trim($str,' - ');
	$str = str_replace('--','-');
	
    $result = '';
    if ($des != null) {
      $result = (strlen($des) > 125) ? ($this->view->string()->chunk(substr($des, 0, 125), 125) . ' ...') : $des;
    } else {
      $result = (strlen($str) > 125) ? $this->view->string()->chunk(substr($str, 0, 125), 125) . ' ...' : $str;
    }
    return $result;
  }

  protected function _getShortImage($item) {
    if ($item->image != "") {
      return '<img class="ynnews-smallthumb" src="' . $item->image . '" align="left" />';
    }
    
  }

  protected function _getShortime($item) {

      try {
        if (is_numeric($item->pubDate)) {
          $shortTime = explode(",", date("F j, Y, H:i:s", $item->pubDate));
        } else {
          $shortTime = explode(" ", $item->pubDate);
        }
      } catch (Exception $ex) {
        $shortTime = explode(" ", $item->pubDate);
      }
    
    $time = "";
    $i = 0;
    if (count($shortTime) <= 1)
      $time = $shortTime[0];
    else {
      for ($i = 0; $i < count($shortTime) - 1; $i++) {
        $time.= $shortTime[$i] . " ";
      }
    }
    return $item->pubDate;
  }

}