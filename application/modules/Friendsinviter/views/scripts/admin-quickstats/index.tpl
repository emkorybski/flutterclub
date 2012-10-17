<h2><?php echo $this->translate("Friends Inviter Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
      
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("100010102") ?>
</p>

<br />


<div class="admin_statistics">

  <table cellpadding='0' cellspacing='0' align='center'>
  <tr>
  <td align='right'><?php echo $this->translate('100010105') ?> &nbsp;</td>
  <td><?php echo $this->total_invites ?> </td>
  </tr>
  <tr>
  <td align='right'><?php echo $this->translate('100010106') ?> &nbsp;</td>
  <td><?php echo $this->total_converted_invites ?> </td>
  </tr>
  <tr>
  <td align='right' style='font-weight:bold'><?php echo $this->translate('100010107') ?> &nbsp;</td>
  <td style='font-weight:bold'><?php echo sprintf("%.2f", $this->contacts_invited_vs_signups) ?> % </td>
  </tr>

  <tr><td>&nbsp;</td><td>&nbsp;</td></tr>

  <tr>
  <td align='right'><?php echo $this->translate('100010108') ?> &nbsp;</td>
  <td><?php echo $this->total_contacts_imported ?> </td>
  </tr>
  <tr>
  <td align='right' ><?php echo $this->translate('100010109') ?> &nbsp;</td>
  <td><?php echo $this->total_contacts_invited ?> </td>
  </tr>
  <tr>
  <td align='right' style='font-weight:bold'><?php echo $this->translate('100010110') ?> &nbsp;</td>
  <td style='font-weight:bold'><?php echo sprintf("%.2f", $this->contacts_imported_vs_invited) ?> % </td>
  </tr>
  </table>


</div>
