<?php
	/*
		Name: go_live.php
		Authors: Xavier Durand-Hollis Jr
		
		A webservice that allows authorized users to make a posting live.
	*/
	
	include 'common.php';

	if(!isset($_POST["postingsIds"])) fail("Please provide at least one or more postings to change the status of.");
	if(!isset($_POST["state"])) fail("Please provide a state to change to.");
	
	$postingsIds = json_decode($_POST["postingsIds"]);
	if(count($postingsIds) <= 0)
		fail("Please provide at least one or more postings to change the status of.");
	
	$state = (int)($_POST["state"]);
	// probably not the best. many queries. in separate trips..
	for($i = 0; $i < count($postingsIds); $i++)
	{
		
		change_state($postingsIds[$i],$state);
	}
	
	$msg = create("Successfully made the given postings " . get_state_title($state));
	$msg["affected_postings"] = $postingsIds;
	finish($msg);
	
	
?>