<?php
/**
 * YouSocialEngine
 *
 * @category    Application_Widget
 * @package     Fancymenu
 * @copyright   Copyright (c) 2011, Shane Barcinas
 * @license     http://yousocialengine.com/view-content/2/License-Terms.html
 * @version     $Id: manifest.php 2011-28-09 21:32 shane $
 * @author      Shane Barcinas
 */

return array (
  // Package -------------------------------------------------------------------
  'package' => array (
    'type' => 'widget',
    'name' => 'fancymenu',
    'version' => '4.2.4',
    'path' => 'application/widgets/fancymenu',
    'title' => 'Fancy Menu',
    'description' => 'Displays a fancy menu for your site',
    'author' => 'Shane Barcinas',
    'actions' => array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'remove',
    ),
    'directories' => array (
      0 => 'application/widgets/fancymenu',
    ),
  ),
  'type' => 'widget',
  'name' => 'fancymenu',
  'version' => '4.2.4',
  'title' => 'Fancy Menu',
  'description' => 'Displays a fancy menu for your site',
  'category' => 'Widgets',
  'adminForm' => array(
    'elements' => array(
      array(
        'Text',
        'title',
        array(
          'label' => 'Title',
          'disableLoadDefaultDecorators' => true,
        )
      ),
      array(
        'Select',
        'nomobile',
        array(
          'label' => 'Hide on mobile site?',
          'disableLoadDefaultDecorators' => true,
        )
      ),
      array(
        'Text',
        'menuname',
        array(
          'label' => 'Menu Name',
          'description' => 'Enter a desired menu name (default: Explore)',
          'value' => 'Explore',
        )
      ),
      array(
        'Select',
        'menucount',
        array(
          'label' => 'Menu Count',
          'description' => 'How many menu items before dropdown menu occurs',
          'multiOptions' => array(
            1 => '1 Item',
            2 => '2 Items',
            3 => '3 Items',
            4 => '4 Items',
            5 => '5 Items',
            6 => '6 Items',
            7 => '7 Items',
            8 => '8 Items',
            9 => '9 Items',
            10 => '10 Items',
            11 => '11 Items',
            12 => '12 Items',
          ),
          'value' => 6,
        )
      ),
      array(
        'Select',
        'menueffect',
        array(
          'label' => 'Menu Effect',
          'description' => 'Select a menu effect',
          'multiOptions' => array(
            'slide & fade' => 'Slide & Fade',
            'slide' => 'Slide',
            'fade' => 'Fade',
          ),
          'value' => 'slide & fade',
        )
      ),
      array(
        'Select',
        'menuphysics',
        array(
          'label' => 'Menu Physics',
          'description' => 'Select a transition for Fancy Menu effect',
          'multiOptions' => array(
            'pow:in' => 'Transition: Pow - Ease In',
            'pow:out' => 'Transition: Pow - Ease Out',
            'pow:in:out' => 'Transition: Pow - Ease In & Out',
            'elastic:in' => 'Transition: Elastic - Ease In',
            'elastic:out' => 'Transition: Elastic - Ease Out',
            'elastic:in:out' => 'Transition: Elastic - Ease In & Out',
            'bounce:in' => 'Transition: Bounce - Ease In',
            'bounce:out' => 'Transition: Bounce - Ease Out',
            'bounce:in:out' => 'Transition: Bounce - Ease In & Out',
            'back:in' => 'Transition: Back - Ease In',
            'back:out' => 'Transition: Back - Ease Out',
            'back:in:out' => 'Transition: Back - Ease In & Out',
          ),
          'value' => 'pow:out',
        )
      ),
      array(
        'Text',
        'fxduration',
        array(
          'label' => 'Duration',
          'description' => 'Duration of the effect in milliseconds (default: 600)',
          'value' => 600,
        )
      ),
      /*array(
        'Text',
        'fxdelay',
        array(
          'label' => 'Delay',
          'description' => 'Elapse time before submenu items disappear (default: 1000)',
        )
      ),*/
      array(
        'Select',
        'menutheme',
        array(
          'label' => 'Theme',
          'description' => 'Select a fancy menu style',
          'multiOptions' => array(
            'dark' => 'Dark',
            'light' => 'Light',
          ),
          'value' => 'light',
        )
      ),
    ),
  ),
); ?>