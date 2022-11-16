<?php
header('Access-Control-Allow-Origin: *');

require("../SendGrid/sendgrid-php.php");
include("../db_connect_info/db.php");


date_default_timezone_set('America/New_York');

// Get PlotID 
$plot_id_in = $_GET['plot_id'];

// Check Chaarmi License on Main Server
$valid_license = "active"; 

// Get Metaverse Galaxy Settings from Internal Server
$metaverse_galaxy_settings = "txtCompanyRequired=0,txtUsernameRequired=0,txtEmailRequired=0";
$metaverse_galaxy_additional_options = "totalusersperplot=16,totalplotclones=-1";
$var_png_image_url_512_by_65 = "";

$page_title = "Metaverse Galaxy Powered by Chaarmi Worlds Inc.";

function isMobile() {
    return preg_match("/(Mozilla|Oculus|android|pacific|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

function setup_page_header($page_title, $custom_styles)
{
	?>
		<!DOCTYPE html>
		<html lang="en-us">
		  <head>
			<script type="text/javascript" src="./Native/unity-webgl-tools.js"></script>
			<script type="text/javascript" src="./Native/microphone.js"></script>
			<meta charset="utf-8">
			<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<meta name="description" content="">
			<meta name="author" content="">
				<title><?php echo $page_title;?></title>
			<link rel="icon" href="favicon.ico">
			<style>
			#rpm-container
			{
			  width: 100%;
			  height: 100%;
			  margin: 0;
			  padding: 0;
			  position:absolute;
			  display:none;
			  pointer-events: inherit;
			}

			.rpm-frame {
			  width: 100%;
			  height: 95%;
			  margin: 0;
			  padding: 0;
			  font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Oxygen, Ubuntu, Cantarell, Fira Sans,
			  Droid Sans, Helvetica Neue, sans-serif;
			  font-size: 14px;
			  border: none;
			  display:block;
			  pointer-events: inherit;
			}

			#rpm-hide-button {
			  cursor: pointer;
			  width: 5%;
			  height: 5%;
			  font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Oxygen, Ubuntu, Cantarell, Fira Sans,
			  Droid Sans, Helvetica Neue, sans-serif;
			  font-size: 14px;
			  border: none;
			  color:#000;
			  background-color: #e2e3ec;
			  padding: 0;
			  margin: 0;
			  box-shadow: inset 0px 0px 8px 2px rgba(0,0,0,0.2);
			  pointer-events: inherit;
			}

			#rpm-hide-button:hover {
			  background-color: #f1f2fa;
			}

			#rpm-hide-button:Active {
			  background-color: #e2e3ec;
			}

			body { padding: 0; margin: 0; overflow: hidden; background-color: #000000 !important;}
			#unity-container { position: absolute }
			#unity-container.unity-desktop { left: 50%; top: 50%; transform: translate(-50%, -50%) }
			#unity-container.unity-mobile { width: 100%; height: 100% }
			#unity-canvas { background: #231F20 }
			.unity-mobile #unity-canvas { width: 100%; height: 100% }
			#unity-loading-bar { position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); display: block }
			#unity-logo { width: 154px; height: 130px; background: url('chaarmi-logo_white.png') no-repeat center }
			#unity-progress-bar-empty { width: 141px; height: 18px; margin-top: 10px; background: url('progress-bar-empty-dark.png') no-repeat center }
			#unity-progress-bar-full { width: 0%; height: 18px; margin-top: 10px; background: url('progress-bar-full-dark.png') no-repeat center }
			#unity-footer { position: relative }
			.unity-mobile #unity-footer { display: none }
			#unity-build-title { float: right; margin-right: 10px; line-height: 38px; font-family: arial; font-size: 18px }
			#unity-fullscreen-button { float: right; width: 38px; height: 38px; background: url('fullscreen-button.png') no-repeat center }
			#unity-mobile-warning { position: absolute; left: 50%; top: 5%; transform: translate(-50%); background: white; padding: 10px; display: none }
			#entervr:enabled { width: 100px; height: 100px; background-color: #1eaed3; display: inline-block; border: 0; }
			#entervr:disabled { width: 100px; height: 100px; background-color: #dddddd; display: inline-block; border: 0; }
			
			<?php echo $custom_styles;?>
			</style>
		<!-- Latest compiled and minified BOOTSTRAP CSS -->
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		</head>
		<body>			

	<?php
}

function setup_page_footer()
{
	?>			<!-- Bootstrap core JavaScript
			================================================== -->
			<!-- Placed at the end of the document so the pages load faster -->
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		</body>
		</html>
	<?php
}


