<?php
/**
 * YouSocialEngine
 *
 * @category    Application_Widget
 * @package     Fancymenu
 * @copyright   Copyright (c) 2011, Shane Barcinas
 * @license     http://yousocialengine.com/view-content/2/License-Terms.html
 * @version     $Id: index.tpl 2011-27-09 19:08 shane $
 * @author      Shane Barcinas
 */

// Get requested module and action name
// using Zend_Controller_Front
$module_name = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
$action_name = Zend_Controller_Front::getInstance()->getRequest()->getActionName();


// Set menu count
if(empty($this->menucount)) {
    $this->menucount = 6;
} else {
    $this->menucount = $this->menucount;
}

?>
<script src="<?php echo $this->baseUrl(); ?>/externals/fancymenu/fancymenu.js" type="text/javascript"></script>
<script type="text/javascript">

var fmCSS = Asset.css("<?php echo $this->baseUrl(); ?>/externals/fancymenu/themes/<?php if(empty($this->menutheme)) { echo "light"; } else { echo $this->menutheme; } ?>.css",{
    id: "fmCSS",
    title: "fmCSS"
});

window.addEvent('domready', function() {
    var menu = new FancyMenu({
        effect: "<?php if(empty($this->menueffect)) { echo "slide & fade"; } else { echo $this->menueffect; } ?>",
        physics: "<?php if(empty($this->menuphysics)) { echo "pow:in:out"; } else { echo $this->menuphysics; } ?>",
        duration: <?php if(empty($this->fxduration)) { echo "600"; } else { echo $this->fxduration; } ?>,
        subMenusContainerId: "explore_container"
    });
});

</script>
<div class="layout_core_menu_main">
    <ul id="navigation">
        
        <?php $s = 0; $total = 0; $more = array(); foreach($this->navigation as $item): ?>
        <?php
            $label = $item->getLabel();
            $class = $item->getClass();
            
            if($s < $this->menucount) {
                // We have to this manually
                if((strstr(strtolower($label), $module_name) != "") || ($module_name == "core" && $label == "Home") || ($module_name == "user" && $label == "Home" && $action_name == "home") || ($module_name == "user" && $label == "Members" && $action_name == "browse")) { ?>
                <li class="active">
                    <a href="<?php echo $item->getHref(); ?>" class="<?php echo $class; ?>"><span><?php echo $this->translate($item->getLabel()); ?></span></a>
                </li>
                <?php
                    $total += 1;
                    $s += 1;
                } else {
                ?>
                <li>
                    <a href="<?php echo $item->getHref(); ?>" class="<?php echo $class; ?>"><span><?php echo $this->translate($item->getLabel()); ?></span></a>
                </li>
                <?php
                    $s += 1;
                }
            } else {
                if(strstr(strtolower($label), $module_name) != "") {
                    $more[$s] = "<li class='active'><a href='". $item->getHref() . "' class='". $class ."'><span>". $this->translate($item->getLabel()) ."</span></a></li>";
                    $s += 1;
                } else {
                    $more[$s] = "<li><a href='". $item->getHref() . "' class='". $class ."'><span>". $this->translate($item->getLabel()) ."</span></a></li>";
                    $s += 1;
                }
            }
        ?>
        <?php endforeach;
        
        if($s > $this->menucount) {
            
        ?>
        <li class="explore">
            <a href="javascript:void(0);">
                <span>
                    <?php
                    if(empty($this->menuname)) {
                        echo $this->translate('Explore');
                    } else {
                        echo $this->translate($this->menuname);
                    }
                    ?>
                </span>
            </a>
            <ul>
                <?php
                foreach($more as $more_item) {
                    echo $more_item;
                }
                ?>
            </ul>
        </li>
        <?php
        }
        ?>
    </ul>
</div>
