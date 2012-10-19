<?php
class News_Form_Edit extends Engine_Form
{
  public function init()
  {
     // Init form
    $this
      ->setTitle('Edit news information')
      ->setAttrib('class',   '')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('format'=>'smoothbox'), 'news_edit_news'))
      ;
      $content_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('content_id');
     $news_info = Engine_Api::_()->getItem('news_content', $content_id);
     $this->addElement('Hidden', 'content_id', array(
      'value' => $news_info->content_id,
    ));
    
    // Init name
    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'required' => true,
      'style' =>      "width:546px",   
      'value' =>$news_info->title,
      'description'  => 'Description'
    ));
    $this->title->getDecorator('Description')->setOption('placement', 'append');
    $this->addElement('TinyMce', 'description', array(
      'disableLoadDefaultDecorators' => true,
      'value'    => $news_info->description,
      'decorators' => array(
        'ViewHelper'
      ),
      'filters' => array(),
    ));
    
    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'decorators' => array(
          array('ViewScript', array(
                'viewScript' => '_formButtonCancel.tpl',
                'class'      => 'form element'
          ))
      ),
    ));
  }
  public function saveValues()
  { 
    $translate = Zend_Registry::get('Zend_Translate');
    $values = $this->getValues();
    $news     = Engine_Api::_()->getItem('news_content', $values['content_id']);
    $news->title  = $values['title'];
    $news->description  = $values['description'];
    $news->save();
  }
}
