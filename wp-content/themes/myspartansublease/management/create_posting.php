<?php

/*
	Name: create_posting.php
	Authors: Xavier Durand-Hollis Jr
	
	Updates a request with given info, then creates a posting and links that request
	to the posting. Also links given photos to the posting. 
	
*/

	include 'security.php';
	include 'mss_config.php'; // database connection
	include 'functions.php';
	
	if(is_actionable($_POST["id"]))
	{
	
		$state = STATE_REVIEW;
		$postingId = create_posting($_POST["id"], $state); // set posting to pending review

		$message = map_upload($_POST["photoUpload1"], $postingId, 0);
		if(!$message["success"]) rollback($_POST["id"], $postingId, $message);

		$message = map_upload($_POST["photoUpload2"], $postingId, 1);
		if(!$message["success"]) rollback($_POST["id"], $postingId, $message);
		
		$message = map_upload($_POST["photoUpload3"], $postingId, 2);
		if(!$message["success"]) rollback($_POST["id"], $postingId, $message);
		
		$message = map_upload($_POST["photoUpload4"], $postingId, 3);
		if(!$message["success"]) rollback($_POST["id"], $postingId, $message);
		
		$msg = modify_request(0);
		if(!$msg["success"]) modify_unsuccessful($_POST["id"], $postingId, $msg);
		
		$posted_items["success"] = true;
		$posted_items["message"] = "Successfully created a new posting.";
		$posted_items["state"] = $state . "";
		$posted_items["readonly"] = "1";
		$posted_items["postingId"] = $postingId;
		echo json_encode($posted_items);
		exit();
	}else{
		fail("This request is not actionable.");
	}
	function modify_unsuccessful($requestId, $postingId, $message)
	{
		set_actionable($requestId, 1);
		$msg = create_posting($requestId, STATE_UNKNOWN);
		if(!$message["success"])
			$message["message"] .= "\n" . $msg["message"];
		finish($message);
	}
	function rollback($requestId, $postingId, $message)
	{
		set_actionable($requestId, 1);
		$msg = remove_posting($postingId); 
		if(!$msg["success"]){
			create_posting($requestId, STATE_UNKNOWN);
			finish($msg);
		}
		
		
		finish($message);
	}

?>