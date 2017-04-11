<?php

/*
	Name: upload.php
	Authors: Xavier Durand-Hollis Jr
	
	A webservice that allows authorized users to upload files to the server.
	
*/
	include 'security.php';
	include 'states.php';
	include 'mss_config.php'; // database connection
	if(mysqli_connect_errno())
	{
		fail(mysqli_connect_error());
	}
	
	if(isset($_POST["delete"]) && $_POST["delete"] == true)
	{
		
		if(isset($_POST["id"]))
		{
			$query = 'SELECT 
				u.id, u.type, u.path, u.DTSInserted, 
				pim.UploadId, pim.PostingID, 
				p.State
				FROM mss_uploads u 
				LEFT JOIN mss_post_image_mappings pim 
					ON u.id = pim.UploadId
				LEFT JOIN mss_postings p
					ON p.id = pim.PostingId
			WHERE u.id = ?';
			$my_path = "";
			if($q = $mysqli->prepare($query))
			{
				$q->bind_param('i',$id);
				$id = $_POST["id"];
				if(!$q->execute())
					fail("There was a problem find the upload to delete. " . $mysqli->error);
				$q->bind_result($my_id,$my_type,$my_path,$my_dts,$uploadId, $postingId, $state);
				
				if(!$q->fetch()){
					fail("Couldnt fetch the specified id: " . $_POST["id"]);
				}else{
					if(isset($postingId)) // don't care about state if it's just a default value.
					{
						if(read_only($state))
						{
							fail("Cannot delete this image. It is part of a posting that is " . get_state_title($state) . ".");
						}
					}
				}
				$q->close();
			}else{
				fail("There was a problem deleting the file. " . $mysqli->error);
			}
			$query = 'DELETE FROM mss_post_image_mappings WHERE UploadId = ?';
			if($q = $mysqli->prepare($query))
			{
				$q->bind_param('i',$id);
				$id = $_POST["id"];
				
				if(!$q->execute())
					fail("There was a problem deleting the upload from the posting " . $mysqli->error);
				$q->close();

			}
			$query = 'DELETE FROM mss_uploads WHERE id = ?';
			if($q = $mysqli->prepare($query))
			{
				$q->bind_param('i',$id);
				$id = $_POST["id"];
				
				if(!$q->execute())
				{
					fail("There was a problem deleting the upload. " . $mysqli->error);
				
				}else{
					unlink($_SERVER['DOCUMENT_ROOT'].$my_path);
				}
				$q->close();
								
				deleteSuccess($my_id);
			}

		}else{
		
			fail("No id was given to delete.");
		}
	}

	$absolute_path = $_SERVER['DOCUMENT_ROOT']."/wp-content/sublease-images/";
	$relative_path = "/wp-content/sublease-images/";
	$file = $_FILES["file"];
	
	if($file["size"] <= 0)
	{
		fail("No file was uploaded.");
	}
	
	$query = 'INSERT INTO mss_uploads (id, Type, Path, DTSInserted) VALUES (?,?,?,NOW())';
	if($q = $mysqli->prepare($query))
	{
		$q->bind_param('iss',$id, $type, $path);
		$id = 0;
		$type = $file["type"];
		
		$guid = getGUID();
		
		if($fileType = getFileExtension($type))
			$path = $relative_path . $guid . $fileType;
		else
			fail("Invalid file type. Must provide .jpg or .png.");
		
		$q->execute();
		
		$id = $mysqli->insert_id;
		
		$q->close();
		
		if(!move_uploaded_file($file["tmp_name"], $absolute_path . $guid . $fileType))
		{
			$query = "DELETE FROM mss_uploads WHERE id='$id'";
			$mysqli->query($query);
			fail("Failed to upload the file.");
		}
		
		success($path, $id);
	}
	
	fail("There was a problem uploading the file.");
	
	function getFileExtension($fileType)
	{
		// Not totally secure
		// see exif_imagetype()
		if($fileType == "image/jpg") return ".jpg";
		if($fileType == "image/jpeg") return ".jpg";
		if($fileType == "image/png") return ".png";
		
		return false;
	}
	
	function getGUID(){
		if (function_exists('com_create_guid')){
			return com_create_guid();
		}else{
			mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
			$charid = strtoupper(md5(uniqid(rand(), true)));
			$hyphen = chr(45);// "-"
			$uuid = substr($charid, 0, 8).$hyphen
				.substr($charid, 8, 4).$hyphen
				.substr($charid,12, 4).$hyphen
				.substr($charid,16, 4).$hyphen
				.substr($charid,20,12);
			return $uuid;
		}
	}
	function deleteSuccess($uploadId)
	{
		$post_message["success"] = true;
		$post_message["message"] = "Successfully deleted the file.";
		$post_message["id"] = $uploadId;
		echo json_encode($post_message);
		exit();
	}
	function success($upload, $uploadId)
	{
		$post_message["success"] = true;
		$post_message["message"] = "Successfully uploaded the file.";
		$post_message["path"] = $upload;
		$post_message["id"] = $uploadId;
		echo json_encode($post_message);
		exit();
	}
	
	function fail($message)
	{
		$post_message["success"] = false;
		$post_message["message"] = $message;
		echo json_encode($post_message);
		exit();
	}
	
?>