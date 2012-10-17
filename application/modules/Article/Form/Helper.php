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
 
 
 
class Article_Form_Helper
{

  public static function getContentField($name, $options = array())
  {
    static $content_fields = null;
    
    if (null === $content_fields)
    {
      $content_fields = array(
      
        'title' => array(
                'Text',
                'title',
                array(
                  'label' => 'Title',
                )
              ),
        'max' => array(
                'Text',
                'max',
                array(
                  'label' => 'Max Items',
                  'value' => 5,
                ),
              ),
        'user_type' => array(
                'Radio',
                'user_type',
                array(
                    'label' => 'User',
                    'multiOptions' => array(
                      'owner' => 'OWNER - article\'s created by owner of the current viewing page',
                      'viewer' => 'VIEWER - article\'s created by the current active logged in member',
                    ),
                    'value' => 'owner',
                  ),
                ),
        'user' => array(
                'Text',
                'user',
                array(
                  'label' => 'User',
                )
              ),
        'keyword' => array(
                'Text',
                'keyword',
                array(
                  'label' => 'Keywords',
                )
              ),   
        'category' => array(
                'Select', 
                'category',
                array(
                  'label' => 'Category',
                  'multiOptions' => array(""=>"") + Engine_Api::_()->getItemTable('article_category')->getMultiOptionsAssoc(),
                )
              ),       
        'order' => array(
                'Select', 
                'order',
                array(
                  'label' => 'Sort By',
                  'multiOptions' => array(
                    'recent' => 'Most Recent',
                    'lastupdated' => 'Last Updated',
                    'alphabet' => 'Title',
                    'mostviewed' => 'Most Viewed',
                    'mostcommented' => 'Most Commented',
                    'mostliked' => 'Most Liked',
                    'random' => 'Randomized',
                  ),
                  'value' => 'recent',
                )
              ),
              
        'period' => array(
                'Select', 
                'period',
                array(
                  'label' => 'Time Period',
                  'multiOptions' => array(
                    'all' => 'All Time',
                    '24hrs' => 'Last 24 Hours',
                    'week' => '7 Days',
                    'month' => '30 Days',
                    'quarter' => '3 Months',
                    'year' => '12 Months',
                  ),
                )
              ),
  
        'display_style' => array(
                'Radio',
                'display_style',
                array(
                  'label' => 'Display Style',
                  'multiOptions' => array(
                    'wide' => "Wide (main middle column)",
                    'narrow' => "Narrow (left / side side column)",
                  ),
                  'value' => 'wide',
                )
              ),
        'showphoto' => array(
                'Select', 
                'showphoto',
                array(
                  'label' => 'Show Photo',
                  'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                  ),
                  'value' => 1,
                )
              ),  
        'showdetails' => array(
                'Select', 
                'showdetails',
                array(
                  'label' => 'Show Details',
                  
                  'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                  ),
                  'value' => 1,
                )
              ), 
        'showmeta' =>  array(
                'Select', 
                'showmeta',
                array(
                  'label' => 'Show Meta',
                  
                  'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                  ),
                  'value' => 1,
                )
              ), 
        'showdescription' =>  array(
                'Select', 
                'showdescription',
                array(
                  'label' => 'Show Description',
                  'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                  ),
                  'value' => 1,
                )
              ),  
        'featured' => array(
                'Select', 
                'featured',
                array(
                  'label' => 'Show only featured articles?',
                  'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                  ),
                  'value' => 0,
                )
              ),
        'sponsored' => array(
                'Select', 
                'sponsored',
                array(
                  'label' => 'Show only sponsored articles?',
                  'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                  ),
                  'value' => 0,
                )
              ),     
        'showemptyresult' => array(
            'Select', 
            'showemptyresult',
            array(
              'label' => 'Show Empty Result',
              'multiOptions' => array(
                0 => 'No',
                1 => 'Yes',
              ),
              'value' => 0,
            )
          ),
        'showmemberitemlist' => array(
            'Select', 
            'showmemberitemlist',
            array(
              'label' => 'Show Member\'s Article Link',
              'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
              ),
              'value' => 1,
            )
          ),
        'showlinkall' => array(
            'Select', 
            'showlinkall',
            array(
              'label' => 'Show Link All',
              'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
              ),
              'value' => 1,
            )
          ),
      );
    }
    
    if (array_key_exists($name, $content_fields)) {
      $field = $content_fields[$name];
    }
    else {
      $field = array(
        'Text',
        $name,
        array(
          'label' => $name
        ),
      );
    }
    
    $keys = array('value', 'label', 'multiOptions');
    foreach ($options as $key => $value) {
      if (in_array($key, $keys)) {
        $field[2][$key] = $value;
      }
    }
    
    return $field;
  }

}