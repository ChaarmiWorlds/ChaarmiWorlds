<?php

// Show nice easy install header logo
// Ask for $DB_HOST = localhost is default, $DB_USERNAME, $DB_PASSWORD, $DB_NAME
// Make sure that user has full access to database $DB_NAME;
	// Note if DigitalOcean, you need to go into mysql as root and create a new user as follows:
	// mysql -u root -p
	// ENTER PASSWORD
	// CREATE USER 'chaarmiuser'@'localhost' IDENTIFIED BY 'password_you_want';
	// CREATE DATABASE chaarmi;
	// (VERY IMPORTANT STEP) => GRANT ALL PRIVILEGES ON chaarmi.* TO 'chaarmiuser'@'localhost';
	// quit;
	// Now login again with the new username and password using => mysql -u username -p 
	// enter the password
	// Check to see if database exists by typing in => show databases;
	// If all is good, then you now have your database host (localhost) username (chaarmiuser) password (password_you_want) and db_name (chaarmi)
	
// Generate a random AES key
// Generate a random AES Key iv
// Ask for sendgrid key
// Ask for Photon license (Not photon voice)
// Ask for chaarmi admin username
// Ask for chaarmi admin password

// TEST database system to see if it works




// FUNCTIONS

function aes_encrypt($val, $AES_Key, $AES_Key_iv)
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



$install_step = $_POST['method_item'];



