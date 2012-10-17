<?php


/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Article
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
?>

  <form method="post" class="global_form_popup">
    <div>
      <h3><?php echo $this->translate("Sponsored Article?") ?></h3>
      <p>
        <?php echo $this->translate("ARTICLE_VIEWS_SCRIPTS_ADMINMANAGE_SPONSORED_DESCRIPTION") ?>
      </p>
      <br />
      <p>
        <input type="hidden" name="confirm" value="<?php echo $this->article_id?>"/>

        <button type='submit' name="sponsored" value="yes"><?php echo $this->translate("Yes") ?></button>
        <button type='submit' name="sponsored" value="no"><?php echo $this->translate("No") ?></button>

        <?php echo $this->translate("or") ?>
        <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
        <?php echo $this->translate("cancel") ?></a>
      </p>
    </div>
  </form>

<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>
