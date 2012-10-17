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

class Inviter_Model_DbTable_Tokens extends Engine_Db_Table
{
  public function findUserToken($user_id = null, $provider = 'twitter', $active = 1)
  {
    if ($user_id === null) {
      $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    }

    if (!$user_id) {
      return false;
    }

    $select = $this->select()
      ->where('user_id = ?', $user_id)
      ->where('provider = ?', $provider)
      ->where('active = ?', $active);

    return $this->fetchRow($select);
  }

  public function getUserToken($user_id = null, $provider = 'twitter', $active = 1)
  {
    $tokenRow = $this->findUserToken($user_id, $provider, $active);

    if (!$tokenRow) {
      return false;
    }

    $token = new Zend_Oauth_Token_Access();
    $token->setParams(array(
      'token_id' => $tokenRow->token_id,
      'oauth_token' => $tokenRow->oauth_token,
      'oauth_token_secret' => $tokenRow->oauth_token_secret,
      'user_id' => $tokenRow->object_id,
      'screen_name' => $tokenRow->object_name
    ));

    return $token;
  }

  public function findUserTokenByObjectId($user_id = null, $object_id = null, $provider = 'twitter')
  {
    if ($user_id === null) {
      $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    }

    if (!$user_id) {
      return false;
    }

    $select = $this->select()
      ->where('user_id = ?', $user_id)
      ->where('provider = ?', $provider)
      ->where('object_id = ?', $object_id);

    return $this->fetchRow($select);
  }

  public function getUserTokenByObjectId($user_id = null, $object_id = null, $provider = 'twitter')
  {
    $tokenRow = $this->findUserTokenByObjectId($user_id, $object_id, $provider);

    if (!$tokenRow) {
      return false;
    }

    $token = new Zend_Oauth_Token_Access();
    $token->setParams(array(
      'token_id' => $tokenRow->token_id,
      'oauth_token' => $tokenRow->oauth_token,
      'oauth_token_secret' => $tokenRow->oauth_token_secret,
      'user_id' => $tokenRow->object_id,
      'screen_name' => $tokenRow->object_name
    ));

    return $token;
  }

  public function getUserTokenByArray($account_info)
  {
    $token = new Zend_Oauth_Token_Access();
    $token->setParams(array(
      'oauth_token' => $account_info['oauth_token'],
      'oauth_token_secret' => $account_info['oauth_token_secret'],
      'user_id' => $account_info['object_id'],
      'screen_name' => $account_info['object_name']
    ));

    return $token;
  }
}
