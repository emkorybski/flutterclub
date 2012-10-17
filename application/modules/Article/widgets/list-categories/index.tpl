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
 
 // quicklinks articles_categories_quicklinks
?>
<?php 
/*
<div class="quicklinks articles_categories_quicklinks">
  <ul>
    <?php foreach ($this->categories[0] as $category): ?>
      <li>
        <?php 
          $attrs = array();
          if ($this->showphoto) {
            $attrs['class'] = 'buttonlink';
            if ($category->photo_id) {
              $attrs['style'] = "background-image: url(".$category->getPhotoUrl('thumb.mini').");";
            }
          }
          //print_r($attrs);
          echo $this->htmlLink($category->getHref(), $this->translate($category->getTitle()), $attrs
        );?>
        <?php if ($this->showdetails): ?>
          <div class="article_category_desc">
            <?php echo $this->radcodes()->text()->truncate($category->getDescription(), $this->descriptionlength); ?>
          </div>
        <?php endif; ?>
        
        <?php if (isset($this->categories[$category->getIdentity()]) && count($this->categories[$category->getIdentity()])): ?>
          <ul>
          <?php foreach ($this->categories[$category->getIdentity()] as $subcategory): ?>
            <li>
              <?php 
                $attrs = array();
                if ($this->showphoto) {
                  $attrs['class'] = 'buttonlink';
                  if ($subcategory->photo_id) {
                    $attrs['style'] = "background-image: url(".$subcategory->getPhotoUrl('thumb.mini').");";
                  }
                }
                echo $this->htmlLink($subcategory->getHref(), $this->translate($subcategory->getTitle()), $attrs
              );?>
            </li>
          <?php endforeach; ?>
          </ul>
        <?php endif;?>
        
      </li>
    <?php endforeach;?>
  </ul>
</div>
*/ 
?>
<div class="radcodes_categories_list">
  <ul>
    <?php foreach ($this->categories[0] as $category): ?>
      <li>
        <?php if (isset($this->categories[$category->getIdentity()]) && count($this->categories[$category->getIdentity()])): ?>
          <a class="radcodes_categories_subcategory_toggle radcodes_categories_subcategory_toggle_collapse"><span>+</span></a>
        <?php endif; ?>
        <?php 
          $attrs = array();
          if ($this->showphoto) {
            $attrs['class'] = 'buttonlink';
            if ($category->photo_id) {
              $attrs['style'] = "background-image: url(".$category->getPhotoUrl('thumb.mini').");";
            }
          }
          //print_r($attrs);
          echo $this->htmlLink($category->getHref(), $this->translate($category->getTitle()), $attrs
        );?>
        <?php if ($this->showdetails): ?>
          <div class="radcodes_category_desc">
            <?php echo $this->radcodes()->text()->truncate($category->getDescription(), $this->descriptionlength); ?>
          </div>
        <?php endif; ?>
        
        <?php if (isset($this->categories[$category->getIdentity()]) && count($this->categories[$category->getIdentity()])): ?>
          <ul style="display: none;">
          <?php foreach ($this->categories[$category->getIdentity()] as $subcategory): ?>
            <li>
              <?php 
                $attrs = array();
                if ($this->showphoto) {
                  $attrs['class'] = 'buttonlink';
                  if ($subcategory->photo_id) {
                    $attrs['style'] = "background-image: url(".$subcategory->getPhotoUrl('thumb.mini').");";
                  }
                }
                echo $this->htmlLink($subcategory->getHref(), $this->translate($subcategory->getTitle()), $attrs
              );?>
            </li>
          <?php endforeach; ?>
          </ul>
        <?php endif;?>
        
      </li>
    <?php endforeach;?>
  </ul>
</div>
<script type="text/javascript">
en4.core.runonce.add(function(){
  $$('a.radcodes_categories_subcategory_toggle').addEvent('click', function(){
    var radcodes_sub_cat = $(this).getParent().getChildren('ul');
    radcodes_sub_cat.toggle();
    if (radcodes_sub_cat.getStyle('display') == 'block') {
      $(this).removeClass('radcodes_categories_subcategory_toggle_collapse').addClass('radcodes_categories_subcategory_toggle_expand');
    }
    else {
      $(this).removeClass('radcodes_categories_subcategory_toggle_expand').addClass('radcodes_categories_subcategory_toggle_collapse');
    }
  });
});
</script>

