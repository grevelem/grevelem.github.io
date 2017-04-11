<?php
/*
	Name: manage_subleases.php
	Authors: Xavier Durand-Hollis Jr
	
	Manages data in the subleases tables
*/
if(isset($_POST["id"]))
{
	$user_id = $_POST["id"];

}

include 'security.php';

include 'mss_config.php'; // database connection
if(mysqli_connect_errno())
{
	fail(mysqli_connect_error());
}

/*
	For now we only require that the user be logged in. This could be a huge security
	vulnerability if registration on the site was opened and people found out about this
	form.
*/

if(isset($user_id))
{
	$query = "SELECT * FROM mss_requests WHERE id = ?";
	if($q = $mysqli->prepare($query))
	{
		
		$q->bind_param('i',$id);
		$id = $user_id;
		$q->execute();
		$q->bind_result($my_id, $actionable, $firstName, $lastName, $price, $semesters, $address, $contactNumber, $roomsAvailable, $timeToBeReached, $dtsInserted);

		
		if($q->fetch())
		{
			$results = array();
			$results["id"] = $my_id;
			$results["Actionable"] = $actionable;
			$results["FirstName"] = $firstName;
			$results["LastName"] = $lastName;
			$results["Price"] = $price;
			$results["Semesters"] = $semesters;
			$results["Address"] = $address;
			$results["ContactNumber"] = $contactNumber;
			$results["RoomsAvailable"] = $roomsAvailable;
			$results["TimeToBeReached"] = $timeToBeReached;
			$results["DTSInserted"] = $dtsInserted;
			
			success($results);
		}else{
			fail("No request was found for the given id");
		}	
		
		
	}
	else
	{
		fail($mysqli->error);
	}
}
else
{
	fail("No id was given.");
}


function success($message)
{
	echo json_encode($message);
	exit();
}
function fail($message)
{
	$post_message = array();
	$post_message["success"] = false;
	$post_message["message"] = $message;
	echo json_encode($post_message);
	exit();
}

?>