<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Nonefriends.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_Model_DbTable_Nonefriends extends Engine_Db_Table
{
  protected $_name = 'inviter_nonefriends';

  public function getNonefriends(User_Model_DbTable_Users $user)
  {
    $select = $this->select()->where('user_id = ?', $user->getIdentity)->limit(1);
    $nonfriends = $this->fetchRow($select);

    $nonfriend_ids = (isset($nonfriends->nonefriend_ids) && trim($nonfriends->nonefriend_ids) != '')?$nonfriends->nonefriend_ids:0;

     $userTb = Engine_Api::_()->getItemTable('user');
     $userSl = $userTb->select()->where("user_id IN ($nonfriend_ids)");

     return $userTb->fetchAll($userSl);
  }

  public function getSuggests($params = array('current_suggests' =>null, 'noneFriend_id'=>0, 'total_suggests' => 32))
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $noneFriendsSL = $this->select()
     ->where('user_id = ?', $viewer->getIdentity());
    $noneFriends = $this->fetchRow($noneFriendsSL);

    if ($params['noneFriend_id'])
    {
      if (is_null($noneFriends))
      {
        $noneFriends = $this->createRow(array(
          'user_id'=>$viewer->getIdentity(),
          'nonefriend_ids'=>$params['noneFriend_id'],
        ));
      }
      else
      {
        $noneFriends->nonefriend_ids = ($noneFriends->nonefriend_ids && trim($noneFriends->nonefriend_ids) != '')?$noneFriends->nonefriend_ids.','.$params['noneFriend_id']:$params['noneFriend_id'];
        $noneFriends->nonefriend_ids = implode(',', array_unique(explode(',', $noneFriends->nonefriend_ids)));
      }

      $noneFriends->save();
    }

    $noneFriendsCount = (isset($noneFriends->nonefriend_ids))?count(explode(',', $noneFriends->nonefriend_ids)):0;

    $membershipTb = Engine_Api::_()->getDbtable('membership', 'user');

    try {
      $membershipSl = $membershipTb->select()
        ->setIntegrityCheck(false)
        ->from($membershipTb->info('name'), array('user_id'))
        ->where('resource_id = ?', $viewer->getIdentity());
//        ->where('active = ?', 1); // - если раскоментить, в результат будут включены и те, кому я отправил friend request

      $friend_list = $membershipTb->getAdapter()->fetchCol($membershipSl);
    }
    catch (Exception $e) {}

    $friends = ($friend_list) ? implode(',', $friend_list) : 0;

    $noneFriends = (isset($noneFriends->nonefriend_ids) && trim($noneFriends->nonefriend_ids) != '') ? $noneFriends->nonefriend_ids : 0;
    $noneFriends = (is_null($params['current_suggests']) || trim($params['current_suggests']) == '') ? $noneFriends:$noneFriends . ',' . $params['current_suggests'];

    $_friends_array = explode(',', $friends);
    $friends_array = array();
    foreach ($_friends_array as $friend_id) {
      if ($friend_id !== '') {
        $friends_array[] = $friend_id;
      }
    }

    $friends_array = (count($friends_array) > 0) ? $friends_array : array(0);
    $friends = implode(',', $friends_array);

    $noneFriends_array = explode(',', $noneFriends);
    $_noneSuggests_array = array_unique(array_merge($friends_array, $noneFriends_array, array($viewer->getIdentity())));
    $noneSuggests_array = array();

    foreach ($_noneSuggests_array as $friend_id) {
      if ($friend_id !== '') {
        $noneSuggests_array[] = $friend_id;
      }
    }

    $noneSuggests_array = (count($noneSuggests_array) > 0) ? $noneSuggests_array : array(0);
    $noneSuggests = implode(',', $noneSuggests_array);

    $suggestsSl = $membershipTb->select()
      ->setIntegrityCheck(false)
      ->from($membershipTb->info('name'), array('user_id', 'GROUP_CONCAT(`resource_id`) as mutual_friends' ))
      ->where("resource_id IN({$friends})")
      ->where("user_id NOT IN({$noneSuggests})")
      ->where('active = ?', 1)
      ->group('user_id');

    $allSuggests = $membershipTb->fetchAll($suggestsSl);

    $suggest_ids = array();
    $mutualFriends = array();

    if (isset($allSuggests) && $allSuggests->count()>0)
    {
      foreach ($allSuggests as $suggest)
      {
        $suggest_ids[] = $suggest->user_id;
        $mutualFriends[$suggest->user_id] = $suggest->mutual_friends;
      }
    }
    $rand_suggest_ids = $suggest_ids;
    shuffle($rand_suggest_ids);

    $rand_suggest_list = array();
    for ($index = 0; ($index < $params['total_suggests'] && $index < count($rand_suggest_ids)); $index++) {
      $rand_suggest_list[$index] = $rand_suggest_ids[$index];
    }

    $suggest_ids = (count($rand_suggest_list) > 0) ? implode(',', $rand_suggest_list) : 0;

    $userTb = Engine_Api::_()->getItemTable('user');

    try {

      if (isset($params['widget']) && $params['widget']){
        return array(
        'suggestsSl'=> $userTb->select()
          ->where("user_id IN({$suggest_ids})")
          ->order('RAND()'),
        'mutual_friends'=>$mutualFriends);
      }

      $suggestSl = $userTb->select()
      ->where("user_id IN({$suggest_ids})")
      ->limit($params['total_suggests'])
      ->order('RAND()');


    $suggests = ($params['noneFriend_id'])?$userTb->fetchRow($suggestSl):$userTb->fetchAll($suggestSl);
    }

    catch (Exception $e)
    {

    }

    return array(
      'suggests'=>$suggests,
      'mutual_friends'=>$mutualFriends,
      'noneFriendCount'=>$noneFriendsCount);
  }
}
