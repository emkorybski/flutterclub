<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    News
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: category.tpl 7253 2010-09-01 20:40:55Z jung $
 * @author     John
 */
?>	
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
        return confirm("<?php echo $this->translate("Are you sure you want to delete the selected categories?") ?>");
    }
  }
  alert("You don\'t choose any category to delete");
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
<ul class="form-errors">
    <li>
        <ul class="errors"> <li>
            <?php if ($_SESSION['result'] ==0):?>
    <?php echo $this->translate("Error occurs when delete the category !" );?>
    <?php else:?>
        <?php 
            switch($_SESSION['result'])
            {
                case 1:
                    echo $this->translate("Delete successfully.");break;
                case 2 :
                    echo $this->translate("Update status successfully. ");break;
                default:
                    break;
                
            }
        ?>

    <?php endif;?>  
       </li>  </ul>
    </li>
</ul>
    
    
   <?php  unset($_SESSION['result']); $_SESSION['result'] = null;   ?>
<?php endif;?>  
<div class='clear'>
  <div class='settings'>  
  <?php echo $this->form->render($this) ?>
  
<?php if( count($this->paginator) ): ?>
  <form id='multidelete_form' style="width: 100%" method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
  <table class='news_admin_table'>
    <thead>
      <tr>
        <th width="2%" class='news_admin_table_short' ><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
        <th width="15%" style="text-align: left;"><?php echo $this->translate("Name") ?></th>
        <th width="50%" style="text-align: left;"><?php echo $this->translate("Description") ?></th>        
        <th width="10%" style="text-align: left;"><?php echo $this->translate("Total Feed") ?></th>
        <th width="5%" style="text-align: left;"><?php echo $this->translate("Status") ?></th>
        <th width="10%" style="text-align: left;"><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
        <tr>
          <td><input type='checkbox' name='delete_<?php echo $item->category_id;?>' value='<?php echo $item->category_id ?>' class='checkbox' value="<?php echo $item->category_id ?>"/></td>
          <td><?php echo wordwrap($item->category_name,50,"<br />\n",TRUE); ?></td>
          <td><?php echo wordwrap($item->category_description,50,"<br />\n",TRUE); ?></td>
          <td>
          <?php
            $table = Engine_Api::_()->getDbtable('Categories', 'News');

            $select = $table->select('engine4_news_categories ')->setIntegrityCheck(false)
            ->where('engine4_news_categories.category_parent_id= ? ', $item->category_id)
            ;
            $items = $table->fetchAll($select);
            echo count($items);
          ?>
          </td>
          <td><?php if ( $item->is_active == 1) :?> <?php echo $this->translate("Active")?><?php else:?><?php echo $this->translate("Inactive")?> <?php endif;?></td>
          <td align="center">          
              <span><a class='smoothbox'  href="<?php echo $this->url(array('action' => 'editcategory', 'id' => $item->category_id));?>" >edit</a></span>
          </td>          
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
      <br />
         
    <div class='buttons'>
      <button type='submit' id="buttondelete" name="buttondelete"><?php echo $this->translate("Delete Selected") ?></button>&nbsp;&nbsp;
      <button type='button' id='buttonactive' onclick ="getrssSelect(1);document.getElementById('is_active_form').submit();" ><?php echo $this->translate("Active Selected") ?></button>&nbsp;&nbsp;
      <button type='button' id='buttondeactive' onclick ="getrssSelect(0);document.getElementById('is_active_form').submit();" ><?php echo $this->translate("Inactive Selected") ?></button>&nbsp;&nbsp;
      
      
    </div>
  </form>
  
    <form method="post" id ="is_active_form" name ="is_active_form" action="<?php echo $this->url(array('action'=>'caactiverss'));?>" >
        <input type="hidden" value="1" name="is_active_name" id="is_active_name"/>
        <input type="hidden" value="" name="categories_active" id="categories_active"/>
    </form>
  <br />

  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no categories posted by your members yet.") ?>
    </span>
  </div>
<?php endif; ?>
</div>
</div>
<script>
    function getrssSelect(active)
    {
            var Checkboxs = document.forms[1].elements;
            var values = "";
            for(var i = 0; i < Checkboxs.length; i++) 
            {    
                 var type = Checkboxs[i].type;
                 if (type=="checkbox" && Checkboxs[i].checked)
                 {                 
                       values += "," + Checkboxs[i].value;                
                 }
            }
            if(values != "")
            {
                values = "(" + values + ",)";
            }
            $('is_active_name').value =active;
             $('categories_active').value =values;
            //$('is_active_form').submit();
            return false;
    }
</script>
</div>
</div>
