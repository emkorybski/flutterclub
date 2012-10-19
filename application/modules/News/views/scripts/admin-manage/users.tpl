
<?php
	$this->headLink()
    	->appendStylesheet($this->baseUrl() . '/application/modules/News/externals/styles/main.css');
?>
<style>

/*
ADMIN TABLE LIST
Styles most tabular data in the admin panel.
*/
table.news_admin_table thead tr th
{
  +foreground;
  padding: 10px;
  border-bottom: 1px solid #aaa;
  font-weight: bold;
  padding-top: 7px;
  padding-bottom: 7px;
  white-space: nowrap;
  background-color: #E9F4FA;
}
table.news_admin_table thead tr th a
{
  font-weight: bold;
}
table.news_admin_table tbody tr:nth-child(even)
{
  background-color: #E9F4FA;
}
table.news_admin_table tbody tr td
{
  padding: 10px;
  border-bottom: 1px solid #eee;
  font-size: .9em;
  padding-top: 7px;
  padding-bottom: 7px;
  white-space: wrap;
  vertical-align: top;
}
th.news_admin_table_short
{
  width: 1%;
}
td.news_admin_table_options
{
  color: #ccc;
}
.news_admin_table_centered
{
  text-align: center;
}
.news_admin_table_centered a
{
  text-align: center;
}
.news_admin_table_bold,
.news_admin_table_bold a
{
  font-weight: bold;
}
td.news_admin_table_options > span.sep
{
  display: none;
}
td.news_admin_table_options > a + span.sep
{
  display: inline;
}
.news_admin_table_email
{
  white-space: normal !important;
  max-width: 200px;
  word-wrap: break-word;
}
.settings form {
    -moz-border-radius: 0px;
    background: none;
    float: left;
    overflow: hidden;
    padding: 10px;
}
.settings form > div {
    border: 0px solid #D7E8F1;
}

</style>

<script type="text/javascript">

function multiDelete()
{
 var i;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length; i++) {
    if (inputs[i].checked == true){
        return confirm("<?php echo $this->translate("Are you sure you want to delete the selected users?") ?>");
    }
  }
  alert("You don\'t choose any user to delete");
  return false;
}

function selectAll()
{
  var i;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}
</script>
<div id='global_content_wrapper'>
    <div id='global_content'>
<h2><?php echo $this->translate("News Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
  </div>
<?php endif; ?>

<?php if (isset($_SESSION['result'])):?>
<?php if ($_SESSION['result'] ==0):?>
        <ul class="form-errors">
            <li>
    <?php echo $this->translate("Error occurs when deleting user !" );?>
            </li>
        </ul>
    <?php else:?>
<ul class="form-errors">
    <li>
        <?php
            switch($_SESSION['result'])
            {
                case 1:
                    echo $this->translate("Deleted successfully.");break;
                case 2 :
                    echo $this->translate("Update status successfully. ");break;
                default:
                    break;

            }
        ?>
</li>  </ul>
    <?php endif;?>



   <?php  unset($_SESSION['result']); $_SESSION['result'] = null;   ?>
<?php endif;?>
<div class='clear'>
  <div class='settings'>

      <?php if($this->message != ""): ?>
    <ul class="form-errors">
        <li>
            <?php echo $this->message; ?>
        </li>
    </ul>
      <?php endif; ?>
  <?php echo $this->form->render($this) ?>

<?php if( count($this->paginator) ): ?>
  <form id='multidelete_form' style="width: 100%" method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
  <table class='news_admin_table'>
    <thead>
      <tr>
        <th width="2%" class='news_admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
        <th width="30%" style="text-align: left;"><?php echo $this->translate("User Name") ?></th>
        <th width="35%" style="text-align: left;"><?php echo $this->translate("Display Name") ?></th>
        <th width="30%" style="text-align: left;"><?php echo $this->translate("Email") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
        <tr>
          <td><input type='checkbox' name='delete_<?php echo $item->user_id;?>' value='<?php echo $item->user_id ?>' class='checkbox' value="<?php echo $item->user_id ?>"/></td>
          <td><?php echo $item->username ?></td>
          <td><?php echo $item->displayname; ?></td>
           <td><?php echo $item->email; ?></td>
          <td align="center">

          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

    <div class='buttons'>
      <button type='submit' id="buttondelete" name="buttondelete"><?php echo $this->translate("Delete Selected") ?></button>
    </div>
  </form>
  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no users set by admin yet.") ?>
    </span>
  </div>
<?php endif; ?>
</div>
</div>
</div>
</div>
