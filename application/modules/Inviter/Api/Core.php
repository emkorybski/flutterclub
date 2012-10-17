<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_Api_Core extends Core_Api_Abstract
{
    public function getProviders($all = false, $limit = 1000, $include_disabled = false)
    {
        $where = ($all) ? ' provider_default IN (0, 1) ' : ' provider_default=1 ';
        $table = Engine_Api::_()->getDbtable('providers', 'inviter');
        $select = $table->select('provider_id, provider_title, provider_logo')
            ->where($where)
            ->limit($limit);

        if (!$include_disabled) {
            $select->where('provider_enabled = ?', 1);
        }

        $rows = $table->fetchAll($select);

        return $rows;
    }

    public function getProviders2($all = false, $limit = 1000, $include_disabled = false)
    {
        $providerApi = Engine_Api::_()->getApi('provider', 'inviter');
        $where = ($all) ? ' provider_default IN (0, 1) ' : ' provider_default=1 ';
        $table = Engine_Api::_()->getDbtable('providers', 'inviter');
        $select = $table->select('provider_id, provider_title, provider_logo', 'isopeninvoter')
            ->where($where)
            ->order('isopeninviter DESC')
            ->limit($limit);

        if (!$include_disabled) {
            $select->where('provider_enabled = ?', 1);
        }

        $rows = $table->fetchAll($select);

        $res = array();
        foreach ($rows as $row) {
            if (!$row->isopeninviter) {
                $provider_title = $providerApi->checkProvider($row->provider_title);
                if ($providerApi->checkIntegratedProvider($provider_title)) {
                    $res[] = $row;
                }
            } else {
                $res[] = $row;
            }
        }
        return $res;
    }

    public function getProviderArray($all = false)
    {
        $provider_row = $this->getProviders($all);
        $providers = array();
        $i = 0;

        foreach ($provider_row as $provider)
        {
            $providers[$i]['id'] = $provider->provider_id;
            $providers[$i]['title'] = $provider->provider_title;
            $providers[$i]['logo'] = $provider->provider_logo;
            $i++;
        }

        return $providers;
    }

    public function getInvitation($invite_id)
    {
        $invitationTable = Engine_Api::_()->getDbtable('invites', 'inviter');
        $invitationSelect = $invitationTable->select()
            ->where('invite_id = ?', $invite_id)->limit(1);

        return $invitationTable->fetchRow($invitationSelect);
    }

    public function findIdByRecipient($recipient)
    {
        $table = Engine_Api::_()->getDbTable('invites', 'inviter');
        $select = $table->select()
            ->where(" recipient = '{$recipient}' && new_user_id != 0")->limit(1);


        return $table->fetchRow($select);
    }

    public function getInviterPaginator($params = array())
    {
        $paginator = Zend_Paginator::factory($params['select']);
        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }

        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }

        if (empty($params['limit'])) {
            $paginator->setItemCountPerPage(20);
        }

        return $paginator;
    }

    public function getNonefriends()
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$viewer->getIdentity()) {
            return false;
        }

        $nonefriendTb = Engine_Api::_()->getDbtable('nonefriends', 'inviter');
        $nonefriendSl = $nonefriendTb->select()->where('user_id = ?', $viewer->getIdentity())->limit(1);
        $nonefreinds = $nonefriendTb->fetchRow($nonefriendSl);

        $nonefreind_ids = (isset($nonefreinds->nonefriend_ids) && trim($nonefreinds->nonefriend_ids) != '')
            ? $nonefreinds->nonefriend_ids
            : 0;

        $userTb = Engine_Api::_()->getItemTable('user');
        $userSl = $userTb->select()->where("user_id IN ({$nonefreind_ids})");

        return Zend_Paginator::factory($userSl);
    }

    public function getMutualfriends($params)
    {
        $user_id = $params['user_id'];

        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$viewer->getIdentity() && !$user_id) {
            return false;
        }

        $membershipTb = Engine_Api::_()->getDbtable('membership', 'user');

        $membershipSl = $membershipTb->select()
            ->setIntegrityCheck(false)
            ->from($membershipTb->info('name'), array('user_id'))
            ->where('resource_id = ?', $viewer->getIdentity())
            ->where('active = ?', 1);

        $friend_list = $membershipTb->getAdapter()->fetchCol($membershipSl);
        $friend_list = ($friend_list) ? $friend_list : array(0);

        $mutualSl = $membershipTb->select()
            ->setIntegrityCheck(false)
            ->from($membershipTb->info('name'), array('GROUP_CONCAT(`resource_id`) as mutual_friends'))
            ->where("resource_id IN (?)", $friend_list)
            ->where("user_id = ?", $user_id)
            ->where('active = ?', 1);

        $mutual = $membershipTb->fetchRow($mutualSl);

        $userTb = Engine_Api::_()->getItemTable('user');

        $select = $userTb->select()
            ->where("user_id IN({$mutual->mutual_friends})");

        return Zend_Paginator::factory($select);
    }

    public function getContactsFromFile($uploaded_file)
    {
        $contacts = array();

        //Mozilla Thunderbird
        if (empty($contacts)) {
            $fh = fopen($uploaded_file, "r");

            while (($row = fgets($fh, 4096)) != false) {
                $value = explode(':', trim($row));
                $dn = trim($value[0]);

                if ($dn == 'dn') {
                    $value = trim($value[1]);
                    $exploded = explode(',', str_replace(array('cn=', 'mail='), array('', ''), trim($value)));
                    if (is_array($exploded) && count($exploded) == 2 && $this->is_email($exploded[1])) {
                        $contacts[trim($exploded[1])] = trim($exploded[0]);
                    }
                }
            }
            fclose($fh);
        }

        //Outlook
        if (empty($contacts)) {
            $fh = fopen($uploaded_file, "r");

            while (($row = fgetcsv($fh, 1024, ',')) != false) {
                foreach ($row as $value) {
                    $value = trim(str_replace(array('"', ')'), array('', ''), $value));
                    $exploded = explode('(', $value);
                    if (is_array($exploded) && count($exploded) == 2 && $this->is_email($exploded[1])) {
                        $contacts[trim($exploded[1])] = trim($exploded[0]);
                    }
                }
            }

            fclose($fh);
        }

        //Outlook Express

        if (empty($contacts)) {
            $fh = fopen($uploaded_file, "r");

            while (($row = fgetcsv($fh, 1024, ';')) != false) {
                foreach ($row as $value) {
                    $email = trim($value);
                    $exploded = explode('@', $email);
                    if (is_array($exploded) && count($exploded) == 2 && $this->is_email($email)) {
                        $contacts[$email] = trim($exploded[0]);
                    }
                }
            }

            fclose($fh);
        }

        //Comma separated CSV FILE
        if (empty($contacts)) {
            $fh = fopen($uploaded_file, "r");

            while (($row = fgetcsv($fh, 1024, ',')) != false) {
                foreach ($row as $value) {
                    $email = trim($value);
                    $exploded = explode('@', $email);
                    if (is_array($exploded) && count($exploded) == 2 && $this->is_email($email)) {
                        $contacts[$email] = trim($exploded[0]);
                    }
                }
            }

            fclose($fh);
        }

        return $contacts;
    }

    public function is_email($email)
    {
        $validate = new Zend_Validate_EmailAddress();
        try {
            $res = $validate->isValid($email);
        } catch (Exception $e) {
            $res = false;
        }
        return $res;
    }

    public function getMutualFriendCount($user_id, $viewer_id = false)
    {
        if (!$viewer_id) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $viewer_id = $viewer->getIdentity();
        }

        if (!$user_id || !$viewer_id) {
            return false;
        }

        $membershipTbl = Engine_Api::_()->getDbtable('membership', 'user');

        $sub_select = $membershipTbl->select()
            ->setIntegrityCheck(false)
            ->from($membershipTbl->info('name'), array('user_id'))
            ->where('resource_id = ?', $user_id)
            ->where('active = ?', 1);

        $main_select = $membershipTbl->select()
            ->setIntegrityCheck(false)
            ->from($membershipTbl->info('name'), new Zend_Db_Expr('COUNT(user_id)'))
            ->where('resource_id = ?', $viewer_id)
            ->where('active = ?', 1)
            ->where('user_id IN (' . $sub_select . ')');

        return $membershipTbl->getAdapter()->fetchOne($main_select);
    }

    public function getMutualLikeCount($user_id, $viewer_id = false)
    {
        if (!$viewer_id) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $viewer_id = $viewer->getIdentity();
        }

        if (!$user_id || !$viewer_id) {
            return false;
        }

        // check like plugin installed
        $modulesTbl = Engine_Api::_()->getDbTable('modules', 'core');
        $modulesSel = $modulesTbl->select()
            ->where('name = ?', 'like')
            ->where('enabled = ?', 1)
            ->limit(1);

        if (!$modulesTbl->fetchRow($modulesSel)) {
            return false;
        }

        $likesTbl = Engine_Api::_()->getDbtable('likes', 'core');

        $innerSelect = $likesTbl->select()
            ->setIntegrityCheck(false)
            ->from($likesTbl->info('name'), array('resource_type', 'resource_id'))
            ->where('poster_id = ?', $user_id);

        $select = $likesTbl->select()
            ->setIntegrityCheck(false)
            ->from(array('like1' => $likesTbl->info('name')), new Zend_Db_Expr('COUNT(like1.resource_id)'))
            ->joinInner(array('tmp' => $innerSelect), 'tmp.resource_type = like1.resource_type', array())
            ->where('tmp.resource_id = like1.resource_id')
            ->where('like1.poster_id = ?', $viewer_id);

        return $likesTbl->getAdapter()->fetchOne($select);
    }

    public function getInviterUsedFriendCount()
    {
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $membershipTbl = Engine_Api::_()->getDbtable('membership', 'user');
        $invitesTbl = Engine_Api::_()->getDbTable('invites', 'inviter');

        $sub_select = $membershipTbl->select()
            ->setIntegrityCheck(false)
            ->from($membershipTbl->info('name'), array(new Zend_Db_Expr('DISTINCT user_id')))
            ->where('resource_id = ?', $viewer_id)
            ->where('active = ?', 1);

        $main_select = $invitesTbl->select()
            ->setIntegrityCheck(false)
            ->from($invitesTbl->info('name'), array(new Zend_Db_Expr('COUNT(DISTINCT user_id)')))
            ->where('user_id IN (' . $sub_select . ')');

        return $invitesTbl->getAdapter()->fetchOne($main_select);
    }

    public function getInviterUsedFriends($parameters)
    {
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $usersTable = Engine_Api::_()->getItemTable('user');
        $membershipTbl = Engine_Api::_()->getDbtable('membership', 'user');
        $invitesTbl = Engine_Api::_()->getDbTable('invites', 'inviter');

        $sub_select = $membershipTbl->select()
            ->setIntegrityCheck(false)
            ->from($membershipTbl->info('name'), array(new Zend_Db_Expr('DISTINCT user_id')))
            ->where('resource_id = ?', $viewer_id)
            ->where('active = ?', 1);

        $inner_select = $invitesTbl->select()
            ->setIntegrityCheck(false)
            ->from($invitesTbl->info('name'), array(new Zend_Db_Expr('DISTINCT user_id')))
            ->where('user_id IN (' . $sub_select . ')');

        if (isset($parameters['order_rand'])) {
            $inner_select->limit(1000);
            $friend_ids = $invitesTbl->getAdapter()->fetchCol($inner_select);
            shuffle($friend_ids);
            $friend_list = array();
            for ($index = 0; ($index < $parameters['count'] && $index < count($friend_ids)); $index++) {
                $friend_list[$index] = $friend_ids[$index];
            }

            $friend_list = (count($friend_list) > 0) ? $friend_list : array(0);
            $select = $usersTable->select()
                ->where('user_id IN (?)', $friend_list)
                ->order('RAND()');
        }
        else {
            $select = $usersTable->select()
                ->setIntegrityCheck(false)
                ->from($usersTable->info('name'))
                ->join(array('tmp' => $inner_select), "tmp.user_id = {$usersTable->info('name')}.user_id");

            if (isset($parameters['keyword']) && $parameters['keyword']) {
                $select->where("{$usersTable->info('name')}.displayname LIKE ?", '%' . $parameters['keyword'] . '%', 'STRING');
            }
        }

        return Zend_Paginator::factory($select);
    }

    public function getFacebookFriends($fb_user_id, $token = null)
    {
        //$cache_id = md5('facebook_friends' . $fb_user_id);
        $cache_id = 'facebook_friends' . $fb_user_id;
        $friends_data = array();

        if (APPLICATION_ENV == 'production') {
            $cache = Engine_Cache::factory();
            $friends_data = $cache->load($cache_id);

            if ($friends_data && $friends_data['continue'] != 1) {
                return $friends_data['items'];
            }
        }

        if (isset($friends_data['limit_start']) && isset($friends_data['limit_count'])) {
            $limit_start = $friends_data['limit_start'] + $friends_data['limit_count'];
            $limit_count = $friends_data['limit_count'];
        } else {
            $limit_start = 0;
            $limit_count = 100;
        }

        $facebook = Inviter_Api_Provider::getFBInstance();

        $fql = "SELECT uid, name, profile_url, pic_square FROM user "
            . "WHERE uid IN (SELECT uid2 FROM friend WHERE uid1={$fb_user_id}) "
            . "LIMIT 0, 100";

        $_friends = $facebook->api(array('method' => 'fql.query', 'query' => $fql, 'access_token' => $token));

        $friends = ($friends_data && $friends_data['items']) ? $friends_data['items'] : array();
        foreach ($_friends as $friend_info) {
            $friends[$friend_info['uid']] = $friend_info;
        }

        $friends_data = array(
            'items' => $friends,
            'limit_start' => $limit_start,
            'limit_count' => $limit_count,
            'continue' => count($_friends) == $limit_count
        );

        if (APPLICATION_ENV == 'production') {
            $cache->save($friends_data, $cache_id);
            $cache->setLifetime(2 * 24 * 3600); // TODO SET RIGHT VALUE
        }

        return $friends_data['items'];
    }

    public function getNoneMemberFbFriends($fb_user_id, $count = 9)
    {
        $fb_users = $this->getFacebookFriends($fb_user_id);

        if (!$fb_users) {
            return array();
        }

        $fb_user_ids = array_keys($fb_users);

        // exclude already invited users
        $invitesTbl = Engine_Api::_()->getDbTable('invites', 'inviter');

        $invitesSel = $invitesTbl->select()
            ->setIntegrityCheck(false)
            ->from($invitesTbl->info('name'), array('recipient'))
            ->where('provider = ?', 'facebook')
            ->where('new_user_id != ?', 0);

        $invited_users = $invitesTbl->getAdapter()->fetchCol($invitesSel);
        $fb_user_ids = array_diff($fb_user_ids, $invited_users);

        // exclude already joined users
        $facebookTbl = Engine_Api::_()->getDbTable('facebook', 'user');
        $facebookSel = $facebookTbl->select()
            ->setIntegrityCheck(false)
            ->from($facebookTbl->info('name'), array('facebook_uid'));

        $facebook_users = $facebookTbl->getAdapter()->fetchCol($facebookSel);
        $fb_user_ids = array_diff($fb_user_ids, $facebook_users);

        if (!$fb_user_ids) {
            return array();
        }

        shuffle($fb_user_ids);

        $fb_none_members = array();
        foreach ($fb_user_ids as $fb_uid) {
            $fb_none_members[] = $fb_users[$fb_uid];

            if (count($fb_none_members) == $count) {
                break;
            }
        }

        return $fb_none_members;
    }

    public function getNoneFriendFbFriends($fb_user_id, $count = 9)
    {
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        if (!$fb_user_id) {
            return array();
        }

        $fb_friends = $this->getFacebookFriends($fb_user_id);

        if (!$fb_friends) {
            return array();
        }

        $fb_friend_ids = array_keys($fb_friends);

        $usersTbl = Engine_Api::_()->getItemTable('user');
        $facebookTbl = Engine_Api::_()->getDbTable('facebook', 'user');
        $invitesTbl = Engine_Api::_()->getDbTable('invites', 'inviter');
        $membershipTbl = Engine_Api::_()->getDbtable('membership', 'user');

        $select = $usersTbl->select()
            ->setIntegrityCheck(false)
            ->from(array('user' => $usersTbl->info('name')), array('user.user_id', new Zend_Db_Expr('IF (invite.recipient, invite.recipient, facebook.facebook_uid) AS facebook_uid')))
            ->joinLeft(array('facebook' => $facebookTbl->info('name')), 'user.user_id = facebook.user_id', array())
            ->joinLeft(array('invite' => $invitesTbl->info('name')), 'user.user_id = invite.new_user_id AND invite.provider = "facebook"', array())
            ->joinLeft(array('friend' => $membershipTbl->info('name')), 'user.user_id = friend.user_id AND friend.resource_id = ' . $viewer_id, array())
            ->where('invite.recipient OR facebook.facebook_uid')
            ->where('ISNULL(friend.resource_id)');

        $fb_members = $usersTbl->getAdapter()->fetchPairs($select);

        $fb_none_friends = array();
        $se_none_friend_ids = array();
        foreach ($fb_members as $se_user_id => $fb_member_id) {
            if (!in_array($fb_member_id, $fb_friend_ids)) {
                continue;
            }

            if (count($se_none_friend_ids) >= $count) {
                break;
            }

            $fb_none_friends[$se_user_id] = new Inviter_Model_FacebookFriend($fb_friends[$fb_member_id]);
            $se_none_friend_ids[] = $se_user_id;
        }

        if (count($se_none_friend_ids) == 0) {
            return array();
        }

        shuffle($se_none_friend_ids);

        return array('fb_users' => $fb_none_friends, 'se_users' => Engine_Api::_()->getItemMulti('user', $se_none_friend_ids));
    }

    public function getNoneFriendFbFriendsBox($params)
    {
        $tokensTbl = Engine_Api::_()->getDbTable('tokens', 'inviter');
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $settings = Engine_Api::_()->getDbTable('settings', 'core');
        $app_id = $settings->getSetting('inviter.facebook.consumer.key', false);
        $secret = $settings->getSetting('inviter.facebook.consumer.secret', false);
        $fbApi = Engine_Api::_()->loadClass('Inviter_Plugin_fbApi');
        $fbApi->init($app_id, $secret);
        $token = $tokensTbl->findUserToken($viewer_id, 'facebook');
        $fb_user_id = $fbApi->getMe($token['oauth_token'], true);

        //    $fb_user_id = Inviter_Api_Provider::getFBUserId();

        $members = $this->getNoneFriendFbFriends($fb_user_id, 1000);

        $se_users = (isset($members['se_users']) && $members['se_users']) ? $members['se_users'] : array();

        if ($params && isset($params['keyword']) && $params['keyword']) {
            $all_users = array();
            foreach ($se_users as $user) {
                if (strstr($user->getTitle(), $params['keyword']) === false) {
                    continue;
                }

                $all_users[] = $user;
            }
        } else {
            $all_users = $se_users;
        }

        return Zend_Paginator::factory($all_users);
    }

    public function getAlreadyMemberFbFriends($fb_user_id, $token = null)
    {

        $fb_friends = $this->getFacebookFriends($fb_user_id, $token);
        $fb_friend_ids = array_keys($fb_friends);

        $invitesTbl = Engine_Api::_()->getDbTable('invites', 'inviter');

        $invitesSel = $invitesTbl->select()
            ->setIntegrityCheck(false)
            ->from($invitesTbl->info('name'), array('recipient'))
            ->where('provider = ?', 'facebook')
            ->where('new_user_id != ?', 0);

        $invited_users = $invitesTbl->getAdapter()->fetchCol($invitesSel);
        $fb_members = array_intersect($invited_users, $fb_friend_ids);

        // exclude already joined users
        $facebookTbl = Engine_Api::_()->getDbTable('facebook', 'user');
        $facebookSel = $facebookTbl->select()
            ->setIntegrityCheck(false)
            ->from($facebookTbl->info('name'), array('facebook_uid'));

        $facebook_users = $facebookTbl->getAdapter()->fetchCol($facebookSel);
        $fb_members = array_merge($fb_members, array_intersect($facebook_users, $fb_friend_ids));

        if (!$fb_members) {
            return array();
        }

        return $fb_members;
    }

    public function sendFacebookInvites($fb_user_ids, $code)
    {
        if (!$fb_user_ids || !$code) {
            return;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $invitesTbl = Engine_Api::_()->getDbTable('invites', 'inviter');
        $facebook = Inviter_Api_Provider::getFBInstance();

        $fb_viewer_id = Inviter_Api_Provider::getFBUserId();
        $fb_friends = $this->getFacebookFriends($fb_viewer_id);

        $sent_date = new Zend_Db_Expr('NOW()');

        $not_found_arr = array();
        foreach ($fb_user_ids as $fb_user_id) {
            if (!isset($fb_friends[$fb_user_id])) {
                $not_found_arr[] = $fb_user_id;
            }
        }

        try {
            if (count($not_found_arr) > 0) {
                $facebook = Inviter_Api_Provider::getFBInstance();
                $not_found_str = implode(',', $not_found_arr);

                $fql = "SELECT uid, name, profile_url, pic_square FROM user "
                    . "WHERE uid IN ($not_found_str) "
                    . "LIMIT 0, 100";

                $_friends = $facebook->api(array('method' => 'fql.query', 'query' => $fql));

                if ($_friends && is_array($_friends)) {
                    foreach ($_friends as $friend_info) {
                        $fb_friends[$friend_info['uid']] = $friend_info;
                    }
                }
            }
        }
        catch (Exception $e) {
        }

        foreach ($fb_user_ids as $fb_user_id) {

            if (!isset($fb_friends[$fb_user_id])) {
                continue;
            }

            $fb_friend = $fb_friends[$fb_user_id];

            $invitesTbl->insertInvitation(array(
                'user_id' => $viewer->getIdentity(),
                'sender' => $fb_viewer_id,
                'recipient' => $fb_user_id,
                'code' => $code,
                'message' => '',
                'sent_date' => $sent_date,
                'provider' => 'facebook',
                'recipient_name' => $fb_friend['name']
            ));
        }
    }

    public function getAlreadyFriendContactsBox($params)
    {
        $provider = isset($params['provider']) ? $params['provider'] : 'facebook';
        $providerApi = Engine_Api::_()->getApi('provider', 'inviter');
        $tokensTbl = Engine_Api::_()->getDbTable('tokens', 'inviter');
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        if ($provider == 'facebook') {
            $facebook = Inviter_Api_Provider::getFBInstance();
            if (!$facebook->getAppId() || $viewer_id == 0) {
                return array();
            }

            $settings = Engine_Api::_()->getDbTable('settings', 'core');
            $app_id = $settings->getSetting('inviter.facebook.consumer.key', false);
            $secret = $settings->getSetting('inviter.facebook.consumer.secret', false);
            $fbApi = Engine_Api::_()->loadClass('Inviter_Plugin_fbApi');
            $fbApi->init($app_id, $secret);
            $token = $tokensTbl->findUserToken($viewer_id, $provider);
            $fb_user_id = $fbApi->getMe($token['oauth_token'], true);

            if (!$fb_user_id) {
                return array();
            }
            $token = $tokensTbl->getUserTokenByObjectId($viewer_id, $fb_user_id, $provider);
        } else {
            $token = $tokensTbl->getUserToken($viewer_id, $provider);
        }

        if (!$token) {
            return array();
        }

        $members = $providerApi->getAlreadyFriendContacts($token, $provider, 1000);

        $se_users = (isset($members['se_users']) && $members['se_users']) ? $members['se_users'] : array();

        if ($params && isset($params['keyword']) && $params['keyword']) {
            $all_users = array();
            foreach ($se_users as $user) {
                if (strstr($user->getTitle(), $params['keyword']) === false) {
                    continue;
                }

                $all_users[] = $user;
            }
        } else {
            $all_users = $se_users;
        }

        return Zend_Paginator::factory($all_users);
    }

    public function getNoneFriendContactsBox($params)
    {
        $provider = isset($params['provider']) ? $params['provider'] : 'facebook';

        $providerApi = Engine_Api::_()->getApi('provider', 'inviter');
        $tokensTbl = Engine_Api::_()->getDbTable('tokens', 'inviter');
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        if ($provider == 'facebook') {
            $facebook = Inviter_Api_Provider::getFBInstance();
            if (!$facebook->getAppId() || $viewer_id == 0) {
                return array();
            }
            $settings = Engine_Api::_()->getDbTable('settings', 'core');
            $app_id = $settings->getSetting('inviter.facebook.consumer.key', false);
            $secret = $settings->getSetting('inviter.facebook.consumer.secret', false);
            $fbApi = Engine_Api::_()->loadClass('Inviter_Plugin_fbApi');
            $fbApi->init($app_id, $secret);
            $token = $tokensTbl->findUserToken($viewer_id, $provider);
            $fb_user_id = $fbApi->getMe($token['oauth_token'], true);

            if (!$fb_user_id) {
                return array();
            }
            $token = $tokensTbl->getUserTokenByObjectId($viewer_id, $fb_user_id, $provider);
        } else {
            $token = $tokensTbl->getUserToken($viewer_id, $provider);
        }

        if (!$token) {
            return array();
        }

        $members = $providerApi->getNoneFriendContacts($token, $provider, 1000);

        $se_users = (isset($members['se_users']) && $members['se_users']) ? $members['se_users'] : array();

        if ($params && isset($params['keyword']) && $params['keyword']) {
            $all_users = array();
            foreach ($se_users as $user) {
                if (strstr($user->getTitle(), $params['keyword']) === false) {
                    continue;
                }

                $all_users[] = $user;
            }
        } else {
            $all_users = $se_users;
        }

        return Zend_Paginator::factory($all_users);
    }

    public function checkInitFbApp()
    {
        $init_fb_app = true;

        $modulesTbl = Engine_Api::_()->getDbTable('modules', 'core');
        $modulesSel = $modulesTbl->select()
            ->where('name = ?', 'socialdna')
            ->where('enabled = ?', 1);

        if ($modulesTbl->fetchRow($modulesSel)) {
            $contentTbl = Engine_Api::_()->getDbTable('content', 'core');
            $contentSel = $contentTbl->select()
                ->where('name = ?', 'socialdna.boot');

            $init_fb_app = !($contentTbl->fetchRow($contentSel));
        }

        return $init_fb_app;
    }

    public function getPageAdminsLikes($page_id, $member_list = array())
    {
        if (!$member_list) {
            return array();
        }

        $likesTb = Engine_Api::_()->getDbtable('likes', 'core');
        $pageTeamTb = Engine_Api::_()->getDbtable('membership', 'page');

        $member_ids = array();
        foreach ($member_list as $member) {
            $member_ids[] = $member['user_id'];
        }

        $select = $likesTb->select()
            ->setIntegrityCheck(false)
            ->from($likesTb->info('name'), array('poster_id'))
            ->where('resource_id = ?', $page_id)
            ->where('resource_type = ?', 'page')
            ->where('poster_id IN (?)', $member_ids);
        $likes = $likesTb->getAdapter()->fetchCol($select);

        $select = $pageTeamTb->select()
            ->setIntegrityCheck(false)
            ->from($pageTeamTb->info('name'), array('user_id'))
            ->where('resource_id = ?', $page_id)
            ->where('user_id IN (?)', $member_ids);
        $team = $pageTeamTb->getAdapter()->fetchCol($select);

        $ignore_users = $likes + $team;

        if (!$ignore_users) {
            return array();
        }

        $teamAndLikes = array();
        foreach ($member_list as $member) {
            if (in_array($member['user_id'], $ignore_users)) {
                $teamAndLikes[] = $member;
            }
        }

        return $teamAndLikes;
    }

    public function getItemPhotoUrl($item, $base_url)
    {
        if (!$item) return '';
        $type = $item->getType();
        $module = Engine_Api::_()->inflect(Engine_Api::_()->getItemModule($type));
        $url = $item->getPhotoUrl();
        if (!$url) {
            $url = $base_url . "application/modules/" . $module . "/externals/images/nophoto_" . $type . "_thumb_profile.png";
        }
        return $url;
    }

    public function getUserPhotoUrl($user, $base_url)
    {
        $url = $user->getPhotoUrl();
        if (!$url) {
            $url = $base_url . "application/modules/User/externals/images/nophoto_user_thumb_profile.png";
        }
        return $url;
    }

    public function getPagePhotoUrl($user, $base_url)
    {
        $url = $user->getPhotoUrl();
        if (!$url) {
            $url = $base_url . "application/modules/Page/externals/images/nophoto_page_thumb_profile.png";
        }
        return $url;
    }

    public function getIntegratedProviders()
    {
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $providers = array();
        $providers['facebook'] = $settings->getSetting('inviter.facebook.consumer.key', false);
        $providers['twitter'] = $settings->getSetting('inviter.twitter.consumer.key', false);
        $providers['linkedin'] = $settings->getSetting('inviter.linkedin.consumer.key', false);
        $providers['gmail'] = $settings->getSetting('inviter.gmail.consumer.key', false);
        $providers['yahoo'] = $settings->getSetting('inviter.yahoo.consumer.key', false);
        $providers['hotmail'] = $settings->getSetting('inviter.hotmail.consumer.key', false);
        $providers['lastfm'] = $settings->getSetting('inviter.lastfm.api.key', false);
        $providers['foursquare'] = $settings->getSetting('inviter.foursquare.consumer.key', false);
        $providers['mailru'] = $settings->getSetting('inviter.mailru.secret.key', false);
        return $providers;
    }

    public function getFacebookSettings($view = null, $page = null, $signup = false)
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        $settings = array();
        $settings['host'] = $host_url;
        if (!$page) {
            $codes_tbl = Engine_Api::_()->getDbTable('codes', 'inviter');
            $settings['caption'] = 'INVITER_Join our social network!';
            $settings['message'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('invite.message');
            $settings['invitation_url'] = $codes_tbl->getUserReferralLink($viewer->getIdentity(), $signup);
            $settings['invite_code'] = $codes_tbl->getUserCode($viewer->getIdentity(), $signup);

            if ($view)
                $settings['picture'] = $host_url . Engine_Api::_()->inviter()->getItemPhotoUrl($viewer, $view->layout()->staticBaseUrl);
            $router = Zend_Controller_Front::getInstance()->getRouter();
            if(!$signup) {
                $params = array('module' => 'inviter', 'controller' => 'index', 'action' => 'facebookaftersend');
                $settings['redirect_url'] = $host_url . $router->assemble($params, 'default', true);
            }
            else
                $settings['signup'] = $signup;

        } else {
            $page = Engine_Api::_()->getItem('page', $page);
            $settings['page_id'] = $page->getIdentity();
            $settings['caption'] = 'PAGE_INVITER_Check Page';
            $settings['message'] = 'PAGE_INVITER_You are being invited to join our social network.';
            $settings['invitation_url'] = $host_url . $page->getHref();
            $settings['redirect_url'] = $host_url . $page->getHref();
            if ($view)
                $settings['picture'] = $host_url . Engine_Api::_()->inviter()->getItemPhotoUrl($page, $view->layout()->staticBaseUrl);
        }
        return $settings;
    }
}
