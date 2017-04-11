<?php
	/*
		Name: constants.php
		Authors: Xavier Durand-Hollis Jr
		
		Contains functions for creating links / formatting emails that won't change.
	*/
	
	/*
		Constants
	*/
	
	function site()
	{
		return "http://www.myspartansublease.com";
	}
	
	function sublease_link($requestId)
	{	
		return site() . "/wp-content/themes/myspartansublease/management/sublease.php?id=" . $requestId;
	}
	
	function mail_subject_prefix()
	{
		return "[My Spartan Sublease] ";
	}
	
?>