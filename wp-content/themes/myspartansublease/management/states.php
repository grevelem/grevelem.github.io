<?php
/*
	Name: states.php
	Authors: Xavier Durand-Hollis Jr
	
	Constant information about Posting states
*/

	define("STATE_LIVE",0);
	define("STATE_REVIEW",2);
	define("STATE_REJECTED",3);
	define("STATE_DELETED",4);
	define("STATE_UNKNOWN",5);
	
	function get_state_title($state)
	{
		switch($state)
		{
			case STATE_LIVE:
				return "Live";
			case STATE_REVIEW:
				return "Pending Review";
			case STATE_REJECTED:
				return "Rejected / Unlisted";
			case STATE_DELETED:
				return "Deleted";
			default:
				return "Unknown";
		}
	}
	
	function read_only($state)
	{
		return $state == STATE_LIVE || $state == STATE_REVIEW || $state == STATE_DELETED;
	}
	
	function can_change($stateFrom, $stateTo)
	{
		if($stateFrom == STATE_REVIEW)
		{
			return $stateTo == STATE_LIVE || $stateTo == STATE_REJECTED;
		}
		if($stateFrom == STATE_LIVE)
		{
			return $stateTo == STATE_REJECTED;
		}
		if($stateFrom == STATE_REJECTED)
		{
			return $stateTo == STATE_DELETED || $stateTo == STATE_REVIEW;
		}
		
		return false;
	}

?>