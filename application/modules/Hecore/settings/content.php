<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: content.php 2010-09-06 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
return array(
  array(
    'title' => 'Friends',
    'description' => 'Displays member\'s friends.',
    'category' => 'Hire-Experts Core Module',
    'type' => 'widget',
    'name' => 'hecore.friends',
    'defaultParams' => array(
      'title' => 'Friends',
      'titleCount' => true
    )
  ),
  array(
    'title' => 'Mutual Friends',
    'description' => 'Displays the mutual friends.',
    'category' => 'Hire-Experts Core Module',
    'type' => 'widget',
    'name' => 'hecore.mutual-friends',
    'defaultParams' => array(
      'title' => 'Mutual Friends',
      'titleCount' => true
    )
  ),
 array(
    'title' => 'Featured Members',
    'description' => 'Displays Featured Members. Please select featured members from Hire-Experts Core page under Plugins menu.',
    'category' => 'Hire-Experts Core Module',
    'type' => 'widget',
    'name' => 'hecore.featured-members',
    'defaultParams' => array(
      'title' => 'Featured Members',
      'titleCount' => true
    )
  ),
 array(
    'title' => 'Featured Carousel',
    'description' => 'Displays Featured Members in a nice carousel. Please select featured members from Hire-Experts Core page under Plugins menu.',
    'category' => 'Hire-Experts Core Module',
    'type' => 'widget',
    'name' => 'hecore.featured-carousel',
    'defaultParams' => array(
      'title' => 'Featured Carousel',
      'titleCount' => true
    )
  ),
);