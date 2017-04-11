<?php

/*
	Name: public_create_sublease.php
	Authors: Xavier Durand-Hollis Jr
	
	Handles a form submission from the main site
*/


	$agreeToTerms = $_POST["agreeToTerms"]; 
	if(!$agreeToTerms)
	{
		fail("You did not agree to the terms of usage.");
	}
	
/*
	Connect to the MySQL Database.
*/
		
	include $_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/sublease-manager/public.php";

	config($mysqli);

	
/*
	Prepare the query.
*/
	$query = 'INSERT INTO mss_requests (id, Actionable, FirstName, LastName, Email, Price, Semesters, Address, ContactNumber, RoomsAvailable, TimeToBeReached, DTSInserted) VALUES (?, ?,?,?,?,?,?,?,?,?,?,NOW())';
	if($q = $mysqli->prepare($query))
	{
		// Statement preparation
		$q->bind_param('iissssissis',$id,$actionable,$firstName,$lastName,$email,$price,$semesters,$address,$phone,$rooms,$timeToBeReached);

		$id = 0;
		$firstName = $_POST["firstName"];
		$lastName = $_POST["lastName"];
		$address = $_POST["address"];
		
		$semester_fall = false;
		$semester_spring = false;
		$semester_summer = false;
		if(isset($_POST["semesterFall"]) && $_POST["semesterFall"] == "on")
			$semester_fall = true;
		if(isset($_POST["semesterSpring"]) && $_POST["semesterSpring"] == "on")
			$semester_spring = true;		
		if(isset($_POST["semesterSummer"]) && $_POST["semesterSummer"] == "on")
			$semester_summer = true;

			
		/* Check if at least one semester is selected */
		if(!$semester_fall && !$semester_spring && !$semester_summer)
			fail("You must select at least one semester to sublease.");
			
		$email = $_POST["email"];
		$phone = $_POST["phone"];
		$timeToBeReached = $_POST["timeToBeReached"];

		$actionable = 1; // This item was submitted from a request form and is actionable
		$price = 0; // default price value
		$semesters = 0; // No semesters selected
		$rooms = 0;  // default rooms value
		if($semester_fall)
		{
			$semesters = $semesters | 1; // fall is 001
		}
		if($semester_spring)
		{
			$semesters = $semesters | 2; // spring is 010
		}
		if($semester_summer)
		{
			$semesters = $semesters | 4; // spring is 100
		}
		
		$dtsInserted = new DateTime('NOW');
		
		
		//echo $actionable . $firstName . $lastName . $price . $semesters . $address . $phone . $rooms . $dtsInserted;


		if($q->execute())
		{
		/*
			email(
"
Dear " . $firstName . ",
<br/><br/>
<h3>Your sublease request has been received.</h3>
<br/><br/>
<b>What happens next?</b>
<ul>
<li>We have sent you an email containing information about the subleasing process. Check the inbox of the email address that you provided.</li>
<li>A marketing specialist from My Spartan Sublease will be contacting you shortly – our phone number is (517) 679-4006, so you might want to add it to your contacts!</li>
</ul>

<h4>Thank you for choosing My Spartan Sublease!</h4>
Andy Lauten<br/>
My Spartan Sublease<br/>
<a href='http://www.myspartansublease.com'>MySpartanSublease.com</a>
Tel: (517) 679-4006
",
			$email,
			"Your sublease request on My Spartan Sublease!"
			);
			*/
		}else{
			fail("There was a problem receiving your request. Please try again later.");
		}
		$q->close();
	}
	else
	{
		fail($mysqli->error);
	}
	$posted_items = array();
	$posted_items["success"] = true;
	

	
	$mysqli->close();
	finish_successfully("Successfully submitted request.");
	
	function finish_successfully($message)
	{
		$posted_items["success"] = true;
		$posted_items["message"] = $message;
		echo json_encode($posted_items);
		exit();
	}
	
	
?>