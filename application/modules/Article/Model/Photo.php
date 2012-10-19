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
 
 
 
class Article_Model_Photo extends Core_Model_Item_Collectible
{
  protected $_parent_type = 'article_album';

  protected $_owner_type = 'user';

  protected $_collection_type = 'article_album';

  protected $_searchTriggers = array();
  
  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'article_extended',
      'reset' => true,
      'controller' => 'photo',
      'action' => 'view',
      'article_id' => $this->getCollection()->getOwner()->getIdentity(),
      'photo_id' => $this->getIdentity(),
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  public function getPhotoUrl($type = null)
  {
    if( empty($this->file_id) )
    {
      return null;
    }

    $file = Engine_Api::_()->getApi('storage', 'storage')->get($this->file_id, $type);
    if( !$file )
    {
      return null;
    }

    return $file->map();
  }

  public function getArticle()
  {
    return Engine_Api::_()->getItem('article', $this->article_id);
    //return $this->getCollection()->getGroup();
  }

  public function isSearchable()
  {
    $collection = $this->getCollection();
    if( !$collection instanceof Core_Model_Item_Abstract )
    {
      return false;
    }
    return $collection->isSearchable();
  }

  public function getAuthorizationItem()
  {
    return $this->getParent('article');
  }

  
  public function setPhoto($photo)
  {
    if( $photo instanceof Zend_Form_Element_File ) {
      $file = $photo->getFileName();
      $fileName = $file;
    } else if( $photo instanceof Storage_Model_File ) {
      $file = $photo->temporary();
      $fileName = $photo->name;
    } else if( $photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id) ) {
      $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
      $file = $tmpRow->temporary();
      $fileName = $tmpRow->name;
    } else if( is_array($photo) && !empty($photo['tmp_name']) ) {
      $file = $photo['tmp_name'];
      $fileName = $photo['name'];
    } else if( is_string($photo) && file_exists($photo) ) {
      $file = $photo;
      $fileName = $photo;
    } else {
      throw new Classified_Model_Exception('invalid argument passed to setPhoto');
    }

    if( !$fileName ) {
      $fileName = basename($file);
    }

    $extension = ltrim(strrchr(basename($fileName), '.'), '.');
    $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    
    $params = array(
      'parent_type' => 'article',
      'parent_id' => $this->article_id,
      'user_id' => $this->user_id,
      'name' => $fileName,
    );

    // Save
    $filesTable = Engine_Api::_()->getItemTable('storage_file');

    // Resize image (main)
    $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(720, 720)
      ->write($mainPath)
      ->destroy();

    // Resize image (normal)
    $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(140, 160)
      ->write($normalPath)
      ->destroy();

    // Store
    $iMain = $filesTable->createFile($mainPath, $params);
    $iIconNormal = $filesTable->createFile($normalPath, $params);
    
    $iMain->bridge($iIconNormal, 'thumb.normal');
    
    // Remove temp files
    @unlink($mainPath);
    @unlink($normalPath);

    // Update row
    $this->modified_date = date('Y-m-d H:i:s');
    $this->file_id = $iMain->file_id;
    $this->save();
    
    return $this;
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
   * Gets a proxy object for the tags handler
   *
   * @return Engine_ProxyObject
   **/
  public function tags()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
  }

  protected function _postDelete()
  {
    if( $this->_disableHooks ) return;

    // This is dangerous, what if something throws an exception in postDelete
    // after the files are deleted?
    try
    {
      $file = Engine_Api::_()->getApi('storage', 'storage')->get($this->file_id);
      $file->remove();
      $file = Engine_Api::_()->getApi('storage', 'storage')->get($this->file_id, 'thumb.normal');
      $file->remove();

      $album = $this->getCollection();

      if( (int) $album->photo_id == (int) $this->getIdentity() )
      {
	      $album->photo_id = $this->getNextCollectible()->getIdentity();
	      $album->save();
      }
    }
    catch( Exception $e )
    {
      // @todo completely silencing them probably isn't good enough
      //throw $e;
    }
  }
  
  public function canEdit($user)
  {
    return $this->getParent()->getParent()->authorization()->isAllowed($user, 'edit') || $this->isOwner($user);
  }  
}