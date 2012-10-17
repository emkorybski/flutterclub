<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Radcodes
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
/*
$this->category->getType() = article_category
$this->category->getType(true) = ArticleCategory
$this->category->getShortType() = Category
$this->category->getShortType(true) = Category
$this->category->getModuleName() = Article
 */

class Radcodes_Model_Category extends Core_Model_Item_Abstract
{
  // Properties
  protected $_searchTriggers = false;

  protected $_moduleItemType;
  
  protected $_parentCategory;
  
  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => $this->getModuleItemType() . '_general',
      'action' => 'browse',
      'reset' => true,
      'category' => $this->category_id,
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }
  
  public function getTitle()
  {
    return $this->category_name;
  }


  
  public function getModuleItemType()
  {
    if (null === $this->_moduleItemType)
    {
      $this->_moduleItemType = strtolower($this->getModuleName());
    }
    
    return $this->_moduleItemType;
  }
  
  public function hasChildrenCategory()
  {
  	return count($this->getChildrenCategory()) > 0;
  }
  
  public function getChildrenCategory($params = array())
  {
    $childTable = Engine_Api::_()->getItemTable($this->getType());
    $categories = $childTable->getChildrenOfParent($this, $params);
    return $categories;
  }

  
  public function getUsedCount($include_children = false)
  {
    //$table  = Engine_Api::_()->getDbTable('articles', 'article');
    
    $table = Engine_Api::_()->getItemTable($this->getModuleItemType());
    
    $rName = $table->info('name');
    $select = $table->select()
                    ->from($rName)
                    ->where($rName.'.category_id = ?', $this->category_id);
    $row = $table->fetchAll($select);
    $total = count($row);
    
    if ($include_children)
    {
      $children = $this->getChildrenCategory();
      foreach ($children as $child)
      {
        $total += $child->getUsedCount();
      }
    }
    
    return $total;
  }


  public function setPhoto($photo)
  {
    if( $photo instanceof Zend_Form_Element_File ) {
      $file = $photo->getFileName();
    } else if( $photo instanceof Storage_Model_File ) {
      $file = $photo->temporary();
    } else if( $photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id) ) {
      $file = Engine_Api::_()->getItem('storage_file', $photo->file_id)->temporary();
    } else if( is_array($photo) && !empty($photo['tmp_name']) ) {
      $file = $photo['tmp_name'];
    } else if( is_string($photo) && file_exists($photo) ) {
      $file = $photo;
    } else {
      throw new Core_Model_Item_Exception('invalid argument passed to setPhoto');
    }

    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => $this->getType(),
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

    // Resize image (mini)
    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 16, 16)
      ->write($path.'/imn_'.$name)
      ->destroy();      
      
    // Store
    $iMain = $storage->create($path.'/m_'.$name, $params);
    $iProfile = $storage->create($path.'/p_'.$name, $params);
    $iIconNormal = $storage->create($path.'/in_'.$name, $params);
    $iSquare = $storage->create($path.'/is_'.$name, $params);
    $iMini = $storage->create($path.'/imn_'.$name, $params);
    
    $iMain->bridge($iProfile, 'thumb.profile');
    $iMain->bridge($iIconNormal, 'thumb.normal');
    $iMain->bridge($iSquare, 'thumb.icon');
    $iMain->bridge($iMini, 'thumb.mini');
    
    // Remove temp files
    @unlink($path.'/p_'.$name);
    @unlink($path.'/m_'.$name);
    @unlink($path.'/in_'.$name);
    @unlink($path.'/is_'.$name);
    @unlink($path.'/imn_'.$name);
    
    // Update row
    $this->photo_id = $iMain->file_id;
    $this->save();
    
    return $this;
  } 
  
  public function removePhoto()
  {
    if (empty($this->photo_id))
    {
      return;
    }
    
    $types = array(null, 'thumb.profile', 'thumb.normal', 'thumb.icon', 'thumb.mini');
    foreach ($types as $type)
    {
      $file = Engine_Api::_()->getApi('storage', 'storage')->get($this->photo_id, $type);
      if ($file)
      {
        $file->remove();
      } 
    }
    
    $this->photo_id = 0;
  }   
  
  public function hasParentCategory()
  {
  	return $this->parent_id != 0;
  }
  
  public function getParentCategory()
  {
    if ($this->hasParentCategory())
    {
      if (null === $this->_parentCategory)
      {
        $this->_parentCategory = $this->getTable()->getCategory($this->parent_id);
      }
      return $this->_parentCategory;
    }
    else
    {
      return null;
    }
  }
  
  public function isParentOfCategory($category)
  {
  	return $this->getIdentity() == $category->parent_id;
  }
  
  public function isChildOfCategory($category)
  {
  	return $this->parent_id == $category->getIdentity();
  }
  
  
  public function getProfileTypeLabel()
  {
    return $this->getModuleApi()->profile()->getLabel($this->getProfileTypeId());
  }
  
  public function getProfileTypeId()
  {
    if ($this->hasParentCategory()) {
      $profile_type_id = $this->getParentCategory()->profile_type_id;
    }
    else {
      $profile_type_id = $this->profile_type_id;
    }
    
    if (!$this->getModuleApi()->profile()->isValidTypeId($profile_type_id)) {
      $profile_type_id = $this->getModuleApi()->profile()->getDefaultTypeId();
    }
    
    return $profile_type_id;
  }
  
  
  public function supportProfileType()
  {
    return $this->getTable()->supportProfileType();
  }
  
  public function hasProfileType()
  {
    if (!$this->supportProfileType()) {
      return false;
    }
    
    if (!$this->profile_type_id) {
      return false;
    }
    
    return $this->getModuleApi()->profile()->isValidTypeId($this->profile_type_id);
  }

  
  /**
   * @return Core_Api_Abstract
   */
  public function getModuleApi()
  {
    return Engine_Api::_()->getApi('core', strtolower($this->getModuleName()));
  }
  
}