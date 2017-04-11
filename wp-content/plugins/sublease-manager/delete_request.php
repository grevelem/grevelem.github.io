<?php
	/*
		Name: delete_request.php
		Authors: Xavier Durand-Hollis Jr
		
		Deletes a request that has no posting associated with it.
	*/
	

	include 'common.php';
	
	if(!isset($_POST["requestIds"])) fail("Please provide at least one or more requests to delete.");
	
	$requestIds = json_decode($_POST["requestIds"]);
	if(count($requestIds) <= 0)
		fail("Please provide at least one or more requests to delete.");
	
	// probably not the best. many queries. in separate trips..
	for($i = 0; $i < count($requestIds); $i++)
	{
		delete_request($requestIds[$i]);
	}

	
	$msg = create("Successfully deleted requests.");
	$msg["affected_postings"] = $requestIds;
	
	finish($msg);
	
?>