if ($valid_license == "active")
{
	$custom_styles = "";
	setup_page_header($page_title, $custom_styles);
	?>
				<form>
					<input type = 'hidden' id = 'unity_license_id' value = ''>
					<input type = 'hidden' id = 'unity_plot_id' value = '<?php echo $plot_id_in;?>'>
					<input type = 'hidden' id = 'photon_realtime_license_id' value = '<?php echo $photonserver_api_key;?>'>
					<input type = 'hidden' id = 'photon_voice_license_id' value = '<?php echo $photonvoice_api_key;?>'>
					<input type = 'hidden' id = 'metaverse_galaxy_settings' value = '<?php echo $metaverse_galaxy_settings;?>'>
					<input type = 'hidden' id = 'additional_options' value = '<?php echo $metaverse_galaxy_additional_options;?>'>
					<input type = 'hidden' id = 'var_url' value = '<?php echo $var_png_image_url_512_by_65;?>'>
				</form>								
				<div id="unity-container" class="unity-desktop" style = "width: 100%; height: 100%;">
				<div id="unity-canvas-container" style = "width: 100%; aspect-ratio: 16/9;">
				  <div id="rpm-container" style="display: none">
					<iframe id="rpm-frame" class="rpm-frame" allow="camera *; microphone *" style="width:100%; height:100%; position:absolute; z-index: 9998;"></iframe>
					<button id="rpm-hide-button" style="position:absolute; z-index: 9999;" onclick="hideRpm()">Hide</button>
					<div id="unity-fullscreen-button" style="position:absolute; z-index: 9997;"></div>
				  </div>
				<button id="entervr" style="width: 800px; height: 100px; display: none;  margin: 0 auto;" value="Enter VR" enabled>Enter VR Mode (Click Here)</button>
				  <canvas id="unity-canvas" style="width: 100%; height: 100%;"></canvas>
				</div>
				  <div id="unity-loading-bar">					
					<div id="unity-logo"></div>
					<div id="unity-progress-bar-empty">
					  <div id="unity-progress-bar-full"></div>
					</div>
				  </div>				  
				  <!--<div id="unity-footer">-->
					<!--<div id="unity-progress">NOTE: WILL NOT WORK ON PHONES - PC/MAC and Oculus Quest Only at the moment => LOADING... Please Wait</div>-->
					<!--<div id="status"></div>-->
						<!--<h1 id="progress"></h1>-->			  					
					<!--<div id="unity-build-title"><a href = "https://www.chaarmi.com" target = "_blank">Chaarmi Worlds Inc. Metaverse Technology</a></div>-->
				  <!--</div>-->
				</div>
				<script>
				  var buildUrl = "Build";
				  var loaderUrl = buildUrl + "/Frontend.loader.js";
				  var config = {
					dataUrl: buildUrl + "/Frontend.data",
					frameworkUrl: buildUrl + "/Frontend.framework.js",
					codeUrl: buildUrl + "/Frontend.wasm",
					streamingAssetsUrl: "StreamingAssets",
					companyName: "Chaarmi Worlds Inc.",
					productName: "Chaarmi Worlds Inc. Metaverse Technology",
					productVersion: "0.0.1",
				  };


				  var container = document.querySelector("#unity-container");
				  var canvas = document.querySelector("#unity-canvas");
				  var canvasContainer = document.querySelector("#unity-canvas-container");
				  var loadingBar = document.querySelector("#unity-loading-bar");
				  var progressBarFull = document.querySelector("#unity-progress-bar-full");
				  var fullscreenButton = document.querySelector("#unity-fullscreen-button");
				  var unityInstance = null;
				  var unityProgress = document.querySelector("#unity-progress");
					var rpmFrame = document.getElementById("rpm-frame");
					var rpmContainer = document.getElementById("rpm-container");
					var rpmHideButton = document.getElementById("rpm-hide-button");
					var enterVRButton2 = document.getElementById('entervr');

				  loadingBar.style.display = "block";



function _JS_Sound_Init() {
  try {
    window.AudioContext = window.AudioContext || window.webkitAudioContext;
    WEBAudio.audioContext = new AudioContext();
    var tryToResumeAudioContext = function() {
      if (WEBAudio.audioContext.state === 'suspended')
        WEBAudio.audioContext.resume();
      else {
        clearInterval(resumeInterval);
      }
    };
    var resumeInterval = setInterval(tryToResumeAudioContext, 400);
    WEBAudio.audioWebEnabled = 1;
  } catch (e) {
    //alert("Web Audio API is not supported in this browser");
  }
}

// RPM START
rpmHideButton.onclick = function () {
    if (document.fullscreenElement) {
        canvasWrapper.requestFullscreen();
    }
    rpmContainer.style.display = "none";
};


function setupRpmFrame() {
    rpmFrame.src = "https://chaarmi.readyplayer.me/avatar?frameApi";

    window.addEventListener("message", subscribe);
    document.addEventListener("message", subscribe);
    function subscribe(event) {
        const json = parse(event);
        if (
            unityInstance == null ||
            json?.source !== "readyplayerme" ||
            json?.eventName == null
        ) {
            return;
        }

        // Subscribe to all events sent from Ready Player Me once frame is ready
        if (json.eventName === "v1.frame.ready") {
            rpmFrame.contentWindow.postMessage(
                JSON.stringify({
                    target: "readyplayerme",
                    type: "subscribe",
                    eventName: "v1.**",
                }),
                "*"
            );
        }

        // Get avatar GLB URL
        if (json.eventName === "v1.avatar.exported") {
            rpmContainer.style.display = "none";
            // Send message to a Gameobject in the current scene
            unityInstance.SendMessage(
                "ChaarmiJSToUnityCode", // Target GameObject name
                "OnWebViewAvatarGenerated", // Name of function to run
                json.data.url
            );
			rpmFrame.src = "";
            console.log(`Avatar URL: ${json.data.url}`);
        }

        // Get user id
        if (json.eventName === "v1.user.set") {
            console.log(`User with id ${json.data.id} set: ${JSON.stringify(json)}`);
        }
    }

    function parse(event) {
        try {
            return JSON.parse(event.data);
        } catch (error) {
            return null;
        }
    }
}

function showRpm() {
    rpmContainer.style.display = "block";
}

function hideRpm() {
    rpmContainer.style.display = "none";
}

// RPM END

// CUSTOM CHAARMI INTEROP UNITY INTERNAL FUNCTIONS

function enterVRMode()
{
	//alert("VR MODE");
	document.getElementById('entervr').style.display = "block";
	canvasContainer.style.width = "1366px";
	canvasContainer.style.height = "768px";

}

function enterFullscreenMode()
{
	canvasContainer.requestFullscreen();
}

// END CUSTOM CHAARMI INTEROP UNITY INTERNAL FUNCTIONS

				  var script = document.createElement("script");
				  script.src = loaderUrl;
				  script.onload = () => {
					createUnityInstance(canvas, config, (progress) => {
					  progressBarFull.style.width = 100 * progress + "%";
					}).then((instance) => {
						unityInstance = instance;
					  loadingBar.style.display = "none";
					  if (fullscreenButton)
					  {
						fullscreenButton.onclick = () => {
						  //window.vuplex.SetFullscreen(1)/*https://support.vuplex.com/articles/webgl-fullscreen*/;
						};
					  }
					  
					  _JS_Sound_Init();
					  
					}).catch((message) => {
					  alert(message);
					});
				  };
				  document.body.appendChild(script);
				  
				  // Show VR button ONLY when Enter VR has been clicked on internally						
				  document.getElementById('entervr').style.display = "none";

				  let enterVRButton = document.getElementById('entervr');

				  document.addEventListener('onVRSupportedCheck', function (event) {
					//enterVRButton.disabled = !event.detail.supported;
				  }, false);

				  enterVRButton.addEventListener('click', function (event) {
					unityInstance.Module.WebXR.toggleVR();
				  }, false);				  

// New Fullscreen Code for Desktop
window.addEventListener('DOMContentLoaded', (event) => {
  const button = fullscreenButton;
  button.addEventListener('click', () => {
    canvasContainer.requestFullscreen();
  });
});

				</script>
	<?php
	
	setup_page_footer();

}
else if ($valid_license == "deactive")
{
	
	setup_page_header($page_title);
	?>
		<div class="container">

		  <div class="starter-template">
			<h1>License Is Deactive!</h1>
			<p class="lead"><font style ="color:#f00"><b>If you are the owner of this Chaarmi Metaverse Galaxy please reach out to the Chaarmi team to re-enable your Metaverse Galaxy by emailing <a href = "mailto:contact@chaarmi.com">contact@chaarmi.com</a></b></font></p>
		  </div>

		</div><!-- /.container -->
	<?php
	
	setup_page_footer();
	
}
else
{
	setup_page_header($page_title);
	?>
		<div class="container">

		  <div class="starter-template">
			<h1>License Does Not Exist!</h1>
			<p class="lead"><font style ="color:#f00"><b>If you are the owner of this Chaarmi Metaverse Galaxy please reach out to the Chaarmi team to get your Metaverse Galaxy up and running by emailing <a href = "mailto:contact@chaarmi.com">contact@chaarmi.com</a> or visit us at <a href = "https://www.chaarmi.com" target="_blank">https://www.chaarmi.com</a> today!</b></font></p>
		  </div>

		</div><!-- /.container -->
	<?php
	
	setup_page_footer();

}

