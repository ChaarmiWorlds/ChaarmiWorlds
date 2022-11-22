<?php

include("../../db_connect_info/db.php");

header('Access-Control-Allow-Origin: *');

$plot_id = $_POST['plot_id'];

	if (strlen($plot_id) <= 0)
	{
		$plot_id = $_GET['plot_id'];	
	}
	$plot_id_clean_name = $plot_id;
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

	mysqli_close($con);

	$file = $plot_id_clean_name.".txt";
	$txt = fopen($file, "w") or die("Unable to open file!");
	fwrite($txt, $final_string);
	fclose($txt);

	header('Content-Description: File Transfer');
	header('Content-Disposition: attachment; filename='.basename($file));
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($file));
	header("Content-Type: text/plain");
	readfile($file);


// *****************


//echo $_POST['plot_id'];
//echo $_POST['user_license'];

//$plot_id = $_POST['plot_id'];
//$license_id = $_POST['user_license'];

// Check to see if Plot is not genesis or ryina
/*
$bDownloadFlag = "false";

if ($plot_id == "genesis" || $plot_id == "ryina")
{
	$bDownloadFlag = "true";
}
else
{
	// Check to see if License is VALID
	
	
	// FUTURE CODE TO ADD NEXT VERSION
	$license_id = aes_encrypt($license_id);
	$license_name = aes_encrypt("chaarmi_license");
	
	$sql = "SELECT * FROM chrm_server_settings WHERE setting_name = '".$license_name."'";

$result = mysqli_query($con, $sql);

$license_data_out = "";

$row = mysqli_fetch_assoc($result);
   foreach($row as $column => $value) {
		if ($column == "setting_value")
		{
			if (strlen($value) > 0)
			{
				$license_data_out = $license_data_out.aes_decrypt($value);
			}
			else
			{
				echo "NO_DATA";
			}
		}
	}
echo $license_data_out;

	// Check Chaarmi License on Main Server
	$valid_license = file_get_contents('https://www.chaarmi.com/Master/license_validator.php?license_key='.$chaarmi_metaverse_license_key);

	if ($valid_license == "active")
	{
		$bDownloadFlag = "true";
	}

}

if ($bDownloadFlag == "true")
{

}
*/
