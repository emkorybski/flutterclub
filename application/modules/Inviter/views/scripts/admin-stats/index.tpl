<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2010-07-02 17:53 ermek $
 * @author     Ermek
 */
?>

<style type="text/css">
table.admin_home_stats td:first-child {
    width:180px;
}

.inviter_admin_search .search form > div:first-child {
  margin-left: 10px;
}
</style>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<h2><?php echo $this->translate('INVITER_View Statistics'); ?></h2>
<p>
  <?php echo $this->translate("INVITER_VIEWS_SCRIPTS_ADMINMANAGE_INDEX_DESCRIPTION") ?>
</p>
<br/>

<div class="admin_home_right">
    <h3 class="sep">
      <span>
        <?php echo $this->translate('INVITER_Quick Stats') ?>
      </span>
    </h3>

    <table class='admin_home_stats'>
      <thead>
        <tr>
          <th colspan='2'><?php echo $this->translate('INVITER_Network Information') ?></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo $this->translate('INVITER_Total Invitations sent') ?></td>
          <td><?php echo $this->total_sent_invites ?></td>
        </tr>
        <tr>
          <td><?php echo $this->translate('INVITER_Total Signups by Invitations') ?></td>
          <td><?php echo $this->total_refferred_users ?></td>
        </tr>
        <tr>
          <td class="admin_table_bold"><?php echo $this->translate('INVITER_Invites/Signups Ratio') ?></td>
          <td class="admin_table_bold"><?php echo $this->invitest_to_refferred ?>%</td>
        </tr>
      </tbody>
    </table>
</div>

<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction){
    // Just change direction
    if( order == currentOrder ) {
      $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else {
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  }

function multiModify()
{
  var multimodify_form = $('multimodify_form');
  if (multimodify_form.submit_button.value == 'delete')
  {
    return confirm('<?php echo $this->string()->escapeJavascript($this->translate("INVITER_Are you sure you want to delete the selected user accounts?")) ?>');
  }
}

function selectAll()
{
  var i;
  var multimodify_form = $('multimodify_form');
  var inputs = multimodify_form.elements;
  for (i = 1; i < inputs.length - 1; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}
</script>

<div class="admin_home_left">
    <h3 class="sep">
        <span><?php echo $this->translate('INVITER_Filter inviters'); ?></span>
    </h3>
    <div class='admin_search inviter_admin_search' style="clear:none;">
      <?php echo $this->formFilter->render($this) ?>
    </div>
    
    <br />
</div>

<br />
<div class="admin_home_middle">
    <div class='admin_results'>
		<div>
	    	<?php $memberCount = $this->paginator->getTotalItemCount() ?>
	    	<?php echo $this->translate(array("INVITER_%s inviter found", "%s inviters found", $memberCount), ($memberCount)) ?>
	  	</div>
	  	<div>
	    	<?php echo $this->paginationControl($this->paginator); ?>
	  	</div>
    </div>

    <br />

    <table class='admin_table'>
    <thead>
      <tr>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('displayname', 'ASC');"><?php echo $this->translate("Display Name") ?></a></th>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate("Username") ?></a></th>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('email', 'ASC');"><?php echo $this->translate("Email") ?></a></th>
        <th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('inviter_sent', 'ASC');" title="Order By Invites"><?php echo $this->translate("INVITER_Sent Invites") ?></a></th>
        <th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('inviter_referred', 'ASC');"  title="Order By Referrals"><?php echo $this->translate("INVITER_Referred Users") ?></a></th>
      </tr>
    </thead>
	<tbody>
      <?php if( count($this->paginator) ): ?>
        <?php foreach( $this->paginator as $item ): ?>
          <tr>
            <td><?php echo $item->user_id ?></td>
            <td class='admin_table_bold'><?php echo $this->htmlLink($this->item('user', $item->user_id)->getHref(), $this->item('user', $item->user_id)->getTitle(), array('target' => '_blank')) ?></td>
            <td class='admin_table_bold'><?php echo $this->htmlLink($this->item('user', $item->user_id)->getHref(), $this->item('user', $item->user_id)->username, array('target' => '_blank')) ?></td>
            <td>
              <?php if( !$this->hideEmails ): ?>
                <a href='mailto:<?php echo $item->email ?>'><?php echo $item->email ?></a>
              <?php else: ?>
                (hidden)
              <?php endif; ?>
            </td>
            <td class='admin_table_centered'><?php echo $item->inviter_sent ?></td>
            <td class='admin_table_centered'><?php echo $item->inviter_referred ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
    </table>
    <br />
</div>