<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Invites.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_Model_DbTable_Invites extends Engine_Db_Table
{
  protected $_name = 'inviter_invites';

  public function deleteInvitations($ids = array())
  {
    if (!$ids || empty($ids)) {
      return false;
    }

    $this->getAdapter()->beginTransaction();
    try {
      foreach ($ids as $id)
        $this->delete($this->getAdapter()->quoteInto("invite_id = ?", $id));
      $this->getAdapter()->commit();
      return true;
    } catch (Exception $e) {
      $this->getAdapter()->rollBack();
      return false;
    }
  }

  public function getUsedProviders($use_link = true)
  {
    $invites = $this->fetchAll($this->select());
    $invites = $invites->toArray();
    $providers = array();
    foreach ($invites as $invite) {
      if ($invite['provider'])
        $providers[] = $invite['provider'];
      elseif ($invite['code'] && $invite['recipient']) {
        $providers[] = 'email';
      } elseif ($invite['code'] && $use_link) {
        $providers[] = 'link';
      }
    }
    $providers = array_unique($providers);
    $tmp = array(0 => '');
    foreach ($providers as $provider) {
      if ($provider == 'email') {
        $tmp[$provider] = Zend_Registry::get('Zend_Translate')->_('INVITER_Provider_Email');
        continue;
      }
      if ($provider == 'link' && $use_link) {
        $tmp[$provider] = Zend_Registry::get('Zend_Translate')->_('INVITER_Provider_Link');
        continue;
      }
      $tmp[$provider] = Zend_Registry::get('Zend_Translate')->_(ucfirst($provider));
    }
    return $tmp;
  }

  public function getReferralsPaginator($params = array())
  {
    $select = $this->select()->where('user_id = ? and new_user_id != 0', $params['user_id']);
    $prefix = $this->getTablePrefix();

    if ($params['name']) {
      $table = Engine_Api::_()->getDbtable('users', 'user');
      $tableName = $table->info('name');
      $select = $table->select()->setIntegrityCheck(false);
      $prefix = $table->getTablePrefix();

      $select
        ->from($tableName)
        ->joinLeft($prefix . "inviter_invites", $prefix . "inviter_invites.new_user_id = {$tableName}.user_id", array('new_user_id',
        'code', 'provider', 'recipient', 'referred_date', 'invite_id'))
        ->where("{$tableName}.displayname like ?", "%" . $params['name'] . "%")
        ->where("{$prefix}inviter_invites.user_id = ? and {$prefix}inviter_invites.new_user_id != 0", $params['user_id']);
    }

    if ($params['provider']) {
      if ($params['provider'] == 'link') {
        $select = $select->where("{$prefix}inviter_invites.code !='' and {$prefix}inviter_invites.provider=?", $params['provider']);
      }
      elseif ($params['provider'] == 'email') {
        $select = $select->where("{$prefix}inviter_invites.code!=0 and {$prefix}inviter_invites.recipient!=0 and {$prefix}inviter_invites.provider=''");
      }
      else {
        $select = $select->where('provider=?', $params['provider']);
      }
    }

    //        print_arr($params);
    //        print_die($select  .'');

    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($params['ipp']);
    $paginator->setCurrentPageNumber($params['page']);
    return $paginator;
  }

  public function getInvitesPaginator($params = array())
  {
    $select = $this->select()->where('user_id = ? && new_user_id = 0 && sender !="" && recipient!="" ', $params['user_id']);

    if ($params['recipient']) {
      $select = $select->where("recipient_name like ?", "%" . $params['recipient'] . "%");
    }

    if ($params['provider']) {
      if ($params['provider'] == 'email') {
        $select = $select->where("code!=0 and recipient!=0 and provider=''");
      }
      else {
        $select = $select->where('provider=?', $params['provider']);
      }
    }

    if ($params['date']) {
      if ($params['date'] == 'ASC')
        $select = $select->order('sent_date ASC');
      else
        $select = $select->order('sent_date DESC');
    } else {
      $select = $select->order('sent_date DESC');
    }

    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($params['ipp']);
    $paginator->setCurrentPageNumber($params['page']);
    return $paginator;
  }

  public function updateInvitation($invitation = array())
  {
    if (!is_array($invitation) || count($invitation) == 0) return false;

    $inviteSelect = $this->select()
      ->where("user_id = '{$invitation['user_id']}' && recipient = '{$invitation['recipient']}' && new_user_id = 0")
      ->limit(1);
    if (null !== ($inviteRow = $this->fetchRow($inviteSelect))) {
      $inviteRow->sender = $invitation['sender'];
      $inviteRow->recipient_name = $invitation['recipient_name'];
      $inviteRow->provider = $invitation['provider'];
      $inviteRow->code = $invitation['code'];
      $inviteRow->sent_date = $invitation['sent_date'];
      $inviteRow->message = $invitation['message'];

      return $inviteRow->save();
    }
    else
    {
      return false;
    }
  }

  public function insertInvitation($invitation = array())
  {
    if (!is_array($invitation) || count($invitation) == 0) return false;

    $inviteRow = $this->createRow();
    $inviteRow->user_id = $invitation['user_id'];
    $inviteRow->sender = $invitation['sender'];
    $inviteRow->provider = $invitation['provider'];
    $inviteRow->recipient = $invitation['recipient'];
    $inviteRow->recipient_name = $invitation['recipient_name'];
    $inviteRow->code = $invitation['code'];
    $inviteRow->sent_date = $invitation['sent_date'];
    $inviteRow->message = $invitation['message'];

    $result = $inviteRow->save();

    if ($result) {
      Engine_Hooks_Dispatcher::getInstance()->callEvent('onInviterSendInvite', $inviteRow);
    }
    return $result;
  }

  public function insertReferralInvitation($invitation = array())
  {
    if (!is_array($invitation) || count($invitation) == 0) return false;

    $inviteRow = $this->createRow();
    $inviteRow->user_id = $invitation['user_id'];
    $inviteRow->code = $invitation['code'];
    $inviteRow->referred_date = $invitation['referred_date'];

    $result = $inviteRow->save();

    if ($result) {
      Engine_Hooks_Dispatcher::getInstance()->callEvent('onInviterSendInvite', $inviteRow);
      $inviteRow->invite_id;
    }
    return $result;
  }
}
