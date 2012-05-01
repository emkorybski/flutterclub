<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 9643 2012-03-10 00:34:52Z john $
 * @author     Sami
 */
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    $$('th.admin_table_short input[type=checkbox]').addEvent('click', function(event) {
      var el = $(event.target);
      $$('input[type=checkbox]').set('checked', el.get('checked'));
    });
  });
  
  var delectSelected = function() {
    var checkboxes = $$('input[type=checkbox]');
    var selecteditems = [];

    checkboxes.each(function(item, index){
      var checked = item.get('checked');
      var value = item.get('value');
      if (checked == true && value != 'on'){
        selecteditems.push(value);
      }
    });

    $('ids').value = selecteditems;
    $('delete_selected').submit();
  }

</script>

<h2>
  <?php echo $this->translate("Abuse Reports") ?>
</h2>
<p>
  <?php echo $this->translate("This page lists all of the reports your users have sent in regarding inappropriate content, system abuse, spam, and so forth. You can use the search field to look for reports that contain a particular word or phrase. Very old reports are periodically deleted by the system.") ?>
</p>


<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <script type="text/javascript">
    var currentOrder = '<?php echo $this->filterValues['order'] ?>';
    var currentOrderDirection = '<?php echo $this->filterValues['direction'] ?>';
    var changeOrder = function(order, default_direction){
      // Just change direction
      if( order == currentOrder ) {
        $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
      } else {
        $('order').value = order;
        $('direction').value = default_direction;
      }
      $('filter_form').submit();
    }
  </script>

  <div class='admin_search'>
    <?php echo $this->formFilter->render($this) ?>
  </div>

  <br />
<?php endif; ?>



<div class='admin_results'>
  <div>
    <?php $count = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s report found", "%s reports found", $count), $count) ?>
  </div>
  <div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->filterValues,
      'pageAsQuery' => true,
    )); ?>
  </div>
</div>

<br />



<?php if( count($this->paginator) ): ?>
<div class="admin_table_form">
  <table class='admin_table'>
    <thead>
      <tr>
        <th style="width: 1%;" class="admin_table_short"><input type='checkbox' class='checkbox'></th>
        <th style="width: 1%;">
          <a href="javascript:void(0);" onclick="javascript:changeOrder('report_id', 'ASC');">
            <?php echo $this->translate("ID") ?>
          </a>
        </th>
        <th>
          <a href="javascript:void(0);" onclick="javascript:changeOrder('description', 'ASC');">
            <?php echo $this->translate("Description") ?>
          </a>
        </th>
        <th style="width: 1%;">
          <?php echo $this->translate("Reporter") ?>
        </th>
        <th style="width: 1%;">
          <a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'ASC');">
            <?php echo $this->translate("Date") ?>
          </a>
        </th>
        <th style="width: 1%;">
          <a href="javascript:void(0);" onclick="javascript:changeOrder('subject_type', 'ASC');">
            <?php echo $this->translate("Content Type") ?>
          </a>
        </th>
        <th style="width: 1%;">
          <a href="javascript:void(0);" onclick="javascript:changeOrder('category', 'ASC');">
            <?php echo $this->translate("Reasons") ?>
          </a>
        </th>
        <th style="width: 1%;">
          <?php echo $this->translate("Options") ?>
        </th>
      </tr>
    </thead>
    <tbody>
      <?php foreach( $this->paginator as $item ): ?>
      <tr>
        <td><input type='checkbox' class='checkbox' value="<?php echo $item->report_id?>"></td>
        <td><?php echo $item->report_id ?></td>
        <td style="white-space: normal;"><?php echo $item->description ?></td>
        <td class="nowrap"><?php echo $this->htmlLink($this->item('user', $item->user_id)->getHref(), $this->item('user', $item->user_id)->getTitle(), array('target' => '_blank')) ?></td>
        <td class="nowrap"><?php echo $item->creation_date ?></td>
        <td class="nowrap"><?php echo $item->subject_type ?></td>
        <td class="nowrap"><?php echo $item->category ?></td>
        <td class="admin_table_options">
          <?php echo $this->htmlLink(array('action' => 'action', 'id' => $item->getIdentity(), 'reset' => false, 'format' => 'smoothbox'), $this->translate("take action"), array('class' => 'smoothbox')) ?>
          <span class="sep">|</span>
          <?php if( !empty($item->subject_type) ): ?>
            <?php echo $this->htmlLink(array('action' => 'view', 'id' => $item->getIdentity(), 'reset' => false), $this->translate("view content"), array('target' => '_blank')) ?>
            <span class="sep">|</span>
          <?php endif; ?>
          <?php echo $this->htmlLink(array('action' => 'delete', 'id' => $item->getIdentity(), 'reset' => false), $this->translate("dismiss")) ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <br/>

  <div class='buttons'>
    <button onclick="javascript:delectSelected();" type='submit'><?php echo $this->translate("Dismiss Selected") ?></button>
  </div>

  <form id='delete_selected' method='post' action='<?php echo $this->url(array('action' =>'deleteselected')) ?>'>
    <input type="hidden" id="ids" name="ids" value=""/>
  </form>

<?php else:?>

  <div class="tip">
    <span><?php echo $this->translate("There are currently no outstanding abuse reports.") ?></span>
  </div>

<?php endif; ?>


</div>