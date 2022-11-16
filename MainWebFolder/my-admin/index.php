<?php
require("../../SendGrid/sendgrid-php.php");
include("../../db_connect_info/db.php");


date_default_timezone_set('America/New_York');

// MANUAL ADMIN CREATION - EDIT AND UNCOMMENT BELOW THEN COPY AND PASTE THE RESULT INTO SQL
//echo "INSERT INTO chrm_backend_master_user (id, email, password, metamask_id, username, status, password_reset_status, user_type) VALUES (NULL, '".aes_encrypt('your_email')."', '".aes_encrypt('12345678')."', NULL, '".aes_encrypt('admin')."', NULL, NULL, '".aes_encrypt('metaverse_owner')."')";
	
// ------------------------------------

$page_title = "Metaverse Galaxy - Powered by Chaarmi.com";

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

	if ($logged_in == 'false')
	{
		?>
		<!DOCTYPE html>
		<html>
		   <head>
			  <title><?php echo $page_title;?></title>
			  <meta http-equiv = "refresh" content = "1; url = index.php?logout=1&keyvalue=<?php echo $keyValueIn;?>" />
		   </head>
		   <body>
			  <center><p>Token Expired...Logging Out...</p></center>
		   </body>
		</html>				
		<?php
	}
	else if ($logged_in == 'true')
	{
		return 'true';
	}
}


function show_login_page()
{
			?>
		<html>
			<head>
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
				<meta name="description" content="">
				<meta name="author" content="">
				<title><?php echo $page_title;?></title>
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
			<center><img src = "chaarmi-logo.png" style="width: 200px"></center>
			  <form class="form-signin" action="index.php" method="post">
				<h2 class="form-signin-heading">Please sign in</h2>

				<label for="inputUsername" class="sr-only">Username</label>
				<input type="text" name="inputUsername" id="inputUsername" class="form-control" placeholder="Username" required autofocus>

				<label for="inputPassword" class="sr-only">Password</label>
				<input type="password" name="inputPassword" id="inputPassword" class="form-control" placeholder="Password" required>

				<input type="hidden" name="method_item" id="method_item" value="step2">

				<center><a href = "index.php?forgot_password=1">Forgot Password?</a></center>
				<p><br>
				<button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
			  </form>

			</div> <!-- /container -->
		</body>
		</html>
		<?php
}


$SIGNED_IN = 'false';
$username_in = "";
$password_in = "";
$username_encrypted = "";
$password_encrypted = "";
$key_to_pass = "";

if (strlen($_GET['keyvalue']) > 0)
{
	$key_to_pass = $_GET['keyvalue'];
}


function setup_page_header($key_to_pass, $page_title, $custom_styles, $active_menu_number)
{
	// Main Menu Navigation Bar Links (Update in TWO spots, TOP and $_POST['method_item'] == "step2")
	$link_home = "index.php?keyvalue=".$key_to_pass."&pageid=home";
	$link_plots = "index.php?keyvalue=".$key_to_pass."&pageid=plots";
	$link_customizations = "index.php?keyvalue=".$key_to_pass."&pageid=customizations";
	$link_settings = "index.php?keyvalue=".$key_to_pass."&pageid=settings";
	$link_help = "index.php?keyvalue=".$key_to_pass."&pageid=help";
	$link_logout = "index.php?keyvalue=".$key_to_pass."&logout=1";

	?>
		<!DOCTYPE html>
		<html lang="en">
		  <head>
			<meta charset="utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
			<meta name="description" content="">
			<meta name="author" content="">
				<title><?php echo $page_title;?></title>
			<link rel="icon" href="../../favicon.ico">
			<style>
			body {
				padding-top: 50px;
			}
			.starter-template {
				padding: 40px 15px;
				text-align: center;
			}
			<?php echo $custom_styles;?>
			</style>
		<!-- Latest compiled and minified BOOTSTRAP CSS -->
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		</head>
		<body>
			<nav class="navbar navbar-inverse navbar-fixed-top">
			  <div class="container">
				<div class="navbar-header">			  			 
				  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				  </button>
				  <a class="navbar-brand" href="#" ><img src = "chaarmi-logo_white.png" style="width: 180px; height: 24px;"></a>
				</div>
				<div id="navbar" class="collapse navbar-collapse">
				  <ul class="nav navbar-nav">
					<li <?php if($active_menu_number == '1') { echo "class='active'"; }?>><a href="<?php echo $link_home;?>">Home</a></li>
					<li <?php if($active_menu_number == '2') { echo "class='active'"; }?>><a href="<?php echo $link_plots;?>">Plots</a></li>
					<li <?php if($active_menu_number == '3') { echo "class='active'"; }?>><a href="<?php echo $link_customizations;?>">Customizations</a></li>
					<li <?php if($active_menu_number == '4') { echo "class='active'"; }?>><a href="<?php echo $link_settings;?>">Settings</a></li>
					<li><a href="<?php echo $link_logout;?>">LOGOUT</a></li>
				  </ul>
				</div><!--/.nav-collapse -->
			  </div>
			</nav>

	<?php
}

