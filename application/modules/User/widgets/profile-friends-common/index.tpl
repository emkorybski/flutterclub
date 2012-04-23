<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 9445 2011-10-28 01:16:46Z shaun $
 * @author     John
 */
?>


<ul>
  <?php foreach( $this->paginator as $user ): ?>
    <li><?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon', $user->getTitle()), array('title'=>$user->getTitle())) ?></li>
  <?php endforeach; ?>
</ul>
