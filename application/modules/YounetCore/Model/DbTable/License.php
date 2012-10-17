<?php
class YounetCore_Model_DbTable_License extends Engine_Db_Table
{
    protected $_primary = 'id';  
    //protected $_rowClass = 'YounetCore_Model_License';
    public static function getInstance()
    {
        if(null == self::$_inst){
            self::$_inst = new self();
        }
        return self::$_inst;    
    }
    
}