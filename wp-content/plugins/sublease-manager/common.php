<?php
	/*
		Name: common.php
		Authors: Xavier Durand-Hollis Jr
		
		Contains some functions that may be of use to various areas of the plugin
	*/

	require_once($_SERVER['DOCUMENT_ROOT']."/wp-content/themes/myspartansublease/management/states.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/wp-content/themes/myspartansublease/management/security.php");
	
	include 'public.php';
	
	function email_contact($postingId, $from_firstName, $from_lastName, $from_contactNumber, $from_email)
	{
		config($mysqli);
		prepare("SELECT r.FirstName, r.LastName, r.Email 
		FROM mss_postings p
		JOIN mss_requests r
			ON r.id = p.RequestId
		WHERE p.id = ? AND p.State = 0", $q, $mysqli);
		$q->bind_param('i', $postingId);
		execute($q, $mysqli);
		$q->bind_result($firstName, $lastName, $email);
		if($q->fetch())
		{
			$body = "
			Dear " . $firstName . ", 
			<br/><br/>
			Someone would like to get in touch with you about your sublease. They have provided their contact details so you can contact them directly.
			<br/>
			<br/>
			" . $from_firstName . " " . $from_lastName . "<br/>
			Phone: " . $from_contactNumber . "<br/>
			Email: " . $from_email . "<br/>
			<br/>
			<br/>
			Thanks,<br/>
			<br/>
			My Spartan Sublease, LLC<br/>
			325 East Grand River Avenue<br/>
			info@myspartansublease.com<br/>
					";
			
			email($body, $email, "Someone has contacted you about your sublease!");
		}else{
			fail("This contact could not be found.");
		}
	}
	
	function email_request($requestIds, $address_to)
	{
		$requests = "";
		
		for($i = 0; $i < count($requestIds); $i++)
		{
			$requests  .= "<br/>" . sublease_link($requestIds[$i]) . "<br/>";
		}
		
		$body = "
Hi,
<br/><br/>
Please review and fill out the following user submitted sublease requests.
<br/>
" . $requests . "
<br/>
Andy Lauten<br/>
My Spartan Sublease<br/>
		";
		
		email($body, $address_to, "You have been sent new sublease requests.");
	}
	
	function delete_request($requestId)
	{
		if(!isset($requestId))
			fail("No request id was provided.");
		check_admin();
		
		config($mysqli);
		prepare("SELECT id FROM mss_postings WHERE RequestId = ?", $q, $mysqli);
		$q->bind_param('i', $requestId);
		execute($q, $mysqli);
		$q->bind_result($id);
		if($q->num_rows > 0)
		{
			fail("Cannot delete a request that has a posting associated with it. Please delete the posting instead.");
		}
		$q->close();
		
		prepare("DELETE FROM mss_requests WHERE id = ?", $q, $mysqli);
		$q->bind_param('i', $requestId);
		execute($q, $mysqli);
		$q->close();
		
		
		return true;
	}
	
	function change_state($postingId, $newState)
	{
		if(!isset($newState))
			fail("No state was provided.");
		if(!isset($postingId))
			fail("No posting id was provided.");
		check_admin();
		
		config($mysqli);
		prepare("SELECT State, RequestId FROM mss_postings WHERE id = ?", $q, $mysqli);
		$q->bind_param('i', $postingId);
		execute($q, $mysqli);
		$q->bind_result($state, $requestId);
		if($q->fetch())
		{
			
			if(can_change($state, $newState))
			{
				$q->close();
				prepare("UPDATE mss_postings SET State = ? WHERE id = ?", $q, $mysqli);
				$q->bind_param('ii', $newState, $postingId);
				execute($q, $mysqli);
				
				$actionable = 0;
				if(!read_only($newState))
					$actionable = 1;
					
				$q->close();
				prepare("UPDATE mss_requests SET Actionable = ? WHERE id = ?", $q, $mysqli);
				$q->bind_param('ii', $actionable, $requestId);
				execute($q, $mysqli);

			}else{
				fail("Cannot change state directly from " .  get_state_title($state) . " to " . get_state_title($newState));
			}
		}else{
			fail("Failed to retrieve data for posting id " . $postingId);
		}
		$q->close();
		$mysqli->close();
		
		return true;
	}
	
	function delete_posting($postingId)
	{
		change_state($postingId, STATE_DELETED);
	}

	
	
?>