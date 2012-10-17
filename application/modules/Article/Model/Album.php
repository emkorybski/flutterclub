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
 
 
 
class Article_Model_Album extends Core_Model_Item_Collection
{
  protected $_parent_type = 'article';

  protected $_owner_type = 'article';

  protected $_children_types = array('article_photo');

  protected $_collectible_type = 'article_photo';

  protected $_searchTriggers = array();
  
  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'article_extended',
      'reset' => true,
      'controller' => 'photo',
      'action' => 'list',
      'subject' => $this->getArticle()->getGuid(),
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  public function getArticle()
  {
    return $this->getOwner();
  }

  public function getAuthorizationItem()
  {
    return $this->getParent('article');
  }

  protected function _delete()
  {
    // Delete all child posts
    $photoTable = Engine_Api::_()->getItemTable('article_photo');
    $photoSelect = $photoTable->select()->where('album_id = ?', $this->getIdentity());
    foreach( $photoTable->fetchAll($photoSelect) as $articlePhoto ) {
      $articlePhoto->delete();
    }

    parent::_delete();
  }
}