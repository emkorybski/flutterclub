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
 
 
 
class Article_Model_Article extends Core_Model_Item_Abstract
{
  // Properties

  protected $_parent_type = 'user';

  //protected $_owner_type = 'user';

  protected $_searchColumns = array('title', 'body');

  protected $_parent_is_owner = true;

  protected $category;
  
  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array())
  {
    $slug = $this->getSlug();
    
    $params = array_merge(array(
      'route' => 'article_entry_view',
      'reset' => true,
      //'user_id' => $this->owner_id,
      'article_id' => $this->article_id,
      'slug' => $slug,
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  
  public function getExcerpt($length=255, $truncate_string='...', $truncate_lastspace=false)
  {
  	$text = strip_tags($this->body);
    return Radcodes_Lib_Helper_Text::truncate($text, $length, $truncate_string, $truncate_lastspace);
  }
  
  
  public function getDescription()
  {
    $description = parent::getDescription();
    $description = trim($description);
    if (empty($description)) {
      $description = $this->getExcerpt();
    }
  	return $description;
  }
  
  public function getKeywords($separator = ' ')
  {
    $keywords = array();
    foreach( $this->tags()->getTagMaps() as $tagmap ) {
      $tag = $tagmap->getTag();
      $keywords[] = $tag->getTitle();
    }

    if( null === $separator ) {
      return $keywords;
    }

    return join($separator, $keywords);
  }

  
  public function setPhoto($photo)
  {
    if( $photo instanceof Zend_Form_Element_File ) {
      $file = $photo->getFileName();
    } else if( is_array($photo) && !empty($photo['tmp_name']) ) {
      $file = $photo['tmp_name'];
    } else if( is_string($photo) && file_exists($photo) ) {
      $file = $photo;
    } else {
      throw new Article_Model_Exception('invalid argument passed to setPhoto');
    }

    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => 'article',
      'parent_id' => $this->getIdentity()
    );

    // Save
    $storage = Engine_Api::_()->storage();

    // Resize image (main)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(720, 720)
      ->write($path.'/m_'.$name)
      ->destroy();

    // Resize image (profile)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(200, 400)
      ->write($path.'/p_'.$name)
      ->destroy();

    // Resize image (normal)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(140, 160)
      ->write($path.'/in_'.$name)
      ->destroy();

    // Resize image (icon)
    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 48, 48)
      ->write($path.'/is_'.$name)
      ->destroy();

    // Store
    $iMain = $storage->create($path.'/m_'.$name, $params);
    $iProfile = $storage->create($path.'/p_'.$name, $params);
    $iIconNormal = $storage->create($path.'/in_'.$name, $params);
    $iSquare = $storage->create($path.'/is_'.$name, $params);

    $iMain->bridge($iProfile, 'thumb.profile');
    $iMain->bridge($iIconNormal, 'thumb.normal');
    $iMain->bridge($iSquare, 'thumb.icon');

    // Remove temp files
    @unlink($path.'/p_'.$name);
    @unlink($path.'/m_'.$name);
    @unlink($path.'/in_'.$name);
    @unlink($path.'/is_'.$name);

    // Add to album
    $viewer = Engine_Api::_()->user()->getViewer();
    $photoTable = Engine_Api::_()->getItemTable('article_photo');
    $articleAlbum = $this->getSingletonAlbum();
    $photoItem = $photoTable->createRow();
    $photoItem->setFromArray(array(
      'article_id' => $this->getIdentity(),
      'album_id' => $articleAlbum->getIdentity(),
      'user_id' => $viewer->getIdentity(),
      'file_id' => $iMain->getIdentity(),
      'collection_id' => $articleAlbum->getIdentity(),
    ));
    $photoItem->save();

    // Update row
    $this->modified_date = date('Y-m-d H:i:s');
    $this->photo_id = $photoItem->file_id;
    $this->save();

    return $this;
  }

  public function getPhoto($photo_id = null)
  {
    if ($photo_id === null) {
      $photo_id = $this->photo_id;
    }
    
    $photoTable = Engine_Api::_()->getItemTable('article_photo');
    $select = $photoTable->select()
      ->where('file_id = ?', $photo_id)
      ->limit(1);

    $photo = $photoTable->fetchRow($select);
    return $photo;
  }
  
  public function getSingletonAlbum()
  {
    $table = Engine_Api::_()->getItemTable('article_album');
    $select = $table->select()
      ->where('article_id = ?', $this->getIdentity())
      ->order('album_id ASC')
      ->limit(1);

    $album = $table->fetchRow($select);

    if( null === $album )
    {
      $album = $table->createRow();
      $album->setFromArray(array(
        'title' => $this->getTitle(),
        'article_id' => $this->getIdentity()
      ));
      $album->save();
    }

    return $album;
  }


  public function isPublished()
  {
  	return $this->published ? true : false;
  }
  

  // Interfaces
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
  
  
  /**
   * 
   * @return Article_Model_Category
   */
  public function getCategory()
  {
    if (!($this->category instanceof Article_Model_Category) || $this->category->getIdentity() != $this->category_id)
    {
      $category = Engine_Api::_()->getItemTable('article_category')->getCategory($this->category_id);
      if (!($category instanceof Article_Model_Category))
      {
        $category = new Article_Model_Category(array());
      }
      $this->category = $category;
    }

    return $this->category;
  }
  
 
  
}