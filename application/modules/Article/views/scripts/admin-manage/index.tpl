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


var delectSelected = function(){

    var checkboxes = $$('input.checkboxes');
    var selecteditems = [];

    checkboxes.each(function(item, index){
      var checked = item.get('checked');
      if (checked) {
        selecteditems.push(item.get('value'));
      }
    });

  if (selecteditems == "") {
    return false;
  }  

  $('ids').value = selecteditems;
  $('delete_selected').submit();
}
  
function selectAll()
{
  var checkboxes = $$('input.checkboxes');
  var selecteditems = [];

  var chked = $('checkboxes_toggle').get('checked');
  
  checkboxes.each(function(item, index){
    item.set('checked', chked);
  });
}
  
</script>

<h2><?php echo $this->translate("Articles Plugin") ?></h2>

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
  <?php echo $this->translate("ARTICLES_VIEWS_SCRIPTS_ADMINMANAGE_INDEX_DESCRIPTION") ?>
</p>
<br />

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<br />

<div class='admin_results'>
  <div>
    <?php $articleCount = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s article found", "%s articles found", $articleCount), ($articleCount)) ?>
  </div>
  <div>
    <?php // echo $this->paginationControl($this->paginator, null, null, array('params'=>$this->params)); ?>
    
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->formValues
    )); ?>  
    
  </div>
</div>
<?php //print_r($this->params)?>
<br />

<?php if( count($this->paginator) ): ?>

<table class='admin_table'>
  <thead>
    <tr>
      <th class='admin_table_short'><input onclick="selectAll()" type='checkbox' id='checkboxes_toggle' /></th>
      <th class='admin_table_short'><a href="javascript:void(0);" onclick="javascript:changeOrder('article_id', 'DESC');">ID</a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate("Title") ?></a></th>
      <th><?php echo $this->translate("Owner") ?></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('view_count', 'ASC');"><?php echo $this->translate("Views") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('comment_count', 'ASC');"><?php echo $this->translate("Comments") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('like_count', 'ASC');"><?php echo $this->translate("Likes") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate("Date") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('published', 'DESC');"><?php echo $this->translate("Status") ?></a></th>
      <th><?php echo $this->translate("Icons") ?> [<a href="javascript:void(0);" onclick="Smoothbox.open($('article_icons_legend')); return false;">?</a>]</th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): // $this->string()->chunk($item->getTitle(), 5) ?>
      <tr>
        <td><input type='checkbox' class='checkboxes' value="<?php echo $item->article_id ?>"/></td>
        <td><?php echo $item->article_id ?></td>
        <td style="white-space: normal;"><?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('target' => '_blank')) ?></td>
        <td><?php echo $this->htmlLink($this->user($item->owner_id)->getHref(), $this->user($item->owner_id)->getTitle(), array('target' => '_blank')) ?></td>
        <td><?php echo $this->locale()->toNumber($item->view_count) ?></td>
        <td><?php echo $this->locale()->toNumber($item->comment_count) ?></td>
        <td><?php echo $this->locale()->toNumber($item->like_count) ?></td>
        <td><?php echo $this->timestamp($item->creation_date) ?></td>
        <td><?php echo $this->htmlLink(
            array('route' => 'default', 'module' => 'article', 'controller' => 'admin-manage', 'action' => 'published', 'id' => $item->article_id),
            $this->translate($item->published ? "Published" : "Draft"),
            array('class' => 'smoothbox', 'title' => $this->translate($item->published ? "Published" : "Draft"))) ?>
        </td>
        <td><?php echo $this->htmlLink(
            array('route' => 'default', 'module' => 'article', 'controller' => 'admin-manage', 'action' => 'published', 'id' => $item->article_id),
            $this->htmlImage('./application/modules/Article/externals/images/'.($item->published ? "publish" : "draft").'.png'),
            array('class' => 'smoothbox', 'title' => $this->translate($item->published ? "Published" : "Draft"))) ?>
            <?php echo $this->htmlLink(
            array('route' => 'default', 'module' => 'article', 'controller' => 'admin-manage', 'action' => 'featured', 'id' => $item->article_id),
            $this->htmlImage('./application/modules/Article/externals/images/featured'.($item->featured ? "" : "_off").'.png'),
            array('class' => 'smoothbox', 'title' => $this->translate($item->featured ? "Featured" : "Not Featured"))) ?>
            <?php echo $this->htmlLink(
            array('route' => 'default', 'module' => 'article', 'controller' => 'admin-manage', 'action' => 'sponsored', 'id' => $item->article_id),
            $this->htmlImage('./application/modules/Article/externals/images/sponsored'.($item->sponsored ? "" : "_off").'.png'),
            array('class' => 'smoothbox', 'title' => $this->translate($item->sponsored ? "Sponsored" : "Not Sponsored"))) ?>
            </td>
        <td>
          <a href="<?php echo $this->url(array('action'=>'edit', 'article_id' => $item->article_id), 'article_specific', true) ?>" target="_blank">
            <?php echo $this->translate("edit") ?>
          </a>
          |
          <?php echo $this->htmlLink(
            array('route' => 'default', 'module' => 'article', 'controller' => 'admin-manage', 'action' => 'delete', 'id' => $item->article_id),
            $this->translate("delete"),
            array('class' => 'smoothbox')) ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<br />

<div class='buttons'>
  <button onclick="javascript:delectSelected();" type='submit'>
    <?php echo $this->translate("Delete Selected") ?>
  </button>
</div>

<form id='delete_selected' method='post' action='<?php echo $this->url(array('action' =>'deleteselected')) ?>'>
  <input type="hidden" id="ids" name="ids" value=""/>
</form>
<br/>

<?php //print_r($this->params)?>
<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no articles by your members yet.") ?>
    </span>
  </div>
<?php endif; ?>

<div style="display: none">
  <ul class="radcodes_admin_icons_legend" id="article_icons_legend">
    <li><?php echo $this->htmlImage('./application/modules/Article/externals/images/publish.png');?><?php echo $this->translate('Published')?></li>
    <li><?php echo $this->htmlImage('./application/modules/Article/externals/images/draft.png');?><?php echo $this->translate('Draft')?></li>
    <li><?php echo $this->htmlImage('./application/modules/Article/externals/images/featured.png');?><?php echo $this->translate('Featured')?></li>
    <li><?php echo $this->htmlImage('./application/modules/Article/externals/images/featured_off.png');?><?php echo $this->translate('Not Featured')?></li>
    <li><?php echo $this->htmlImage('./application/modules/Article/externals/images/sponsored.png');?><?php echo $this->translate('Sponsored')?></li>
    <li><?php echo $this->htmlImage('./application/modules/Article/externals/images/sponsored_off.png');?><?php echo $this->translate('Not Sponsored')?></li>
  </ul>
</div>
