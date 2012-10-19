<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-09-07 17:53 idris $
 * @author     Idris
 */

?>

<div class="he_widget">
  <div class="he_friends_widget">
    <div class="he_widget_header">
      <div class="he_widget_see_all">
        <?php echo $this->htmlLink('javascript:he_friend.see_all("mutual")', $this->translate('See All'), array('class' => 'he_see_all', 'id' => 'he_friend_see_all')); ?>
        (<?php echo $this->friends->getTotalItemCount(); ?>)
      </div>
      <div class="clr"></div>
    </div>
    <div class="clr"></div>
    <div class="he_widget_container" >
      <?php echo $this->render('_friends_list.tpl'); ?>
    </div>
  </div>
</div>