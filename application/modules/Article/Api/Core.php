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
 
 
 
class Article_Api_Core extends Radcodes_Api_Abstract
{
  const IMAGE_WIDTH = 720;
  const IMAGE_HEIGHT = 720;

  const THUMB_WIDTH = 140;
  const THUMB_HEIGHT = 160;
  
  public function countArticles($params = array())
  {
    $paginator = $this->getArticlesPaginator($params);
    return $paginator->getTotalItemCount();  
  }  
  
  /**
   * Gets a paginator for approved - published articles
   *
   * @param Core_Model_Item_Abstract $user The user to get the messages for
   * @return Zend_Paginator
   */  
  public function getPublishedArticlesPaginator($params = array(), $options = null)
  {
  	$params['published'] = 1;
  	return $this->getArticlesPaginator($params, $options);
  }
  
  // Select
  /**
   * Gets a paginator for articles
   *
   * @param Core_Model_Item_Abstract $user The user to get the messages for
   * @return Zend_Paginator
   */
  public function getArticlesPaginator($params = array(), $options = null)
  {
    $paginator = Zend_Paginator::factory($this->getArticlesSelect($params, $options));
    if( !empty($params['page']) )
    {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if( !empty($params['limit']) )
    {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return $paginator;
  }

  /**
   * Gets a select object for the user's article entries
   *
   * @param Core_Model_Item_Abstract $user The user to get the messages for
   * @return Zend_Db_Table_Select
   */
  public function getArticlesSelect($params = array(), $options = null)
  {
    $table = $this->getArticleTable();
    
    $rName = $table->info('name');

    if (empty($params['order'])) {
      $params['order'] = 'recent';
    }    
    
    $select = $table->selectParamBuilder($params);
    
    // Process options
    $tmp = array();
    foreach( $params as $k => $v ) {
      if( is_object($v) || null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0) ) {
        continue;
      } else if( false !== strpos($k, '_field_') ) {
        list($null, $field) = explode('_field_', $k);
        $tmp['field_' . $field] = $v;
      } else if( false !== strpos($k, '_alias_') ) {
        list($null, $alias) = explode('_alias_', $k);
        $tmp[$alias] = $v;
      } else {
        $tmp[$k] = $v;
      }
    }
    $params = $tmp;     
    
    // Build search part of query
    $searchParts = Engine_Api::_()->fields()->getSearchQuery('article', $params);
    if (!empty($searchParts))
    {
      $searchTable = Engine_Api::_()->fields()->getTable('article', 'search')->info('name');
      
      $select = $select
        ->setIntegrityCheck(false)
        ->from($rName)
        ->join($searchTable, "$searchTable.item_id = $rName.article_id")
        ->group("$rName.article_id");     
      foreach( $searchParts as $k => $v ) 
      {
        $select = $select->where("`{$searchTable}`.{$k}", $v);
      }
    }      
    
    if( !empty($params['tag']) )
    {          
      $tagTable = Engine_Api::_()->getDbtable('TagMaps', 'core')->info('name');
      
      $select = $select
        ->setIntegrityCheck(false);
        
      if (!array_key_exists($rName, $select->getPart(Zend_Db_Select::FROM)))  {
        $select->from($rName);
      }
       // ->from($rName)
      $select->join($tagTable, "$tagTable.resource_id = $rName.article_id")
        ->where($tagTable.'.resource_type = ?', 'article')
        ->where($tagTable.'.tag_id  IN (?)', $params['tag']);
      if (is_array($params['tag'])) {
        $select->group("$rName.article_id");
      }
    }
    
    //echo $select->__toString();
    //exit;
    return $select;
  }  
  
  /***
   * @return Article_Model_DbTable_Articles
   */
  public function getArticleTable()
  {
    return Engine_Api::_()->getDbtable('articles', 'article');
  }
  


  public function getCategories()
  {
    $categories = Engine_Api::_()->getItemTable('article_category')->getCategories();
    return $categories;    
  }

  public function getCategory($category_id)
  {
    return Engine_Api::_()->getItemTable('article_category')->getCategory($category_id);
  }

  public function getUserCategories($user_id)
  {
    $table  = Engine_Api::_()->getDbtable('categories', 'article');
    $uName = Engine_Api::_()->getDbtable('articles', 'article')->info('name');
    $iName = $table->info('name');

    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($iName, array('category_name'))
      ->joinLeft($uName, "$uName.category_id = $iName.category_id")
      ->group("$iName.category_id")
      ->where($uName.'.owner_id = ?', $user_id);

    return $table->fetchAll($select);
  }

  public function convertCategoriesToArray($categories)
  {
    $categories_prepared = array();
    foreach ($categories as $category){
      $categories_prepared[$category->category_id]= $category->category_name;
    }
    return $categories_prepared;
  }
  
  function getArchiveList($params = array())
  {

    $table = Engine_Api::_()->getDbtable('articles', 'article');
    $rName = $table->info('name');

    $select = $table->select()
      ->from($rName, array("DATE_FORMAT(creation_date, '%Y-%m') as period", "COUNT(*) as total"))
      ->group('period')
      ->order("period DESC");

    if( !empty($params['user_id']) && is_numeric($params['user_id']) )
    {
      $select->where($rName.'.owner_id = ?', $params['user_id']);
    }
    if( isset($params['published']) && strlen($params['published']) )
    {
      $select->where($rName.'.published = ?', $params['published']);
    }
   
    $stmt = $select->query();
    $results = $stmt->fetchAll();
    
    return $results;
  }

  public function createPhoto($params, $file)
  {
    if( $file instanceof Storage_Model_File )
    {
      $params['file_id'] = $file->getIdentity();
    }

    else
    {
      // Get image info and resize
      $name = basename($file['tmp_name']);
      $path = dirname($file['tmp_name']);
      $extension = ltrim(strrchr($file['name'], '.'), '.');

      $mainName = $path.'/m_'.$name . '.' . $extension;
      $thumbName = $path.'/t_'.$name . '.' . $extension;

      $image = Engine_Image::factory();
      $image->open($file['tmp_name'])
          ->resize(self::IMAGE_WIDTH, self::IMAGE_HEIGHT)
          ->write($mainName)
          ->destroy();

      $image = Engine_Image::factory();
      $image->open($file['tmp_name'])
          ->resize(self::THUMB_WIDTH, self::THUMB_HEIGHT)
          ->write($thumbName)
          ->destroy();

      // Store photos
      $photo_params = array(
        'parent_id' => $params['article_id'],
        'parent_type' => 'article',
      );

      $photoFile = Engine_Api::_()->storage()->create($mainName, $photo_params);
      $thumbFile = Engine_Api::_()->storage()->create($thumbName, $photo_params);
      $photoFile->bridge($thumbFile, 'thumb.normal');

      $params['file_id'] = $photoFile->file_id;
      $params['photo_id'] = $photoFile->file_id;

      // Remove temp files
      @unlink($mainName);
      @unlink($thumbName);
      
    }

    $row = Engine_Api::_()->getDbtable('photos', 'article')->createRow();
    $row->setFromArray($params);
    $row->save();
    return $row;
  }
  
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
  
  public function getPopularTags($options = array())
  {
    $resource_type = 'article';
    
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
        ->where($tmName.'.resource_type = ?', $resource_type)
        ->where($tmName.'.tag_type = ?', 'core_tag')
        ->group("$tName.tag_id")
        ->order("total desc")
        ->limit("$limit");

    $params = array('published' => 1, 'search' => 1); 
    $article_table = $this->getArticleTable();
    $rName = $article_table->info('name');
    
    $select->setIntegrityCheck(false)
        ->join($rName, "$tmName.resource_id = $rName.article_id");
    $select = $article_table->selectParamBuilder($params, $select);    
    //echo $select;
    
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
      if ($order == 'text') {
        natcasesort($columns);
      }
      else {
        arsort($columns);
      }

      foreach ($columns as $k => $name)
      {
        $tags[$k] = $records[$k];
      }
    }

    return $tags;
  }  
  
  
  /**
		Can return NULL
   */
  public function getRelatedArticles($article, $params = array())
  {
    // related articles
    $tag_ids = array();
    foreach ($article->tags()->getTagMaps() as $tagMap) {
      $tag = $tagMap->getTag();
      if (!empty($tag->text)) {
        $tag_ids[] = $tag->tag_id;
      }
    }
    //print_r($tag_ids);
    
    if (empty($tag_ids)) {
      return null;
    }
    
    $values = array(
      'tag' => $tag_ids,
      'order' => 'random',
      'limit' => 5,
      'exclude_article_ids' => array($article->getIdentity())
    );

    $params = array_merge($values, $params);
    
    $paginator = Engine_Api::_()->article()->getPublishedArticlesPaginator($params);
    
    if ($paginator->getTotalItemCount() == 0) {
      return null;
    }
    
    return $paginator;
    /*
    //return;
    foreach ($this->view->relatedArticles as $article) {
      echo "<br>";
      echo $article->article_id;
      echo " - ".$article->getTitle();
    }
    */
  }

  public function getSpecialAlbum(User_Model_User $user, $type = 'article')
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
  
  public function getTopSubmitters($params = array())
  {
    $column = 'owner_id';
    
    $table = $this->getArticleTable();
    $rName = $table->info('name');
    
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), array(
      'user_id' => $column,
      'total' => new Zend_Db_Expr('COUNT(*)'),
    ));
    $select->group($column);

    $select->order('total desc');
    
    if (isset($params['limit'])) {
      $select->limit($params['limit']);
      unset($params['limit']);
    }
    
    $select = $table->selectParamBuilder($params, $select);
    //echo $select;
    $rows = $select->query()->fetchAll();
    
    $result = array();
    foreach ($rows as $row) {
      $result[$row['user_id']] = $row;
    }
    
    return $result;
  }  
  
}