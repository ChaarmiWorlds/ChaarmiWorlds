<?php
// mysqli_connect($host,$user,$pass,$database);
$con = mysqli_connect("localhost", "YOUR_DB_USERNAME", "YOUR_DB_PASSWORD", "YOUR_DB_NAME");
$AES_Key = "tEnChAr89t"; // Random 10 Character Key generated upon install for protection of Data
$AES_Key_iv = "eIghTeenChaRIct3rS"; // Random 18 Character Key generated upon install for protection of Data
$sendgrid_api_key = "SG.YOUR_SENDGRID_KEY"; // Obtain from SendGrid.com, start with a free account for now
$photonserver_api_key = "YOUR_PHOTON_REALTIME_KEY"; // Create a new app at https://www.photonengine.com/
$photonvoice_api_key = "YOUR_PHOTON_VOICE_KEY"; // Create a new photon voice app at https://www.photonengine.com/
$chaarmi_metaverse_license_key = "KEY_HERE"; // Obtained from www.Chaarmi.com - NOTE: Without a valid license you miss out on unlimited plots of land and other great features. Get a chaarmi license today at www.Chaarmi.com and support the metaverse!

// FUNCTIONS

function aes_encrypt($val)
{
	$textToEncrypt    = $val;
	$key              = $AES_Key;
	$iv               = $AES_Key_iv;
	$keyHex           = bin2hex($key);
	$ivHex            = bin2hex($iv);

    return base64_encode(openssl_encrypt($textToEncrypt, "AES-256-CBC", $key, 0, $iv));
}

function aes_decrypt($val)
{
	$val = base64_decode($val);
    $textToDecrypt    = $val;
	$key              = $AES_Key;
	$iv               = $AES_Key_iv;
	$keyHex           = bin2hex($key);
	$ivHex            = bin2hex($iv);

    return openssl_decrypt($textToDecrypt, "AES-256-CBC", $key, 0, $iv);
}

function send_an_email($from_email, $from_name, $to_email_address, $to_name, $subject, $body, $sendgrid_key)
{
	$email = new \SendGrid\Mail\Mail();
	$email->setFrom($from_email, $from_name);
	$email->setSubject($subject);
	$email->addTo($to_email_address, $to_name);
	$email->addContent("text/html", $body);

	$sendgrid = new \SendGrid($sendgrid_key);
	try {
		$response = $sendgrid->send($email);
		//print $response->statusCode() . "\n";
		//print_r($response->headers());
		//print $response->body() . "\n";
	} catch (Exception $e) {
		echo 'Caught exception: '. $e->getMessage() ."\n";
	}
}

if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit();
}
?>