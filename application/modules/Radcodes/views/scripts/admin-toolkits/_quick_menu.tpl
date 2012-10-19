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
?>


      <ul style="float: right">
        <li><?php echo $this->htmlLink($this->url(array('action'=>'delete-page')),
          $this->translate("Delete Content Page")
        ); ?></li>
        <li><?php echo $this->htmlLink($this->url(array('action'=>'run-installer-function')),
          $this->translate("Execute Installer Function")
        ); ?></li>
      </ul>
