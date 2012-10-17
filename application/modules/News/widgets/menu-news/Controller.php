<?php

class News_Widget_MenuNewsController extends Engine_Content_Widget_Abstract
{
   public function indexAction()
  {
    $username  = Engine_Api::_()->user()->getViewer()->username;
    $users = Engine_Api::_()->news()->getAllUsers();
    $flag = false;
    foreach ($users as $user)
    {
       if ($user['username'] == $username)
       {
           $flag = true; 
       }
   }
    if (Engine_Api::_()->user()->getViewer()->level_id == 1 || Engine_Api::_()->user()->getViewer()->level_id == 2)
       {
           $flag = true; 
       }
   $this->view->flag = $flag;
   $this->view->navigation = $this->getNavigation();
  }
  protected $_navigation;
  public function getNavigation()
  {
    $tabs   = array();
    $tabs[] = array(
              'label'      => 'Browse News',
              'route'      => 'news_general',
              'action'     => 'index',
              'controller' => 'index',
              'module'     => 'news'
            );
       $tabs[] = array(
              'label'      => 'News Management',
              'route'      => 'news_general',
              'action'     => 'manage',
              'controller' => 'index',
              'module'     => 'news'
            );
    if( is_null($this->_navigation) ) {
      $this->_navigation = new Zend_Navigation();
      $this->_navigation->addPages($tabs);
    }
    return $this->_navigation;
  }
}
