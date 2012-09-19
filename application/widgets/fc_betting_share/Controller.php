<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('Share.php');
require_once('custom/config.php');
require_once(PATH_DOMAIN . 'user.php');
require_once(PATH_DOMAIN . 'event.php');
require_once(PATH_DOMAIN . 'selection.php');

class Widget_FC_Betting_ShareController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		if (!$this->getRequest()->isPost()) {
			$this->view->form = $form = new FC_Betting_Share();
			$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
			return;
		}

		$idSelection = $this->getRequest()->getQuery('id');
		$message = $this->getRequest()->getPost('message');

		$queryStringData = array(
			'name' => 'fc_template',
			'id_selection' => $idSelection,
			'message' => $message,
			'template' => 'activity.bet_share',
			'format' => 'html');
		$templateWidgetUrl = WEB_HOST . WEB_ROOT . "widget?" . http_build_query($queryStringData);
		$activityText = trim(file_get_contents($templateWidgetUrl));

		$seUserId = \bets\User::getSocialEngineUserId(\bets\User::getCurrentUser()->id);
		$seUser = \Engine_Api::_()->user()->getUser($seUserId);
		\Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($seUser, $seUser, 'status', $activityText);

		$request = $this->getRequest();
		$request->setModuleName('core');
		$request->setControllerName('utility');
		$request->setActionName('success')->setDispatched(false);
		$request->setParams(array(
			'smoothboxClose' => 3000,
			'parentRefresh' => false,
			'messages' => array('Your bet has been shared')
		));

		$this->setNoRender();
	}
}