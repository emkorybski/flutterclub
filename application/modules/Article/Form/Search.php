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
 
 
 
class Article_Form_Search extends Fields_Form_Search
{
  protected $_fieldType = 'article';
  
  public function init()
  {
    parent::init();

    $this->loadDefaultDecorators();

    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'articles_browse_filters field_search_criteria',
      ))
      ->getDecorator('HtmlTag')
        ->setOption('class', 'browsearticles_criteria articles_browse_filters');
    
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'article_browse',true));
    
    // Add custom elements
    $this->getAdditionalOptionsElement();
  }

  public function getAdditionalOptionsElement()
  {
    $i = -5000;
    
    $this->addElement('Hidden', 'tag', array(
      'order' => $i++,
    ));
    
    $this->addElement('Hidden', 'user', array(
      'order' => $i++,
    ));
    
    $this->addElement('Hidden', 'start_date', array(
      'order' => $i++,
    ));

    $this->addElement('Hidden', 'end_date', array(
      'order' => $i++,
    ));

    $this->addElement('Text', 'keyword', array(
      'label' => 'Keywords',
      'order' => $i++,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
    )); 
    
    $categories = Engine_Api::_()->getItemTable('article_category')->getMultiOptionsAssoc();
        
    $this->addElement('Select', 'category', array(
      'label' => 'Category',
      'multiOptions' => array(""=>"All Categories") + $categories,
      'order' => $i++,
      'filters' => array(
        new Radcodes_Lib_Filter_Null()
      ),    
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));
    
    $this->addElement('Select', 'order', array(
      'label' => 'Browse By',
      'multiOptions' => array(
        'recent' => 'Most Recent',
        'lastupdated' => 'Last Updated',
        'alphabet' => 'Title',
        'mostviewed' => 'Most Viewed',
        'mostcommented' => 'Most Commented',
        'mostliked' => 'Most Liked',
      ),
      'order' => $i++,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),      
    ));

    if (count($this->_fieldElements)) {
      $this->_order['separator1'] = $i++;
    }
    else {
      $this->removeElement('separator1');
    }    
    
    $j = 10000000;  
    
    $this->addElement('Button', 'done', array(
      'label' => 'Search',
      'type' => 'submit',
      'ignore' => true,
      'order' => $j++,
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));
    
  }
}
