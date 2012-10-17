<?php
class Friendsinviter_Form_Element_MultiText extends Engine_Form_Element_Text
{
    public $helper = 'formMultiText';

    protected $_separator = '<li>';

    public function loadDefaultDecorators()
    {
      if( $this->loadDefaultDecoratorsIsDisabled() )
      {
        return;
      }
      $decorators = $this->getDecorators();
      if( empty($decorators) )
      {
        $this->addDecorator('ViewHelper');
        Engine_Form::addDefaultDecorators($this);
      }
    }
}

