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
  <?php echo $this->translate("FRIENDSINVITER_ADMIN_LEADERBOARD_DESC") ?> (<a href="<?php echo $this->url(array('action' => 'clear')) ?>"><?php echo $this->translate("100010122") ?></a>)
</p>

<br>

<table cellpadding=0 cellpadding=0 width="100%">
<tr>
  <td style="width: 50%; vertical-align: top">

<h1><?php echo $this->translate("100010123") ?></h1>
<h2><?php echo $this->translate("100010124") ?></h2>

<br>

<?php if(count($this->leaderboard) > 0) : ?>

  <table class='admin_table'>
  <thead>
  <tr>
  <th><b><?php echo $this->translate("100010127") ?></b></th>
  <th><b><?php echo $this->translate("100010118") ?></b></th>
  <th><b><?php echo $this->translate("100010119") ?></b></th>
  </thead>
  </tr>
    <?php foreach ($this->leaderboard as $leader): ?>
    <tr>
    <td align='center'><?php echo $this->htmlLink($this->item('user', $leader->user_id)->getHref(), $this->item('user', $leader->user_id)->getTitle(), array('target' => '_blank')) ?></td>
    <td align='center'><?php echo $leader->invites_sent ?></td>
    <td align='center'> - </td>
    </tr>
    <?php endforeach; ?>
  </table>

<?php else: ?>

      <br><br><i><?php echo $this->translate("100010128") ?></i>


<?php endif; ?>

  </td>
  <td style="width: 50%; vertical-align: top">

<h1><?php echo $this->translate("100010125") ?></h1>
<h2><?php echo $this->translate("100010126") ?></h2>

<br>
  
  <br><br><i><?php echo $this->translate("Available in the Advanced Version") ?> <a target=_blank href="http://www.socialenginemods.net/social-engine/plugins/1/friends-inviter-contacts-importer"><?php echo $this->translate("Click here to visit") ?></a></i>

  </td>
</tr>
</table>
