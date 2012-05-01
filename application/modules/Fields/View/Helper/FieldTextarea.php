<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: FieldTextarea.php 9669 2012-04-02 22:28:19Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @author     John
 */
class Fields_View_Helper_FieldTextarea extends Fields_View_Helper_FieldAbstract
{
  public function fieldTextarea($subject, $field, $value)
  {
    return nl2br($this->view->string()->chunk($value->value));
  }
}