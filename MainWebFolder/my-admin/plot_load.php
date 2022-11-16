<?php
header('Access-Control-Allow-Origin: *');

require("../../SendGrid/sendgrid-php.php");
include("../../db_connect_info/db.php");


date_default_timezone_set('America/New_York');

// Obtain keyvalue and check the database to see if user is still logged in
$key_value_in = $_GET['keyvalue'];

// Get PlotID 
$plot_id_in = $_GET['plot_id'];

?>
<!DOCTYPE html>
<html lang="en-us">
  <head>
    <script type="text/javascript" src="./Native/unity-webgl-tools.js"></script>
    <script type="text/javascript" src="./Native/microphone.js"></script>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="TemplateData/favicon.ico">
    <!--<link rel="stylesheet" href="TemplateData/style.css">-->
	<style>
	body { padding: 0; margin: 0 }
	#unity-container { position: absolute }
	#unity-container.unity-desktop { left: 50%; top: 50%; transform: translate(-50%, -50%) }
	#unity-container.unity-mobile { width: 100%; height: 100% }
	#unity-canvas { background: #231F20 }
	.unity-mobile #unity-canvas { width: 100%; height: 100% }
	#unity-loading-bar { position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); display: none }
	#unity-logo { width: 154px; height: 130px; background: url('chaarmi-logo_white_small.png') no-repeat center }
	#unity-progress-bar-empty { width: 141px; height: 18px; margin-top: 10px; background: url('progress-bar-empty-dark.png') no-repeat center }
	#unity-progress-bar-full { width: 0%; height: 18px; margin-top: 10px; background: url('progress-bar-full-dark.png') no-repeat center }
	#unity-footer { position: relative }
	.unity-mobile #unity-footer { display: none }
	#unity-webgl-logo { float:left; width: 204px; height: 38px; background: url('webgl-logo.png') no-repeat center }
	#unity-build-title { float: right; margin-right: 10px; line-height: 38px; font-family: arial; font-size: 18px }
	#unity-fullscreen-button { float: right; width: 38px; height: 38px; background: url('fullscreen-button.png') no-repeat center }
	#unity-mobile-warning { position: absolute; left: 50%; top: 5%; transform: translate(-50%); background: white; padding: 10px; display: none }
	</style>
  </head>
  <body>
	<form>
		<input type = 'hidden' id = 'unity_key_id' value = '<?php echo $key_value_in;?>'>
		<input type = 'hidden' id = 'unity_plot_id' value = '<?php echo $plot_id_in;?>'>
		<input type = 'hidden' id = 'unity_license_id' value = '<?php echo $chaarmi_metaverse_license_key;?>'>
	</form>
    <div id="unity-container" class="unity-desktop">
      <canvas id="unity-canvas"></canvas>
      <div id="unity-loading-bar">
        <div id="unity-logo"></div>
        <div id="unity-progress-bar-empty">
          <div id="unity-progress-bar-full"></div>
        </div>
      </div>
      <div id="unity-mobile-warning">
        Chaarmi currently does not support mobile devices but in the future will be coming out with a mobile edition. Please check out <a href = "https://www.chaarmi.com">https://www.chaarmi.com</a> for more details!
      </div>
      <div id="unity-footer">
        <div id="unity-fullscreen-button"></div>
        <div id="unity-build-title">Chaarmi Worlds Inc. Metaverse Technology</div>
      </div>
    </div>
    <script>
      var buildUrl = "Build";
      var loaderUrl = buildUrl + "/Backend_PlotEditor.loader.js";
      var config = {
        dataUrl: buildUrl + "/Backend_PlotEditor.data",
        frameworkUrl: buildUrl + "/Backend_PlotEditor.framework.js",
        codeUrl: buildUrl + "/Backend_PlotEditor.wasm",
        streamingAssetsUrl: "StreamingAssets",
        companyName: "Chaarmi Worlds Inc.",
        productName: "Chaarmi Worlds Inc. Metaverse Technology",
        productVersion: "0.0.1",
      };

      var container = document.querySelector("#unity-container");
      var canvas = document.querySelector("#unity-canvas");
      var loadingBar = document.querySelector("#unity-loading-bar");
      var progressBarFull = document.querySelector("#unity-progress-bar-full");
      var fullscreenButton = document.querySelector("#unity-fullscreen-button");
      var mobileWarning = document.querySelector("#unity-mobile-warning");
	  var unityInstanceMain = null;

      if (/iPhone|iPad|iPod|Android/i.test(navigator.userAgent)) {
        container.className = "unity-mobile";
        config.devicePixelRatio = 1;
        mobileWarning.style.display = "block";
        setTimeout(() => {
          mobileWarning.style.display = "none";
        }, 5000);
      } else {
        canvas.style.width = "1366px";
        canvas.style.height = "768px";
      }
      loadingBar.style.display = "block";

		
		function load_plot(plotid) {
				var plot_and_key_info = "<?php echo $key_value_in;?>" + "," + plotid;				
				unityInstanceMain.SendMessage("WebCode", "ReloadNewPlotData", plot_and_key_info);
		}

      var script = document.createElement("script");
      script.src = loaderUrl;
      script.onload = () => {
        createUnityInstance(canvas, config, (progress) => {
          progressBarFull.style.width = 100 * progress + "%";
        }).then((unityInstance) => {
		  unityInstanceMain = unityInstance;
          loadingBar.style.display = "none";
          fullscreenButton.onclick = () => {
            window.vuplex.SetFullscreen(1)/*https://support.vuplex.com/articles/webgl-fullscreen*/;
          };
        }).catch((message) => {
          alert(message);
        });
      };
      document.body.appendChild(script);
	  
	  document.addEventListener('keydown', function(event) {
		  if (event.ctrlKey && String.fromCharCode(event.keyCode) === 'D') {
			//console.log("you pressed ctrl-D");
			event.preventDefault();
			//event.stopPropagation();
		  }
		  if (event.ctrlKey && String.fromCharCode(event.keyCode) === 'S') {
			//console.log("you pressed ctrl-S");
			event.preventDefault();
			//event.stopPropagation();
		  }
		}, true);
	  
    </script>
  </body>
</html>
