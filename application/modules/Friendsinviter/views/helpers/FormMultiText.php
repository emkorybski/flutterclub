<?php

class Friendsinviter_View_Helper_FormMultiText extends Zend_View_Helper_FormElement { //Zend_View_Helper_Abstract {

    protected $_inputType = 'text';

    protected $_isArray = true;


    public function formMultiText($name, $value = null, $attribs = null,
        //$options = null, $listsep = "<br />\n")
        $options = null, $listsep = "<li>") // where <li> comes from?
    {

        $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
        extract($info); // name, value, attribs, options, listsep, disable


        // the radio button values and labels
        //$options = (array) $options;
        $options = (array) $value;

        // build the element
        $xhtml = '';
        $list  = array();

        // should the name affect an array collection?
        $name = $this->view->escape($name);
        if ($this->_isArray && ('[]' != substr($name, -2))) {
            $name .= '[]';
        }

        // ensure value is an array to allow matching multiple times
        $value = (array) $value;

        // XHTML or HTML end tag?
        $endTag = ' />';
        if (($this->view instanceof Zend_View_Abstract) && !$this->view->doctype()->isXhtml()) {
            $endTag= '>';
        }
        
        $min = 5;
        $addmore = true;
        
        // add radio buttons to the list.
        // require_once 'Zend/Filter/Alnum.php';
        $filter = new Zend_Filter_Alnum();

        $key = 0;
        do {
          $value = isset($options[$key]) ? $options[$key] : '' ;


            // is it disabled?
            $disabled = '';
            if (true === $disable) {
                $disabled = ' disabled="disabled"';
            }


            // generate ID
            $optId = $id . '-' . $key;

            $input = '<input type="' . $this->_inputType . '"'
                    . ' name="' . $name . '"'
                    . ' id="' . $optId . '"'
                    . ' value="' . $this->view->escape($value) . '"'
                    . $disabled
                    . $this->_htmlAttribs($attribs)
                    . $endTag;

            // add to the array of radio buttons
            $list[] = $input;
            
        } while(isset($options[$key++]) || ($key < $min));
        
        $list[] =   '<div id="' . $name . '_addmorerow" name="' . $name . '_addmorerow"><a href="" onclick="semods_add_row(\'' . $name . '\'); this.blur(); return false;"> ' . '+ Add More ' . '</a></div>';

        // done!
        if( '<li>' === $listsep ) {
          $xhtml .= "<ul class=\"form-options-wrapper\">\n<li>" . implode("</li>\n<li>", $list) . "</li>\n</ul>\n";
        } else {
          $xhtml .= implode($listsep, $list);
        }
        
        $xhtml .= <<< EOC
<div style="display:none">
  <span id="{$name}_template" name="{$name}_template">
EOC;

        if( '<li>' === $listsep ) {
          $xhtml .= '<li>';
        }

        $xhtml .= <<< EOC

  <input name='{$name}' value='' type="text" class="text">
EOC;
        if( '<li>' === $listsep ) {
          $xhtml .= '</li>';
        }
        $xhtml .= <<< EOC
  </span>
</div>
EOC;
        

      
        return $xhtml;
    }

}

