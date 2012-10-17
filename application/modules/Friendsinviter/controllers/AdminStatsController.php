<?php

class Friendsinviter_AdminStatsController extends Core_Controller_Action_Admin
{

  protected $_periods = array(
    Zend_Date::DAY, //dd
    Zend_Date::WEEK, //ww
    Zend_Date::MONTH, //MM
    Zend_Date::YEAR, //y
  );

  protected $_allPeriods = array(
    Zend_Date::SECOND,
    Zend_Date::MINUTE,
    Zend_Date::HOUR,
    Zend_Date::DAY,
    Zend_Date::WEEK,
    Zend_Date::MONTH,
    Zend_Date::YEAR,
  );

  protected $_periodMap = array(
    Zend_Date::DAY => array(
      Zend_Date::SECOND => 0,
      Zend_Date::MINUTE => 0,
      Zend_Date::HOUR => 0,
    ),
    Zend_Date::WEEK => array(
      Zend_Date::SECOND => 0,
      Zend_Date::MINUTE => 0,
      Zend_Date::HOUR => 0,
      Zend_Date::WEEKDAY_8601 => 1,
    ),
    Zend_Date::MONTH => array(
      Zend_Date::SECOND => 0,
      Zend_Date::MINUTE => 0,
      Zend_Date::HOUR => 0,
      Zend_Date::DAY => 1,
    ),
    Zend_Date::YEAR => array(
      Zend_Date::SECOND => 0,
      Zend_Date::MINUTE => 0,
      Zend_Date::HOUR => 0,
      Zend_Date::DAY => 1,
      Zend_Date::MONTH => 1,
    ),
  );
  

  public function indexAction()
  {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('friendsinviter_admin_main', array(), 'friendsinviter_admin_main_stats');
    
    //$filter_types = array("'user.invitations'");
    
    //// Get types
    //$statsTable = Engine_Api::_()->getDbtable('statistics', 'core');
    //$select = new Zend_Db_Select($statsTable->getAdapter());
    //$select
    //  ->from($statsTable->info('name'), 'type')
    //  ->where("type in ('friendsinviter.imported_contacts','friendsinviter.invited_contacts')")
    //  ->distinct(true)
    //  ;
    //
    //$data = $select->query()->fetchAll();
    //$types = array();
    //foreach( $data as $datum ) {
    //  $type = $datum['type'];
    //  $fancyType = '_FRIENDSINVITER_ADMIN_STATS_' . strtoupper(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $type), '_'));
    //  $types[$type] = $fancyType;
    //}

    $types['invitedvsconverted'] = '100010096';
    $types['importedvsinvited'] = '100010097';
    
    $this->view->filterForm = $filterForm = new Friendsinviter_Form_Admin_Statistics_Filter();
    $filterForm->type->setMultiOptions($types);
    
  }


