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
 
 
 
class Article_Form_Filter_Manage extends Article_Form_Search
{
  protected $_fieldType = 'article';
  
  public function init()
  {
    parent::init();

    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'article_manage',true));
    $this->removeElement('user');
  }

}
