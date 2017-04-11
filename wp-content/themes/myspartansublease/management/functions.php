<?php

/*
	Name: functions.php
	Authors: Xavier Durand-Hollis Jr
	
	Methods for inserting, updating, and deleting on mss tables.
	
	Also has the 'fail' function.
*/

	include($_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/sublease-manager/common.php");

	function map_upload($uploadId, $postingId, $order)
	{
		if($uploadId == "")
		{
			return create_fail("Missing an upload.");
		}
		config($mysqli);
		$query = 'SELECT id, UploadId, PostingId, UploadOrder FROM mss_post_image_mappings WHERE PostingId=? AND UploadOrder=?';
		if($q = $mysqli->prepare($query))
		{
			$q->bind_param('ii', $my_postingId, $my_order);
			$my_uploadId = $uploadId;
			$my_postingId = $postingId;
			$my_order = $order;
			if(!$q->execute()) return create_fail("There was a problem checking for existing uploads for this posting." . $mysqli->error);
			$q->bind_result($ret_id,$ret_uploadId,$ret_postingId,$ret_order);
			
			if($q->fetch())
			{
				$q->close();
				$query = 'UPDATE mss_post_image_mappings SET UploadId=? WHERE id=?';
				if($q = $mysqli->prepare($query))
				{
					$q->bind_param('ii',$uploadId, $ret_id);
					if(!$q->execute()) fail("There was a problem updating an upload for this posting." . $mysqli->error);
					$q->close();
					return create("Success");
				}else{
					return create_fail("There was a problem checking for existing uploads for this posting." . $mysqli->error);
				}
			}

		}else{
			return create_fail("There was an internal problem adding an upload to this posting." . $mysqli->error);
		}
		
		$query = 'INSERT INTO mss_post_image_mappings (id, UploadId, PostingID, UploadOrder) VALUES (0,?,?,?)';
		if($q = $mysqli->prepare($query))
		{
			$q->bind_param('iii',$my_uploadId, $my_postingId, $my_order);
			$my_uploadId = $uploadId;
			$my_postingId = $postingId;
			$my_order = $order;
			if(!$q->execute())
			{
				return create_fail("There was a problem while adding an upload to the posting." . $mysqli->error);
			}
			$q->close();
			
			return create("Success");
		}else{
			return create_fail("There was an internal problem while adding an upload to the posting."  . $mysqli->error);
		}
	}
	
	function remove_posting($postingId)
	{
		config($mysqli);
		
		// Remove all mapped images
		$query = 'DELETE FROM mss_post_image_mappings WHERE PostingId = ?';
		if($q = $mysqli->prepare($query))
		{
			$q->bind_param('i',$my_postingId);
			$my_postingId = $postingId;
			if(!$q->execute())
				return create_fail($mysqli->error);
			$q->close();
		}else{
			return create_fail($mysqli->error);
		}

		// Remove the posting
		$query = 'DELETE FROM mss_postings WHERE id = ?';
		if($q = $mysqli->prepare($query))
		{
			$q->bind_param('i',$my_postingId);
			$my_postingId = $postingId;
			if(!$q->execute())
				return create_fail($mysqli->error);
			$q->close();
			
			return create("Successfully removed the posting.");
		}else{
			return create_fail($mysqli->error);
		}

	}

	function create_posting($requestId, $state)
	{
		config($mysqli);
		
		$query = 'SELECT id, RequestId, State, DTSInserted FROM mss_postings WHERE requestId=?';
		if($q = $mysqli->prepare($query))
		{
			$q->bind_param('i',$requestId);
			if(!$q->execute())
			{
				fail("There was a problem while looking for an existing posting to update.");
			}else{
				$q->bind_result($ret_id,$ret_requestId,$ret_state,$ret_dtsInserted);
				if($q->fetch())
				{
					$q->close();
					$query = 'UPDATE mss_postings SET State=? WHERE id=?';
					if($q = $mysqli->prepare($query))
					{
						$q->bind_param('ii', $state, $my_id);
						$my_id = $ret_id;
						if(!$q->execute()) fail("There was a problem updating the posting.");
						$q->close();
						return $ret_id;
					}else{
						fail("Internal problem updating existing posting");
					}
				}
			}
		}else{
			fail("There was an internal problem creating the posting.");
		}
		
		$query = 'INSERT INTO mss_postings (id, RequestId, State, DTSInserted) VALUES (0,?,?,NOW())';
		if($q = $mysqli->prepare($query))
		{
			$q->bind_param('ii',$my_requestId, $my_state);
			$my_requestId = $requestId;
			$my_state = $state;
			if(!$q->execute())
			{
				fail("There was a problem creating the posting.");
			}
			$q->close();
			
			return $mysqli->insert_id;
		}else{
			fail("There was an internal problem creating the posting.");
		}
	}	

	function is_actionable($requestId)
	{
		config($mysqli);

		$query = 'SELECT Actionable FROM mss_requests WHERE id=?';
		if($q = $mysqli->prepare($query))
		{
			$q->bind_param('i',$id);
			$id = $_POST["id"];
			$q->execute();
			$q->bind_result($actionable);
			if(!$q->fetch())
			{
				fail("Could not find request with id: " . $id);
			}else{
				if($actionable == 0)
					return false;
				return true;
			}
			$q->close();
		}else{
			fail("There was an internal problem looking up the request.");
		}
		
		return false;
	}
	
	function set_actionable($requestId, $actionable)
	{
		config($mysqli);
		prepare("UPDATE mss_requests SET Actionable = ? WHERE id = ?", $q, $mysqli);
		$q->bind_param('ii', $actionable, $requestId);
		execute($q, $mysqli);
	}
	
	function modify_request($actionable_state)
	{
		config($mysqli);
		
		$query = 'SELECT 
		id, Actionable, FirstName, LastName, Email, Price, Semesters,
		Address, ContactNumber, RoomsAvailable, TimeToBeReached, GoogleMapsData, DTSInserted
		FROM mss_requests WHERE id=?';
		if($q = $mysqli->prepare($query))
		{
			$q->bind_param('i',$id);
			$id = $_POST["id"];
			$q->execute();
			$q->bind_result($id,$actionable,$firstName,$lastName,$email,$price,$semesters,$address,$phone,$rooms,$timeToBeReached,$googleMapsData,$dtsInserted);
			if(!$q->fetch())
			{
				return create_fail("Could not find request with id: " . $id);
			}else{
				if($actionable == 0)
					return create_fail("This request has already been submitted.");
			}
			$q->close();
		}else{
			return create_fail("There was an internal problem modifying the request: " . $mysqli->error);
		}
		$query2 = 'UPDATE mss_requests SET 
		Actionable = ?, 
		FirstName = ?, 
		LastName = ?, 
		Email = ?,
		Price = ?, 
		Semesters = ?, 
		Address = ?, 
		ContactNumber = ?, 
		RoomsAvailable = ?, 
		TimeToBeReached = ?,
		GoogleMapsData = ?,
		MoveInDate = ?,
		MoveOutDate = ?,
		PetsAllowed = ?,
		UtilitiesIncluded = ?,
		ApartmentGroup = ?
		WHERE id = ?';
		if($q = $mysqli->prepare($query2))
		{
			// Statement preparation
			$q->bind_param('issssississssiisi'
			,$actionable,$firstName,$lastName,$email
			,$price,$semesters,$address,$phone
			,$rooms,$timeToBeReached, $googleMapsData, $moveInDate
			, $moveOutDate, $petsAllowed, $utilitiesIncluded, $apartmentGroup 
			,$id);

			
			$apartmentGroup = $_POST["apartmentGroup"];
			$petsAllowed = ($_POST["petsAllowed"] == "on" || $_POST["petsAllowed"] == "true") ? 1 : 0;
			$utilitiesIncluded = ($_POST["utilitiesIncluded"] == "on" || $_POST["utilitiesIncluded"] == "true") ? 1 : 0;
			$moveInDate = $_POST["moveInDate"];
			$moveOutDate = $_POST["moveOutDate"];
			
			$id = $_POST["id"];
			$actionable = $actionable_state;
			$firstName = $_POST["firstName"];
			if($firstName == "") return create_fail("You must specify the owner's first name for this sublease.");
			$lastName = $_POST["lastName"];
			if($lastName == "") return create_fail("You must specify the owner's last name for this sublease.");
			$address = $_POST["address"];
			if($address == "") return create_fail("You must specify an address for this sublease.");
			
			$semester_fall = false;
			$semester_spring = false;
			$semester_summer = false;
			
			if(isset($_POST["semesterFall"]) && ($_POST["semesterFall"] == "on" || $_POST["semesterFall"] == "true"))
				$semester_fall = true;
			if(isset($_POST["semesterSpring"]) && ($_POST["semesterSpring"] == "on" || $_POST["semesterSpring"] == "true"))
				$semester_spring = true;		
			if(isset($_POST["semesterSummer"]) && ($_POST["semesterSummer"] == "on" || $_POST["semesterSummer"] == "true"))
				$semester_summer = true;
				
			/* Check if at least one semester is selected */
			if(!$semester_fall && !$semester_spring && !$semester_summer)
				return create_fail("You must select at least one semester to sublease.");
				
			$email = $_POST["email"];
			if($email == "") return create_fail("You must specify a valid email for this sublease.");
			$phone = $_POST["contactNumber"];
			if(!preg_match("/^\([0-9]{3}\) [0-9]{3}\-[0-9]{4}$/", $phone))
			{
				return create_fail("You must specify a valid phone number for this sublease formatted like: \n(xxx) xxx-xxx. \n\nNumber Entered: " . $phone . error_get_last());
			}			
			$timeToBeReached = $_POST["timeToBeReached"];
			
			$price = $_POST["price"]; // default price value
			if($price == "") return create_fail("You must specify the price of this sublease.");
			if($price == 0) return create_fail("A sublease must have a price higher than zero.");
			
			$semesters = 0; // No semesters selected

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

			
			
			if(isset($_POST["googleMapsData"]))
				$googleMapsData = $_POST["googleMapsData"];
			else
				$googleMapsData = "{}";
			if(!$q->execute())
			{
				return create_fail("Failed to update request: " . $mysqli->error);
			}
			$q->close();
			
			return create("Successfully updated request.");;
		}
		else
		{
			return create_fail("Failed to update request: " . $mysqli->error);
		}
	}

	
?>