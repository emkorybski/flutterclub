<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Statistics.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_Model_DbTable_Statistics extends Engine_Db_Table
{
  protected $_name = 'inviter_statistics';
  
  public function increment($type, $value = 1, $time = null)
  {
    // Check args
    if( $value === 0 ) {
      return $this;
    }

    if( !is_numeric($value) ) {
      throw new Engine_Exception('statistics can only handle numeric values');
    }

    if( null === $time ) {
      $time = time();
    }

    // Check db
    $periodValue = gmdate('Y-m-d', $time);
    //$periodValue = gmdate('Y-m-d H:i:s');

    $sign = ( $value > 0 ? '+' : '-' );
    $absValue = abs($value);

    try {

      $this->insert(array(
        'value' => 0,
        'type' => 'inviter.sents',
        'date' => $periodValue,
      ));

      $this->insert(array(
        'value' => 0,
        'type' => 'inviter.referreds',
        'date' => $periodValue,
      ));
    } catch( Exception $e ) {
      // Meh, just ignore
      //throw $e;
    }

    $this->update(array(
      'value' => new Zend_Db_Expr('value ' . $sign . ' ' . $this->getAdapter()->quote($absValue)),
    ), array(
      'type = ?' => $type,
      'date = ?' => $periodValue,
    ));

    return $this;
  }

  public function getTotal($type, $start = null, $end = null)
  {
    $select = new Zend_Db_Select($this->getAdapter());
    $select
      ->from($this->info('name'), 'SUM(value) as sum')
      ->where('type = ?', $type)
      ;

    // Can pass "today" into start
    switch( $start ) {
      case 'day':
        $start = mktime(0, 0, 0, gmdate("n"), gmdate("j"), gmdate("Y"));
        $end = mktime(0, 0, 0, gmdate("n"), gmdate("j") + 1, gmdate("Y"));
        break;
      case 'week':
        $start = mktime(0, 0, 0, gmdate("n"), gmdate("j") - gmdate('N') + 1, gmdate("Y"));
        $end = mktime(0, 0, 0, gmdate("n"), gmdate("j") - gmdate('N') + 1 + 7, gmdate("Y"));
        break;
      case 'month':
        $start = mktime(0, 0, 0, gmdate("n"), gmdate("j"), gmdate("Y"));
        $end = mktime(0, 0, 0, gmdate("n") + 1, gmdate("j"), gmdate("Y"));
        break;
      case 'year':
        $start = mktime(0, 0, 0, gmdate("n"), gmdate("j"), gmdate("Y"));
        $end = mktime(0, 0, 0, gmdate("n"), gmdate("j"), gmdate("Y") + 1);
        break;
    }

    if( null !== $start ) {
      $select->where('date >= ?', gmdate('Y-m-d', $start));
    }

    if( null !== $end ) {
      $select->where('date < ?', gmdate('Y-m-d', $end));
    }

    $data = $select->query()->fetch();

    if( !isset($data['sum']) ) {
      return 0;
    }

    return $data['sum'];
  }
}