  public function chartDataAction()
  {
    // Disable layout and viewrenderer
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $translate = Zend_Registry::get('Zend_Translate');

    // Get params
    $type = $this->_getParam('type');
    $start = $this->_getParam('start');
    $offset = $this->_getParam('offset', 0);
    $mode = $this->_getParam('mode');
    $chunk = $this->_getParam('chunk');
    $period = $this->_getParam('period');
    $periodCount = $this->_getParam('periodCount', 1);
    //$end = $this->_getParam('end');

    // Validate chunk/period
    if( !$chunk || !in_array($chunk, $this->_periods) ) {
      $chunk = Zend_Date::DAY;
    }
    if( !$period || !in_array($period, $this->_periods) ) {
      $period = Zend_Date::MONTH;
    }

    if( array_search($chunk, $this->_periods) >= array_search($period, $this->_periods) ) {

      $response = <<<EOC
{
  "title": {
    "text": "{$translate->translate('Please choose valid sub-period')}",
    "style": "{font-size: 14px;font-weight: bold;margin-bottom: 10px; color: #777777;}"
  },
  "elements": [
    {
      "type": "line",
      "values": [
        0
      ],
      "colour": "#5ba1cd"
    }
  ],
  "bg_colour": "#ffffff",
  "x_axis": {
    "labels": {
      "steps": 1,
      "labels": [
      ]
    },
    "colour": "#416b86",
    "grid-colour": "#dddddd",
    "steps": 1
  },
  "y_axis": {
    "min": 0,
    "max": 1,
    "steps": 1,
    "colour": "#416b86",
    "grid-colour": "#dddddd"
  }
}
EOC;
      $this->getResponse()->setBody( $response );
      return;
    }

    // Validate start
    if( $start && !is_numeric($start) ) {
      $start = strtotime($start);
    }
    if( !$start ) {
      $start = time();
    }
    
    // Make start fit to period?
    $startObject = new Zend_Date($start);
    
    $partMaps = $this->_periodMap[$period];
    foreach( $partMaps as $partType => $partValue ) {
      $startObject->set($partValue, $partType);
    }

    // Do offset
    if( $offset != 0 ) {
      $startObject->add($offset, $period);
    }
    
    // Get end time
    $endObject = new Zend_Date($startObject->getTimestamp());
    $endObject->add($periodCount, $period);

    $multiline = false;
    
    switch($type) {

      //case 'invitedvsimported':
      case 'invitedvsconverted':

        $multiline = true;
        $title_var = '100010096';
        $key1 = '100010118';
        $key2 = '100010119';
        
        // Get data
        $type = 'friendsinviter.invites';
        $statsTable = Engine_Api::_()->getDbtable('statistics', 'core');
        $statsSelect = $statsTable->select()
          ->where('type = ?', $type)
          ->where('date >= ?', gmdate('Y-m-d H:i:s', $startObject->getTimestamp()))
          ->where('date < ?', gmdate('Y-m-d H:i:s', $endObject->getTimestamp()))
          ->order('date ASC')
          ;

        $rawData = $statsTable->fetchAll($statsSelect);

        $type = 'friendsinviter.converted_invites';
        $statsTable = Engine_Api::_()->getDbtable('statistics', 'core');
        $statsSelect = $statsTable->select()
          ->where('type = ?', $type)
          ->where('date >= ?', gmdate('Y-m-d H:i:s', $startObject->getTimestamp()))
          ->where('date < ?', gmdate('Y-m-d H:i:s', $endObject->getTimestamp()))
          ->order('date ASC')
          ;

        $rawData2 = $statsTable->fetchAll($statsSelect);
        
        break;
        


      case 'importedvsinvited':

        $multiline = true;
        $title_var = '100010097';
        $key1 = '100010116';
        $key2 = '100010117';
        
        // Get data
        $type = 'friendsinviter.imported_contacts';
        $statsTable = Engine_Api::_()->getDbtable('statistics', 'core');
        $statsSelect = $statsTable->select()
          ->where('type = ?', $type)
          ->where('date >= ?', gmdate('Y-m-d H:i:s', $startObject->getTimestamp()))
          ->where('date < ?', gmdate('Y-m-d H:i:s', $endObject->getTimestamp()))
          ->order('date ASC')
          ;

        $rawData = $statsTable->fetchAll($statsSelect);

        $type = 'friendsinviter.invited_contacts';
        $statsTable = Engine_Api::_()->getDbtable('statistics', 'core');
        $statsSelect = $statsTable->select()
          ->where('type = ?', $type)
          ->where('date >= ?', gmdate('Y-m-d H:i:s', $startObject->getTimestamp()))
          ->where('date < ?', gmdate('Y-m-d H:i:s', $endObject->getTimestamp()))
          ->order('date ASC')
          ;

        $rawData2 = $statsTable->fetchAll($statsSelect);
        
      break;
    
    }
    
    // Now create data structure
    $currentObject = clone $startObject;
    $nextObject = clone $startObject;
    $data = array();
    $dataLabels = array();
    $cumulative = 0;
    $previous = 0;

    $data2 = array();
    $cumulative2 = 0;

    do {
      $nextObject->add(1, $chunk);
      
      $currentObjectTimestamp = $currentObject->getTimestamp();
      $nextObjectTimestamp = $nextObject->getTimestamp();

      $data[$currentObjectTimestamp] = $cumulative;
      
      if($multiline) {
        $data2[$currentObjectTimestamp] = $cumulative2;
      }

      // Get everything that matches
      $currentPeriodCount = 0;
      foreach( $rawData as $rawDatum ) {
        $rawDatumDate = strtotime($rawDatum->date);
        if( $rawDatumDate >= $currentObjectTimestamp && $rawDatumDate < $nextObjectTimestamp ) {
          $currentPeriodCount += $rawDatum->value;
        }
      }

      if($multiline) {
        $currentPeriodCount2 = 0;
        foreach( $rawData2 as $rawDatum ) {
          $rawDatumDate = strtotime($rawDatum->date);
          if( $rawDatumDate >= $currentObjectTimestamp && $rawDatumDate < $nextObjectTimestamp ) {
            $currentPeriodCount2 += $rawDatum->value;
          }
        }
      }

      // Now do stuff with it
      switch( $mode ) {
        default:
        case 'normal':
          $data[$currentObjectTimestamp] = $currentPeriodCount;
          if($multiline) {
            $data2[$currentObjectTimestamp] = $currentPeriodCount2;
          }
          break;
        case 'cumulative':
          $cumulative += $currentPeriodCount;
          $data[$currentObjectTimestamp] = $cumulative;
          break;
        case 'delta':
          $data[$currentObjectTimestamp] = $currentPeriodCount - $previous;
          $previous = $currentPeriodCount;
          break;
      }
      
      $currentObject->add(1, $chunk);
    } while( $currentObject->getTimestamp() < $endObject->getTimestamp() );

    // Reprocess label
    $labelStrings = array();
    $labelDate = new Zend_Date();
    foreach( $data as $key => $value ) {
      $labelDate->set($key);
      $labelStrings[] = $this->view->locale()->toDate($labelDate, array('size' => 'short')); //date('D M d Y', $key);
    }

    // Let's expand them by 1.1 just for some nice spacing
    $minVal = min($data);
    $maxVal = max($data);
    $minVal = floor($minVal * ($minVal < 0 ? 1.1 : (1 / 1.1)) / 10) * 10;
    $maxVal = ceil($maxVal * ($maxVal > 0 ? 1.1 : (1 / 1.1)) / 10) * 10;

    // Remove some labels if there are too many
    $xlabelsteps = 1;
    if( count($data) > 10 ) {
      $xlabelsteps = ceil(count($data) / 10);
    }

    // Remove some grid lines if there are too many
    $xsteps = 1;
    if( count($data) > 100 ) {
      $xsteps = ceil(count($data) / 100);
    }

    // Create base chart
    require_once 'OFC/OFC_Chart.php';

    // Make x axis labels
    $x_axis_labels = new OFC_Elements_Axis_X_Label_Set();
    $x_axis_labels->set_steps( $xlabelsteps );
    $x_axis_labels->set_labels( $labelStrings );

    // Make x axis
    $labels = new OFC_Elements_Axis_X();
    $labels->set_labels( $x_axis_labels );
    $labels->set_colour("#416b86");
    $labels->set_grid_colour("#dddddd");
    $labels->set_steps($xsteps);

    // Make y axis
    $yaxis = new OFC_Elements_Axis_Y();
    $yaxis->set_range($minVal, $maxVal/*, $steps*/);
    $yaxis->set_colour("#416b86");
    $yaxis->set_grid_colour("#dddddd");
    
    // Make data
    $graph = new OFC_Charts_Line();
    $graph->set_values( array_values($data) );
    $graph->set_colour("#5ba1cd");
    $graph->set_key($translate->translate($key1), "12");
    
    if($multiline) {

      $graph2 = new OFC_Charts_Line();
      $graph2->set_values( array_values($data2) );
      $graph2->set_colour("#C89341");
      $graph2->set_key($translate->translate($key2), "12");
      
    }

    // Make title
    //$titleStr = $translate->_('_FRIENDSINVITER_ADMIN_STATS_' . strtoupper(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $type), '_')));
    $titleStr = $translate->_($title_var);
    $title = new OFC_Elements_Title( $titleStr . ': '. $startObject->toString() . ' to ' . $endObject->toString() );
    $title->set_style( "{font-size: 14px;font-weight: bold;margin-bottom: 10px; color: #777777;}" );

    // Make full chart
    $chart = new OFC_Chart();
    $chart->set_bg_colour('#ffffff');

    $chart->set_x_axis($labels);
    $chart->add_y_axis($yaxis);
    $chart->add_element($graph);
    if($multiline) {
      $chart->add_element($graph2);
    }
    $chart->set_title( $title );
    
    // Send
    $this->getResponse()->setBody( $chart->toPrettyString() );
  }


}