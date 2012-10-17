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
 
 
 
class Article_Form_Admin_Level extends Authorization_Form_Admin_Level_Abstract
{
  
  public function init()
  {
    parent::init();
    
    $this
      ->setTitle('Member Level Settings')
      ->setDescription("These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.");
      
    $this->addElement('Radio', 'view', array(
      'label' => 'Allow Viewing of Articles?',
      'description' => 'Do you want to let members view articles? If set to no, some other settings on this page may not apply.',
      'multiOptions' => array(
        2 => 'Yes, allow viewing of all articles, even private ones.',
        1 => 'Yes, allow viewing of articles.',
        0 => 'No, do not allow articles to be viewed.',
      ),
      'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    if( !$this->isModerator() ) {
      unset($this->view->options[2]);
    }
    
    if( !$this->isPublic() ) 
    {
      
	    $this->addElement('Radio', 'create', array(
	      'label' => 'Allow Creation of Articles?',
	      'description' => 'Do you want to let members create articles? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view articles, but only want certain levels to be able to create articles.',
	      'multiOptions' => array(
	        1 => 'Yes, allow creation of articles.',
	        0 => 'No, do not allow articles to be created.'
	      ),
	      'value' => 1,
	    ));    
	    
	    $this->addElement('Radio', 'edit', array(
	      'label' => 'Allow Editing of Articles?',
	      'description' => 'Do you want to let members edit articles? If set to no, some other settings on this page may not apply.',
	      'multiOptions' => array(
	        2 => 'Yes, allow members to edit all articles.',
	        1 => 'Yes, allow members to edit their own articles.',
	        0 => 'No, do not allow members to edit their articles.',
	      ),
	      'value' => ( $this->isModerator() ? 2 : 1 ),
	    ));
      if( !$this->isModerator() ) {
        unset($this->edit->options[2]);
      }
      
	    $this->addElement('Radio', 'delete', array(
	      'label' => 'Allow Deletion of Articles?',
	      'description' => 'Do you want to let members delete articles? If set to no, some other settings on this page may not apply.',
	      'multiOptions' => array(
	        2 => 'Yes, allow members to delete all articles.',
	        1 => 'Yes, allow members to delete their own articles.',
	        0 => 'No, do not allow members to delete their articles.',
	      ),
	      'value' => ( $this->isModerator() ? 2 : 1 ),
	    ));
      if( !$this->isModerator() ) {
        unset($this->delete->options[2]);
      }
	
      // Element: comment
      $this->addElement('Radio', 'comment', array(
        'label' => 'Allow Commenting on Articles?',
        'description' => 'Do you want to let members of this level comment on articles?',
        'multiOptions' => array(
          2 => 'Yes, allow members to comment on all articles, including private ones.',
          1 => 'Yes, allow members to comment on articles.',
          0 => 'No, do not allow members to comment on articles.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->comment->options[2]);
      }
      

      // Element: photo
      $this->addElement('Radio', 'photo', array(
        'label' => 'Allow Uploading of Photos?',
        'description' => 'Do you want to let members upload photos to a article? If set to no, the option to upload photos will not appear.',
        'multiOptions' => array(
          2 => 'Yes, allow photo uploading to all articles, including private ones.',
          1 => 'Yes, allow photo uploading to articles.',
          0 => 'No, do not allow photo uploading.'
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->photo->options[2]);
      } 
	    
	    $this->addElement('Radio', 'approval', array(
	      'label' => 'Approval for Published Articles?',
	      'description' => 'Would you like to manually publish articles?',
	      'multiOptions' => array(
	        1 => 'Yes, articles will auto save as Draft, admin will manually publish them',
	        0 => 'No, let member decide whether to save as Draft or Published'
	      ),
	      'value' => 0,
	    ));
	    
	    $this->addElement('Radio', 'featured', array(
	      'label' => 'Mark as Featured on Creation?',
	      'description' => 'Would you like to mark article as Featured when created?',
	      'multiOptions' => array(
	        1 => 'Yes, mark article as Featured when created.',
	        0 => 'No, do not mark article as Featured when created.'
	      ),
	      'value' => 0,
	    ));
	    
	    $this->addElement('Radio', 'sponsored', array(
	      'label' => 'Mark as Sponsored on Creation?',
	      'description' => 'Would you like to mark article as Sponsored when created?',
	      'multiOptions' => array(
	        1 => 'Yes, mark article as Sponsored when created.',
	        0 => 'No, do not mark article as Sponsored when created.'
	      ),
	      'value' => 0,
	    ));
      
	    // PRIVACY ELEMENTS
	    $this->addElement('MultiCheckbox', 'auth_view', array(
	      'label' => 'Articles Article Privacy',
	      'description' => 'Your members can choose from any of the options checked below when they decide who can see their articles. These options appear on your members\' "Add Articles" and "Edit Entry" pages. If you do not check any options, everyone will be allowed to view articles.',
	        'multiOptions' => array(
	          'everyone'            => 'Everyone',
	          'registered'          => 'Registered Members',
	          'owner_network'       => 'Friends and Networks',
	          'owner_member_member' => 'Friends of Friends',
	          'owner_member'        => 'Friends Only',
	          'owner'               => 'Just Me'
	        ),
	        'value' => array('everyone', 'registered', 'owner_network','owner_member_member', 'owner_member', 'owner')
	    ));
	
	    $this->addElement('MultiCheckbox', 'auth_comment', array(
	      'label' => 'Article Comment Options',
	      'description' => 'Your members can choose from any of the options checked below when they decide who can post comments on their articles. If you do not check any options, everyone will be allowed to post comments on articles.',
	      'description' => '',
	        'multiOptions' => array(
	          'registered'          => 'Registered Members',
	          'owner_network'       => 'Friends and Networks',
	          'owner_member_member' => 'Friends of Friends',
	          'owner_member'        => 'Friends Only',
	          'owner'               => 'Just Me'
	        ),
	        'value' => array('registered', 'owner_network','owner_member_member', 'owner_member', 'owner')
	    ));	    
	    

      /*
      $this->addElement('Text', 'auth_html', array(
        'label' => 'HTML in Articles?',
        'description' => 'ARTICLE_FORM_ADMIN_LEVEL_HTML_DESCRIPTION',
        'value'=> 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr'
      ));    
    
      // Element: auth_htmlattrs
      $this->addElement('Text', 'auth_htmlattrs', array(
        'label' => 'HTML Attributes in WYSIWYG Editor',
        'description' => 'ARTICLE_FORM_ADMIN_LEVEL_HTMLATTRS_DESCRIPTION',
        'class' => 'long',
        'value'=> 'href, src, alt, border, align, width, height, vspace, hspace, target, style, name, value, id, title, class, colspan, type, allowscriptaccess, allowfullscreen, rows, cols, size, language'
      ));    
	    */
	    
      $this->addElement('Text', 'max', array(
        'label' => 'Maximum Allowed Articles',
        'description' => 'Enter the maximum number of allowed articles. The field must contain an integer. Enter 0 for unlimited articles.',
        'class' => 'short'
      ));      
      
    } // end isPublic()
  }
  
}