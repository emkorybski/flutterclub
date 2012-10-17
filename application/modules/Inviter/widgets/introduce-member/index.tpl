<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-02-08 14:58 ermek $
 * @author     Ermek
 */
?>

<?php
  $this->headScript()->appendFile('application/modules/Inviter/externals/scripts/core.js');
  $ajaxRequestUrl = $this->url(array('module' => 'inviter', 'controller' => 'introduce', 'action' => 'ajax-request'), 'default', true);

  $this->headScript()->appendScript('en4.core.runonce.add(function() {InviterIntroduce.url = ' . $this->jsonInline($ajaxRequestUrl) . ';});');
  
  $widget_uid = uniqid('introduce_member_');
?>

<div id="<?php echo $widget_uid; ?>" class="introduce_member_box">

  <div class="introduce_member_cont">
    <?php echo $this->render('_member_introduce.tpl'); ?>
  </div>
  
</div>