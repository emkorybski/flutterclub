<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    News
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AdminManageController.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    News
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
require_once('YnsRSSFeed/YnsRSS.php');

class News_AdminManageController extends Core_Controller_Action_Admin {

    public function indexAction() {
        $_SESSION['result'] = null;
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('news_admin_main', array(), 'news_admin_main_manage');

        $page = $this->_getParam('page', 1);
        $this->view->paginator = Engine_Api::_()->news()->getContentsPaginator(array(
                    'orderby' => 'content_id', 'order_feature' => 'manage',
                ));
        $this->view->paginator->setItemCountPerPage(25);
        $this->view->paginator->setCurrentPageNumber($page);

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            try {
                foreach ($values as $key => $value) {
                    if ($key == 'delete_' . $value) {
                        $content = Engine_Api::_()->getItem('contents', $value);
                        $content->delete();
                    }
                }
            } catch (Exception $ex) {
                $_SESSION['result'] = 0;
            }
            $_SESSION['result'] = 1;
        }
    }

    public function categoryAction() {
        $_SESSION['result'] = null;
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('news_admin_main', array(), 'news_admin_main_category');

        $page = $this->_getParam('page', 1);
        $this->view->paginator = Engine_Api::_()->news()->getCategoriesPaginator(array(
                    'orderby' => 'category_id',
                ));
        $this->view->paginator->setItemCountPerPage(25);
        $this->view->paginator->setCurrentPageNumber($page);

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            try {
                foreach ($values as $key => $value) {
                    if ($key == 'delete_' . $value) {
                        $category = Engine_Api::_()->getItem('categories', $value);
                        $category->delete();
                        $this->deleteItemData($value);
                    }
                }
            } catch (Exception $e) {
                $this->view->result = 1;
                $_SESSION['result'] = 0;
            }
            if (!isset($_SESSION['result']))
                $_SESSION['result'] = 1;
        }
    }

    public function categoriesAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('news_admin_main', array(), 'news_admin_main_categories');
        $this->view->form = $form = new News_Form_Admin_Manage_Createcategory();
        $page = $this->_getParam('page', 1);
        $this->view->paginator = Engine_Api::_()->news()->getCategoryparentsPaginator(array(
                    'orderby' => 'category_id',
                ));
        $this->view->paginator->setItemCountPerPage(25);
        $this->view->paginator->setCurrentPageNumber($page);
        if (isset($_POST['category_name'])) {
            $values = $_POST;
            if (trim($values['category_name'] == "")) {
                $form->addError("Please insert category name - it is required.");
                return;
            }
            $isValid = $this->isValidDataCategory($values);
            if ($isValid == false) {
                $form->addError("The Category name already exists .");
                return;
            }
            $db = Engine_Api::_()->getDbtable('categoryparents', 'news')->getAdapter();
            $db->beginTransaction();

            try {
                // Create event
                $table = $this->_helper->api()->getDbtable('categoryparents', 'news');
                $event = $table->createRow();

                $event->setFromArray($values);
                $event->save();
                $form->addNotice("Add new Category successfully.");
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
        if (isset($_POST['buttondelete'])) {
            $values = $_POST;
            try {
                foreach ($values as $key => $value) {
                    if ($key == 'delete_' . $value) {
                        $category = Engine_Api::_()->getItem('categoryparents', $value);
                        if ($category)
                            $category->delete();

                        $this->deleteItemData($value);
                    }
                }
            } catch (Exception $e) {
                $this->view->result = 1;
                $_SESSION['result'] = 0;
            }
            if (!isset($_SESSION['result']))
                $_SESSION['result'] = 1;
            if (isset($_SESSION['result']))
                $this->view->result = $_SESSION['result'];
        }
        $this->view->paginator = Engine_Api::_()->news()->getCategoryparentsPaginator(array(
                    'orderby' => 'category_id',
                ));
        $this->view->paginator->setItemCountPerPage(25);
        $this->view->paginator->setCurrentPageNumber($page);
    }

    public function usersAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('news_admin_main', array(), 'news_admin_main_users');
        $this->view->form = $form = new News_Form_Admin_Manage_Createuser();
        $page = $this->_getParam('page', 1);
        $this->view->paginator = Engine_Api::_()->news()->getUsersPaginator(array(
                    'orderby' => 'user_id',
                ));
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
        if (isset($_POST['username'])) {
            $values = $_POST;

            if (trim($values['username'] == "")) {
                $form->addError("Please insert username - it is required.");
                return;
            }
            $isValid = $this->isValidDataUser($values);
            if ($isValid == 'in_list') {
                $form->addError("This username is already in the list .");
                return;
            }
            if ($isValid == 'not_a_user') {
                $form->addError("This username does not exist .");
                return;
            }
            $db = Engine_Api::_()->getDbtable('nusers', 'news')->getAdapter();
            $db->beginTransaction();


            try {
                // Create event
                $table = $this->_helper->api()->getDbtable('nusers', 'news');
                $event = $table->createRow();

                $event->setFromArray($values);
                $event->save();
                $form->addError("Added new User successfully.");
                //$this->view->message = 'Added new User successfully.';
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }

        if (isset($_POST['buttondelete'])) {
            $values = $_POST;
            try {
                foreach ($values as $key => $value) {
                    if ($key == 'delete_' . $value) {
                        $user = Engine_Api::_()->getItem('news_nusers', $value);
                        $user->delete();
                    }
                }
            } catch (Exception $e) {
                $this->view->result = 1;
                $_SESSION['result'] = 0;
            }
            if (!isset($_SESSION['result']))
                $_SESSION['result'] = 1;
            if (isset($_SESSION['result']))
                $this->view->result = $_SESSION['result'];
        }
        $this->view->paginator = Engine_Api::_()->news()->getUsersPaginator(array(
                    'orderby' => 'user_id',
                ));
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
    }

    function deleteItemData($category_id) {
        $table = Engine_Api::_()->getDbtable('Contents', 'News');
        $selectTop = $table->select()
                ->where('category_id = ? ', $category_id);


        $contents = $table->fetchAll($selectTop);
        // echo count($contents);die();
        foreach ($contents as $content) {
            $content->delete();
        }
    }

    function isValidDataCategory($data = array()) {
        $categories = Engine_Api::_()->news()->getAllCategoryparents();

        foreach ($categories as $category) {
            if (($category['category_name'] == trim($data['category_name']))) {

                return false;
            }
        }

        return true;
    }

    function isValidDataUser($data = array()) {
        $users = Engine_Api::_()->news()->getAllUsers();

        $users_core = Engine_Api::_()->getItemTable('user');
        $users_core = $users_core->fetchAll()->toArray();
//       var_dump($users_core);
//       die();

        $isUser = 0;
        foreach ($users_core as $user) {
            if ($user['username'] == trim($data['username'])) {
                $isUser = 1;
                break;
            }
        }

        if ($isUser == 0) {

            return 'not_a_user';
        }

        foreach ($users as $user) {
            if (($user['username'] == trim($data['username']))) {

                return 'in_list';
            }
        }

        return false;
    }

    function isValidData($data = array()) {
        $categories = Engine_Api::_()->news()->getAllCategories();

        //$content = Engine_Api::_()->news()->getAllContent(array('link_detail' => $edata['link_detail'],));
        foreach ($categories as $category) {
            if (($category['category_name'] == trim($data['category_name'])) || ($category['url_resource'] == trim($data['url_resource']))) {

                return false;
            }
        }

        return true;
    }

    public function createAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('news_admin_main', array(), 'news_admin_main_create');

        $this->view->form = $form = new News_Form_Admin_Manage_Create();

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Process
        $values = $form->getValues();

        $isValid = $this->isValidData($values);
        if ($isValid == false) {
            $form->addError("The Feed name or the Feed URL already exists .");
            //$this->view->form = $form;
            return;
        }
        $rss = new YnsRSS();
        $feed = $rss->getParse(null, $values['url_resource']);
        if (!empty($values['logo'])) {

            $values['category_logo'] = $this->uploadPhoto($form->logo); //die('s');
            if ($values['category_logo'] == NULL) {
                $form->addError("Invalid file type for Feed provider logo");
                return;
            }
        } elseif ($values['category_logo'] == "") {



            if (isset($feed['image_logo'])) {
                $values['category_logo'] = $feed['image_logo'];
            }
        }
        if (isset($feed['logo_ico'])) {

            $url = $this->saveImg($feed['logo_ico'], $category_id);
            $values['logo'] = $url;
        }
        $values['posted_date'] = date('Y-m-d H:i:s');
        if ($values['category_parent_id'] > 0) {
            $categoryparent = Engine_Api::_()->news()->getAllCategoryparents(array(
                        'category_id' => $values['category_parent_id'],
                    ));
            if ($categoryparent[0]['is_active'] <= 0) {
                $values['is_active'] = '0';
            } else {
                $values['is_active'] = $categoryparent[0]['is_active'];
            }
        }
        $db = Engine_Api::_()->getDbtable('categories', 'news')->getAdapter();
        $db->beginTransaction();

        try {
            // Create event
            $table = $this->_helper->api()->getDbtable('categories', 'news');
            $event = $table->createRow();
            $event->setFromArray($values);
            $event->save();

            foreach ($form->getElements() as $name => $element) {
                $element->setValue("");
            }
            $form->addNotice("Add new RSS Feed successfully.");
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    private function uploadPhoto($photo) {
        $imglist = array('gif', 'png');
        //echo $photo; die();
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
        } else {
            //throw new Exception('Invalid argument passed to setPhoto: '.print_r($photo,true));
            echo 'Invalid argument passed to setPhoto' . print_r($photo, true);
            return false;
        }
        //  print_r($_FILES['logo']); die();
        $info = pathinfo($file);
        //echo  $info['extension'];die();
        if (!in_array(strtolower($info['extension']), $imglist))
            return NULL;
        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        //$admin = Engine_Api::_()->news()->getAdminAccount();
        $params = array(
            'parent_type' => 'category_logo',
            'parent_id' => Engine_Api::_()->user()->getViewer()->getIdentity()
        );

        // Save
        $storage = Engine_Api::_()->storage();

        // Resize image (main)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(720, 720)
                ->write($path . '/m_' . $name, $info['extension'])
                ->destroy();

        // Resize image (profile)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(200, 400)
                ->write($path . '/p_' . $name, $info['extension'])
                ->destroy();

        // Resize image (normal)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(100, 100)
                ->write($path . '/in_' . $name, $info['extension'])
                ->destroy();

        // Resize image (icon)
        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 65, 65)
                ->write($path . '/is_' . $name, $info['extension'])
                ->destroy();

        // Store
        $iMain = $storage->create($path . '/m_' . $name, $params);
        $iProfile = $storage->create($path . '/p_' . $name, $params);
        $iIconNormal = $storage->create($path . '/in_' . $name, $params);
        $iSquare = $storage->create($path . '/is_' . $name, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        $iMain->bridge($iSquare, 'thumb.icon');

        // Update row
        return $iMain->storage_path;
    }

    public function saveImg($data, $name) {
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        //$admin = Engine_Api::_()->news()->getAdminAccount();
        $params = array(
            'parent_type' => 'category_logo',
            'parent_id' => Engine_Api::_()->user()->getViewer()->getIdentity()
        );

        // Save
        $storage = Engine_Api::_()->storage();

        // $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public';
        $filename = $path . DIRECTORY_SEPARATOR . $name . '.png';
        $f = fopen($filename, 'w+');
        fwrite($f, $data);
        fclose($f);
        $iMain = $storage->create($path . '/' . $name . '.png', $params);
        return $iMain->storage_path;
        // die();
    }

    public function editAction() {

        $category_id = $this->_getParam('id', null);
        $this->view->form = $form = new News_Form_Admin_Manage_Create();

        $category = Engine_Api::_()->news()->getAllCategories(array(
                    'category_id' => $category_id,
                ));

        // Posting form
        if ($this->getRequest()->isPost()) {

            if ($form->isValid($this->getRequest()->getPost())) {

                $data_array = $form->getValues();
                $rss = new YnsRSS();
                $feed = $rss->getParse(null, $data_array['url_resource']);
                //echo "<pre>".print_r($data_array,true)."</pre>";die();
                if (!empty($data_array['logo'])) {

                    $data_array['category_logo'] = $this->uploadPhoto($form->logo); //die('s');
                    if ($data_array['category_logo'] == NULL) {
                        $form->addError("Invalid file type for Feed provider logo");
                        return;
                    }
                } elseif ($data_array['category_logo'] == "") {

                    if (isset($feed['image_logo'])) {
                        $data_array['category_logo'] = $feed['image_logo'];
                    }
                }
                //echo $feed['logo_ico'].$feed['logo_ico_url'];die();
                if (isset($feed['logo_ico']) && $feed['logo_ico'] != "") {

                    $url = $this->saveImg($feed['logo_ico'], $category_id);
                    $data_array['logo'] = $url;
                }
                if ($data_array['category_parent_id'] != 0) {
                    $tb = Engine_Api::_()->getDbTable('categoryparents', 'news');
                    $select = $tb->select()
                            ->where("category_id = ?", $data_array['category_parent_id'])
                            ->where("is_active =?", 0);
                    $inactive = $tb->fetchAll($select);
                    if (count($inactive) > 0) {
                        $data_array['is_active'] = 0;
                    }
                }
                //echo $data_array['is_active']; die;
                $table = Engine_Api::_()->getDbTable('categories', 'news');
                $where = $table->getAdapter()->quoteInto('category_id = ?', $category_id);
                $table->update($data_array, $where);
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'format' => 'smoothbox',
                'messages' => array('Feed Edited.')
            ));
        }

        // Initialize data
        else {
            foreach ($form->getElements() as $name => $element) {
                if (isset($category[0][$name])) {
                    $element->setValue($category[0][$name]);
                }
            }
        }
    }

    public function editcategoryAction() {

        $category_id = $this->_getParam('id', null);
        $this->view->form = $form = new News_Form_Admin_Manage_Createcategory();

        $category = Engine_Api::_()->news()->getAllCategoryparents(array(
                    'category_id' => $category_id,
                ));

        // Posting form
        if (isset($_POST['category_name'])) {
            $data_array = $_POST;
            if (trim($data_array['category_name'] == "")) {
                $form->addError("Please insert category name - it is required.");
                return;
            }
            $table = Engine_Api::_()->getDbTable('categoryparents', 'news');
            if ($data_array['is_active'] == "")
                $data_array['is_active'] = 0;
            $where = $table->getAdapter()->quoteInto('category_id = ?', $category_id);
            $table->update(array('category_name' => $data_array['category_name'], 'category_description' => $data_array['category_description'], 'is_active' => $data_array['is_active']), $where);

            if (is_numeric($category_id)) {
                $category = Engine_Api::_()->getDbTable('categories', 'news');
                $content = Engine_Api::_()->getDbTable('contents', 'news');

                $where_category = $category->getAdapter()->quoteInto('category_parent_id = ?', $category_id);
                $category->update(array('is_active' => $data_array['is_active']), $where_category);

                $categories = Engine_Api::_()->news()->getAllCategories(array(
                            'category_parent' => $category_id,
                        ));
                foreach ($categories as $categoryitem) {
                    $where_content = $content->getAdapter()->quoteInto('category_id = ?', $categoryitem['category_id']);
                    $content->update(array('is_active' => $data_array['is_active']), $where_content);
                }
            }
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'format' => 'smoothbox',
                'messages' => array('Category Edited.')
            ));
        } else {
            foreach ($form->getElements() as $name => $element) {
                if (isset($category[0][$name])) {
                    $element->setValue($category[0][$name]);
                }
            }
        }
    }

    private function getAtomFeed($url, $cateogry_id, $owner_id) {
        $url_arr = parse_url($url);
        if ($url_arr['scheme'] != 'http') {
            $url = str_replace($url_arr['scheme'] . '://', 'http://', $url);
        }
        $feed = Zend_Feed::import($url);
        $data = array(
            'title' => $feed->title(),
            'link' => $feed->id(),
            'dateModified' => $feed->updated(),
            'description' => $feed->subtitle(),
            'author' => $feed->author(),
            'entries' => array(),
        );
        $count_feed = count($feed);
        foreach ($feed as $entry) {
            if ($entry->updated() == null) {
                $time = strtotime($feed->updated()) + $count_feed;
                $count_feed--;
                $pub = "";
            } else {

                $time = strtotime($entry->updated());
                $pub = $entry->updated();
            }
            $is_active = 1;
            $category = Engine_Api::_()->news()->getAllCategories(array(
                        'category_id' => $cateogry_id,
                    ));
            if ($category[0]['is_active'] == 0) {
                $is_active = 0;
            }
            if ($data['author'] == "") {
                $url_arr = parse_url($data['link']);

                $host = @$url_arr['host'];

                if (strpos($host, 'http') || $host == "") {
                    $url = str_replace('http://', '', $feed['link']);

                    $index = strpos($url, '/');

                    $host = substr($url, 0, $index);
                }
                $data['author'] = $host;
            }
            $edata = array(
                'category_id' => $cateogry_id,
                'owner_type' => "user",
                'owner_id' => $owner_id,
                'title' => $entry->title(),
                'description' => $entry->summary(),
                'content' => $entry->content(),
                'image' => "",
                'link_detail' => $entry->id(),
                'author' => $data['author'],
                'pubDate' => $time,
                'pubDate_parse' => $pub,
                'posted_date' => date('Y-m-d H:i:s'),
                'is_active' => $is_active
            );

            //insert data to database
            $db = Engine_Api::_()->getDbtable('contents', 'news')->getAdapter();
            $db->beginTransaction();

            try {
                //check news exist by link
                $content = Engine_Api::_()->news()->getAllContent(array('link_detail' => $edata['link_detail'],));
                if (count($content) > 0) {
                    $table = Engine_Api::_()->getDbTable('contents', 'news');
                    $where = $table->getAdapter()->quoteInto('content_id = ?', $content[0]['content_id']);
                    $table->update($edata, $where);
                } else {
                    // Create content
                    $table = $this->_helper->api()->getDbtable('contents', 'news');
                    $content = $table->createRow();
                    $content->setFromArray($edata);
                    $content->save();
                }
                $edata = null;
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }

    function remote_filesize($url) {
        //echo $url;
        $size = getimagesize($url);
        return $size;
        // print_r($size);
        /* die();
          $ch = curl_init($url);
          curl_setopt($ch, CURLOPT_NOBODY, false);

          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_HEADER, array('USER_AGENT'=>'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12'));
          $data = curl_exec($ch);
          $infor = curl_getinfo($ch);
          var_dump($infor);
          var_dump($data);

          curl_close($ch);
          if ($data === false)
          return false;
          if (preg_match('/Content-Length: (\d+)/', $data, $matches)){
          print_r($data);
          die();
          return (float)$matches[1];
          }

          die('1'); */
    }

    private function parseDescription(&$description) {
        preg_match_all('/<img[^>]+>/i', $description, $result);
        $img = array();
        if (isset($result[0])) {
            foreach ($result[0] as $img_tag) {
                preg_match_all('/(alt|title|src)=("[^"]*")/i', $img_tag, $img[$img_tag]);
                if (isset($img[$img_tag][2][0])) {
                    list($width, $height) = getimagesize(str_replace('"', '', $img[$img_tag][2][0]));
                    if ($width >= 40 && $height >= 40) {
                        $description = str_replace($img_tag, "", $description);
                        return str_replace('"', '', $img[$img_tag][2][0]);
                    }
                };
            }
        }
    }

    private function catch_that_image(&$des) {
        $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $des, $matches);
        $first_img = @$matches [1] [0];

        if (empty($first_img)) { //Defines a default image
            return '';
        }
        return $first_img;
    }

    public function getdataAction() {

        $rss = new YnsRSS();
        try {

            $categories = $this->_getParam('cat');

            $doc = new DOMDocument();
            //get categories to read data
            if ($categories != "") {
                $categories = str_replace(array("(", ")", "on"), "", $categories);
                $category_arr = array_filter(explode(",", $categories));
                $where = "(" . implode(",", $category_arr) . ")";
                $categories = Engine_Api::_()->news()->getCategoriesById($where);
            }
            /* else
              {
              $categories = Engine_Api::_()->news()->getAllCategories();
              }
             */
            if (count($categories) > 0) {
                //get admin account
                //$admin = Engine_Api::_()->news()->getAdminAccount();

                foreach ($categories as $category) {
                    //get data from remote server
                    try {

                        //$feed = new Zend_Feed_Rss($category['url_resource']);

                        $feed = $rss->getParse(null, $category['url_resource'], null);

                        //die();
                        //$feed = $rss->getParse(null,'localhost/portada.xml',null);
                        //print_r($feed);die();
                        $data = array(
                            'title' => $feed['title'],
                            'link' => $feed['link'],
                            'dateModified' => $feed['dateModified'],
                            'description' => $feed['description'],
                            'author' => $feed['author'],
                            'entries' => array(),
                        );
                        if ($data['author'] == "") {
                            $url_arr = parse_url($data['link']);

                            $host = @$url_arr['host'];

                            if (strpos($host, 'http') || $host == "") {
                                $url = str_replace('http://', '', $feed['link']);

                                $index = strpos($url, '/');

                                $host = substr($url, 0, $index);
                            }
                            $data['author'] = $host;
                        }
                        $count_feed = count($feed['entries']);
                        foreach ($feed['entries'] as $entry) {
                            if ($entry['author'] == "" || $entry['author'] == null) {
                                $entry['author'] = $data['author'];
                            }
                            $des = $entry['description'];
                            $tmp_img = $this->parseDescription($entry['description']);

                            if (!$tmp_img || $tmp_img == null || $tmp_img == '') {
                                $tmp_img = $this->catch_that_image($des);
                            }
                            $entry['description'] = $des;
                            $a = date('Y-m-d', $entry['pubDate']);
                            $pubdate = strtotime($a);
                            $edata = array(
                                'category_id' => $category['category_id'],
                                'owner_type' => "user",
                                'owner_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
                                'title' => $entry['title'],
                                'description' => $entry['description'],
                                'content' => $entry['content'],
                                'image' => $tmp_img,
                                'link_detail' => $entry['link_detail'],
                                'author' => $entry['author'],
                                'pubDate' => $pubdate,
                                'pubDate_parse' => $entry['pubDate_parse'],
                                'posted_date' => date('Y-m-d H:i:s'),
                                'is_active' => "1"
                            );

                            //insert data to database
                            $db = Engine_Api::_()->getDbtable('contents', 'news')->getAdapter();
                            $db->beginTransaction();

                            try {
                                //check news exist by link
                                $content = Engine_Api::_()->news()->getAllContent(array('link_detail' => $edata['link_detail'], 'title' => $edata['title']));

                                if (count($content) > 0) {
//                                    $table = Engine_Api::_()->getDbTable('contents', 'news');
//                                    $where = $table->getAdapter()->quoteInto('content_id = ?', $content[0]['content_id']);
//                                    $table->update($edata, $where);
                                } else {
                                    // Create content
                                    $table = $this->_helper->api()->getDbtable('contents', 'news');
                                    $content = $table->createRow();
                                    $content->setFromArray($edata);
                                    $content->save();

                                    //set auth
                                    $auth = Engine_Api::_()->authorization()->context;
                                    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
                                    $auth_view = "everyone";
                                    $viewMax = array_search($auth_view, $roles);
                                    foreach ($roles as $i => $role)
                                        $auth->setAllowed($content, $role, 'view', ($i <= $viewMax));
                                }

                                $db->commit();
                                $edata = null;
                                
                            } catch (Exception $e) {
                                $db->rollBack();
                                //  throw $e;
                            }
                        }
                    } catch (exception $ex) {
                        //throw $ex;
                        $this->getAtomFeed($category['url_resource'], $category['category_id'], Engine_Api::_()->user()->getViewer()->getIdentity());
                        //print_r($feed);die();
                    }
                }
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'format' => 'smoothbox',
                'messages' => array('Get data successful.')
            ));
        } catch (exception $ex) {
            $this->_forward('failed', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'format' => 'smoothbox',
                'messages' => array('Get data fail.')
            ));
            throw $ex;
        }
    }

    public function activerssAction() {
        if ($this->getRequest()->isPost()) {
            $value = $this->getRequest()->getPost();

            //print_r( $value);die();
            $categories_id = explode(',', $value['categories_active']);
            $table = Engine_Api::_()->getDbTable('categories', 'news');
            $content = Engine_Api::_()->getDbTable('contents', 'news');
            try {
                foreach ($categories_id as $category_id) {
                    if (is_numeric($category_id)) {
                        $where = $table->getAdapter()->quoteInto('category_id = ?', $category_id);
                        $table->update(array('is_active' => $value['is_active_name']), $where);

                        $where_content = $content->getAdapter()->quoteInto('category_id = ?', $category_id);
                        //echo $where_content;
                        $content->update(array('is_active' => $value['is_active_name']), $where_content);
                        $category = Engine_Api::_()->getItem('categories', $category_id);
                        $categoriesparent_id = $category->category_parent_id;
                        $inactive = false;
                        if ($categoriesparent_id > 0) {
                            $inactive = Engine_Api::_()->news()->checkcategoriesparentinactive($categoriesparent_id);

                            if ($inactive == true) {
                                $where = $table->getAdapter()->quoteInto('category_id = ?', $category_id);
                                $table->update(array('is_active' => 0), $where);
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                $this->view->result = 0;
                $_SESSION['result'] = 0;
            }
            $this->view->result = 2;
        }

        $_SESSION['result'] = $this->view->result;
        $this->_redirect("admin/news/manage/category", array('result' => $this->view->result));
        //$this->_forward("category","admin","news",array('result'=>$this->view->result));
    }

    public function caactiverssAction() {
        if ($this->getRequest()->isPost()) {
            $value = $this->getRequest()->getPost();

            //print_r( $value);die();
            $categories_id = explode(',', $value['categories_active']);
            $table = Engine_Api::_()->getDbTable('categoryparents', 'news');
            $category = Engine_Api::_()->getDbTable('categories', 'news');
            $content = Engine_Api::_()->getDbTable('contents', 'news');
            try {
                foreach ($categories_id as $category_id) {
                    if (is_numeric($category_id)) {
                        $where = $table->getAdapter()->quoteInto('category_id = ?', $category_id);
                        $table->update(array('is_active' => $value['is_active_name']), $where);

                        $where_category = $category->getAdapter()->quoteInto('category_parent_id = ?', $category_id);
                        $category->update(array('is_active' => $value['is_active_name']), $where_category);

                        $categories = Engine_Api::_()->news()->getAllCategories(array(
                                    'category_parent' => $category_id,
                                ));
                        foreach ($categories as $categoryitem) {
                            $where_content = $content->getAdapter()->quoteInto('category_id = ?', $categoryitem['category_id']);
                            $content->update(array('is_active' => $value['is_active_name']), $where_content);
                        }
                    }
                }
            } catch (Exception $e) {
                $this->view->result = 0;
                $_SESSION['result'] = 0;
            }
            $this->view->result = 2;
        }

        $_SESSION['result'] = $this->view->result;
        $this->_redirect("admin/news/manage/categories", array('result' => $this->view->result));
    }

    public function featuredAction() {
        if ($this->getRequest()->isPost()) {
            $value = $this->getRequest()->getPost();
            $content_ids = explode(',', $value['news_featured']);
            $content = Engine_Api::_()->getDbTable('contents', 'news');
            try {
                foreach ($content_ids as $content_id) {
                    if (is_numeric($content_id)) {
                        $where_content = $content->getAdapter()->quoteInto('content_id = ?', $content_id);
                        $content->update(array('is_featured' => $value['is_set_featured']), $where_content);
                    }
                }
            } catch (Exception $e) {
                $this->view->result = 0;
                $_SESSION['result'] = 0;
            }
            $this->view->result = 2;
        }

        $_SESSION['result'] = $this->view->result;
        if ($this->getRequest()->page > 1 && !empty($this->getRequest()->page))
            $this->_redirect("admin/news/manage/index/page/" . $this->getRequest()->page, array('result' => $this->view->result));
        else
            $this->_redirect("admin/news/manage/", array('result' => $this->view->result));
        //$this->_forward("category","admin","news",array('result'=>$this->view->result));
    }

    public function settingAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('news_admin_main', array(), 'news_admin_main_timeframe');

        $this->view->form = $form = new News_Form_Admin_Manage_Setting();

        $timeFrame = Engine_Api::_()->news()->getTimeframe();

        // Posting form
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $data_array = $form->getValues();

                $data = array();
                $data['minutes'] = $data_array['minutes'];
                $data['hour'] = $data_array['hour'];
                $data['month'] = $data_array['month'];
                $data['day'] = $data_array['day'];
                $data['weekday'] = $data_array['weekday'];

                $table = Engine_Api::_()->getDbTable('timeframe', 'news');
                $where = $table->getAdapter()->quoteInto('timeframe_id = ?', $data_array['id']);
                $table->update($data, $where);

                $this->view->mess = "Set timeframe successful!";
            }
        } else {
            foreach ($form->getElements() as $name => $element) {
                if (isset($timeFrame[0][$name])) {
                    $element->setValue($timeFrame[0][$name]);
                } elseif ($name == "id") {
                    $element->setValue($timeFrame[0]['timeframe_id']);
                }
            }
            $this->view->mess = "";
        }
    }

    public function deleteAction() {
        // In smoothbox

        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->category_id = $id;
        // Check post
        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $group = Engine_Api::_()->getItem('categories', $id);
                $group->delete();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('')
            ));
        }
        // Output
        $this->renderScript('admin-manage/delete.tpl');
    }

}

?>