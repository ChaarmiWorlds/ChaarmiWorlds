<?php

include("../../db_connect_info/db.php");

header('Access-Control-Allow-Origin: *');

function login_check($keyValueIn, $con_in)
{	
	// Delete all Expired Users	
	$sql = "DELETE FROM chrm_backend_user_login_activity WHERE expDate < NOW()";
	$result2 = mysqli_query($con_in, $sql) or die(mysqli_error($con_in));
	//mysqli_free_result($result2);

	// Check if this user is still logged in
	$sql = "SELECT * from chrm_backend_user_login_activity WHERE key_value = '".$keyValueIn."'";
	$result = mysqli_query($con_in, $sql) or die(mysqli_error($con_in));
	$row = mysqli_fetch_assoc($result);
	
	$logged_in = 'false';

	foreach($row as $column => $value) {
	   if ($column == 'username' && strlen($value) > 0)
	   {
		   $logged_in = 'true';
	   }
	}	

	//mysqli_free_result($result);

	if ($logged_in == 'true')
	{
		return 'true';
	}
	else
	{
		return 'false';
	}
}

$plot_id = aes_encrypt($_POST['plot_id']);

// Obtain keyvalue and check the database to see if user is still logged in
$key_value_in = $_POST['keyvalue'];

// Obtain Plot Data In
$plot_data_in = aes_encrypt($_POST['plot_data']);

// First check if user is correctly logged in, or else fail
$logged_in_status = login_check($key_value_in, $con);

if ($logged_in_status == 'true')
{
	
	// Connect to server and update plot where plot_id 

	$sql = "UPDATE chrm_plot_list SET land_data_base_interior = '".$plot_data_in."' WHERE plot_id = '".$plot_id."'";

	$result = mysqli_query($con, $sql);

	//mysqli_free_result($result);
	
	echo "success";
}

mysqli_close($con);

?>