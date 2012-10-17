
<div id="submit-wrapper" class="form-wrapper">
  <div id="submit-label" class="form-label"> </div>
  <div id="submit-element" class="form-element">
    <button type="submit" id="done" name="done">
      <?php echo ( $this->element->getLabel() ? $this->element->getLabel() : $this->translate('Save Changes')) ?>
    </button>
      <?php echo $this->translate('or') ?>
      <a href="javascript:void(0);" onclick="parent.Smoothbox.close();">
        <?php echo $this->translate('cancel') ?>
      </a>
  </div>
</div>