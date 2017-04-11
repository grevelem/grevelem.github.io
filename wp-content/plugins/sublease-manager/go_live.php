<?php
	/*
		Name: go_live.php
		Authors: Xavier Durand-Hollis Jr
		
		A webservice that allows authorized users to make a posting live.
	*/
	
	include 'common.php';

	if(!isset($_POST["postingsIds"])) fail("Please provide at least one or more postings to go live.");

	$postingsIds = json_decode($_POST["postingsIds"]);
	if(count($postingsIds) <= 0)
		fail("Please provide at least one or more postings to go live.");
	
	// probably not the best. many queries. in separate trips..
	for($i = 0; $i < count($postingsIds); $i++)
	{
		change_state($postingsIds[$i],STATE_LIVE);
	}
	
	$msg = create("Successfully made the given postings live.");
	$msg["state"] = STATE_LIVE;
	$msg["readonly"] = (read_only(STATE_LIVE) ? 1 : 0);
	finish($msg);
	
	
?>