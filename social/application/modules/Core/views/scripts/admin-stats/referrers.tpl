<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: referrers.tpl 9684 2012-04-18 02:07:28Z richard $
 * @author     John
 */
?>

<h2><?php echo $this->translate("Top Referring Sites") ?></h2>
<p>
  <?php echo $this->translate("CORE_VIEWS_SCRIPTS_ADMINSTATS_REFERRERS_DESCRIPTION") ?>
</p>

<?php
  $settings = Engine_Api::_()->getApi('settings', 'core');
  if( $settings->getSetting('user.support.links', 0) == 1 ) {
    echo 'More info: <a href="http://www.socialengine.net/support/documentation/article?q=220&question=Admin-Panel---Stats--Referring-URLs" target="_blank">See KB article</a>.';	
  } 
?>	

<br />
<br />

<script type="text/javascript">
  var clearReferrers = function() {
    if( !confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to clear the referrers?")) ?>') ) {
      return;
    }
    var url = '<?php echo $this->url(array('action' => 'clear-referrers')) ?>';
    var request = new Request.JSON({
      url : url,
      data : {
        format : 'json'
      },
      onComplete : function() {
        window.location.replace( window.location.href );
      }
    });
    request.send();
  }
</script>

<?php if( count($this->referrers) > 0 ): ?>

  <div>
    <?php echo $this->htmlLink('javascript:void(0);', 'Clear Referrer List', array(
      'class' => 'buttonlink admin_referrers_clear',
      'onclick' => "clearReferrers();",
    )) ?>
  </div>

  <br />

  <table class='admin_table'>
    <thead>
      <tr>
        <th><?php echo $this->translate("Hits") ?></th>
        <th><?php echo $this->translate("Referring URL") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach( $this->referrers as $referrer ): ?>
        <tr>
          <td>
            <?php echo $this->locale()->toNumber($referrer->value) ?>
          </td>
          <td>
            <?php
              $href = $referrer->host . $referrer->path . ( $referrer->query ? '?' . $referrer->query : '' );
              echo $this->htmlLink('http://' . $href, 'http://' . $href, array('target' => '_blank'))
            ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

<?php else: ?>

  <div class="tip">
    <span>
      <?php echo $this->translate("There have not been any referrers logged yet.") ?>
    </span>
  </div>

<?php endif; ?>