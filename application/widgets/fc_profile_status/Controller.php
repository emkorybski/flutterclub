<?php
class Widget_FC_Profile_StatusController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		// Don't render this if not authorized
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!Engine_Api::_()->core()->hasSubject()) {
			return $this->setNoRender();
		}

		// Get subject and check auth
		$subject = Engine_Api::_()->core()->getSubject('user');
		//if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
		//  return $this->setNoRender();
		//}

		$this->view->auth = ($subject->authorization()->isAllowed(null, 'view'));
	}
}