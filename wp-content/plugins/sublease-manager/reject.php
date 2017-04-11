<?php
	/*
		Name: reject.php
		Authors: Xavier Durand-Hollis Jr
		
		A webservice that allows authorized users to make a posting rejected / unlisted.
	*/
	
	include 'common.php';

	if(!isset($_POST["postingsIds"])) fail("Please provide at least one or more postings to reject.");
	
	$postingsIds = json_decode($_POST["postingsIds"]);
	if(count($postingsIds) <= 0)
		fail("Please provide at least one or more postings to reject.");

	// probably not the best. many queries. in separate trips..
	for($i = 0; $i < count($postingsIds); $i++)
	{
		change_state($postingsIds[$i],STATE_REJECTED);
	}
	
	$msg = create("Changed state of posting tovnbvbnv " . get_state_title(STATE_REJECTED));
	$msg["state"] = STATE_REJECTED;
	$msg["readonly"] = (read_only(STATE_REJECTED) ? 1 : 0);
	finish($msg);
	
	
?>