<?php
?>    
<?php
    $this->headLink()
        ->appendStylesheet($this->baseUrl() . '/application/modules/News/externals/styles/main.css');  
?>
<?php
    $username  = Engine_Api::_()->user()->getViewer()->username;
    $users = Engine_Api::_()->news()->getAllUsers();
    $flag = false;
    foreach ($users as $user):
       if ($user['username'] == $username):
           $flag = true; 
       endif;
   endforeach;
   if ( Engine_Api::_()->user()->getViewer()->level_id == 1 || Engine_Api::_()->user()->getViewer()->level_id == 2):
           $flag = true; 
       endif;
   if($flag == true):
?>
<div class="headline">
  <h2>
    <?php echo $this->translate('News');?>
  </h2>
  <?php if( count($this->navigation) > 0 ): ?>
    <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
  <?php endif; ?>
</div>
<?php endif; ?>
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
</style>
<script type="text/javascript">

function multiDelete()
{
   
  var i;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length; i++) {
    if (inputs[i].checked == true){
        return confirm("<?php echo $this->translate("Are you sure you want to delete the selected news?") ?>");
    }
  }
  alert("You don\'t choose any news to delete");  
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
<?php 
if (isset($_SESSION['result'])):?>
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
<?php if( count($this->paginator) ): ?>

  <form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete();">

  <table class='news_admin_table'>
    <thead>
      <tr>
        <th width="2%" class='news_admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
        <th width="3%" class='news_admin_table_short'><?php echo $this->translate("ID") ?></th>
        <th width="15%"><?php echo $this->translate("Category") ?></th>
        <th width="50%"><?php echo $this->translate("Title") ?></th>        
        <th width="15%"><?php echo $this->translate("Posted Date") ?></th>
        <th width="5%"><?php echo $this->translate("Featured") ?></th>
        <th width="20%"><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
        <tr>
          <td><input type='checkbox' name='delete_<?php echo $item->content_id;?>' value='<?php echo $item->content_id ?>' class='checkbox' value="<?php echo $item->content_id ?>"/></td>
          <td><?php echo $item->content_id ?></td>
          <td><?php $category = Engine_Api::_()->news()->getAllCategories(array('category_id'=>$item->category_id,)); if(isset($category[0])){ echo($category[0]['category_name']);} else {echo "";} ?></td>
          <td><?php echo $item->title; ?></td>          
          <td><?php echo date('Y-m-d',$item->pubDate) ?></td>
          <td>
            <?php if ( $item->is_featured == 1):?>
                <?php echo $this->translate('Yes')?>
            <?php else:?>
                <?php echo $this->translate('No')?>
            <?php endif;?>
          </td>
          <td>            
                  <a href="<?php echo 
                    $this->url(array('content_id'=>$item->content_id), 'news_edit_news') ?>" class = 'smoothbox '><?php echo $this->translate('edit') ?> </a>
                            | 
                  <?php echo $this->htmlLink($item->getHref(), $this->translate("view")) ?>               
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
      <br />

    <div class='buttons'>
      <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
       <button type='button' id='buttonactive' onclick ="getnewsSelect(1);if (is_select){document.getElementById('is_set_featured_form').submit();}" ><?php echo $this->translate("Set Featured") ?></button>
      <button type='button' id='buttondeactive' onclick ="getnewsSelect(0);if (is_select){document.getElementById('is_set_featured_form').submit();}" ><?php echo $this->translate("Unset Featured") ?></button>
    </div>
  </form>
   <form method="post" id ="is_set_featured_form" name ="is_set_featured_form" action="<?php echo $this->url(array('action'=>'featured'));?>" >
        <input type="hidden" value="1" name="is_set_featured" id="is_set_featured"/>
        <input type="hidden" value="" name="news_featured" id="news_featured"/>
    </form>
  <br />

  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no news got from remote servers.") ?>
    </span>
  </div>
<?php endif; ?>
</div>
</div>
<script>
    var is_select = false;
    function getnewsSelect(active)
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
                is_select = true;
            }
            else
            {
                is_select = false;
                alert('Please select a news');
            }
            if ( active >-1)
            {
                $('is_set_featured').value =active;
                $('news_featured').value =values;    
            }
            else
            {
                
                return is_select ;
            }
            //$('is_active_form').submit();
            //alert(values);
            return false;
    }
</script>
