<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Article
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
 
class Article_Widget_ListSearchController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
  	
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->form = $form = new Article_Form_Search();

    $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(),'article_browse', true));
    
    $request = Zend_Controller_Front::getInstance()->getRequest();
  	$params = $request->getParams();
  	
  	foreach (array('action','module','controller','rewrite') as $system_key) {
  	  unset($params[$system_key]);
  	}    
    
    // Populate form data
    if( $form->isValid($params) )
    {
      $params = $form->getValues();
    } 
    
    
  }

}