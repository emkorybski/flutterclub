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

class Article_Model_DbTable_Articles extends Engine_Db_Table
{
  protected $_rowClass = "Article_Model_Article";

  
  public function selectParamBuilder($params = array(), $select = null)
  {
    $rName = $this->info('name');
    
    if ($select === null)
    {
      $select = $this->select();
    }
    
    if (isset($params['live']) && $params['live'])
    {
      $params['published'] = 1;
      unset($params['live']);
    }
    
    if (isset($params['user_id'])) {
    	$params['user'] = $params['user_id'];
    }
    
    if (isset($params['user']) && $params['user']) 
    {
      $user = Engine_Api::_()->user()->getUser($params['user']);
      $select->where($rName.'.owner_id = ?', $user->getIdentity());
    }
    
    if( !empty($params['users']) )
    {
      //$str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
      //$select->where($rName.'.owner_id in (?)', new Zend_Db_Expr($str));
      $select->where($rName.'.owner_id in (?)', $params['users']);
    }
    
    if (isset($params['category']) && $params['category'])
    {
      $category_id = ($params['category'] instanceof Core_Model_Item_Abstract) ? $params['category']->getIdentity() : (int) $params['category'];
      
      $category_ids = array($category_id);
      $categories = Engine_Api::_()->getItemTable('article_category')->getChildrenOfParent($category_id);
      foreach ($categories as $category) {
        $category_ids[] = $category->getIdentity();
      }

      $select->where($rName.'.category_id IN (?)', $category_ids);
    }
    
    foreach (array('featured', 'sponsored', 'search', 'published') as $field)
    {
      if (isset($params[$field]))
      {
        $select->where($rName.".$field = ?", $params[$field] ? 1 : 0);
      }  
    }

    if( !empty($params['keyword']) )
    {
      $select->where($rName.".title LIKE ? OR ".$rName.".description LIKE ? OR ".$rName.".body LIKE ?", '%'.$params['keyword'].'%');
    }   
    
    if( !empty($params['start_date']) )
    {
      $select->where($rName.".creation_date >= ?", date('Y-m-d', $params['start_date']));
    }

    if( !empty($params['end_date']) )
    {
      $select->where($rName.".creation_date <= ?", date('Y-m-d', $params['end_date']));
    }
    
    if (isset($params['exclude_article_ids']) and !empty($params['exclude_article_ids']))
    {
      $select->where($rName.".article_id NOT IN (?)", $params['exclude_article_ids']);
    }    
    
    if( !empty($params['period']))
    {
      $period_maps = array(
        '24hrs' => 1,
        'week' => 7,
        'month' => 30,
        'quarter' => 90,
        'year' => 365,
      
        7 => 7,
        30 => 30,
        90 => 90,
        180 => 180,
        365 => 365,
      
      );
      if (isset($period_maps[$params['period']]) && $period_maps[$params['period']])
      {
        $select->where($rName.".creation_date >= ?", date('Y-m-d', time() - $period_maps[$params['period']] * 86400));
      }
    }   

    if (isset($params['order'])) 
    {
      switch ($params['order'])
      {
        case 'random':
          $order_expr = new Zend_Db_Expr('RAND()');
          break;
        case 'recent':
          $order_expr = $rName.".creation_date DESC";
          break;
        case 'lastupdated':
          $order_expr = $rName.".modified_date DESC";
          break;
        case 'mostcommented':
          $order_expr = $rName.".comment_count DESC";
          break;
        case 'mostliked':
          $order_expr = $rName.".like_count DESC";
          break;  
        case 'mostviewed':
          $order_expr = $rName.".view_count DESC";
          break;        
        case 'alphabet':
          $order_expr = $rName.".title ASC";
          break;

        default:
          $order_expr = !empty($params['order']) ? $params['order'] : $rName.'.creation_date DESC';
          
          if (!empty($params['order_direction'])) {
            $order_expr .= " " .$params['order_direction'];
          }
          
          if (!is_array($order_expr) && !($order_expr instanceof Zend_Db_Expr) and strpos($order_expr, '.') === false) {
            $order_expr = $rName.".".trim($order_expr);
          }
          break;
      }

      if (isset($params['preorder']) && $params['preorder'])
      {
        $pre_orders = array(
          1 => array("{$rName}.sponsored DESC"), // Sponsored listings, then user preference",
          2 => array("{$rName}.sponsored DESC","{$rName}.featured DESC"), // "Sponsored listings, featured listings, then user preference",
          3 => array("{$rName}.featured DESC"), // "Featured listings, then user preference",
          4 => array("{$rName}.featured DESC","{$rName}.sponsored DESC"), // "Featured listings, sponsored listings, then user preference",
        );
        if (array_key_exists($params['preorder'], $pre_orders))
        {
          $order_expr = array_merge($pre_orders[$params['preorder']], array($order_expr));
        }
      }

      $select->order( $order_expr );
      unset($params['order']);
    }
    //echo $select;
    return $select;
  }
  
}

