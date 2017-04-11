<?php
	/*
		Name: delete_posting.php
		Authors: Xavier Durand-Hollis Jr
		
		A webservice that allows authorized users to delete a posting (not permanently).
	*/
	include 'common.php';

	if(!isset($_POST["postingsIds"])) fail("Please provide at least one or more postings to delete.");
	
	$postingsIds = json_decode($_POST["postingsIds"]);
	if(count($postingsIds) <= 0)
		fail("Please provide at least one or more postings to delete.");
	
	// probably not the best. many queries. in separate trips..
	for($i = 0; $i < count($postingsIds); $i++)
	{
		delete_posting($postingsIds[$i]);
	}
	
	$msg = create("Successfully deleted the given postings");
	$msg["state"] = STATE_DELETED;
	$msg["readonly"] = (read_only(STATE_DELETED) ? 1 : 0);
	finish($msg);
?>