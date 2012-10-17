<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: add.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     Sami
 */
?>
<?php if (!$this->img_url): ?>
<?php echo $this->form->render($this) ?>
<?php else : ?>
<div style="padding: 10px;"><form action="" method="post">
    <p><?php echo $this->translate('INVITER_Orkut_captcha_description'); ?></p>
    <div style="padding-left: 10px; margin: 0 auto; height: 60px;">
        <div style="float: left;">
            <img width="150" src="<?php echo $this->img_url;?>">
        </div>
        <div style="float:left; margin-top: 10px;">
            <input type="text" name="captcha_value">
            <br/>
            <?php echo $this->translate('INVITER_Orkut_capthca'); ?>
        </div>
        <input type="hidden" value="<?php echo $this->captcha_token; ?>" name="captcha_token">
    </div>
    <div style="margin: 0 auto; display: table; padding-top: 10px;">
        <button type="submit" name="submit" value="submit">Submit</button>
    </div></form>
</div>
<?php endif; ?>