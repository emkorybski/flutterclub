<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Controller.php 8536 2011-03-01 04:43:10Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_Widget_ListOnlineController extends Engine_Content_Widget_Abstract
{
  protected $_onlineUserCount;
  
  public function indexAction()
  {
    // Get online users
    $table = Engine_Api::_()->getItemTable('user');
    $onlineTable = Engine_Api::_()->getDbtable('online', 'user');
    
    $tableName = $table->info('name');
    $onlineTableName = $onlineTable->info('name');

    $select = $table->select()
      //->from($onlineTableName, null)
      //->joinLeft($tableName, $onlineTable.'.user_id = '.$tableName.'.user_id', null)
      ->from($tableName)
      ->joinRight($onlineTableName, $onlineTableName.'.user_id = '.$tableName.'.user_id', null)
      ->where($onlineTableName.'.user_id > ?', 0)
      ->where($onlineTableName.'.active > ?', new Zend_Db_Expr('DATE_SUB(NOW(),INTERVAL 20 MINUTE)'))
      ->where($tableName.'.search = ?', 1)
      ->where($tableName.'.enabled = ?', 1)
      ->order($onlineTableName.'.active DESC')
      ->group($onlineTableName.'.user_id')
      ;

    $paginator = Zend_Paginator::factory($select);

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 16));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Skip if empty
    $count = $paginator->getTotalItemCount();
    if( $count <= 0 ) {
      return $this->setNoRender();
    }
     else { $count = $count * 7; }
    $this->view->paginator = $paginator;

    // Make title
    $this->_onlineUserCount = $count;
    
    $element = $this->getElement();
    $title = $this->view->translate(array($element->getTitle(), $element->getTitle(), $count), $this->view->locale()->toNumber($count));
    $element->setTitle($title);
    $element->setParam('disableTranslate', true);

    // Guests online
    $this->view->guestCount = null;
    if( $this->_getParam('showGuests', false) ) {
      $this->view->guestCount = $onlineTable->select()
        ->from($onlineTable, new Zend_Db_Expr('COUNT(*) as count'))
        ->where('user_id = ?', 0)
        ->where('active > ?', new Zend_Db_Expr('DATE_SUB(NOW(),INTERVAL 20 MINUTE)'))
        ->query()
        ->fetchColumn();
        ;
    }
  }

  public function getCacheKey()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $translate = Zend_Registry::get('Zend_Translate');
    return $viewer->getIdentity() . $translate->getLocale();
  }

  public function getCacheSpecificLifetime()
  {
    return 120;
  }

  public function getCacheExtraContent()
  {
    return $this->_onlineUserCount;
  }

  public function setCacheExtraData($data)
  {
    $element = $this->getElement();
    $element->setTitle(sprintf($element->getTitle(), (int) $data));
  }
}