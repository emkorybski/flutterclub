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
 
 
 
class Article_Form_Create extends Engine_Form
{
  public $_error = array();

  public function init()
  {
    $this->setTitle('Post New Article')
      ->setDescription('Compose your new article below, then click "Post Article" to publish the article.')
      ->setAttrib('name', 'articles_create');


    $categories = Engine_Api::_()->getItemTable('article_category')->getMultiOptionsAssoc();
    
    $this->addElement('Select', 'category_id', array(
      'label' => 'Category',
      'multiOptions' => array(""=>"") + $categories,
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
      ),
      'filters' => array(
       'Int'
      ),
    ));
    
    $this->addElement('Text', 'title', array(
      'label' => 'Article Title',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '127')),
    )));

    $user = Engine_Api::_()->user()->getViewer();
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;

    //$allowed_html = Engine_Api::_()->authorization()->getPermission($user_level, 'article', 'auth_html');
    //$allowed_htmlattrs = str_replace(" ","",Engine_Api::_()->authorization()->getPermission($user_level, 'article', 'auth_htmlattrs'));

    if (Engine_Api::_()->authorization()->isAllowed('album', $user, 'create')){
      $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'upload-photo'), 'article_general', true);
    }    
    
    $this->addElement('TinyMce', 'body', array(
      'label' => 'Article Body',
      'required' => true,
      'allowEmpty' => false,
      'filters' => array(
        new Engine_Filter_Censor(),
      //  new Engine_Filter_Html(array('allowedTags'=>$allowed_html,'useDefaultLists'=>false,'allowedAttributes'=>$allowed_htmlattrs))
      ),
      'editorOptions' => array(
        'upload_url' => $upload_url,
        'remove_script_host' => '',
        'convert_urls' => '',
        'relative_urls' => '',
        'mode' => 'exact',
        'elements' => 'body',
        'width' => 500,
        'height' => 320,
        'media_strict' => false,
        'extended_valid_elements' => '*[*],**,object[width|height|classid|codebase|id|name],param[name|value],embed[src|type|width|height|flashvars|wmode|id|name],iframe[src|style|width|height|scrolling|marginwidth|marginheight|frameborder|id|name|class],video[src|type|width|height|flashvars|wmode|class|poster|preload|id|name],source[src]',
        'plugins' => "emotions, table, fullscreen, preview, paste, style, layer, xhtmlxtras",
        'theme_advanced_buttons1' => "cut,copy,paste,pastetext,pasteword,|,undo,redo,|,link,unlink,anchor,charmap,image,media,|,justifyleft,justifycenter,justifyright,justifyfull,|,hr,removeformat,code,preview",
        'theme_advanced_buttons2' => "bold,italic,underline,strikethrough,|,bullist,numlist,|,outdent,indent,|,tablecontrols",
        'theme_advanced_buttons3' => "formatselect,fontselect,fontsizeselect,|,forecolor,backcolor,|,styleprops,attribs,|,blockquote,sub,sup",
      )
    ));     
    
    
    // Description
    $this->addElement('Textarea', 'description', array(
      'label' => 'Short Description',
      'description' => "You may optionally enter article's excerpt or summary below",     
    ));
    $this->description->getDecorator("Description")->setOption("placement", "prepend");        
        
    
    
    $allowed_upload = Engine_Api::_()->authorization()->getPermission($user_level, 'article', 'photo');
    if($allowed_upload){
      $this->addElement('File', 'photo', array(
        'label' => 'Main Photo'
      ));
      $this->photo->addValidator('Extension', false, 'jpg,png,gif');
    }
    
    $this->addElement('Text', 'tags',array(
      'label'=>'Tags (Keywords)',
      'autocomplete' => 'off',
      'description' => 'Separate tags with commas.',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));
    $this->tags->getDecorator("Description")->setOption("placement", "append");    
    
    // Add subforms
    if (!$this->_item){
      $customFields = new Article_Form_Custom_Fields();
    }
    else $customFields = new Article_Form_Custom_Fields(array('item'=>$this->getItem()));
    
    $this->addSubForms(array(
      'customField' => $customFields
    ));
    
    // View
    $availableLabels = array(
      'everyone'              => 'Everyone',
      'registered'            => 'Registered Members',
      'owner_network'         => 'Friends and Networks',
      'owner_member_member'   => 'Friends of Friends',
      'owner_member'          => 'Friends Only',
      'owner'                 => 'Just Me'
    );
    
    
    $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('article', $user, 'auth_view');
    $options = array_intersect_key($availableLabels, array_flip($options));

    $this->addElement('Select', 'auth_view', array(
      'label' => 'Privacy',
      'description' => 'Who may see this article?',
      'multiOptions' => $options,
      'value' => 'everyone',
    ));
    $this->auth_view->getDecorator('Description')->setOption('placement', 'prepend');

    $options =(array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('article', $user, 'auth_comment');
    $options = array_intersect_key($availableLabels, array_flip($options));

    // Comment
    $this->addElement('Select', 'auth_comment', array(
      'label' => 'Comment Privacy',
      'description' => 'Who may post comments on this article?',
      'multiOptions' => $options,
      'value' => 'registered',
    ));
    $this->auth_comment->getDecorator('Description')->setOption('placement', 'prepend');

    $approval = (int) Engine_Api::_()->authorization()->getPermission($user_level, 'article', 'approval');
    
    if ($approval)
    {
      if (!$this->_item)
      {
        $this->addElement('Radio', 'published', array(
          'label' => 'Status',
          'allowEmpty' => false,
          'required' => true,
          'multiOptions' => array(
            "0"=>"Saved As Draft"
           ),
          'value' => '0',
          'description' => "Administrator will review and manually publish article once it is approved."
        ));
        $this->published->getDecorator('Description')->setOption('placement', 'prepend');
      }
    }
    else 
    {
      if (!$this->_item || ($this->_item && !$this->_item->published)) {
        $this->addElement('Radio', 'published', array(
          'label' => 'Status',
          'allowEmpty' => false,
          'required' => true,
          'multiOptions' => array(
           // ""=>"",
            "1"=>"Published",
            "0"=>"Saved As Draft"
           ),
          'value' => '1',
          'description' => "If article is published, it cannot be switched back to draft mode."
        ));
        $this->published->getDecorator('Description')->setOption('placement', 'prepend');
      }
    }
    
    

    
    $this->addElement('Checkbox', 'search', array(
      'label' => 'People can search for this article',
      'value' => True
    ));
    
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Post Article',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
    $button_group->addDecorator('DivDivDivWrapper');    

    
  }

}