<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 9301 2011-09-21 21:34:34Z john $
 * @author     John
 */
?>

<div id="<?php echo $this->tmpId ?>">
</div>

<?php echo $this->action('index', 'index', 'chat', array(
  'tmpId' => $this->tmpId,
  'no-content' => 1,
)) ?>