if ($install_step == "step3")
{
	//mysqli_report(MYSQLI_REPORT_ALL);
	//error_reporting(E_ALL);
	//ini_set('display_errors',1);

	$DB_HOST = $_POST['inputDBHost'];
	$DB_USERNAME = $_POST['inputDBUsername'];
	$DB_PASSWORD = $_POST['inputDBPassword'];
	$DB_NAME = $_POST['inputDBName'];

	$photonserver_api_key = $_POST['inputPhotonLicense'];
	$sendgrid_api_key = $_POST['inputSendGrid'];
	$photonvoice_api_key = "NA"; // Create a new photon voice app at https://www.photonengine.com/
	$chaarmi_metaverse_license_key = "KEY_HERE"; // Obtained from www.Chaarmi.com

	$admin_username = $_POST['inputAdminUsername'];
	$admin_password = $_POST['inputAdminPassword'];
	$admin_email = $_POST['inputAdminEmail'];

	// GENERATE THE BELOW ITEMS
	$AES_Key = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'),1,10); // Random 10 Character Key generated upon install for protection of Data
	$AES_Key_iv = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'),1,16); // Random 16 Character Key generated upon install for protection of Data
	
	//echo "AES_KEY: ".$AES_Key;
	//echo "<br>AES_KEY_iv: ".$AES_Key_iv;
	
	// After getting all values above, GENERATE the db.php file and then create and generate the database with admin user and 
	// genesis and ryina plot along with final page for instructions and email via sendgrid to the email provided by the
	// user which should lay out all the next steps via email and have the username and password there.
	// User is now ready to begin working with Chaarmi Worlds

	$myfile = fopen("db.php", "w") or die("Unable to open file!");
	
	$txt = "<?php\n";
	$txt .= "\$DB_HOST = \"".$DB_HOST."\";\n";
	$txt .= "\$DB_USERNAME = \"".$DB_USERNAME."\";\n";
	$txt .= "\$DB_PASSWORD = \"".$DB_PASSWORD."\";\n";
	$txt .= "\$DB_NAME = \"".$DB_NAME."\";\n";
	$txt .= "\$con = mysqli_connect(\$DB_HOST, \$DB_USERNAME, \$DB_PASSWORD, \$DB_NAME);\n";
	$txt .= "\$AES_Key = \"".$AES_Key."\";\n";
	$txt .= "\$AES_Key_iv = \"".$AES_Key_iv."\";\n";
	$txt .= "\$sendgrid_api_key = \"".$sendgrid_api_key."\";\n";
	$txt .= "\$photonserver_api_key = \"".$photonserver_api_key."\";\n";
	$txt .= "\$photonvoice_api_key = \"NA\";\n";
	$txt .= "\$chaarmi_metaverse_license_key = \"KEY_HERE\";\n";
	$txt .= "include(\"db_functions.php\");\n";
	$txt .= "?>\n";
	fwrite($myfile, $txt);
	fclose($myfile);

	// Generate Tables
	$con = mysqli_connect($DB_HOST, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);

	// Generate tables with specifically genesis and ryina plots of land
	$sql = "CREATE TABLE IF NOT EXISTS chrm_analytics (
	type varchar(255) DEFAULT NULL, 
	internal_id varchar(64) DEFAULT NULL, 
	date_time varchar(64) DEFAULT NULL, 
	ip_address varchar(64) DEFAULT NULL, 
	country varchar(128) DEFAULT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
	mysqli_query($con, $sql);

	$sql = "CREATE TABLE IF NOT EXISTS chrm_backend_master_user (
	id int(11) NOT NULL,
  email varchar(255) DEFAULT NULL,
  password varchar(255) DEFAULT NULL,
  metamask_id varchar(255) DEFAULT NULL,
  username varchar(128) DEFAULT NULL,
  status varchar(64) DEFAULT NULL,
  password_reset_status varchar(1) DEFAULT NULL,
  user_type varchar(64) DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8";
	mysqli_query($con, $sql);

	$sql = "CREATE TABLE IF NOT EXISTS chrm_backend_password_reset_temp (
  email varchar(255) NOT NULL,
  key_value varchar(255) NOT NULL,
  expDate datetime NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1";
	mysqli_query($con, $sql);

$sql = "CREATE TABLE IF NOT EXISTS chrm_backend_user_login_activity (
username varchar(255) NOT NULL,
  key_value varchar(255) NOT NULL,
  expDate datetime NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8";
	mysqli_query($con, $sql);

$sql = "CREATE TABLE IF NOT EXISTS chrm_code_data (
id varchar(64) DEFAULT NULL,
  code blob DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8";
	mysqli_query($con, $sql);
				
		$sql = "CREATE TABLE IF NOT EXISTS chrm_common_land_data (
metaverse_owner_internal_id varchar(64) DEFAULT NULL,
  metaverse_creator_internal_id varchar(64) DEFAULT NULL,
  road_model varchar(64) DEFAULT NULL,
  water_model varchar(64) DEFAULT NULL,
  tree_model varchar(64) DEFAULT NULL,
  sky_material varchar(64) DEFAULT NULL,
  fog_status varchar(64) DEFAULT NULL,
  fog_distance varchar(64) DEFAULT NULL,
  fog_color varchar(64) DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8";
	mysqli_query($con, $sql);

		$sql = "CREATE TABLE IF NOT EXISTS chrm_land_list (
land_id varchar(64) DEFAULT NULL,
  land_ground_texture_id varchar(64) DEFAULT NULL,
  user_id_of_owner varchar(64) DEFAULT NULL,
  plot_id varchar(64) DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8";
	mysqli_query($con, $sql);

		$sql = "CREATE TABLE IF NOT EXISTS chrm_local_system_settings (
 setting_name varchar(255) DEFAULT NULL,
  setting_description text DEFAULT NULL,
  setting_value varchar(255) DEFAULT NULL,
  setting_additional_data blob DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8";
	mysqli_query($con, $sql);

$sql = "CREATE TABLE IF NOT EXISTS chrm_plot_list (
 plot_id varchar(512) DEFAULT NULL,
  user_id_of_land_owner varchar(512) DEFAULT NULL,
  user_id_of_land_creator varchar(512) DEFAULT NULL,
  user_id_of_land_presenter varchar(512) DEFAULT NULL,
  land_presenter_expiry_date varchar(512) DEFAULT NULL,
  land_data_base_interior mediumtext DEFAULT NULL,
  land_data_presenter_interior mediumtext DEFAULT NULL,
  land_data_base_exterior mediumtext DEFAULT NULL,
  land_data_presenter_exterior mediumtext DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8";
	mysqli_query($con, $sql);

$sql = "CREATE TABLE IF NOT EXISTS chrm_server_settings (
 setting_name varchar(255) DEFAULT NULL,
  setting_description varchar(512) DEFAULT NULL,
  setting_value varchar(255) DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8";
	mysqli_query($con, $sql);

$sql = "CREATE TABLE IF NOT EXISTS chrm_user (
  internal_id varchar(64) DEFAULT NULL,
  email varchar(255) DEFAULT NULL,
  password varchar(255) DEFAULT NULL,
  metamask_id varchar(255) DEFAULT NULL,
  username varchar(128) DEFAULT NULL,
  status varchar(64) DEFAULT NULL,
  password_reset_status varchar(1) DEFAULT NULL,
  user_type varchar(64) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8";
	mysqli_query($con, $sql);

$sql = "CREATE TABLE IF NOT EXISTS chrm_user_login_activity (
  id int(128) NOT NULL,
  guestname varchar(255) DEFAULT NULL,
  guestemail varchar(255) DEFAULT NULL,
  permission_to_email varchar(50) DEFAULT NULL,
  date_time varchar(100) DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8";
	mysqli_query($con, $sql);
	
$sql = "ALTER TABLE chrm_backend_master_user ADD PRIMARY KEY (id)";
	mysqli_query($con, $sql);	

$sql = "ALTER TABLE chrm_user_login_activity ADD PRIMARY KEY (id)";
	mysqli_query($con, $sql);	
	
$sql = "ALTER TABLE chrm_backend_master_user MODIFY id int(11) NOT NULL AUTO_INCREMENT";
	mysqli_query($con, $sql);	

$sql = "ALTER TABLE chrm_user_login_activity MODIFY id int(128) NOT NULL AUTO_INCREMENT";
	mysqli_query($con, $sql);	

	// INSERT INTO DATABASE genesis plot and ryina plot

	// GENERATE New Plot ID for genesis and ryina
	$sql = "INSERT INTO chrm_plot_list (plot_id, user_id_of_land_owner, user_id_of_land_creator, user_id_of_land_presenter, land_presenter_expiry_date, land_data_base_interior, land_data_presenter_interior, land_data_base_exterior, land_data_presenter_exterior) VALUES ('".aes_encrypt("genesis", $AES_Key, $AES_Key_iv)."', null, null, null, null, null, null, null, null)";
	mysqli_query($con, $sql);	
	$sql = "INSERT INTO chrm_plot_list (plot_id, user_id_of_land_owner, user_id_of_land_creator, user_id_of_land_presenter, land_presenter_expiry_date, land_data_base_interior, land_data_presenter_interior, land_data_base_exterior, land_data_presenter_exterior) VALUES ('".aes_encrypt("ryina", $AES_Key, $AES_Key_iv)."', null, null, null, null, null, null, null, null)";
	mysqli_query($con, $sql);	

	// INSERT INTO DATABASE ADMIN user_error	
$sql = "INSERT INTO chrm_backend_master_user (id, email, password, metamask_id, username, status, password_reset_status, user_type) VALUES (NULL, '".aes_encrypt($admin_email, $AES_Key, $AES_Key_iv)."', '".aes_encrypt($admin_password, $AES_Key, $AES_Key_iv)."', NULL, '".aes_encrypt($admin_username, $AES_Key, $AES_Key_iv)."', NULL, NULL, '".aes_encrypt('metaverse_owner', $AES_Key, $AES_Key_iv)."')";
	mysqli_query($con, $sql);	

	// Create BASE Plot Land
	$plot_id = aes_encrypt("genesis", $AES_Key, $AES_Key_iv);
	$plot_data_in = aes_encrypt("62ff6a2f-44d0-46e4-99e0-0172af5eeb19,16.37373,7.847253,9.423854,2.504478E-06,0,0,2.045592,1,1,;62ff6a2f-44d0-46e4-99e0-0172af5eeb19,-10.40644,7.84725,10.08767,-4.325711E-06,270,-6.830189E-06,2.045592,1,1,;62ff6a2f-44d0-46e4-99e0-0172af5eeb19,16.37373,7.847253,-16.76416,2.504478E-06,0,0,2.045592,1,1,;62ff6a2f-44d0-46e4-99e0-0172af5eeb19,15.8645,7.84725,10.08767,-4.325711E-06,270,-6.830189E-06,2.045592,1,1,;62ff6a2f-44d0-46e4-99e0-0172af5eeb19,-10.38643,0.3622673,-39.55225,-4.325711E-06,90,6.830189E-06,1.73691,0.4891048,1,;62ff6a2f-44d0-46e4-99e0-0172af5eeb19,15.8645,0.7622368,10.08767,-4.325711E-06,270,-6.830189E-06,2.045592,1,1,;62ff6a2f-44d0-46e4-99e0-0172af5eeb19,-10.40644,0.7622382,10.08767,-4.325711E-06,270,-6.830189E-06,2.045592,1,1,;62ff6a2f-44d0-46e4-99e0-0172af5eeb19,16.37373,0.7622404,9.423854,2.504478E-06,0,0,2.045592,1,1,;f01d3204-3712-46e0-a87a-a488501217e5,2.568908,5.376367,-3.709173,0,0,0,0.9630995,0.9630995,0.9630995,;f01d3204-3712-46e0-a87a-a488501217e5,2.568908,1.274674,-3.709173,0,0,0,1.257408,1.257408,1.257408,;62ff6a2f-44d0-46e4-99e0-0172af5eeb19,16.37373,0.3622665,-38.86535,2.504478E-06,0,0,2.045592,0.4891048,1,;62ff6a2f-44d0-46e4-99e0-0172af5eeb19,15.90728,0.3622693,-39.55225,-4.325711E-06,90,6.830189E-06,1.73691,0.4891048,1,;1d5ba59e-a8c4-47ff-8c82-b249fee8e917,2.835322,3.632986,-3.982124,2.504478E-06,0,0,0.6240888,1,1,;1a20829a-523f-4a7c-a5bc-45c8eff2c115,2.618997,-0.02507997,-14.6735,2.504478E-06,0,0,1,1,1.794775,;f9bd6f60-f538-436b-a520-d2b9dc50b2ed,14.79963,0.6135433,-37.87466,49.91286,77.70903,144.1055,1,1,1,;9b0a48de-a114-48ff-9124-3fdb95f0e848,2.619486,6.720775,9.315225,-1.34394E-05,180,-2.909356E-08,2.478579,2.478579,2.478579,;f9bd6f60-f538-436b-a520-d2b9dc50b2ed,-0.2801361,2.11743,-38.00338,18.58909,203.6964,202.7835,1,1,1,;f9bd6f60-f538-436b-a520-d2b9dc50b2ed,2.640666,4.420167,8.049914,358.3048,7.062545,355.6406,1,1,1,;^3.48808,-0.05733591,-4.248971^-9.889128E-06,167.6873,1.220883E-06^1^^false,,^^7.580386,2.224402,-2.101134,50,330,0^false||||^false||", $AES_Key, $AES_Key_iv);
	$sql = "UPDATE chrm_plot_list SET land_data_base_interior = '".$plot_data_in."' WHERE plot_id = '".$plot_id."'";
	mysqli_query($con, $sql);	



	// Show next steps (Delete install.php file, move db.php file to the db_connect_info)
	?>
	<html>
			<head>
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
				<meta name="description" content="">
				<meta name="author" content="">
				<title>Chaarmi Worlds Installation FINAL STEPS</title>
				<link rel="icon" href="../../favicon.ico">
				<style>
				body {
				  padding-top: 40px;
				  padding-bottom: 40px;
				  background-color: #eee;
				}

				.form-signin {
				  max-width: 650px;
				  padding: 15px;
				  margin: 0 auto;
				}
				.form-signin .form-signin-heading,
				.form-signin .checkbox {
				  margin-bottom: 10px;
				}
				.form-signin .checkbox {
				  font-weight: normal;
				}
				.form-signin .form-control {
				  position: relative;
				  height: auto;
				  -webkit-box-sizing: border-box;
					 -moz-box-sizing: border-box;
						  box-sizing: border-box;
				  padding: 10px;
				  font-size: 16px;
				}
				.form-signin .form-control:focus {
				  z-index: 2;
				}
				.form-signin input[type="email"] {
				  margin-bottom: -1px;
				  border-bottom-right-radius: 0;
				  border-bottom-left-radius: 0;
				}
				.form-signin input[type="password"] {
				  margin-bottom: 10px;
				  border-top-left-radius: 0;
				  border-top-right-radius: 0;
				}
				</style>
				
				<!-- Latest compiled and minified BOOTSTRAP CSS -->
				<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

			</head>
		<body>

			<div class="container">
			<center><img src = "my-admin/chaarmi-logo.png" style="width: 200px"></center>
			  <form class="form-signin" style="width:500px;" action="install.php" method="post">
				<center><h2 class="form-signin-heading">INSTALLATION</h2></center>
				<p>
				<center><b>-- DATABASE SETUP SUCCESSFUL! --<br><p></center>
				<p>
				FINAL STEPS
				<p>
				 <ul style = "list-style-type: decimal;">
				 <li style = "list-style-type: decimal;">Please copy and paste db.php file that was generated into the db_connect_info folder.</li>
				 <li style = "list-style-type: decimal;">MAKE SURE TO DELETE THE install.php FILE!!</li>
				 <li style = "list-style-type: decimal;">Grab the IP Address of your newly setup server and go to your HOSTING Provider (Ex. GoDaddy, etc.)</li>
				 <li style = "list-style-type: decimal;">Add an "A" Record to your domain called "metaverse" and use the IP Address of your new metaverse galaxy (This process can take up to 24 hours to propogate)</li>				 
				 <li style = "list-style-type: decimal;">Setup SSL on your website so that https is setup. You can do this using a certbot or other method however you MUST have SSL setup on your server.</li>				 
				 <li style = "list-style-type: decimal;">You can now login to https://metaverse.YOUR_DOMAIN.EXT/my-admin/ using the username <?php echo $admin_username; ?> and password you provided. Until the propogation happens you can go now to this link to login => <a href = "my-admin/index.php">ADMIN LOGIN LINK</a></li>				 
				 </ul>
				<p><br>
			  </form>

			</div> <!-- /container -->
		</body>
		</html>
		<?php

}
else if ($install_step == "step2")
{
	
	$DB_HOST = $_POST['inputDBHost'];
	$DB_USERNAME = $_POST['inputDBUsername'];
	$DB_PASSWORD = $_POST['inputDBPassword'];
	$DB_NAME = $_POST['inputDBName'];
	mysqli_report(MYSQLI_REPORT_ALL);
	//var_dump(function_exists('mysqli_connect'));
	error_reporting(E_ALL);
	ini_set('display_errors',1);
	$con = mysqli_connect($DB_HOST, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
	
	// Obtain step3 information from user
		?>
	<html>
			<head>
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
				<meta name="description" content="">
				<meta name="author" content="">
				<title>Chaarmi Worlds Installation STEP 2</title>
				<link rel="icon" href="../../favicon.ico">
				<style>
				body {
				  padding-top: 40px;
				  padding-bottom: 40px;
				  background-color: #eee;
				}

				.form-signin {
				  max-width: 330px;
				  padding: 15px;
				  margin: 0 auto;
				}
				.form-signin .form-signin-heading,
				.form-signin .checkbox {
				  margin-bottom: 10px;
				}
				.form-signin .checkbox {
				  font-weight: normal;
				}
				.form-signin .form-control {
				  position: relative;
				  height: auto;
				  -webkit-box-sizing: border-box;
					 -moz-box-sizing: border-box;
						  box-sizing: border-box;
				  padding: 10px;
				  font-size: 16px;
				}
				.form-signin .form-control:focus {
				  z-index: 2;
				}
				.form-signin input[type="email"] {
				  margin-bottom: -1px;
				  border-bottom-right-radius: 0;
				  border-bottom-left-radius: 0;
				}
				.form-signin input[type="password"] {
				  margin-bottom: 10px;
				  border-top-left-radius: 0;
				  border-top-right-radius: 0;
				}
				</style>
				
				<!-- Latest compiled and minified BOOTSTRAP CSS -->
				<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

			</head>
		<body>

			<div class="container">
			<center><img src = "my-admin/chaarmi-logo.png" style="width: 200px"></center>
			  <form class="form-signin" action="install.php" method="post">
				<center><h2 class="form-signin-heading">INSTALLATION</h2></center>
				<p>
				<center><b>-- CONNECTION SUCCESSFUL! --<br><p></center>
				<p>
				Step 2: Please obtain <a href = "https://www.sendgrid.com" target="_blank">SendGrid API Key</a> and <a href = "https://www.photonengine.com/" target="_blank">Photon License Key</a> information to place below.</b>
				<p>
				<label for="inputSendGrid" class="sr-only">SendGrid API Key</label>
				<input type="text" name="inputSendGrid" id="inputSendGrid" class="form-control" placeholder="SendGrid API Key" required autofocus>

				<label for="inputPhotonLicense" class="sr-only">Photon License Key</label>
				<input type="text" name="inputPhotonLicense" id="inputPhotonLicense" class="form-control" placeholder="Photon License Key" required>

				<label for="inputAdminUsername" class="sr-only">Chaarmi Admin Username</label>
				<input type="text" name="inputAdminUsername" id="inputAdminUsername" class="form-control" placeholder="Chaarmi Admin Username" required>

				<label for="inputAdminPassword" class="sr-only">Chaarmi Admin Password</label>
				<input type="password" name="inputAdminPassword" id="inputAdminPassword" class="form-control" placeholder="Chaarmi Admin Password" required>

				<label for="inputAdminEmail" class="sr-only">Chaarmi Admin Email</label>
				<input type="text" name="inputAdminEmail" id="inputAdminEmail" class="form-control" placeholder="Chaarmi Admin EMAIL" required>


				<input type="hidden" name="inputDBHost" id="inputDBHost" value="<?php echo $DB_HOST;?>">
				<input type="hidden" name="inputDBUsername" id="inputDBUsername" value="<?php echo $DB_USERNAME;?>">
				<input type="hidden" name="inputDBPassword" id="inputDBPassword" value="<?php echo $DB_PASSWORD;?>">
				<input type="hidden" name="inputDBName" id="inputDBName" value="<?php echo $DB_NAME;?>">

				<input type="hidden" name="method_item" id="method_item" value="step3">

				<p><br>
				<button class="btn btn-lg btn-primary btn-block" type="submit">Finalization and Step 3</button>
			  </form>

			</div> <!-- /container -->
		</body>
		</html>
		<?php
}
else
{
	// Obtain information from user
	?>
	<html>
			<head>
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
				<meta name="description" content="">
				<meta name="author" content="">
				<title>Chaarmi Worlds Installation STEP 1</title>
				<link rel="icon" href="../../favicon.ico">
				<style>
				body {
				  padding-top: 40px;
				  padding-bottom: 40px;
				  background-color: #eee;
				}

				.form-signin {
				  max-width: 330px;
				  padding: 15px;
				  margin: 0 auto;
				}
				.form-signin .form-signin-heading,
				.form-signin .checkbox {
				  margin-bottom: 10px;
				}
				.form-signin .checkbox {
				  font-weight: normal;
				}
				.form-signin .form-control {
				  position: relative;
				  height: auto;
				  -webkit-box-sizing: border-box;
					 -moz-box-sizing: border-box;
						  box-sizing: border-box;
				  padding: 10px;
				  font-size: 16px;
				}
				.form-signin .form-control:focus {
				  z-index: 2;
				}
				.form-signin input[type="email"] {
				  margin-bottom: -1px;
				  border-bottom-right-radius: 0;
				  border-bottom-left-radius: 0;
				}
				.form-signin input[type="password"] {
				  margin-bottom: 10px;
				  border-top-left-radius: 0;
				  border-top-right-radius: 0;
				}
				</style>
				
				<!-- Latest compiled and minified BOOTSTRAP CSS -->
				<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

			</head>
		<body>

			<div class="container">
			<center><img src = "my-admin/chaarmi-logo.png" style="width: 200px"></center>
			  <form class="form-signin" action="install.php" method="post">
				<center><h2 class="form-signin-heading">INSTALLATION</h2></center>
				<p>
				<b>Please obtain all database credentials and information to place below.</b>
				<p>
				<label for="inputDBHost" class="sr-only">Database Host</label>
				<input type="text" name="inputDBHost" id="inputDBHost" class="form-control" placeholder="Database HOST" required autofocus>

				<label for="inputDBName" class="sr-only">Database Name</label>
				<input type="text" name="inputDBName" id="inputDBName" class="form-control" placeholder="Database NAME" required>

				<label for="inputDBUsername" class="sr-only">Database Username</label>
				<input type="text" name="inputDBUsername" id="inputDBUsername" class="form-control" placeholder="Database USERNAME" required>

				<label for="inputDBPassword" class="sr-only">Database Password</label>
				<input type="password" name="inputDBPassword" id="inputDBPassword" class="form-control" placeholder="Database PASSWORD" required>

				<input type="hidden" name="method_item" id="method_item" value="step2">

				<p><br>
				<button class="btn btn-lg btn-primary btn-block" type="submit">Test Connection and Step 2</button>
			  </form>

			</div> <!-- /container -->
		</body>
		</html>
		<?php
}



?>