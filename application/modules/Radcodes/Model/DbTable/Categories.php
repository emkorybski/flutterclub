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
 
 
 
class Radcodes_Model_DbTable_Categories extends Engine_Db_Table
{
  protected $_rowClass = 'Radcodes_Model_Category';
  
  protected $_supportProfileType = false;
  
  public function supportProfileType()
  {
    return $this->_supportProfileType;
  }
  
  public function getCategorySelect($params = array())
  {
    $select = $this->select();
    
    if (isset($params['parent']))
    {
      $parent_id = ($params['parent'] instanceof Radcodes_Model_Category) ? $params['parent']->getIdentity() : $params['parent'];
      $select->where("parent_id = ?", $parent_id);
    }
    
    if (isset($params['category_id']))
    {
      $select->where("category_id = ?", $params['category_id']);
    }
    
    if (!empty($params['order'])) 
    {
      $select->order($params['order']);
    }
    else
    {
      //$select->order('order');
    }
    
    return $select;
  }
  
  
  public function getCategory($category_id)
  {
    static $_categories;
    
    // cache all
    if ($_categories === null) {
      $cs = $this->getCategories();
      $_categories = array();
      foreach ($cs as $category) {
        $_categories[$category->getIdentity()] = $category;
      }
    }
    
    if (!isset($_categories[$category_id]))
    {
      $_categories[$category_id] = $this->findRow($category_id);
    }
    
    return $_categories[$category_id];
  }
  
  
  public function getCategories($params = array())
  {
    $params = array_merge(array('order' => 'order'), $params);
    $select = $this->getCategorySelect($params);
    
    return $this->fetchAll($select);
  }
  
  public function getChildrenOfParent($parent, $params = array())
  {
  	$params = array_merge($params, array('parent' => $parent));
  	return $this->getCategories($params);
  }
  
  /**
   * get top-level / root categories
   */
  public function getTopLevelCategories($params = array())
  {
    return $this->getCategories(array_merge($params, array('parent'=>0)));
  }
  
  public function getTopLevelCategoriesAssoc($params = array())
  {
    $categories = $this->getTopLevelCategories($params);
    $data = $this->toAssoc($categories);
    return $data;
  }
  
  public function getParentChildrenAssoc($params = array())
  {
    $categories = $this->getCategories($params);
  	
  	$parent_children = array();
  	foreach ($categories as $category)
  	{
  		$parent_children[$category->parent_id][] = $category;
  	}
  	
  	return $parent_children;
  }
  
  public function getMultiOptionsAssoc($params = array())
  {
    $child_prefix = isset($params['child_prefix']) ? $params['child_prefix'] : '|-';
  	$parent_children = $this->getParentChildrenAssoc($params);

  	$data = array();
  	foreach ($parent_children[0] as $category) {
  	  $data[$category->getIdentity()] = $category->getTitle();
  	  if (isset($parent_children[$category->getIdentity()]) && count($parent_children[$category->getIdentity()]))
  	  {
  	    foreach ($parent_children[$category->getIdentity()] as $child) {
  	      $data[$child->getIdentity()] = $child_prefix . ' ' .$child->getTitle();
  	    }
  	  }
  	}
  	
  	return $data;
  }
  
  public function toAssoc($categories)
  {
    $data = array();
    foreach ($categories as $category)
    {
      $data[$category->getIdentity()] = $category->getTitle();
    }
    return $data;
  }
  
  public function getDoubleOptionsAssoc($params = array())
  {
    $assoc = array();
    $parent_children = $this->getParentChildrenAssoc($params);
    
    foreach ($parent_children as $parent_id => $categories) {
      $assoc[$parent_id] = $this->toAssoc($categories);
    }
    
  	return $assoc;
  }  
}