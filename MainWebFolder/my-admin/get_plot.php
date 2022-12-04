<?php

include("../../db_connect_info/db.php");

header('Access-Control-Allow-Origin: *');


	//mysqli_report(MYSQLI_REPORT_ALL);
	//error_reporting(E_ALL);
	//ini_set('display_errors',1);

//echo $_POST['user_id'];
//echo $_POST['user_license'];

$plot_id = $_POST['plot_id'];

if (strlen($plot_id) <= 0)
{
	$plot_id = $_GET['plot_id'];	
}

$plot_id = aes_encrypt($plot_id);

// Connect to internal server
// Check to see the status of user_id and user_license and return it

$sql = "SELECT * FROM chrm_plot_list WHERE plot_id = '".$plot_id."'";

$result = mysqli_query($con, $sql);

$final_string = "";

$row = mysqli_fetch_assoc($result);
   foreach($row as $column => $value) {
		if ($column == "land_data_base_interior")
		{
			if (strlen($value) > 0)
			{
				$final_string = $final_string.aes_decrypt($value);
			}
			else
			{
				echo "NO_DATA";
			}
		}
	}
echo $final_string;
//mysqli_free_result($result);

mysqli_close($con);
?>