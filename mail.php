<?php
$email = $_GET['email'];
$project = $_GET['project'];
$result1 = $_GET['r1'];
$result2 = $_GET['r2'];
$result3 = $_GET['r3'];
$result4 = $_GET['r4'];

$to = $email;
$reqemail = 'admin@enterright.com';
$now = date('Y-m-d');

$subject = 'Results';

$headers = "From: " . $reqemail . "\r\n";
$headers .= "Reply-To: ". $reqemail . "\r\n";
$headers .= "CC: susan@example.com\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";


$message = '<html><body>';
$message .= '<h1>Hello, World!</h1>';
$message .= '</body></html>';


$message = '<html><body>';
$message .= '<img src="http://enterright.com/images/logosmall.png" alt="Enter Right" />';
$message .= '<h1>' . $project.'</h1><br>';
$message .= '<h2>Atmospheric Tests Readings</h2><br>';
$message .=  '<h3>'. $now . '</h3>';
$message .= '<br>Here are your answers:<br>';
$message .= '<br><b>Oxygen (O2):</b> ' . $result1 . '%';
$message .= '<br><b>Flammable/Explosive Gas:</b> ' . $result2 . '%';
$message .= '<br><b>Hydrogen Sulfide (H2S) PPM:</b> ' . $result3 ;
$message .= '<br><b>Carbon Monoxide (CO) PPM:</b> ' . $result4 . '<br><br>';

$message .= '<br>Thanks for participating:';
$message .= '<br><b>Enter Right</b>';
$message .= "</body></html>";


mail($to, $subject, $message, $headers);
//mail("business@ubermc.net","Your project results","Your results here", null,"-fadmin@enterright.com");
echo "Sent mail??";
?>