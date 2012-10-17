<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_Widget_ListSuggestController extends Engine_Content_Widget_Abstract
{
  protected $_errors = array(), $_success;
  
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if(!Engine_Api::_()->authorization()->isAllowed('inviter', null, 'use') && !$viewer->getIdentity())
    {
      return $this->setNoRender();
    }

    $total_suggests = $this->_getParam('itemCountPerPage', 4);

    $suggest_array = Engine_Api::_()->getDbtable('nonefriends', 'inviter')->getSuggests(array(
      'current_suggests' => null,
      'noneFriend_id' => 0,
      'widget'=>true,
      'total_suggests'=>$total_suggests,
    ));

    $select = $suggest_array['suggestsSl'];
      //SELECT  `engine4_users`.*  FROM  `engine4_users`  WHERE  (user_id  IN(3352,2880,3443,3291))  ORDER  BY  RAND()  ASC



    $this->view->suggests = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage( $total_suggests );

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }
    
    $current_suggests = array();
    
    foreach ($paginator as $suggest)
    {
      $current_suggests[$suggest->getIdentity()]['user_id'] = $suggest->getIdentity();
      $current_suggests[$suggest->getIdentity()]['mutual_friends'] = explode(',', $suggest_array['mutual_friends'][$suggest->getIdentity()]);
    }
    
    $this->view->current_suggests = $current_suggests;
    $this->view->headTranslate(array(
      'INVITER_Mutual Friends',
    ));
  }
}