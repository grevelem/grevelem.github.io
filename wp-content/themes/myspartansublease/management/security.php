<?php

/*
	Name: security.php
	Authors: Xavier Durand-Hollis Jr
	
	Determines if a user is authorized to use this service using
	Wordpress login / roles.
	
*/
	require_once($_SERVER['DOCUMENT_ROOT'].'/wp-blog-header.php');
	if(!is_user_logged_in())
	{
		die("Not authorized"); // not authorized
	}

	function UserIsAdmin()
	{
		// based on the Wordpress role.
		if(current_user_can('manage_options'))
		{
			return true;
		}
	
		return false;
	}
	
?>