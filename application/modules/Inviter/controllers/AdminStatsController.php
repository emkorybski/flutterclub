<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminStatsController.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_AdminStatsController extends Core_Controller_Action_Admin
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

  public function init()
  {
    $this->view->headTranslate(array(
      'INVITER_Sent Invites',
      'INVITER_Referred Invites',
    ));
  }

  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('inviter_admin_main', array(), 'inviter_admin_main_stats');

    $this->view->formFilter = $formFilter = new User_Form_Admin_Manage_Filter();

    $formFilter->removeElement('enabled');
    $page = $this->_getParam('page',1);

    $invitesTb = Engine_Api::_()->getDbtable('invites', 'inviter');
    $table = $this->_helper->api()->getDbtable('users', 'user');
    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($table->info('name'))
      ->join($invitesTb->info('name'),
             $invitesTb->info('name').'.user_id='.$table->info('name').'.user_id
             LEFT JOIN(
                    SELECT user_id, COUNT(invite_id) AS inviter_referred
                      FROM '.$invitesTb->info('name').'
                      WHERE '.$invitesTb->info('name').'.new_user_id > 0
                      GROUP BY user_id
                    ) AS ref
                    ON '.$invitesTb->info('name').'.user_id = ref.user_id',
            array('COUNT('.$invitesTb->info('name').'.invite_id) AS inviter_sent', 'IF(ref.inviter_referred>0, ref.inviter_referred, 0) AS inviter_referred'))

      ->group($invitesTb->info('name').'.user_id')
      ;
    
    // Process form
    $values = array();
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    }

    foreach( $values as $key => $value ) {
      if( null === $value ) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
      'order' => 'inviter_sent',
      'order_direction' => 'DESC',
    ), $values);
    
    $this->view->assign($values);

    // Set up select info
    $select->order(( !empty($values['order']) ? $values['order'] : 'user_id' ) . ' ' . ( !empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    if( !empty($values['username']) )
    {
      $select->where('username LIKE ?', '%' . $values['username'] . '%');
    }

    if( !empty($values['email']) )
    {
      $select->where('email LIKE ?', '%' . $values['email'] . '%');
    }

    if( !empty($values['level_id']) )
    {
      $select->where('level_id = ?', $values['level_id'] );
    }
    
    if( isset($values['enabled']) && $values['enabled'] != -1 )
    {
      $select->where('enabled = ?', $values['enabled'] );
    }
    
    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator = $paginator = $paginator->setCurrentPageNumber( $page );
  
    // Make statistic
    $sentSl = $invitesTb->select()
      ->setIntegrityCheck(false)
      ->from($invitesTb->info('name'), array('COUNT(invite_id) AS sent'))
      ->limit(1);

    $referredSl = $invitesTb->select()
      ->setIntegrityCheck(false)
      ->from($invitesTb->info('name'), array('COUNT(invite_id) AS referred'))
      ->where('new_user_id > ?', 0)
      ->limit(1);

    $sent = $invitesTb->fetchRow($sentSl);
    $referred = $invitesTb->fetchRow($referredSl);

    $this->view->total_sent_invites = $sent->sent;
    $this->view->total_refferred_users = $referred->referred;

    if ($sent->sent > 0)
      $this->view->invitest_to_refferred = round(floatval(($referred->referred*100)/$sent->sent), 2);
    else 
      $this->view->invitest_to_refferred = 0;

    $this->view->superAdminCount = count(Engine_Api::_()->user()->getSuperAdmins());
    $this->view->hideEmails = _ENGINE_ADMIN_NEUTER;
    //$this->view->formDelete = new User_Form_Admin_Manage_Delete();
  }

  public function chartAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('inviter_admin_main', array(), 'inviter_admin_main_charts');

      // Get types
    $statsTable = Engine_Api::_()->getDbtable('statistics', 'inviter');
    $select = new Zend_Db_Select($statsTable->getAdapter());
    $select
      ->from($statsTable->info('name'), 'type')
      ->distinct(true)
      ->order('type DESC')
      ;

    $data = $select->query()->fetchAll();
    $types = array();

    foreach( $data as $datum )
    {
      $type = $datum['type'];
      $fancyType = 'INVITER_ADMIN_STATS_' . strtoupper(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $type), '_'));
      $types[$type] = $fancyType;
    }
    $t = array();

    $t[implode(',', array_keys($types))] = implode('_',$types);

    foreach ($types as $key=>$value)
    {
      $t[$key] = $value;  
    }

    $this->view->filterForm = $filterForm = new Core_Form_Admin_Statistics_Filter();
    $filterForm->type->setMultiOptions($t);
  }

  public function chartDataAction()
  {
    // Disable layout and viewrenderer
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    // Get params
    $types = $this->_getParam('type');
    $start = $this->_getParam('start');
    $offset = $this->_getParam('offset', 0);
    $mode = $this->_getParam('mode');
    $chunk = $this->_getParam('chunk');
    $period = $this->_getParam('period');
    $periodCount = $this->_getParam('periodCount', 1);
    //$end = $this->_getParam('end');
    $types = explode(',', str_replace(' ', '', $types));
    $types = is_array($types)?$types:array($types);
    
    // Validate chunk/period
    if( !$chunk || !in_array($chunk, $this->_periods) ) {
      $chunk = Zend_Date::DAY;
    }
    if( !$period || !in_array($period, $this->_periods) ) {
      $period = Zend_Date::MONTH;
    }
    if( array_search($chunk, $this->_periods) >= array_search($period, $this->_periods) ) {
      die('whoops');
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

    // Get data
    $statsTable = Engine_Api::_()->getDbtable('statistics', 'inviter');

    $rawDatas = array();
    foreach($types as $type)
    {
    $statsSelect = $statsTable->select()
      ->where('type = ?', $type)
      ->where('date >= ?', gmdate('Y-m-d H:i:s', $startObject->getTimestamp()))
      ->where('date < ?', gmdate('Y-m-d H:i:s', $endObject->getTimestamp()))
      ->order('date ASC')
      ;
    $rawDatas[] = $statsTable->fetchAll($statsSelect);
    }

    // Now create data structure
    $currentObject = clone $startObject;
    $nextObject = clone $startObject;
    $values = array();
    $dataLabels = array();
    $cumulative = 0;
    $previous = 0;

    do {
      $nextObject->add(1, $chunk);
      
      $currentObjectTimestamp = $currentObject->getTimestamp();
      $nextObjectTimestamp = $nextObject->getTimestamp();

      foreach($rawDatas as $key =>$rawData)
      {
        $values[$key][$currentObjectTimestamp] = $cumulative;

        // Get everything that matches
        $currentPeriodCount = 0;
        foreach( $rawData as $rawDatum ) {
          $rawDatumDate = strtotime($rawDatum->date);
          if( $rawDatumDate >= $currentObjectTimestamp && $rawDatumDate < $nextObjectTimestamp ) {
            $currentPeriodCount += $rawDatum->value;
          }
        }

        // Now do stuff with it
        switch( $mode
        ) {
          default:
          case 'normal':
            $values[$key][$currentObjectTimestamp] = $currentPeriodCount;
            break;
          case 'cumulative':
            $cumulative += $currentPeriodCount;
            $values[$key][$currentObjectTimestamp] = $cumulative;
            break;
          case 'delta':
            $values[$key][$currentObjectTimestamp] = $currentPeriodCount - $previous;
            $previous = $currentPeriodCount;
            break;
        }
      }

      $currentObject->add(1, $chunk);
    } while( $currentObject->getTimestamp() < $endObject->getTimestamp() );

    // Reprocess label

    $labelStrings = array();
    $labelDate = new Zend_Date();
    foreach($values[0] as $key => $value ) {
      $labelDate->set($key);
      $labelStrings[] = $labelDate->toString('d/M/Y'); //date('D M d Y', $key);
    }

    $tmp_values = array();
    foreach ($values as $value)
    {
      $tmp_values[] = array_values($value);
    }

    $data = array();
    foreach ($labelStrings as $key=>$label)
    {
      $data[$key] = array();
      $data[$key][] = $label;
      foreach($tmp_values as $value)
      {
        $data[$key][] = $value[$key];
      }
    }
    

    $translate = Zend_Registry::get('Zend_Translate');
    foreach ($types as $type)
    {
      $titleStr[] = 'INVITER_ADMIN_STATS_' . strtoupper(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $type), '_'));
    }

    $this->view->title = $translate->_(implode('_', $titleStr));
    $this->view->data = $data;
    // Send
  }
}