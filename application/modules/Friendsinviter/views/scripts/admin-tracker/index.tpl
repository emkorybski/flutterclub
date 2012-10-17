<h2><?php echo $this->translate("Friends Inviter Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("100010288") ?>
</p>

<br />

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
    return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete the selected user accounts?")) ?>');
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

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<br />

<table class='admin_table'>
  <thead>
    <tr>
      <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('displayname', 'ASC');"><?php echo $this->translate("Display Name") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate("Username") ?></a></th>

      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('invites_sent', 'ASC');"><?php echo $this->translate("Invites Sent") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('invites_converted', 'ASC');"><?php echo $this->translate("Referred Users") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('referer_username', 'ASC');"><?php echo $this->translate("Referred by User") ?></a></th>
      
    </tr>
  </thead>
</table>

  <br><br><i><?php echo $this->translate("Available in the Advanced Version") ?> <a target=_blank href="http://www.socialenginemods.net/social-engine/plugins/1/friends-inviter-contacts-importer"><?php echo $this->translate("Click here to visit") ?></a></i>
