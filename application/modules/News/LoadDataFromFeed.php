<?php
date_default_timezone_set('UTC');

// Constants
define('_ENGINE', TRUE);
define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);

// Define full application path, environment, and name
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(dirname(dirname(dirname(__FILE__))))));
defined('APPLICATION_PATH_COR') || define('APPLICATION_PATH_COR', realpath(dirname(dirname(dirname(__FILE__)))));
defined('APPLICATION_PATH_SET') || define('APPLICATION_PATH_SET', APPLICATION_PATH_COR . DS . 'settings');
defined('APPLICATION_NAME') || define('APPLICATION_NAME', 'Core');

// Application
require_once APPLICATION_PATH_COR . DS . 'libraries/Engine/Loader.php';
require_once APPLICATION_PATH_COR . DS . 'libraries/Engine/Application.php';
require_once APPLICATION_PATH_COR . DS . 'modules' . DS . 'News' . DS . 'controllers' . DS . 'YnsRSSFeed/YnsRSS.php';
echo APPLICATION_PATH_COR . DS . 'modules' . DS . 'News' . DS . 'controllers' . DS . 'YnsRSSFeed/YnsRSS.php';
Engine_Loader::getInstance()
        // Libraries
        ->register('Engine', APPLICATION_PATH_COR . DS . 'libraries' . DS . 'Engine')
        ->register('Zend', APPLICATION_PATH_COR . DS . 'libraries' . DS . 'Zend');

class initDatabase
{

    static function _initDb()
    {
        // set default timezone


        $file = APPLICATION_PATH . '/application/settings/database.php';
        $options = include $file;

        $db = Zend_Db::factory($options['adapter'], $options['params']);
        /*
          // Non-production
          if( APPLICATION_ENV !== 'production' )
          {
          $db->setProfiler(array(
          'class' => 'Zend_Db_Profiler_Firebug',
          'enabled' => true
          ));
          }



          // set DB to UTC timezone for this session

          switch ($options['adapter']) {
          case 'mysqli':
          case 'mysql':
          case 'pdo_mysql': {
          $db->query("SET time_zone = '+0:00'");
          break;
          }

          case 'postgresql': {
          $db->query("SET time_zone = '+0:00'");
          break;
          }

          default: {
          // do nothing
          }
          }

          // attempt to disable strict mode
          try {
          $db->query("SET SQL_MODE = ''");
          } catch (Exception $e) {

          }
         * 
         */

        return $db;
    }

}
//get db connection
$db = initDatabase::_initDb();
$rss = new YnsRSS();
$sql_admin = 'SELECT * FROM engine4_users WHERE level_id = 1 limit 1';
//get admin account
$admin = $db->fetchAll($sql_admin);

//log
$strLog = "";
$strLog .= "\n###############################################\n";
$strLog .= "Start get data\n";

//get categies
$sql_category = " SELECT * FROM engine4_news_categories WHERE is_active = 1 ORDER BY category_id DESC";
$categories_arr = $db->fetchAll($sql_category);
foreach ($categories_arr as $category)
{

    $strLog .= "Category:" . $category['category_name'] . "(" . date('Y-m-d H:i:s') . ")\n";

    //get data from remote server		
    try {
        //$feed = new Zend_Feed_Rss($category['url_resource']);
        $feed = $rss->getParse(null, $category['url_resource'], null);
        $data = array(
            'title' => $feed['title'],
            'link' => $feed['link'],
            'dateModified' => $feed['dateModified'],
            'description' => $feed['description'],
            'author' => $feed['author'],
            'entries' => array(),
        );
        // trunglt: parse url to get host and assign it to author field
        if ($data['author'] == "")
        {
            $url_arr = parse_url($data['link']);

            $host = @$url_arr['host'];

            if (strpos($host, 'http') || $host == "")
            {
                $url = str_replace('http://', '', $feed['link']);

                $index = strpos($url, '/');

                $host = substr($url, 0, $index);
            }
            $data['author'] = $host;
        }
        foreach ($feed['entries'] as $entry)
        {
            if ($entry['author'] == "" || $entry['author'] == null)
            {
                $entry['author'] = $data['author'];
            }
            $description = $entry['description'];
            $tmp_img = parseDescription($entry['description']);
            if (!$tmp_img || $tmp_img == null || $tmp_img == '')
            {
                $tmp_img = catch_image($description);
            }
            $entry['description'] = $description;
            $a = date('Y-m-d', $entry['pubDate']);
            $pubdate = strtotime($a);
            $edata = array(
                'category_id' => $category['category_id'],
                'owner_type' => "user",
                'owner_id' => $admin[0]['user_id'],
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
            try {

                //check news exist by link
                $sql_content = "SELECT content_id FROM engine4_news_contents where link_detail = '" . $edata['link_detail'] . "'";
                $content = $db->fetchAll($sql_content);

                if (count($content) > 0)
                {
                    
                }
                else
                {
                    $db->insert("engine4_news_contents", $edata);

                    $auth = Engine_Api::_()->authorization()->context;
                    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
                    $auth_view = "everyone";
                    $viewMax = array_search($auth_view, $roles);
                    foreach ($roles as $i => $role)
                        $auth->setAllowed($content, $role, 'view', ($i <= $viewMax));
                }
                $db->commit();
            }
            catch (Exception $e) {
                //throw $e;
            }
        }
    }
    catch (exception $ex) {
        getAtomFeed($category['url_resource'], $category['category_id'], $admin[0]['user_id']);
    }
}
$strLog .= "End get data\n";

$resource_path = APPLICATION_PATH . "/temporary/log/news.cronjob.log";
$writer = new Zend_Log_Writer_Stream($resource_path);
$logger = new Zend_Log($writer);
$logger->info($strLog);

die;
?>
<?php

function catch_image(&$description)
{
    $first_img = @$matches [1] [0];

    if (empty($first_img))
    { //Defines a default image
        return '';
    }
    return $first_img;
}

?>
<?php

function parseDescription(&$description)
{
    $result = "";
    preg_match_all('/<img[^>]+>/i', $description, $result);
    $img = array();
    if (isset($result[0]))
    {
        foreach ($result[0] as $img_tag)
        {
            preg_match_all('/(alt|title|src)=("[^"]*")/i', $img_tag, $img[$img_tag]);
            if (isset($img[$img_tag][2][0]))
            {
                list($width, $height) = @getimagesize(str_replace('"', '', $img[$img_tag][2][0]));
                if ($width >= 40 && $height >= 40)
                {
                    $description = str_replace($img_tag, "", $description);
                    return str_replace('"', '', $img[$img_tag][2][0]);
                }
            };
        }
    }
}

?>
