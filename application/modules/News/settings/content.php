<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    News
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: content.php 6590 2010-06-25 19:40:21Z john $
 * @author     John
 */
return array(
  /*array(
  	'title' => 'News',
    'description' => 'Display all news.',
  	'category' => 'News',
  	'type' => 'widget',
    'name' => 'news.browse-news',
    'defaultParams' => array(
  		'title' => '',
  	),
	'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title', 'value' => '',
          )
        ),     
      )
    ),
  ),*/
    array(
    'title' => 'Menu News',
    'description' => 'Displays menu newa on listing news page.',
    'category' => 'News',
    'type' => 'widget',
    'name' => 'news.menu-news',
  ),
   array(
  	'title' => 'Recent News',
    'description' => 'Displays Recent News',
  	'category' => 'News',
  	'type' => 'widget',
    'name' => 'news.lasted-news',
    'defaultParams' => array(
  		'title' => 'Recent News',
  	),
	'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
			'title' => 'Recent News',
          )
        ),
        
        array(
          'Text',
          'max',
         
           array(
            'label' => 'Max Item Count',
            'description' => 'Number of shown data item of each widget.',
            'value' => 5,            
          )          
        ),     
      )
    ),
  ),
   array(
    'title' => 'Search News',
    'description' => 'Displays search News on Listing News page.',
    'category' => 'News',
    'type' => 'widget',
    'name' => 'news.search-news',
  ),
   array(
  	'title' => 'Top News',
    'description' => 'Displays Top News',
  	'category' => 'News',
  	'type' => 'widget',
    'name' => 'news.top-news',
    'defaultParams' => array(
  		'title' => 'Top News',
  	),
	'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
			'title' => 'Top News',
          )
        ),
        
        array(
          'Text',
          'max',
         
           array(
            'label' => 'Max Item Count',
            'description' => 'Number of shown data item of each widget.',
            'value' => 5,            
          )          
        ),     
      )
    ),
  ),
  array(
      'title' => 'Most Commented News',
    'description' => 'Displays Most Commented News',
      'category' => 'News',
      'type' => 'widget',
    'name' => 'news.most-commented-news',
    'defaultParams' => array(
          'title' => 'Most Commented News',
      ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
            'title' => 'Most Commented News',
          )
        ),
        
        array(
          'Text',
          'max',
         
           array(
            'label' => 'Max Item Count',
            'description' => 'Number of shown data item of each widget.',
            'value' => 5,            
          )          
        ),     
      )
    ),
  ),
  array(
      'title' => 'Most Liked News',
    'description' => 'Displays Most Liked News',
      'category' => 'News',
      'type' => 'widget',
    'name' => 'news.most-liked-news',
    'defaultParams' => array(
          'title' => 'Most Liked News',
      ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
            'title' => 'Most Liked News',
          )
        ),
        
        array(
          'Text',
          'max',
         
           array(
            'label' => 'Max Item Count',
            'description' => 'Number of shown data item of each widget.',
            'value' => 5,            
          )          
        ),     
      )
    ),
  ),
  array(
      'title' => 'Featured News',
    'description' => 'Displays Featured News',
      'category' => 'News',
      'type' => 'widget',
    'name' => 'news.featured-news',
    'defaultParams' => array(
          'title' => 'Featured News',
      ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
            'title' => 'Featured News',
          )
        ),
        
        array(
          'Text',
          'max',
         
           array(
            'label' => 'Max Item Count',
            'description' => 'Number of shown data item of each widget.',
            'value' => 5,            
          )          
        ),     
      )
    ),
  ),

  array(
  	'title' => 'Listing News',
    'description' => 'Displays Listing News page',
  	'category' => 'News',
  	'type' => 'widget',
    'name' => 'news.list-news',
    'defaultParams' => array(
  		'title' => 'Listing News',
  	),
	'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title', 'value' => 'Listing News',
          )
        ),
        /*array(
          'Select',
          'category',
         
           array(
            'label' => 'Category',
            'description' => 'Select category to get news for each widget.',
            'default' => 0,
            'multiOptions' => Engine_Api::_()->news()->getAllCategoriesSelect(),       
          )          
        ),         */
        array(
          'Text',
          'max',
         
           array(
            'label' => 'Max Item Count',
            'description' => 'Number of shown data item of each widget.',
            'value' => 5,            
          )          
        ),     
      )
    ),
  ), 

 array(
      'title' => 'Listing News 2',
    'description' => 'Displays Listing News page',
      'category' => 'News',
      'type' => 'widget',
    'name' => 'news.listing-news',
    'defaultParams' => array(
          'title' => 'Listing News 2',
      ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title', 'value' => 'Listing News',
          )
        ),
        array(
          'Text',
          'max',
         
           array(
            'label' => 'Max Item Count',
            'description' => 'Number of shown data item of each widget.',
            'value' => 5,            
          )          
        ),     
      )
    ),
  ),
) 
?>