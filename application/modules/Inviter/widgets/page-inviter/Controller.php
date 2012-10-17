<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-07-02 19:54 kirill $
 * @author     Kirill
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_Widget_PageInviterController extends Engine_Content_Widget_Abstract
{
    protected $_errors = array(), $_success;

    public function indexAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $inviterApi = Engine_Api::_()->getApi('core', 'inviter');

        $this->view->subject = Engine_Api::_()->core()->getSubject('page');

        if (!$viewer->getIdentity()) {
            return $this->setNoRender();
        }

        $count = 4;
        $this->view->paginator = $paginator = $inviterApi->getInviterUsedFriends(array('order_rand' => true, 'count' => $count));
        $paginator->setItemCountPerPage($count);
    }
}