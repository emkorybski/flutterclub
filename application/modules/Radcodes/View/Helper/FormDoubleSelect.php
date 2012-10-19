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
class Radcodes_View_Helper_FormDoubleSelect extends Zend_View_Helper_FormSelect
{

  protected $_defaultScript = 'application/modules/Radcodes/externals/scripts/doubleselect.js';
  
  public function formDoubleSelect($name, $value = null, $attribs = null, $options = null, $listsep = null)
  {
    $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
    extract($info); // name, value, attribs, options, listsep, disable
    
    $disabled = '';
    if (true === $disable) {
      $disabled = ' disabled="disabled"';
    }      
    
    $options['element_id'] = $this->view->escape($id);
    $options['value'] = $value;
    
    $this->_renderScriptHeader();
    $this->_renderScriptHandler($options);      
    
    $xhtml = '<select'
        . ' name="parent_' . $this->view->escape($name) . '"'
        . ' id="parent_' . $this->view->escape($id) . '"'
        . $disabled
        . $this->_htmlAttribs($attribs)
        . "></select>" . PHP_EOL;  
        
    $xhtml .= '<select'
        . ' name="child_' . $this->view->escape($name) . '"'
        . ' id="child_' . $this->view->escape($id) . '"'
        . $disabled
        . $this->_htmlAttribs($attribs)
        . "></select>" . PHP_EOL;     
        
    $xhtml .= $this->_hidden($name, $value, array('id' => $id));
    
    return $xhtml;
  }
  
  
  
  protected function _renderScriptHeader()
  {
    $script = $this->view->baseUrl() . '/' . $this->_defaultScript;
    $this->view->headScript()->appendFile($script);
    return $this;
  }
  
  protected function _renderScriptHandler($options = array())
  {
    $translator = $this->getTranslator();

    $element_id = $options['element_id'];
    $value = $options['value'];
    
    foreach ($options['parentOptions'] as $k => $v) {
      $options['parentOptions'][$k] = $translator->translate($v);
    }
    
    foreach ($options['multiChildOptions'] as $k => $vs) {
      foreach ($vs as $kk => $vv) {
        $options['multiChildOptions'][$k][$kk] = $translator->translate($vv);
      }
    }
    
    $json_parentOptions = Zend_Json_Encoder::encode($options['parentOptions']);
    $json_multiChildOptions = Zend_Json_Encoder::encode($options['multiChildOptions']);
 
    $js_parentMessage = $this->view->string()->quoteJavascript($translator->translate($options['defaultParentMessage']), false);
    $js_childMessage = $this->view->string()->quoteJavascript($translator->translate($options['defaultChildMessage']), false);
      
    $script = '      en4.core.runonce.add(function(){' . PHP_EOL;
  
    $script .=<<<JSSCRIPT
      
        var doubleSelect_$element_id = new radcodesDoubleSelect('$element_id', {
          parentOptions : $json_parentOptions,
          multiChildOptions : $json_multiChildOptions,
          defaultParentMessage : $js_parentMessage,
          defaultChildMessage : $js_childMessage,
          defaultValue: "$value"      
        });
      
        
JSSCRIPT;
  
    $script .= '});' . PHP_EOL;
  
    $this->view->headScript()->appendScript($script); 
    return $this;
  }
  

}
// Radcodes_View_Helper_FormDoubleSelect
