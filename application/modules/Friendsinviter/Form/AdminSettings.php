<?php

class Friendsinviter_Form_AdminSettings extends Engine_Form
{
  public $saved_successfully = FALSE;

  public function init()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    
    $this
      ->setTitle('Global Settings')
      ->setDescription('Global Friends Inviter Settings.');


    $this->addElement('Text', 'invite_api_key', array(
      'label' => 'API Key',
      'description' => '',
      'value' => $settings->getSetting('friendsinviter.invite_api_key', ''),
    ));

    list($top_domains, $top_networks) = Engine_Api::_()->getApi('core', 'friendsinviter')->fi_get_top_services();

    $this->addElement('Text', 'invite_secret', array(
      'label' => 'API Secret',
      'description' => '',
      'value' => $settings->getSetting('friendsinviter.invite_secret', '')
    ));

    $field = new Friendsinviter_Form_Element_MultiText('top_domains');
    $field->setLabel('Top Domains')
      ->setDescription("100010275")
      ->setValue($top_domains);

    $this->addElement($field);

    
    $top_networks_field = array();
    $top_networks_value = array();
    
    foreach($top_networks as $top_network) {
      $top_networks_field[$top_network['n']] = $top_network['d'];
      if($top_network['e']) {
        $top_networks_value[] = $top_network['n'];
      }
    }

    $this->addElement('MultiCheckbox', 'top_networks', array(
      'label' => 'Top Social Networks',
      'description' => 'TOP_SOCIAL_NETWORKS',
      'multiOptions' => $top_networks_field,
      'value' => $top_networks_value
    ));

    
    $filter_emails = explode( ',', $settings->getSetting('friendsinviter.invite_filteremails','') );
    
    $field = new Friendsinviter_Form_Element_MultiText('filter_emails');
    $field->setLabel('100010284')
      ->setDescription("100010285")
      ->setValue($filter_emails);

    $this->addElement($field);




    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
    
  }
  
  
  public function saveAdminSettings()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    
    $value = $this->getElement('invite_api_key')->getValue();
    if(!preg_match('/^[A-Fa-f0-9]{32}$/',$value)) {
      $this->addError('100010334');
      return false;
    }
    $settings->setSetting('friendsinviter.invite_api_key', $value);

    $value = $this->getElement('invite_secret')->getValue();
    if(!preg_match('/^[A-Fa-f0-9]{32}$/',$value)) {
      $this->addError('100010335');
      return false;
    }
    $settings->setSetting('friendsinviter.invite_secret', $value);

    $value = $this->getElement('top_domains')->getValue();
    $value = $this->remove_array_empty_values( $value );
    $this->getElement('top_domains')->setValue($value);
                                              

    $domains = implode(',',  $value );
    
    $settings->setSetting('friendsinviter.invite_topdomains', $domains);

    $value = $this->getElement('top_networks')->getValue();

    list($top_domains, $top_networks) = Engine_Api::_()->getApi('core', 'friendsinviter')->fi_get_top_services();
    
    foreach($top_networks as &$top_network) {
      $top_network['e'] = in_array($top_network['n'],$value);
    }
    
    $settings->setSetting('friendsinviter.invite_topnetworks', $top_networks);


    $value = $this->getElement('filter_emails')->getValue();
    $value = $this->remove_array_empty_values( $value );
    $this->getElement('filter_emails')->setValue($value);
                                              

    $filter_emails = implode(',',  $value );
    
    $settings->setSetting('friendsinviter.invite_filteremails', $filter_emails);
    
    
    $this->saved_successfully = true;

  }
  
  function remove_array_empty_values($array, $remove_null_number = true) {
    $new_array = array();

    $null_exceptions = array();

    foreach ($array as $key => $value) {
      $value = trim($value);

      if($remove_null_number)
        $null_exceptions[] = '0';

      if(!in_array($value, $null_exceptions) && $value != "")
        $new_array[] = $value;
    }

    return $new_array;
  }

  
}