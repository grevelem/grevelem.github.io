<?php
	/*
		Name: public.php
		Authors: Xavier Durand-Hollis Jr
		
		Contains functions that can be used by public areas of the site
	*/

	require_once($_SERVER['DOCUMENT_ROOT']."/wp-content/themes/myspartansublease/management/states.php");
	require_once("constants.php");
	
	function execute($q, $mysqli)
	{
		if(!$q->execute())
		{
			fail($mysqli->error);
		}
	}
	
	function _execute($q, $mysqli)
	{
		if(!$q->execute())
		{
			return create_fail("Failed to execute query: " . $mysqli->error);
		}
		return create("Success");
	}
	
	function prepare($query, &$q, $mysqli)
	{
		if($q = $mysqli->prepare($query))
		{
			return true;
		}else{
			fail("Failed to prepare query. " . $mysqli->error);
		}
	}	
	
	function _prepare($query, &$q, $mysqli)
	{
		if($q = $mysqli->prepare($query))
		{
			return create("Success");
		}else{
			return create_fail("Failed to prepare query. " . $mysqli->error);
		}
	}
	
	function config(&$mysqli)
	{
		include $_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/sublease-manager/mss_config.php";
		if(mysqli_connect_errno())
		{
			fail("Connection Error: " . mysqli_connect_error());
		}
	}
	
	/*
		Webservice Messages (using JSON)
	*/
	
	function fail($message)
	{
		$posted_items["success"] = false;
		$posted_items["message"] = $message;
		echo json_encode($posted_items);
		exit();
	}
	
	function create_fail($message)
	{
		$posted_items["success"] = false;
		$posted_items["message"] = $message;
		
		return $posted_items;
	}
	
	function create($message)
	{
		$posted_items["success"] = true;
		$posted_items["message"] = $message;
		return $posted_items;
	}
	
	function finish($posted_items)
	{
		echo json_encode($posted_items);
		exit();
	}
	
	/* 
		Email Messaging 
	*/
	
	function email($body, $address_to, $subject)
	{
		require($_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/sublease-manager/phpmailer/PHPMailerAutoload.php");
		$mail = new PHPMailer;

		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = 'smtp.gmail.com';  // Specify main and backup server
		$mail->Port = 587;
		$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted

		$mail->SMTPAuth = TRUE;                               // Enable SMTP authentication
		$mail->Username = 'info@myspartansublease.com';       // SMTP username
		$mail->Password = 'respondfast';                      // SMTP password
		$mail->Priority    = 1; // Highest priority - Email priority (1 = High, 3 = Normal, 5 = low)
		$mail->CharSet     = 'UTF-8';
		$mail->Encoding    = '8bit';
		
		$mail->ContentType = 'text/html; charset=utf-8\r\n';
		$mail->WordWrap    = 900; // RFC 2822 Compliant for Max 998 characters per line

		$mail->From = 'info@myspartansublease.com';
		$mail->FromName = 'My Spartan Sublease';
		$mail->addAddress($address_to);  // Add a recipient

		$mail->isHTML(true);                                  // Set email format to HTML

		$mail->Subject = $subject;
		$mail->MsgHTML($body);
		$mail->AltBody = $body; // make this actually give the plain text version later

		if(!$mail->Send()) {
		   fail("There was a problem sending this email: " . $mail->ErrorInfo);
		}
		
		$mail->SmtpClose();
		return true;
	/*
		// Courtesy of Stack Overflow and Pear Mail Library
		require_once "Mail.php";
		require_once "mime.php";

		$from = '<info.myspartansublease.com>';
		$to = '<'.$address_to.'>';
		$subject = mail_subject_prefix() . $subject;

		$headers = array(
			'From' => $from,
			'To' => $to,
			'Subject' => $subject
		);
		
		$mime = new Mail_mime("\n");
		
		$mime->setTXTBody($body);
        $mime->setHTMLBody(
		"" 
		. $body
		.""
		);
		$body = $mime->get();
		$headers = $mime->headers($headers);
		
		$smtp = Mail::factory('smtp', array(
				'host' => 'ssl://smtp.gmail.com',
				'port' => '465',
				'auth' => true,
				'username' => 'info@myspartansublease.com',
				'password' => 'respondfast'
			));

		$mail = $smtp->send($to, $headers, $body);

		if (PEAR::isError($mail)) {
			fail($mail->getMessage());
		}
		
		return true;
		*/
	}
	
	/* 
		Security 
	*/
	
	function check_admin()
	{
		if(!UserIsAdmin())
			fail("You are not authorized to perform this action.");
	}
	
?>