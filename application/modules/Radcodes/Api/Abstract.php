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
 
class Radcodes_Api_Abstract extends Core_Api_Abstract
{
  
  public function checkLicense()
  {
    $license = Engine_Api::_()->getApi('settings', 'core')->getSetting($this->getModuleName() . '.license');
    return (trim($license) && $license != 'XXXX-XXXX-XXXX-XXXX');
  }  
  // checkLicense
  
  public function convertItemsToArray($items)
  {
    $data = array();
    foreach ($items as $item) {
      $data[$item->getIdentity()] = $item->getTitle();
    }
    return $data;
  }
  // convertItemsToArray
  
  public function localToServerTime($local_time, $user = null)
  {
    if (!($user instanceof User_Model_User))
    {
      $user = Engine_Api::_()->user()->getViewer();
    }
    
    $oldTz = date_default_timezone_get();
    date_default_timezone_set($user->timezone);
    $end = strtotime($local_time);
    date_default_timezone_set($oldTz);
    $server_time = date('Y-m-d H:i:s', $end);
    
    return $server_time;
  }
  // localToServerTime
  
  
  public function serverToLocalTime($server_time, $user = null)
  {
    if (!($user instanceof User_Model_User))
    {
      $user = Engine_Api::_()->user()->getViewer();
    }    
    
    $end = strtotime($server_time);
    $oldTz = date_default_timezone_get();
    date_default_timezone_set($user->timezone);
    $local_time = date('Y-m-d H:i:s', $end);
    date_default_timezone_set($oldTz);
    
    return $local_time;
  }
  // serverToLocalTime
  
  
  public function filterEmptyParams($values)
  {
    foreach ($values as $key => $value)
    {
      if (is_array($value))
      {
        foreach ($value as $value_k => $value_v)
        {
          if (!strlen($value_v))
          {
            unset($value[$value_k]);
          }
        }
      }
      
      if (is_array($value) && count($value) == 0)
      {
        unset($values[$key]);
      }
      else if (!is_array($value) && !strlen($value))
      {
        unset($values[$key]);
      }
    }
    
    return $values;
  }
  // filterEmptyParams
  
  public function getSpecialAlbum(User_Model_User $user, $type)
  {
    $table = Engine_Api::_()->getDbtable('albums', 'album');

    $translate = Zend_Registry::get('Zend_Translate');
    $title = $translate->_(ucfirst($type) . ' Photos');
    
    $select = $table->select()
        ->where('owner_type = ?', $user->getType())
        ->where('owner_id = ?', $user->getIdentity())
        ->where('title = ?', $title)
        ->order('album_id ASC')
        ->limit(1);
    
    $album = $table->fetchRow($select);

    // Create wall photos album if it doesn't exist yet
    if( null === $album )
    {
      $album = $table->createRow();
      $album->owner_type = $user->getType();
      $album->owner_id = $user->getIdentity();
      $album->title = $title;
      //$album->type = $type;

      $album->search = 0;

      $album->save();
      
      // Authorizations
      $auth = Engine_Api::_()->authorization()->context;
      $auth->setAllowed($album, 'everyone', 'view',    true);
      $auth->setAllowed($album, 'everyone', 'comment', true);
        
    }

    return $album;
  }   
  // getSpecialAlbum
  
  
	/**
	 * @param string $type
	 * @return Radcodes_Lib_Rest_Store
	 */
	public function getRest($type)
	{
		if ($type == 'store')
		{
			$rest = new Radcodes_Lib_Rest_Store();
			return $rest;
		}
	}
	
	public function varPrint($var, $name=null)
	{
		echo "<pre>";
		if ($name) echo "$name ::\n";
		print_r($var);
		echo "</pre>";
	}
	
	
  public function varClass($var, $name=null)
  {
    echo "<pre>";
    if ($name) echo "$name ::\n";
    echo get_class($var);
    echo "</pre>";
  }
	
  
  public function varDump($var, $name=null)
  {
    echo "<pre>";
    if ($name) echo "$name ::\n";
    var_dump($var);
    echo "</pre>";
  }
  
  public function getPopularTags($resource_type, $options=array())
  {
    $tag_table = Engine_Api::_()->getDbtable('tags', 'core');
    $tagmap_table = $tag_table->getMapTable();
    
    $tName = $tag_table->info('name');
    $tmName = $tagmap_table->info('name');
    
    if (isset($options['order']))
    {
      $order = $options['order'];
    }
    else
    {
    	$order = 'text';
    }
    
    if (isset($options['sort']))
    {
    	$sort = $options['sort'];
    }
    else
    {
    	$sort = $order == 'total' ? SORT_DESC : SORT_ASC;
    }
    
    $limit = isset($options['limit']) ? $options['limit'] : 50;
    
    $select = $tag_table->select()
        ->setIntegrityCheck(false)
        ->from($tmName, array('total' => "COUNT(*)"))
        ->join($tName, "$tName.tag_id = $tmName.tag_id")
        ->where('resource_type = ?', $resource_type)
        ->where('tag_type = ?', 'core_tag')
        ->group("$tName.tag_id")
        ->order("total desc")
        ->limit("$limit");

    $tags = $tag_table->fetchAll($select);   
    
    $records = array();
    
    $columns = array();
    if (!empty($tags))
    {
	    foreach ($tags as $k => $tag)
	    {
	      $records[$k] = $tag;
	      $columns[$k] = $order == 'total' ? $tag->total : $tag->text; 
	    }
    }

    $tags = array();
    if (count($columns))
    {
      asort($columns, $sort);
	    foreach ($columns as $k => $name)
	    {
	      $tags[$k] = $records[$k];
	    }
    }

    return $tags; 
  }
}