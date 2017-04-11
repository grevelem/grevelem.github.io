<?php
/*
	Name: public_contact_owner.php
	Authors: Xavier Durand-Hollis Jr
	
	Sends an email to the owner of a given posting notifying them that someone is interested in their sublease.
*/

	include($_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/sublease-manager/common.php");
	
	if(!isset($_POST["firstName"]) || !isset($_POST["lastName"]) || !isset($_POST["contactNumber"]) || !isset($_POST["email"]))
	{
		fail("Please provide all required fields.");
	}
	if(!isset($_POST["postingId"]))
	{
		fail("Please provide a posting id.");
	}
	
	email_contact($_POST["postingId"], $_POST["firstName"], $_POST["lastName"], $_POST["contactNumber"], $_POST["email"]);
	
	$msg = create("Successfully contacted owner.");
	finish($msg);
	
?>