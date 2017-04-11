<?php
	/*
		Name: email.php
		Authors: Xavier Durand-Hollis Jr
		
		A webservice that allows authorized users to send out an email containing the link
		for a rejected or pending user request.
	*/

	include 'common.php';
	
	if(!isset($_POST["addressTo"])) fail("Please provide an address to send the email to.");
	if(!isset($_POST["requestIds"])) fail("Please provide one or more requests to email.");
	
	$requestIds = json_decode($_POST["requestIds"]);
	if(count($requestIds) <= 0)
		fail("Please provide one or more requests to email.");
	
	email_request($requestIds, $_POST["addressTo"]);

	$posted_items = create("Successfully emailed requests to " . $_POST["addressTo"]);
	finish($posted_items);
?>