<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 9673 2012-04-11 22:49:36Z richard $
 * @author     John
 */
?>

<div class="content sdk">


  <h3>SocialEngine SDK</h3>

  <p>The SocialEngine 4 SDK allows you to create packages for distribution.</p>
  <p>More info: <a href="http://www.socialengine.net/support/documentation/article?q=132&question=SocialEngine-SDK" target="_blank">See KB article</a>.</p>	
  <br />
  <a class="buttonlink sdk_packages_add" href="<?php echo $this->url(array('action' => 'create')) ?>">
    Create a Package
  </a>
  <p class="buttontext">
    Sets up bare-bones modules, widgets, and more for your local development
    environment.
  </p>
  
  <a class="buttonlink sdk_packages_build" href="<?php echo $this->url(array('action' => 'build')) ?>">
    Build Packages
  </a>
  <p class="buttontext">
    Turns your packages into files that are installable and ready for
    distribution.
  </p>

  <a class="buttonlink sdk_packages_manage" href="<?php echo $this->url(array('action' => 'manage')) ?>">
    Manage Package Files
  </a>
  <p class="buttontext">
    Download package files you've built, combine packages, or delete ones you
    don't want.
  </p>
  
</div>