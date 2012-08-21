<?php

namespace bets;

require_once(PATH_LIB . 'dbrecord.php');

class BetSelection extends DBRecord
{
	protected static $_table = 'fc_bet_selection';

	public function insert()
	{
		call_user_func_array('parent::insert', func_get_args());
	}
	
}