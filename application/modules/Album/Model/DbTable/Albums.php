<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Albums.php 9747 2012-07-26 02:08:08Z john $
 * @author     Sami
 */

/**
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Album_Model_DbTable_Albums extends Engine_Db_Table
{
  protected $_rowClass = 'Album_Model_Album';

  public function getSpecialAlbum(User_Model_User $user, $type)
  {
    if( !in_array($type, array('wall', 'profile', 'message', 'blog')) ) {
      throw new Album_Model_Exception('Unknown special album type');
    }

    $select = $this->select()
        ->where('owner_type = ?', $user->getType())
        ->where('owner_id = ?', $user->getIdentity())
        ->where('type = ?', $type)
        ->order('album_id ASC')
        ->limit(1);
    
    $album = $this->fetchRow($select);

    // Create wall photos album if it doesn't exist yet
    if( null === $album ) {
      $translate = Zend_Registry::get('Zend_Translate');

      $album = $this->createRow();
      $album->owner_type = 'user';
      $album->owner_id = $user->getIdentity();
      $album->title = $translate->_(ucfirst($type) . ' Photos');
      $album->type = $type;

      if( $type == 'message' ) {
        $album->search = 0;
      } else {
        $album->search = 1;
      }

      $album->save();
      
      // Authorizations
      if( $type != 'message' ) {
        $auth = Engine_Api::_()->authorization()->context;
        $auth->setAllowed($album, 'everyone', 'view',    true);
        $auth->setAllowed($album, 'everyone', 'comment', true);
      }
    }

    return $album;
  }
  
  public function getAlbumSelect($options = array())
  {
    $select = $this->select();
    if( !empty($options['owner']) && 
        $options['owner'] instanceof Core_Model_Item_Abstract ) {
      $select
        ->where('owner_type = ?', $options['owner']->getType())
        ->where('owner_id = ?', $options['owner']->getIdentity())
        ->order('modified_date DESC')
        ;
    }

    if( !empty($options['search']) && is_numeric($options['search']) ) {
      $select->where('search = ?', $options['search']);
    }

    return $select;
  }

  public function getAlbumPaginator($options = array())
  {
    return Zend_Paginator::factory($this->getAlbumSelect($options));
  }
}