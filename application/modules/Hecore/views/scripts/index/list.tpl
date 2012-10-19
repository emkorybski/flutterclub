<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: list.tpl 2010-07-02 17:53 idris $
 * @author     Idris
 */
?>

<?php if ($this->error): ?>
<div class="contacts_error no_content"><?php echo $this->message; ?></div>
<?php else: ?>

<?php if ($this->items->getCurrentItemCount() > 0): ?>
<script type="text/javascript">
  he_list.callback = "<?php echo $this->callback; ?>";
  he_list.list_type = "<?php echo $this->list_type; ?>";
  he_list.params = <?php echo Zend_Json_Encoder::encode($this->params); ?>;
  he_list.list = '<?php echo $this->list; ?>';
  he_list.module = '<?php echo $this->module; ?>';
  he_list.ajax_url = '<?php echo $this->url(array('module' => 'hecore', 'controller' => 'index', 'action' => 'list', 'format' => 'json')); ?>';
  he_list.page = <?php echo $this->items->getCurrentPageNumber() ? $this->items->getCurrentPageNumber() : 1; ?>;
</script>
<?php endif; ?>

<div id="he_contacts_loading" style="display:none;">&nbsp;</div>
<div class="he_contacts">
  <?php if ($this->title): ?>
    <h4 class="contacts_header"><?php echo $this->title; ?></h4>
  <?php endif; ?>
  <?php if ($this->items->getCurrentItemCount() > 0): ?>
  <div class="options">
    <div class="select_btns float_right_rtl_force">
      <?php if (!(isset($this->params['disable_list1']) && $this->params['disable_list1'])): ?>
      <a href="javascript:void(0)" <?php if ($this->list_type == 'all'): ?> class="active" <?php endif; ?> onClick="he_list.select('all'); he_contacts.add_class(this, 'active', $$('.select_btns a')[1]); this.blur();">
        <?php echo !empty($this->params['list_title1']) ? $this->params['list_title1'] : $this->translate("Everyone"); ?>
      </a>
      <?php endif; ?>
      <?php if (!(isset($this->params['disable_list2']) && $this->params['disable_list2'])): ?>
      <a href="javascript:void(0)" <?php if ($this->list_type == 'mutual'): ?> class="active" <?php endif; ?> onClick="he_list.select('mutual'); he_contacts.add_class(this, 'active', $$('.select_btns a')[0]); this.blur();">
        <?php echo !empty($this->params['list_title2']) ? $this->params['list_title2'] : $this->translate("Mutual"); ?>
      </a>
      <?php endif; ?>
    </div>
    <div class="contacts_filter float_left_rtl_force">
    <form onsubmit="return false" >
    <div class="list_filter_cont">
      <input type="text" name="q" id="list_filter" title="Search" class="list_filter">
      <a href="javascript://" onclick="this.blur();" title="<?php echo $this->translate('Search'); ?>" class="list_filter_btn" id="list_filter_btn"></a>
    </div>
    </form>
    </div>
    <div class="clr"></div>
  </div>
  <div class="clr"></div>
  <?php endif; ?>
  <div class="contacts">
    <div id="he_list">
      <?php echo $this->render('_hecore_items.tpl'); ?>
    </div>
    <div class="clr"></div>
   </div>
   <div class="clr"></div>
</div>
<?php endif; ?>