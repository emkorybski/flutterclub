<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Photos.php 9747 2012-07-26 02:08:08Z john $
 * @author     Sami
 */

/**
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Album_Model_DbTable_Photos extends Engine_Db_Table
{
  protected $_rowClass = 'Album_Model_Photo';
  
  public function getPhotoSelect(array $params)
  {
    $select = $this->select();
    
    if( !empty($params['album']) && $params['album'] instanceof Album_Model_Album ) {
      $select->where('album_id = ?', $params['album']->getIdentity());
    } else if( !empty($params['album_id']) && is_numeric($params['album_id']) ) {
      $select->where('album_id = ?', $params['album_id']);
    }
    
    if( !isset($params['order']) ) {
      $select->order('order ASC');
    } else if( is_string($params['order']) ) {
      $select->order($params['order']);
    }
    
    return $select;
  }
  
  public function getPhotoPaginator(array $params)
  {
    return Zend_Paginator::factory($this->getPhotoSelect($params));
  }
}
