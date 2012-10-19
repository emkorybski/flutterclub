<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    News
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Core.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    News
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class News_Api_Core extends Core_Api_Abstract {

    public function checkVersionSE() {
        $c_table = Engine_Api::_()->getDbTable('modules', 'core');
        $c_name = $c_table->info('name');
        $select = $c_table->select()
                        ->where("$c_name.name LIKE ?", 'core')->limit(1);

        $row = $c_table->fetchRow($select)->toArray();
        $strVersion = $row['version'];
        $intVersion = (int) str_replace('.', '', $strVersion);
        return $intVersion >= 410 ? true : false;
    }

    public function getAllCategories($params = array()) {
        $table = Engine_Api::_()->getDbtable('categories', 'news');
        $select = $table->select();
        //content id
        if (isset($params['category_id'])) {
            $select->where('category_id = ?', $params['category_id']);
        }
        if (isset($params['category_active']))
            $select->where('is_active = ?', $params['category_active']);

        if (isset($params['category_parent']) && $params['category_parent'] != -1)
            $select->where('category_parent_id = ?', $params['category_parent']);

        $row = $table->fetchAll($select);
        return $row->toArray();
    }

    public function getAllCategoryparents($params = array()) {
        $table = Engine_Api::_()->getDbtable('categoryparents', 'news');
        $select = $table->select();
        //content id
        if (isset($params['category_id'])) {
            $select->where('category_id = ?', $params['category_id']);
        }
        if (isset($params['category_active']))
            $select->where('is_active = ?', $params['category_active']);
        //if (isset)
        $row = $table->fetchAll($select);
        return $row->toArray();
    }

    public function getAllUsers($params = array()) {
        $table = Engine_Api::_()->getDbtable('nusers', 'news');
        $select = $table->select();
        $row = $table->fetchAll($select);
        return $row->toArray();
    }

    public function getCategoriesById($where) {
        $table = Engine_Api::_()->getDbtable('categories', 'news');
        $select = $table->select();
        //content id
        if (!empty($where)) {
            $select->where('category_id IN ' . $where);
        }
        $select->where('is_active = ?', 1);
        $row = $table->fetchAll($select);
        return $row->toArray();
    }

    public function getAllCategoriesSelect() {
        $table = Engine_Api::_()->getDbtable('categories', 'news');
        $select = $table->select();

        $row = $table->fetchAll($select);
        $row = $row->toArray();
        $categories = array();
        $cat = "";
        $categories[0] = "All Categories";
        foreach ($row as $item) {
            $categories[$item['category_id']] = $item['category_name'];
        }
        return $categories;
    }

    public function getAdminAccount($level = 1) {
        $table = Engine_Api::_()->getItemTable('users');
        $select = $table->select();

        $select->where('level_id = ?', $level);
        $select->limit(' 1');


        $row = $table->fetchAll($select);
        return $row->toArray();
    }

    public function getTimeframe() {
        $table = Engine_Api::_()->getDbtable('timeframe', 'news');
        $select = $table->select();

        $select->limit(' 1');


        $row = $table->fetchAll($select);
        return $row->toArray();
    }

    public function getAllContent($params = array()) {
        $table = Engine_Api::_()->getDbtable('contents', 'news');
        $select = $table->select();

        //content id
        if (isset($params['content_id'])) {
            $select->where('content_id = ?', $params['content_id']);
        }

        //news_item_id
        if (isset($params['news_item_id'])) {
            $select->where('news_item_id = ?', $params['news_item_id']);
        }

        // Category
        if (isset($params['category_id']) && $params['category_id'] > 0) {
            $select->where('category_id = ?', $params['category_id']);
        }

        //link detail
        if (isset($params['link_detail'])) {
            $select->where('link_detail = ?', $params['link_detail']);
        }
        //link title
        if (isset($params['title'])) {
            $select->where('title = ?', $params['title']);
        }
        // Limit
        if (isset($params['limit']) && $params['limit'] > 0) {
            $select->limit($params['limit']);
        }

        $select->order(' category_id DESC ');

        $row = $table->fetchAll($select);
        return $row->toArray();
    }

    public function getContentsSelectWithCat($params= array()) {
        
    }

    public function getContentsSelect($params = array()) {
        $table = Engine_Api::_()->getDbTable('contents', 'news');

        $select = $table->select()->from('engine4_news_contents')->setIntegrityCheck(false);
        $select->joinLeft("engine4_news_categories", "engine4_news_categories.category_id= engine4_news_contents.category_id", array('logo' => 'engine4_news_categories.category_logo', 'logo_icon' => 'engine4_news_categories.logo', 'display_logo' => 'engine4_news_categories.display_logo', 'mini_logo' => 'engine4_news_categories.mini_logo'));
        if (isset($params['checkcomment'])) {
            $select->where("engine4_news_contents.content_id IN (SELECT resource_id FROM engine4_core_comments WHERE engine4_core_comments.resource_type='news_content' AND engine4_core_comments.resource_id = engine4_news_contents.content_id)");
        }

        if (isset($params['getcommment'])) {

            $select->joinLeft("engine4_core_comments", "engine4_core_comments.resource_id= engine4_news_contents.content_id AND engine4_core_comments.resource_type = \"news_content\" ", array('total_comment' => "count('engine4_news_contents.content_id')", 'resource_id' => 'engine4_core_comments.resource_id'));
            //$select->where('engine4_core_comments.resource_id <> null');
            $select->group('engine4_news_contents.content_id');
        }

        //content id
        if (isset($params['content_id'])) {
            $select->where('engine4_news_contents.content_id = ?', $params['content_id']);
        }

        //link
        //content id
        if (isset($params['link'])) {
            $select->where('engine4_news_contents.link = ?', $params['link']);
        }

        // is active
        if (isset($params['is_active'])) {
            $select->where('engine4_news_categories.is_active = ?', $params['is_active']);
        }
        $timezone_server_H = date('H');
        $timezone_server_i = date('i');
        $timezone_server_s = date('s');
        $timezone_server = $timezone_server_H * 3600 + $timezone_server_i * 60 + $timezone_server_s;
//  echo $timezone_server."_";
//  echo $timezone_server_H."_";
//  echo $timezone_server_i."_";
//  echo $timezone_server_s."_";

        $oldTz = date_default_timezone_get();
        $viewer = Engine_Api::_()->user()->getViewer();
        $timezone_viewer = $timezone_server;
        if ($viewer->getIdentity() > 0) {
            date_default_timezone_set($viewer->timezone);
            $timezone_viewer_H = date('H');
            $timezone_viewer_i = date('i');
            $timezone_viewer_s = date('s');
            $timezone_viewer = $timezone_viewer_H * 3600 + $timezone_viewer_i * 60 + $timezone_viewer_s;
//  echo $timezone_viewer."_";
//  echo $timezone_viewer_H."_";
//  echo $timezone_viewer_i."_";
//  echo $timezone_viewer_s."_";
        }
        $subtimezone = ($timezone_server - $timezone_viewer);
//echo $subtimezone;
        date_default_timezone_set($oldTz);
//echo ($timezone_server."_".$timezone_viewer.'_'.$subtimezone);
        $subtimezone = 0;
        if (isset($params['start_date']) && $params['start_date'] != '') {
            $start_date_ = $params['start_date'];
            $start_date = strtotime($start_date_) + $subtimezone;
            if ($start_date != $subtimezone)
                $select->where("engine4_news_contents.pubDate >= $start_date ");
        }
        if (isset($params['end_date']) && $params['end_date'] != '') {
            $end_date_ = $params['end_date'];
            $end_date = strtotime($end_date_) + $subtimezone;

            if ($end_date != $subtimezone)
                $select->where("engine4_news_contents.pubDate <= $end_date ");
        }

        // Category
        if (isset($params['category_id']) && $params['category_id'] != 0) {
            $select->where('engine4_news_contents.category_id = ?', $params['category_id']);
        }

        // title
        if (!empty($params['search'])) {
            $select->where('engine4_news_contents.title LIKE ? OR description LIKE ?', $params['search'], $params['search']);
        }
        //groupby
        //check image
        if (isset($params['image']) && $params['image'] == 'yes') {
            $select->where('engine4_news_contents.image <> ""');
        }
        // $select->where("engine4_news_contents.category_id IN (SELECT resource_id FROM engine4_core_comments WHERE engine4_core_comments.resource_type='news_content' AND engine4_core_comments.resource_id = engine4_news_contents.content_id)");
        // Order
        if (isset($params['order_feature']) && $params['order_feature'] != "")
            $select->order('is_featured DESC');
        if (!empty($params['order'])) {
            $select->order($params['order']);
        }
        if (isset($params['order2']) && $params['order2'] != "")
            $select->order($params['order2']);
        else
            $select->order('content_id DESC');
        // Limit
        if (isset($params['limit']) && $params['limit'] > 0) {
            $select->limit($params['limit']);
        }

        return $select;
    }

    public function getContentsPaginator($params = array()) {
        return Zend_Paginator::factory($this->getContentsSelect($params));
    }

    public function getCategoriesSelect($params = array()) {
        $table = Engine_Api::_()->getItemTable('Categories');
        $select = $table->select();
        // Category
        if (isset($params['category_name'])) {
            $select->where('category LIKE ', $params['category']);
        }
        // Category
        if (!empty($params['category_id'])) {
            $select->where('category_id = ?', $params['category_id']);
        }
        // Order
        if (!empty($params['order'])) {
            $select->order($params['order']);
        }

        return $select;
    }

    public function getCategoryparentsSelect($params = array()) {
        $table = Engine_Api::_()->getItemTable('Categoryparents');
        $select = $table->select();
        // Category
        if (isset($params['category_name'])) {
            $select->where('category_name LIKE ', $params['category_name']);
        }
        // Category
        if (!empty($params['category_id'])) {
            $select->where('category_id = ?', $params['category_id']);
        }
        // Order
        if (!empty($params['order'])) {
            $select->order($params['order']);
        }

        return $select;
    }

    public function getUsersSelect($params = array()) {
        $table = Engine_Api::_()->getDbTable('nusers', 'news');
        $select = $table->select()->from('engine4_news_nusers')->setIntegrityCheck(false);
        $select->joinLeft("engine4_users", "engine4_users.username= engine4_news_nusers.username", array('userid' => 'engine4_users.user_id', 'displayname' => 'engine4_users.displayname', 'email' => 'engine4_users.email'));

        if (isset($params['username'])) {
            $select->where('username LIKE ', $params['username']);
        }
        // Order
        if (!empty($params['order'])) {
            $select->order($params['order']);
        }

        return $select;
    }

    public function getCategoriesPaginator($params = array()) {
        return Zend_Paginator::factory($this->getCategoriesSelect($params));
    }

    public function getCategoryparentsPaginator($params = array()) {
        return Zend_Paginator::factory($this->getCategoryparentsSelect($params));
    }

    public function getUsersPaginator($params = array()) {
        return Zend_Paginator::factory($this->getUsersSelect($params));
    }

    public function checkcategoriesparentinactive($category_id) {

        //Should not allow user active a feed when category of this feed is inactive
        $table = Engine_Api::_()->getDbtable('Categoryparents', 'News');

        $select = $table->select('engine4_news_categoryparents ')->setIntegrityCheck(false)
                ->where('engine4_news_categoryparents.category_id = ? ', $category_id)
                ->where('engine4_news_categoryparents.is_active = ? ', 0)
                ->limit(1);
        $items = $table->fetchAll($select);
        if (count($items) > 0)
            return true;
        return false;
    }

}