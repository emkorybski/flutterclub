<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Radcodes
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
 
class Radcodes_Form_Admin_Category_Edit extends Radcodes_Form_Admin_Category_Create
{

  public function init()
  {
    parent::init();
    $this->setTitle('Edit Category')
         ->setDescription('Please fill out the form below to update category.');

    $this->submit->setLabel('Save Changes');
  }
  
}