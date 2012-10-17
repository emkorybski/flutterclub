<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Tokens.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_Model_DbTable_Codes extends Engine_Db_Table
{
    public function getUserCode($user_id, $signup = false)
    {
        if (!$user_id && !$signup) return false;
        $code = $this->getAdapter()->fetchRow($this->select()->where('user_id=?', $user_id));
        if (!$code)
            return $this->createCode($user_id, $signup);

        return $code['code'];
    }

    public function createCode($user_id, $signup = false)
    {
        if (!$user_id && !$signup) return false;
        if ($user_id) {
            $user = $this->getUser($user_id);
            $code_str = $user_id . $user->username . $user->displayname . time();
        }
        else {
            $code_str = time() . mt_rand(99, 9999);
        }

        $code = md5($code_str);
        $code = substr($code, 0, 10);
        $this->getAdapter()->beginTransaction();
        try {
            $this->insert(array('user_id' => $user_id, 'code' => $code));
            $this->getAdapter()->commit();
            return $code;
        } catch (Exception $e) {
            $this->getAdapter()->rollback();
            return false;
        }
    }

    public function getUserReferralLink($user_id, $signup = false)
    {
        if (!$user_id && !$signup) return false;
        $code = $this->getUserCode($user_id, $signup);
        if ($code) {
            $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
            $params = array('code' => $code);
            $router = Zend_Controller_Front::getInstance()->getRouter();
            $referral_link = $host_url . $router->assemble($params, 'inviter_referral', true);
            return $referral_link;
        }
        return false;
    }

    public function getReferralLinkByCode($code)
    {
        $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        $params = array('code' => $code);
        $router = Zend_Controller_Front::getInstance()->getRouter();
        $referral_link = $host_url . $router->assemble($params, 'inviter_referral', true);
        return $referral_link;
    }

    private function getUser($user_id)
    {
        if (!$user_id) return false;
        $users_tbl = Engine_Api::_()->getDbTable('users', 'user');
        $user = $users_tbl->fetchRow($users_tbl->select()->where('user_id=?', $user_id));
        return $user;
    }

    public function getUserId($code)
    {
        if (!$code) return false;
        $code_row = $this->fetchRow($this->select()->where('code=?', $code));
        if (!$code_row || !$code_row['user_id'])
            return false;
        return $code_row['user_id'];
    }

    public function setUserId($code, $user_id)
    {
        if (!$code || !$user_id) return false;
        $code_row = $this->fetchRow($this->select()->where('code=?', $code));
        $code_row->user_id = $user_id;
        $code_row->save();
    }
}
