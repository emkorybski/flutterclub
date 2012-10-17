<?php
class YounetCore_Model_DbTable_Install extends Engine_Db_Table
{
    protected $_rowClass = 'YounetCore_Model_Install';

    public static function getInstance()
    {
        if(null == self::$_inst){
            self::$_inst = new self();
        }
        return self::$_inst;    
    }
    
}