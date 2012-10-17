<?php
class YounetCore_Model_Install extends Core_Model_List
{
    public function getTable()
    {
        if( is_null($this->_table) )
        {
            $this->_table = Engine_Api::_()->getDbtable('Install', 'YounetCore');
        }

        return $this->_table;
    }

}