function setup_page_footer($key_to_pass)
{
	?>

			<!-- Bootstrap core JavaScript
			================================================== -->
			<!-- Placed at the end of the document so the pages load faster -->
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		</body>
		</html>
	<?php
}

function show_home_page($con)
{
	// PAGE CONTENT
	
	$sql = "SELECT * FROM chrm_user_login_activity";

	$result = mysqli_query($con, $sql);
	$total_rows = mysqli_num_rows( $result );
	
	//mysqli_free_result($result);
	?>
		<div class="container">

		  <div class="starter-template">
			<h1>Welcome to your Metaverse Galaxy powered by Chaarmi!</h1>
			<p class="lead">What incredible new worlds will you create today?</p>
			<hr>
			<h1>YOUR METAVERSE ANALYTICS</h1>
			<h2>Total Visitors: <?php echo $total_rows;?></h2>
			<hr>
			<h2>Email and Guest List</h2>
		<table style="border: 1px solid black; padding: 5px; width: 100%">
				<thead>
					<tr>
					  <th style="width: 25%"><center>Guest Name</center></th>
					  <th style="width: 25%"><center>Guest Email</center></th>
					  <th style="width: 25%"><center>Permission to Email</center></th>
					  <th style="width: 25%"><center>Date Login</center></th>
					</tr>
				</thead>
			<tbody>
			<?php			

			$sql = "SELECT * FROM chrm_user_login_activity ORDER BY id DESC LIMIT 100";
			$result = mysqli_query($con, $sql);
			$row = mysqli_fetch_assoc($result);				
			foreach ( $result as $row ) {
					?>
					  <tr>
						<td><center><?php echo aes_decrypt("{$row['guestname']}"); ?></center></td>
						<td><center><?php echo aes_decrypt("{$row['guestemail']}"); ?></center></td>
						<td><center>
							<?php 
							if (aes_decrypt("{$row['permission_to_email']}") == "1")
							{
								echo "<b><u><font color='green'>YES</font></u></b>";
							}
							else
							{						
								echo "<b><u><font color='red'>NO</font></u></b>";
							}						
							?>
							</center>
						</td>
						<td><center><?php echo aes_decrypt("{$row['date_time']}"); ?></center></td>
					  </tr>
					<?php
			}
			?>
			</tbody>
		</table>
		  </div>

		</div><!-- /.container -->

	<?php
}

