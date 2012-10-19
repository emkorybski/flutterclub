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
 
 
 
class Article_Plugin_Core
{
  public function onStatistics($event)
  {
    $table   = Engine_Api::_()->getDbTable('articles', 'article');
    
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), 'COUNT(*) AS count');
    $event->addResponse($select->query()->fetchColumn(0), 'article');
  }


  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if( $payload instanceof User_Model_User ) {
      // Delete articles
      $articleTable = Engine_Api::_()->getDbtable('articles', 'article');
      $articleSelect = $articleTable->select()->where('owner_id = ?', $payload->getIdentity());
      foreach( $articleTable->fetchAll($articleSelect) as $article ) {
        $article->delete();
      }
      // delete images and albums as well
    }
  }
}