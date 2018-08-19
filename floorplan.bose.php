<?php
$start=microtime(true);
require('/var/www/home.egregius.be/secure/settings.php');
if($home){
error_reporting(E_ALL);ini_set("display_errors","on");
session_start();
if(!isset($_SESSION['referer']))$_SESSION['referer']='floorplan.php';

echo '<html><head>
		<title>Floorplan</title>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
		<meta name="HandheldFriendly" content="true"/>
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,minimal-ui"/>
		<meta name="msapplication-TileColor" content="#000000">
		<meta name="msapplication-TileImage" content="images/domoticzphp48.png">
		<meta name="theme-color" content="#000000">
		<link rel="icon" type="image/png" href="images/domoticzphp48.png"/>
		<link rel="shortcut icon" href="images/domoticzphp48.png"/>
		<link rel="apple-touch-startup-image" href="images/domoticzphp450.png"/>
		<link rel="apple-touch-icon" href="images/domoticzphp48.png"/>
		<link rel="stylesheet" type="text/css" href="/styles/floorplan.php">
		<style>
			.btn{height:64px;}
			.input{width:78px;}
			.blackmedia{top:254px;left:0px;height:581px;width:490px;background-color:#000;}
		</style>
	</head>';
if(isset($_POST['Naam'])&&!isset($_POST['dimmer'])){
	sw($_POST['Naam'],$_POST['Actie']);
	usleep($Usleep);
}elseif(isset($_POST['dimmer'])){
	if(isset($_POST['luifelauto'])){
		apcu_store('dimactionluifel',1);
	}elseif(isset($_POST['dimlevelon_x'])){
		sl($_POST['Naam'],100);
		apcu_store('dimtime'.$_POST['Naam'],time);
		apcu_store('dimaction'.$_POST['Naam'],0);
	}elseif(isset($_POST['dimleveloff_x'])){
		sl($_POST['Naam'],0);
		apcu_store('dimtime'.$_POST['Naam'],time);
		apcu_store('dimaction'.$_POST['Naam'],0);
	}elseif(isset($_POST['dimsleep_x'])){
		lg('=> '.$user.' => activated dimmer sleep for '.$_POST['Naam']);
		apcu_store('dimaction'.$_POST['Naam'],1);
	}elseif(isset($_POST['dimwake_x'])){
		lg('=> '.$user.' => activated dimmer wake for '.$_POST['Naam']);
		sl($_POST['Naam'],$_POST['dimwakelevel']+1,'Dimmer');
		apcu_store('dimaction'.$_POST['Naam'],2);
	}elseif(isset($_POST['dimwake3u_x'])){
		lg('=> '.$user.' => activated dimmer wake after 3 hours for '.$_POST['Naam']);
		apcu_store('dimaction'.$_POST['Naam'],3);
	}else{
		sl($_POST['Naam'],$_POST['dimlevel']);
		apcu_store('dimaction'.$_POST['Naam'],0);
	}
	usleep($Usleep);
}
if(isset($_REQUEST['nas'])){
	if($_REQUEST['nas']=='sleep'){shell_exec('/var/www/home.egregius.be/secure/sleepnas.sh');apcu_store('Tnas',time);}
	elseif($_REQUEST['nas']=='wake')shell_exec('/var/www/home.egregius.be/secure/wakenas.sh');
	header("Location: floorplan.media.php");die("Redirecting to: floorplan.media.php");
}
if(isset($_REQUEST['power']))bosekey("POWER");
if(isset($_REQUEST['preset']))bosepreset($_REQUEST['preset']);
if(isset($_REQUEST['volume']))bosevolume($_REQUEST['volume']);
if(isset($_REQUEST['setdimmer'])){
	$name=$_REQUEST['setdimmer'];
	$stat=apcu_fetch($name);
	$dimaction=apcu_fetch('dimaction'.$name);
	echo '<div id="D'.$name.'" class="fix dimmer" >
		<form method="POST" action="floorplan.media.php" oninput="level.value = dimlevel.valueAsNumber">
				<div class="fix z" style="top:15px;left:90px;">';
				if($stat=='Off') echo '<h2>'.ucwords($name).': Off</h2>';
				else echo '<h2>'.ucwords($name).': '.$stat.'%</h2>';
				echo '
					<input type="hidden" name="Naam" value="'.$name.'">
					<input type="hidden" name="dimmer" value="true">
				</div>
				<div class="fix z" style="top:100px;left:30px;">
					<input type="image" name="dimleveloff" value ="0" src="images/Light_Off.png" class="i90"/>
				</div>
				<div class="fix z" style="top:100px;left:150px;">
					<input type="image" name="dimsleep" value ="100" src="images/Sleepy.png" class="i90"/>';
				if($dimaction==1)echo '<div class="fix" style="top:0px;left:0px;z-index:-100;background:#ffba00;width:90px;height:90px;border-radius:45px;"></div>';
				echo '
				</div>
				<div class="fix z" style="top:100px;left:265px;">
					<input type="image" name="dimwake" value="100" src="images/Wakeup.png" style="height:90px;width:90px"/>';
				if($dimaction==2)echo '<div class="fix" style="top:0px;left:0px;z-index:-100;background: #ffba00;width:90px;height:90px;border-radius:45px;"></div>';
				echo '
					<input type="hidden" name="dimwakelevel" value="'.$stat.'">
				</div>';
				if($name=='alex'){echo '
					<div class="fix z" style="top:10px;left:265px;">
						<input type="image" name="dimwake3u" value="100" src="images/Wakeup.png" style="height:90px;width:90px"/>
						<div class="fix" style="top:39px;left:25px;font-size:3em;z-index:-10;" >3u</div>';
					if($dimaction==3){
						echo '<div class="fix" style="top:0px;left:0px;z-index:-100;background: #ffba00;width:90px;height:90px;border-radius:45px;"></div>';
						echo '<div class="fix" style="top:32px;left:95px;z-index:-100;font-size:2em;">'.strftime("%k:%M",apcu_fetch('Tdimactionalex')+10800).'</div>';
					}
					echo '
						<input type="hidden" name="dimwakelevel" value="'.$stat.'">
					</div>';
				}
				echo '
				<div class="fix z" style="top:100px;left:385px;">
					<input type="image" name="dimlevelon" value ="100" src="images/Light_On.png" class="i90"/>
				</div>
				<div class="fix z" style="top:210px;left:10px;">';

			$levels=array(1,2,3,4,5,6,7,8,9,10,12,14,16,18,20,22,24,26,28,30,32,35,40,45,50,55,60,65,70,75,80,85,90,95,100);
			if($stat!=0&&$stat!=100){if(!in_array($stat,$levels))$levels[]=$stat;}
			asort($levels);
			$levels=array_slice($levels,0,35);
			foreach($levels as $level){
				if($stat!='Off'&&$stat==$level)echo '<input type="submit" name="dimlevel" value="'.$level.'"/ class="dimlevel dimlevela">';
				else echo '<input type="submit" name="dimlevel" value="'.$level.'" class="dimlevel"/>';
			}
			echo '
				</div>
			</form>
			<div class="fix z" style="top:5px;left:5px;"><a href=\'javascript:navigator_Go("floorplan.php");\'><img src="/images/close.png" width="72px" height="72px"/></a></div>
		</div>
	</body>
	<script type="text/javascript">function navigator_Go(url){window.location.assign(url);}</script>
</html>';
		exit;
}
$ctx=stream_context_create(array('http'=>array('timeout' =>2)));
echo '<body class="floorplan">
	<div class="fix clock"><a href=\'javascript:navigator_Go("floorplan.bose.php");\'>'.strftime("%k:%M:%S",time).'</a></div>
	<div class="fix z1" style="top:5px;left:5px;"><a href=\'javascript:navigator_Go("'.$_SESSION['referer'].'");\'><img src="/images/close.png" width="72px" height="72px"/></a></div>
	<div class="fix" style="top:100px;left:0px;">
		<form method="POST">';
echo '
		</form>

		';
echo '
	</div>
	';
$Weg=apcu_fetch('Weg');

Dimmer('zithoek');
Dimmer('eettafel');
Schakelaar('jbl','Light');

Schakelaar('tvled','Light');
$stamp=apcu_fetch('Ttvled');
if($stamp>$eendag)echo '<div class="fix z0 right" style="top:55px;left:96px;width:35px;">'.strftime("%k:%M",$stamp).'</div>';

Schakelaar('kristal','Light');
$stamp=apcu_fetch('Tkristal');
if($stamp>$eendag)echo '<div class="fix z0 right" style="top:55px;left:154px;width:35px;">'.strftime("%k:%M",$stamp).'</div>';

Schakelaar('bureel','Light');
$stamp=apcu_fetch('Tbureel');
if($stamp>$eendag)echo '<div class="fix z0 right" style="top:55px;left:213px;width:35px;">'.strftime("%k:%M",$stamp).'</div>';

Schakelaar('keuken','Light');
Schakelaar('wasbak','Light');
Schakelaar('kookplaat','Light');
Schakelaar('werkblad1','Light');

Schakelaar('denon','denon');
$stamp=apcu_fetch('Tdenon');
if($stamp>$eendag)echo '<div class="fix z0 right" style="top:116px;left:99px;width:35px;">'.strftime("%k:%M",$stamp).'</div>';

Schakelaar('tv','TV');
$stamp=apcu_fetch('Ttv');
if($stamp>$eendag)echo '<div class="fix z0 right" style="top:116px;left:175px;width:35px;">'.strftime("%k:%M",$stamp).'</div>';

if($user!='Gast'){
	Schakelaar('kodi','Kodi');
	$stamp=apcu_fetch('Tkodi');
	if($stamp>$eendag)echo '<div class="fix z0 right" style="top:116px;left:229px;width:35px;">'.strftime("%k:%M",$stamp).'</div>';
	$tnas=apcu_fetch('Tnas');
	$nas=apcu_fetch('nas');
	if($nas=='On')echo '<div class="fix nas"><a href=\'javascript:navigator_Go("?nas=sleep");\'><img src="images/nas_On.png" class="i48" alt=""/></a><br>';
	else echo '<div class="fix nas"><a href=\'javascript:navigator_Go("?nas=wake");\'><img src="images/nas_Off.png" class="i48" alt=""/></a><br>';
	if($tnas>$eendag)echo strftime("%H:%M",$tnas);
	echo '</div>';
}




$nowplaying=json_decode(json_encode(simplexml_load_string(file_get_contents('http://192.168.2.194:8090/now_playing'))),true);
if(!empty($nowplaying)){
	if(isset($nowplaying['@attributes']['source'])){
		echo '<div class="fix blackmedia" >
					<form method="POST">';
		if($nowplaying['@attributes']['source']=='STANDBY'){
			echo '<h3>STANDBY</h3>';
			echo '<button type="submit" name="power" value="power" class="btn b1">Power</button>';
		}else{

			$volume=json_decode(json_encode(simplexml_load_string(file_get_contents('http://192.168.2.194:8090/volume'))),true);
			$cv=$volume['actualvolume'];
			$levels=array($cv-10,$cv-5,$cv-3,$cv-2,$cv-1,$cv,$cv+1,$cv+2,$cv+3,$cv+5,$cv+10);
			foreach($levels as $k){
				if($k>=0){
					if($k==$cv)echo '<button type="submit" name="volume" value="'.$k.'" class="btn volume btna">'.$k.'</button>';
					else echo '<button type="submit" name="volume" value="'.$k.'" class="btn volume">'.$k.'</button>';
				}
			}


			if($nowplaying['@attributes']['source']=='SPOTIFY'){
				echo '<h4>Spotify</h4>';
				echo '<h4>'.@$nowplaying['artist'].'<br>'.@$nowplaying['track'].'</h4>';
				echo '<img src="'.str_replace('http://','https://',$nowplaying['art']).'" height="180px" width="auto"/><br><br>';
			}elseif($nowplaying['@attributes']['source']=='INTERNET_RADIO'){
				echo '<h4>Internet Radio</h4>';
				echo '<h4>'.$nowplaying['stationName'].'</h4>';
				echo '<img src="'.str_replace('http://','https://',$nowplaying['art']).'" height="180px" width="auto"/><br><br>';
			}else echo '<h3>'.$nowplaying['@attributes']['source'].'</h3>';

			//echo '<div style="text-align:left;"><pre>';print_r($nowplaying);echo '</pre></div>';

			$presets=json_decode(json_encode(simplexml_load_string(file_get_contents('http://192.168.2.194:8090/presets'))),true);
			foreach($presets as $i){
				$x=1;
				foreach($i as $j){
					//print_r($j);
					echo '<button type="submit" name="preset" class="btn b2" value="'.$j['@attributes']['id'].'">'.$j['ContentItem']['itemName'].'</button>';
					if($x%2==0)echo '<br>';
					$x++;
				}
			}
			echo '<br><br><button type="submit" name="power" value="power" class="btn b1">Power</button>';

		}
		echo '
					</form>
				</div>';
	}
}
echo '<div class="fix bose"><a href=\'javascript:navigator_Go("floorplan.bose.php");\'><img src="images/Bose.png" id="bose" alt=""/></a></div>';
//echo '<div class="fix floorplanstats">'.$udevice.' | '.$ipaddress.' | '.number_format(((microtime(true)-$start)*1000),3).'</div>';

echo '<script type="text/javascript">
			function navigator_Go(url) {window.location.assign(url);}
			setTimeout("window.location.href=window.location.href;",15000);
		</script>';
}

function setShuffle(){
	keyPress("SHUFFLE_ON");
}
function setNextTrack(){
	keyPress("NEXT_TRACK");
}
function setStop(){
	keyPress("STOP");
}


//else{header("Location: index.php");die("Redirecting to: index.php");}
?>
</body></html>