if ($_GET['logout'] == "1")
{
	$key_value_in = $_GET['keyvalue'];
	
	$sql = "SELECT * from chrm_backend_user_login_activity WHERE key_value = '".$key_value_in ."'";

	$result = mysqli_query($con, $sql);
	$row = mysqli_fetch_assoc($result);	
	
	$usernameFromDB = "";
	
	foreach($row as $column => $value) {
	   if ($column == 'username' && strlen($value) > 0)
	   {
		   $usernameFromDB = $value;
	   }
	}
	
	//mysqli_free_result($result);

	// Delete all Expired Users
	$sql = "DELETE FROM chrm_backend_user_login_activity WHERE expDate < NOW()";
	$result2 = mysqli_query($con, $sql);
	//mysqli_free_result($result2);

	$sql = "DELETE FROM chrm_backend_user_login_activity WHERE username = '".$usernameFromDB."'";
	$result3 = mysqli_query($con, $sql);
	//mysqli_free_result($result3);
	
	// Show main page
	show_login_page();
}
else if ($_GET['pageid'] == "home")
{
	// Home
		// Analytics Dashboard and Overview

	// Obtain keyvalue and check the database to see if user is still logged in
	$key_value_in = $_GET['keyvalue'];
	
	// CHECK IF STILL LOGGED IN
	$logged_in_status = login_check($key_value_in, $con);
	
	if ($logged_in_status == 'true')
	{
		setup_page_header($key_to_pass, "Home", "", "1");
		show_home_page($con);
		setup_page_footer($key_to_pass);
	}
}
else if ($_GET['pageid'] == "plots")
{
	// Obtain keyvalue and check the database to see if user is still logged in
	$key_value_in = $_GET['keyvalue'];
	
	// CHECK IF STILL LOGGED IN
	$logged_in_status = login_check($key_value_in, $con);

	$generate_plot_status = $_GET['generate'];
	if ($logged_in_status == 'true')
	{
		$plot_id_to_generate_already_exists = "false";

		// Generate new Plot in System
		if ($generate_plot_status == "true")
		{
			$plot_name_to_generate = $_POST['inputPlotName'];
			
			$sql = "SELECT * FROM chrm_plot_list where plot_id = '".aes_encrypt($plot_name_to_generate)."'";

			$result = mysqli_query($con, $sql);
			$row = mysqli_fetch_assoc($result);	
			$total_rows = mysqli_num_rows( $result );

			if ($total_rows > 0)
			{
				// Plot ID Already Exists
				$plot_id_to_generate_already_exists = "true";
			}
			else 
			{
				// GENERATE New Plot ID
				$sql = "INSERT INTO chrm_plot_list (plot_id, user_id_of_land_owner, user_id_of_land_creator, user_id_of_land_presenter, land_presenter_expiry_date, land_data_base_interior, land_data_presenter_interior, land_data_base_exterior, land_data_presenter_exterior) VALUES ('".aes_encrypt($plot_name_to_generate)."', null, null, null, null, null, null, null, null)";
				$result = mysqli_query($con, $sql);
				//$row = mysqli_fetch_assoc($result);		
			}
			/*
			*/
		}

		// Get PlotID 
		$plot_id_in = $_GET['plot_id'];
		
		$plot_found = 'false';
		
		if (strlen($plot_id_in) > 0)
		{
			$plot_found = 'true';
		}
		
		// Get Plot list		
		$username_in = $_POST['inputUsername'];
		$password_in = $_POST['inputPassword'];
		$username_encrypted = aes_encrypt($username_in);
		$password_encrypted = aes_encrypt($password_in);

		// Get Plot Deletion Information
		$plot_delete_flag = $_GET['delete_plot'];
		if ($plot_delete_flag == "true")
		{
			$plot_found = "false";
			
			// Delete version from table
			$sql = "DELETE FROM chrm_plot_list where plot_id = '".aes_encrypt($plot_id_in)."'";
			$result = mysqli_query($con, $sql);

		}
		
		$custom_styles_data = ".sidebar {					
width: 250px;
position: fixed;
left: 0;
top: 0;
bottom: 0;
padding-top: 50px;
background-color: #111;
overflow-y: scroll;
}

.sidebar div {
padding: 8px;
font-size: 24px;
display: block;
}

.body-text {
margin-left: 250px;
height: 100vh;
bottom: 0;
top: 0;
font-size: 18px;
}";
		setup_page_header($key_to_pass, "Plots", $custom_styles_data, "2");
		?>
		<!-- HTML -->

<div class="sidebar">
	<div><center><b><u><font color="white">PLOT LIST</font></u></b></center></div>
	<script>
	// Create a function to see if iFrame is visible
	</script>
	
	<?php
	
		$sql = "SELECT * FROM chrm_plot_list";

		$result = mysqli_query($con, $sql);
		$row = mysqli_fetch_assoc($result);	
		$total_rows = mysqli_num_rows($result);

		if ($plot_found == 'true')
		{			
				foreach ( $result as $row ) {
						?>
						<div>
							<input type="button" onclick="document.getElementById('myIframe').contentWindow.load_plot('<?php echo aes_decrypt("{$row['plot_id']}");?>');" value="<?php echo aes_decrypt("{$row['plot_id']}");?>"/>
							<?php
								if (aes_decrypt("{$row['plot_id']}") != "genesis")
								{
									?>
									<br>
									<form class="form-signin" action="index.php?keyvalue=<?php echo $key_value_in;?>&pageid=plots&plot_id=<?php echo $plot_id_in;?>&delete_plot=true" method="post" onsubmit="return confirm('Are you sure you want to DELETE PLOT <?php echo aes_decrypt("{$row['plot_id']}");?>? THIS IS IRREVERSABLE...ARE YOU SURE?');"><button style="background: red; bgcolor: red; font-size: 12px" type="submit">DELETE <?php echo aes_decrypt("{$row['plot_id']}");?></button></form>
									<?php
								}
							?>
						</div>
							
						<?php
				}
		}
		else
		{
				foreach ( $result as $row ) {
						?>
						<div><a href = 'index.php?keyvalue=<?php echo $key_value_in;?>&pageid=plots&plot_id=<?php echo aes_decrypt("{$row['plot_id']}");?>'><?php echo aes_decrypt("{$row['plot_id']}");?></a></div>
						<?php
				}
		}
	

	?>
</div>

<div class="body-text">
<?php
	// Load up Unity Plot Editor Instance Here
	// Have a JavaScript function to read the page
	// Make the Plot Editor equal to 90% of size of frame so that a full help section can be below it in order to learn controls by users
	// zoom: 0.75; -moz-transform: scale(0.75); -moz-transform-origin: 0 0; -o-transform: scale(0.75); -o-transform-origin: 0 0; -webkit-transform: scale(0.75); -webkit-transform-origin: 0 0;
	
	if ($plot_found == 'true')
	{
		?>
			<iframe id="myIframe" src="plot_load.php?keyvalue=<?php echo $key_value_in;?>&plot_id=<?php echo $plot_id_in;?>" style="width: 100%; height: 100%; position: relative;"></iframe>
		<?php 
	}
	else
	{
		if ($plot_delete_flag == "true")
		{
			?>
				<center><h5 class="form-signin-heading"><font style = "color:#F00"><b>SUCCESS - Plot <?php echo $plot_id_in;?> Was Deleted Successfully.</b></font></h5></center>
			<?php
		}
		
		?>
			<center><b><-- Please select a Plot to edit on the left side menu list</b></center>
			<p>
			<div style = "width: 75%; margin: auto; padding: 10px;">
				<form class="form-signin" action="<?php echo "index.php?keyvalue=".$key_to_pass."&pageid=plots&generate=true";?>" method="post">
				<h2 class="form-signin-heading">Create New Plot</h2>
				<?php
					if ($plot_id_to_generate_already_exists == "true")
					{
						?>
							<h5 class="form-signin-heading"><font style = "color:#F00"><b>ERROR: That Plot ID Already Exists! Plot Creation Failed. Please Try Again.</b></font></h5>
						<?php
					}
				?>
				<label for="inputPlotName" class="sr-only">Plot Name</label>
				<input type="text" name="inputPlotName" id="inputPlotName" class="form-control" placeholder="Plot Name" required autofocus>
				<p><br>
				<button class="btn btn-lg btn-primary btn-block" type="submit">Create Plot</button>
			  </form>
		  </div>
		<?php
	}
	?>
	
</div>

		<?php			

		setup_page_footer($key_to_pass);
	}
}
else
{

	if($_POST['method_item'] == "step2")
	{
		// Get Username and Password from Post
		$username_in = $_POST['inputUsername'];
		$password_in = $_POST['inputPassword'];
		$username_encrypted = aes_encrypt($username_in);
		$password_encrypted = aes_encrypt($password_in);
		
		$sql = "SELECT * FROM chrm_backend_master_user WHERE username = '".$username_encrypted."'";

		$result = mysqli_query($con, $sql);
		$row = mysqli_fetch_assoc($result);	
		$bDoLogin = 'false';	
		$userEmail = "";

		$username_from_db = "";
		$password_from_db = "";
		
		$i = 0;
		
		foreach($row as $column => $value) {
		   if ($column == 'username' && strlen($value) > 0)
		   {
			   $username_from_db = $value;
		   }
		   if ($column == 'password' && strlen($value) > 0)
		   {
			   $password_from_db = $value;		   
		   }
		   if ($column == 'email' && strlen($value) > 0)
		   {
			   $userEmail = aes_decrypt($value);
		   }
		}

		// Check to see that the username and password are correct
		if ($username_encrypted == $username_from_db && $password_encrypted == $password_from_db)
		{
			// If they are add a new entry to chrm_backend_user_login_activity which will keep a user logged in for a total of 24 hours before being removed.		
			$expFormat = mktime(date("H"), date("i"), date("s"), date("m") ,date("d")+1, date("Y"));
			$expDate = date("Y-m-d H:i:s",$expFormat);
			$key = md5(aes_encrypt($userEmail));
			$addKey = substr(md5(uniqid(rand(),1)),3,10);
			$key = $key . $addKey;
			$key = aes_encrypt($key);
			
			// Insert Into Temp Table
			$sql = "INSERT INTO chrm_backend_user_login_activity (username, key_value, expDate) VALUES ('".aes_encrypt($username_in)."', '".$key."', '".$expDate."')";
			$result2 = mysqli_query($con, $sql);
			//mysqli_free_result($result2);
			
			// Show Main Website
			$SIGNED_IN = 'true';
			
			// Get Username and Password from Post
			$username_in = $_POST['inputUsername'];
			$password_in = $_POST['inputPassword'];
			$username_encrypted = aes_encrypt($username_in);
			$password_encrypted = aes_encrypt($password_in);
			$key_to_pass = $key;
			
		}
		else
		{
			// Wrong Password. Show login screen with message to try again.
			?>
		<html>
			<head>
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
				<meta name="description" content="">
				<meta name="author" content="">
				<title><?php echo $page_title;?></title>
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
			<center><img src = "chaarmi-logo.png" style="width: 200px"></center>
			  <form class="form-signin" action="index.php" method="post">
				<h2 class="form-signin-heading">Please sign in</h2>
				<h5 class="form-signin-heading"><font style = "color:#F00"><b>ERROR: Username or Password Was Incorrect! Please Try Again.</b></font></h5>
				<label for="inputUsername" class="sr-only">Username</label>
				<input type="text" name="inputUsername" id="inputUsername" class="form-control" placeholder="Username" required autofocus>

				<label for="inputPassword" class="sr-only">Password</label>
				<input type="password" name="inputPassword" id="inputPassword" class="form-control" placeholder="Password" required>

				<input type="hidden" name="method_item" id="method_item" value="step2">

				<center><a href = "index.php?forgot_password=1">Forgot Password?</a></center>
				<p><br>
				<button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
			  </form>

			</div> <!-- /container -->
		</body>
		</html>
		<?php
			
		}
		
	}
	else if($_GET['forgot_password'] == "4")
	{

		// Delete all old password requests from db that are BEFORE NOW
		$sql = "DELETE FROM chrm_backend_password_reset_temp WHERE expDate < NOW()";
		$result2 = mysqli_query($con, $sql);
		//mysqli_free_result($result2);
			
		// Search db for THIS specific password request
		$pid_in = $_GET['pid'];
		$sql = "SELECT * FROM chrm_backend_password_reset_temp WHERE key_value = '".$pid_in."'";

		$result = mysqli_query($con, $sql);

		$row = mysqli_fetch_assoc($result);
		
		$bDoReset = 'false';
		
		$userEmail = "";
		
	   foreach($row as $column => $value) {
		   if ($column == 'key_value' && strlen($value) > 0)
		   {
			   if ($value == $pid_in)
			   {
					$bDoReset = 'true';
			   }
		   }
		   if ($column == 'email' && strlen($value) > 0)
		   {
			   $userEmail = aes_decrypt($value);		   
		   }
		}
		
		// Make sure RESET is true and key_value exists then do the actual password change in the DB
		if ($bDoReset == 'true')
		{
			// Get aes_encrypted value of email
			$userEmailEncrypted = aes_encrypt($userEmail);
			
			// Get the password
			$passwordIn = $_POST['inputNewPassword'];
			
			// Do Password Encryption
			$passwordInEncrypted = aes_encrypt($passwordIn);
			
			// Update in Database
			$sql = "UPDATE chrm_backend_master_user SET password = '".$passwordInEncrypted."' WHERE email = '".$userEmailEncrypted."'";
			$result = mysqli_query($con, $sql);

			//mysqli_free_result($result);

			// Remove all requests for this specific email
			$sql = "DELETE FROM chrm_backend_password_reset_temp WHERE email = '".$userEmailEncrypted."'";
			$result3 = mysqli_query($con, $sql);
			//mysqli_free_result($result3);

			mysqli_close($con);

			?>
			<html>
			<head>
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
				<meta name="description" content="">
				<meta name="author" content="">
				<title><?php echo $page_title;?></title>
				<link rel="icon" href="../../favicon.ico">
				<style>
				body {
				  padding-top: 40px;
				  padding-bottom: 40px;
				  background-color: #eee;
				}

				.form-signin {
				  max-width: 540px;
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
				<center><img src = "chaarmi-logo.png" style="width: 200px"></center>
				  <form class="form-signin" action="index.php">
					<h2 class="form-signin-heading">Password Updated Successfully!</h2>
					<h5 class="form-signin-heading">Your password has been updated. Please login with your new password.</h5>
					<p><br>
					<button class="btn btn-lg btn-primary btn-block" type="submit">RETURN TO LOGIN</button>
				  </form>
				</div> <!-- /container -->
			</body>
			</html>		
			<?php					
		}
		else if ($bDoReset != 'true')
		{
	?>
			<html>
			<head>
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
				<meta name="description" content="">
				<meta name="author" content="">
				<title><?php echo $page_title;?></title>
				<link rel="icon" href="../../favicon.ico">
				<style>
				body {
				  padding-top: 40px;
				  padding-bottom: 40px;
				  background-color: #eee;
				}

				.form-signin {
				  max-width: 540px;
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
			<center><img src = "chaarmi-logo.png" style="width: 200px"></center>
			  <form class="form-signin" action="index.php">
				<h2 class="form-signin-heading">Password Request Expired or Does Not Exist</h2>
				<h5 class="form-signin-heading">Your password request has either expired or does not exist. Please try to reset your password again.</h5>
				<p><br>
				<button class="btn btn-lg btn-primary btn-block" type="submit">RETURN TO LOGIN</button>
			  </form>
			</div> <!-- /container -->
			</body>
			</html>
			<?php
		}
			
	}
	else if($_GET['forgot_password'] == "3")
	{

		// Delete all old password requests from db that are BEFORE NOW
		$sql = "DELETE FROM chrm_backend_password_reset_temp WHERE expDate < NOW()";
		$result2 = mysqli_query($con, $sql);
		//mysqli_free_result($result2);
			
		// Search db for THIS specific password request
		$pid_in = $_GET['pid'];
		$sql = "SELECT * FROM chrm_backend_password_reset_temp WHERE key_value = '".$pid_in."'";

		$result = mysqli_query($con, $sql);

		$row = mysqli_fetch_assoc($result);
		
		$bDoReset = 'false';
		
		$userEmail = "";
		
	   foreach($row as $column => $value) {
		   if ($column == 'key_value' && strlen($value) > 0)
		   {
			   if ($value == $pid_in)
			   {
					$bDoReset = 'true';
			   }
		   }
		   if ($column == 'email' && strlen($value) > 0)
		   {
			   $userEmail = aes_decrypt($value);		   
		   }
		}
		//mysqli_free_result($result);

		mysqli_close($con);

		if ($bDoReset == 'true')
		{
			?>
			<html>
			<head>
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
				<meta name="description" content="">
				<meta name="author" content="">
				<title><?php echo $page_title;?></title>
				<link rel="icon" href="../../favicon.ico">
				<style>
				body {
				  padding-top: 40px;
				  padding-bottom: 40px;
				  background-color: #eee;
				}

				.form-signin {
				  max-width: 540px;
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
			<script>
			function checkPasswords()
			{
				var newPassword = document.getElementById("inputNewPassword").value;
				var newPasswordCheck = document.getElementById("inputNewPasswordConfirm").value;
				if(newPassword != newPasswordCheck)
				{
					alert("Passwords do not match, please try again.");
					return false;
				}
				else if (newPassword == newPasswordCheck)
				{
					return true;
				}
			}
			</script>
				<div class="container">
				<center><img src = "chaarmi-logo.png" style="width: 200px"></center>
				  <form class="form-signin" action="index.php?forgot_password=4&pid=<?php echo $pid_in;?>" onsubmit="return checkPasswords()" method="post">
					<h2 class="form-signin-heading">Reset Your Password</h2>
					<h5 class="form-signin-heading">Please setup a new password (We recommend using a 12 character or higher password for added protection - Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters)</h5>
					<p><br>
					<label for="inputNewPassword" class="sr-only">New Password</label>
					<input type="password" name="inputNewPassword" id="inputNewPassword" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" class="form-control" placeholder="New Password" required autofocus>
					<label for="inputNewPasswordConfirm" class="sr-only">Confirm Password</label>
					<input type="password" name="inputNewPasswordConfirm" id="inputNewPasswordConfirm" class="form-control" placeholder="Confirm Password" required>
					<p><br>					
					<button class="btn btn-lg btn-primary btn-block" type="submit">UPDATE PASSWORD</button>
				  </form>
				</div> <!-- /container -->
			</body>
			</html>
			<?php					
		}
		else if ($bDoReset != 'true')
		{
	?>
			<html>
			<head>
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
				<meta name="description" content="">
				<meta name="author" content="">
				<title><?php echo $page_title;?></title>
				<link rel="icon" href="../../favicon.ico">
				<style>
				body {
				  padding-top: 40px;
				  padding-bottom: 40px;
				  background-color: #eee;
				}

				.form-signin {
				  max-width: 540px;
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
			<center><img src = "chaarmi-logo.png" style="width: 200px"></center>
			  <form class="form-signin" action="index.php">
				<h2 class="form-signin-heading">Password Request Expired or Does Not Exist</h2>
				<h5 class="form-signin-heading">Your password request has either expired or does not exist. Please try to reset your password again.</h5>
				<p><br>
				<button class="btn btn-lg btn-primary btn-block" type="submit">RETURN TO LOGIN</button>
			  </form>
			</div> <!-- /container -->
			</body>
			</html>
			<?php
		}
			
	}
	else if($_GET['forgot_password'] == "2")
	{

		// Get username from database by using keyphrase and AES Decrypt key
		$username_in = $_POST['inputUsername'];
		$encrypted_value_to_get_from_database = aes_encrypt($username_in);	
		$sql = "SELECT * FROM chrm_backend_master_user WHERE username = '".$encrypted_value_to_get_from_database."'";

		$result = mysqli_query($con, $sql);

		$row = mysqli_fetch_assoc($result);
		
		$bSendEmailToUser = 'false';
		
		$userEmail = "";
		
	   foreach($row as $column => $value) {
		   if ($column == 'username' && strlen($value) > 0)
		   {
			   if (aes_decrypt($value) == $username_in)
			   {
					// Set boolean to send Email to User
					$bSendEmailToUser = 'true';
			   }
		   }
		   if ($column == 'email' && strlen($value) > 0)
		   {
			   $userEmail = aes_decrypt($value);
		   }
		}

		if ($bSendEmailToUser == 'true')
		{
			// Add username to table for generating new password along with randomized key. It is this random key that will be sent via email.
			$expFormat = mktime(date("H"), date("i"), date("s"), date("m") ,date("d")+1, date("Y"));
			$expDate = date("Y-m-d H:i:s",$expFormat);
			$key = md5(aes_encrypt($userEmail));
			$addKey = substr(md5(uniqid(rand(),1)),3,10);
			$key = $key . $addKey;
			$key = aes_encrypt($key);
			
			// Insert Into Temp Table
			$sql = "INSERT INTO chrm_backend_password_reset_temp (email, key_value, expDate) VALUES ('".aes_encrypt($userEmail)."', '".$key."', '".$expDate."')";
			$result2 = mysqli_query($con, $sql);
			//mysqli_free_result($result2);

		   // Send email to user to reset password
			$from_email = "contact@chaarmi.com";
			$from_name = "Chaarmi Worlds Inc.";
			$to_email_address = $userEmail;
			$to_name = "";
			$subject = 'Password Reset - Chaarmi Metaverse Galaxy';
			$body = 'Hello '.$to_email_address.'! <p>';
			$body = $body.'This email is a password reset request. Someone has requested a password reset. If this was you please follow the link below:<p>';
			$body = $body."<a href='https://".$_SERVER['SERVER_NAME']."/my-admin/index.php?forgot_password=3&pid=".$key."'>https://".$_SERVER['SERVER_NAME']."/my-admin/index.php?forgot_password=3&pid=".$key."</a>";
			$body = $body.'<p>If this was not you, please ignore this email.<p><b>NOTE:</b> This is an autogenerated email from a Chaarmi Metaverse Galaxy from the domain: '.$_SERVER['SERVER_NAME'];
			$sendgrid_key = $sendgrid_api_key;
			send_an_email($from_email, $from_name, $to_email_address, $to_name, $subject, $body, $sendgrid_key);
		}
				   
		//mysqli_free_result($result);

		mysqli_close($con);

		// Show user on this screen that a password reset request has been sent to the email on record and show a return to start link
		
		?>
			<html>
			<head>
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
				<meta name="description" content="">
				<meta name="author" content="">
				<title><?php echo $page_title;?></title>
				<link rel="icon" href="../../favicon.ico">
				<style>
				body {
				  padding-top: 40px;
				  padding-bottom: 40px;
				  background-color: #eee;
				}

				.form-signin {
				  max-width: 540px;
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
			<center><img src = "chaarmi-logo.png" style="width: 200px"></center>
			  <form class="form-signin" action="index.php">
				<h2 class="form-signin-heading">Password Reset Email Has Been Sent!</h2>
				<h5 class="form-signin-heading">An email has been sent to the email on record for a password reset. Please check your email.</h5>
				<p><br>
				<button class="btn btn-lg btn-primary btn-block" type="submit">RETURN TO LOGIN PAGE</button>
			  </form>
			</div> <!-- /container -->
		</body>
		</html>
		<?php
	}
	else if($_GET['forgot_password'] == "1")
	{
					echo "Testing9e";

		?>
			<html>
			<head>
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
				<meta name="description" content="">
				<meta name="author" content="">
				<title><?php echo $page_title;?></title>
				<link rel="icon" href="../../favicon.ico">
				<style>
				body {
				  padding-top: 40px;
				  padding-bottom: 40px;
				  background-color: #eee;
				}

				.form-signin {
				  max-width: 540px;
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
			<center><img src = "chaarmi-logo.png" style="width: 200px"></center>
			  <form class="form-signin" action="index.php?forgot_password=2" method="post">
				<h2 class="form-signin-heading">Please Enter Your Username</h2>
				<h5 class="form-signin-heading">(If it exists we will email password reset instructions to the email on file)</h5>

				<label for="inputUsername" class="sr-only">Username</label>
				<input type="text" name="inputUsername" id="inputUsername" class="form-control" placeholder="Username" required autofocus>
				<p><br>
				<button class="btn btn-lg btn-primary btn-block" type="submit">Reset Password</button>
			  </form>
			  <p><br>
			  <center><a href = "index.php">CANCEL</a></center>

			</div> <!-- /container -->
		</body>
		</html>
		<?php
	}	
	else if ($SIGNED_IN != 'true')
	{
		show_login_page();
	}

	// Shown on INITIAL Login of backend site
	if ($SIGNED_IN == 'true')
	{
		setup_page_header($key_to_pass, "Home", "", "1");
		show_home_page($con);
		setup_page_footer($key_to_pass);
	}
	
}
	
	
	
	
	
	
	
	
?>