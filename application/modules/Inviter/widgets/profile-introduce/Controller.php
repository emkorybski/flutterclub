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

class Inviter_Widget_ProfileIntroduceController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$viewer->getIdentity()) {
            return $this->setNoRender();
        }

        $this->getElement()->setTitle('');

        $introductionTbl = Engine_Api::_()->getDbTable('introductions', 'inviter');

        $subject = false;
        if(Engine_Api::_()->core()->hasSubject('user')) {
            $subject = Engine_Api::_()->core()->getSubject();
        } else {
            return $this->setNoRender();
        }

        $this->view->userIntroduce = $userIntroduce = $introductionTbl->getUserIntroduction($subject->getIdentity());

        if (!$userIntroduce) {
            return $this->setNoRender();
        }

        $period = Engine_Api::_()->getApi('settings', 'core')->getSetting('inviter.introduce_yourself_period', 10);
        $period_ts = strtotime($userIntroduce->displayed_date) + $period * 24 * 3600;

        if (time() < $period_ts) {
            return $this->setNoRender();
        }

        $active_theme = $this->view->activeTheme();
        if ($active_theme && is_string($active_theme)) {
            $this->getElement()->setAttrib('class', $active_theme . '_inviter_introduce_yourself');
        }
    }
}