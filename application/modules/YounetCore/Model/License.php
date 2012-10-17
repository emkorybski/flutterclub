<?php
class YounetCore_Model_License extends Core_Model_List
{
    public function getTable()
    {
        if( is_null($this->_table) )
        {
            $this->_table = Engine_Api::_()->getDbtable('License', 'YounetCore');
        }

        return $this->_table;
    }

}