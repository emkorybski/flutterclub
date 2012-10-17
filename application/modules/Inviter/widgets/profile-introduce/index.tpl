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

  $editUrl = $this->url(array('module' => 'inviter', 'controller' => 'introduce', 'action' => 'edit'), 'default', true);
  $ajaxRequestUrl = $this->url(array('module' => 'inviter', 'controller' => 'introduce', 'action' => 'ajax-request'), 'default', true);

  $this->headScript()->appendScript('en4.core.runonce.add(function() {InviterIntroduce.url = ' . $this->jsonInline($ajaxRequestUrl) . ';});');
  $this->headTranslate(array(
    'INVITER_Your information successfully saved',
    'INVITER_Failed! Please type few words about you.'
  ));

  $widget_uid = uniqid('profile_introduce_');
?>

<div id="<?php echo $widget_uid; ?>">

  <div class="introduce_yourself_cont">
    <a href="javascript:void(0);" class="hide_introduce_btn" onclick="InviterIntroduce.hide('<?php echo $widget_uid; ?>')"></a>
    <div class="introduce_title"><?php echo $this->translate('INVITER_Profile Introduce'); ?></div>

      <div class="introduce_member_body">
          <?php echo $this->userIntroduce->body; ?>
      </div>

    <div class="clr"></div>
  </div>

  <div class="display_none">
    <div class="introduce_form_tpl">
      <h3><?php echo $this->translate('INVITER_Profile Introduce'); ?></h3>

      <textarea rows="5" cols="50" name="body" class="introduce_body"><?php echo $this->userIntroduce->body; ?></textarea>
      <input type="hidden" name="widget_id" value="<?php echo $widget_uid; ?>"/>
      <div style="height:10px;"></div>

      <button onclick="InviterIntroduce.save(this);"><?php echo $this->translate('Save'); ?></button>
      <button onclick="Smoothbox.close();"><?php echo $this->translate('Cancel'); ?></button>
    </div>
  </div>

</div>