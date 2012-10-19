<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: contacts.tpl 2010-07-02 17:53 idris $
 * @author     Idris
 */
?>

<?php if ($this->error): ?>
  <div class="contacts_error"><?php echo $this->message; ?></div>
<?php else: ?>

<script type="text/javascript">
window.HE_CONTACTS = null;
var options = {
  c: "<?php echo $this->callback; ?>",
  listType: "all",
  m: "<?php echo $this->module; ?>",
  l: "<?php echo $this->list; ?>",
  ipp: <?php echo (int)$this->ipp; ?>,
  p: <?php echo (int)$this->items->getCurrentPageNumber(); ?>,
  total: <?php echo (int)$this->items->getTotalItemCount(); ?>,
  params: <?php echo Zend_Json::encode($this->params); ?>,
  nli: <?php echo (int)$this->not_logged_in; ?>,
  contacts: <?php echo Zend_Json_Encoder::encode($this->checkedItems); ?>
};

window.HE_CONTACTS = new HEContacts(options);
</script>

<div id="he_contacts_loading" style="display:none;">&nbsp;</div>
<div id="he_contacts_message" style="display:none;"><div class="msg"></div></div>

<div class="he_contacts">
  <?php if ($this->title): ?>
    <h4 class="contacts_header"><?php echo $this->title; ?></h4>
  <?php endif; ?>

  <?php if (isset($this->items) && $this->items->getCurrentItemCount() > 0): ?>
    <div class="options">
      <div class="select_btns float_right_rtl_force">
        <a href="javascript:void(0)" id="he_contacts_list_all" class="active"><?php echo $this->translate("All"); ?></a>
        <a href="javascript:void(0)" id="he_contacts_list_selected"><?php echo $this->translate("Selected"); ?></a>
      </div>
      <div class="contacts_filter float_left_rtl_force">
        <div class="list_filter_cont">
          <input type="text" class="list_filter" title="Search" id="contacts_filter" name="q" />
          <a class="list_filter_btn" id="contacts_filter_submit" title="Search" href="javascript://"></a>
        </div>
      </div>

      <div class="clr"></div>
    </div>
    <div class="clr"></div>
  <?php endif; ?>

  <div class="contacts">
    <div id="he_contacts_list">
      <?php echo $this->render('_contacts_items.tpl'); ?>
    </div>

    <?php if ($this->items->count() > $this->items->getCurrentPageNumber()): ?>
      <div class="clr"></div>
      <a class="pagination" id="contacts_more" href="javascript:void(0);"><?php echo $this->translate("More"); ?></a>
    <?php endif; ?>

    <div class="clr"></div>
  </div>
  <div class="clr"></div>

  <div class="btn" style="width:450px">
    <button id="submit_contacts" style="float:left;"><?php echo $this->translate((isset($this->params['button_label']))?$this->params['button_label']:"Send"); ?></button>

    <div class="he_contacts_choose_all" style="width: 100px; float:left; margin:5px">
      <input type="checkbox" id="select_all_contacs" name="select_all_contacs" />
      <label for="select_all_contacs"><?php echo $this->translate('HECORE_Select all');?></label>
    </div>
  </div>
</div>

<?php endif; ?>