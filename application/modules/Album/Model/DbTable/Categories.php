<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Categories.php 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Album_Model_DbTable_Categories extends Engine_Db_Table
{
  protected $_rowClass = 'Album_Model_Category';
  
  public function getCategoriesAssoc()
  {
    $data = array();
    $stmt = $this->select()
        ->from($this, array('category_id', 'category_name'))
        ->order('category_name ASC')
        ->query()
        ;
    foreach( $stmt->fetchAll() as $category ) {
      $data[$category['category_id']] = $category['category_name'];
    }
    return $data;
  }
}