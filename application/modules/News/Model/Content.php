<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    News
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Photo.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    News
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class News_Model_Content extends Core_Model_Item_Abstract
{
	public function getHref($params = array())
	{   		
	    $params = array_merge(array(
	      'route' => 'news_specific',
	      'reset' => true,	      
	      'id' => $this->content_id,	
	      'title' => str_replace(array("'", "\"","/"), "", $this->title)      
	    ), $params);
	    $route = $params['route'];
	    $reset = $params['reset'];
	    unset($params['route']);
	    unset($params['reset']);
	    return Zend_Controller_Front::getInstance()->getRouter()
	      ->assemble($params, $route, $reset);
	}
	
	public function getObj($contentId)
	{
		$news_content = Engine_Api::_()->getItem('contents', $contentId);		
		return $news_content;
	}
	
	/**
	   * Gets a proxy object for the comment handler
	   *
	   * @return Engine_ProxyObject
	   **/
	  public function comments()
	  {
	    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
	  }
	  
	  /**
	   * Gets a proxy object for the like handler
	   *
	   * @return Engine_ProxyObject
	   **/
	  public function likes()
	  {
	    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
	  }
      /**
       * Gets a proxy object for the subscribe handler
       *
       * @return Engine_ProxyObject
       **/
      public function subscribes()
      {
            return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('subscribes', 'core'));
      }
}
?>