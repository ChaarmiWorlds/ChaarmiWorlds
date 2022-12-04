<?php

// FUNCTIONS

function aes_encrypt($val)
{
	$textToEncrypt    = $val;
	$key              = $GLOBALS['AES_Key'];
	$iv               = $GLOBALS['AES_Key_iv'];
	$keyHex           = bin2hex($key);
	$ivHex            = bin2hex($iv);

    return base64_encode(openssl_encrypt($textToEncrypt, "AES-256-CBC", $key, 0, $iv));
}

function aes_decrypt($val)
{
	$val = base64_decode($val);
    $textToDecrypt    = $val;
	$key              = $GLOBALS['AES_Key'];
	$iv               = $GLOBALS['AES_Key_iv'];
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