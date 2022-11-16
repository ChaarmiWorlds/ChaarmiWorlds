<?php

include("../../db_connect_info/db.php");

header('Access-Control-Allow-Origin: *');

date_default_timezone_set('America/New_York');

// Obtain User Login Data
$guest_name = $_POST['guest_name'];
$guest_email = $_POST['guest_email'];
$permission_to_email = $_POST['permission_to_email'];
$guest_timestamp = date("Y-m-d H:i:s");

$sql = "INSERT INTO chrm_user_login_activity (id, guestname, guestemail, permission_to_email, date_time) VALUES (NULL, '".aes_encrypt($guest_name)."', '".aes_encrypt($guest_email)."', '".aes_encrypt($permission_to_email)."', '".aes_encrypt($guest_timestamp)."')";

$result = mysqli_query($con, $sql);

//mysqli_free_result($result);

echo "success";

mysqli_close($con);

?>