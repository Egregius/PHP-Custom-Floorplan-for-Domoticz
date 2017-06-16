<?php
$start=microtime(true);require "secure/settings.php";require "secure/functions.php";if($home){
error_reporting(E_ALL);ini_set("display_errors","on");
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
		<meta name="msapplication-config" content="/browserconfig.xml">
		<link rel="manifest" href="/manifests/floorplan.json">
		<meta name="theme-color" content="#000000">
		<link rel="icon" type="image/png" href="images/domoticzphp48.png"/>
		<link rel="shortcut icon" href="images/domoticzphp48.png"/>
		<link rel="apple-touch-startup-image" href="images/domoticzphp450.png"/>
		<link rel="apple-touch-icon" href="images/domoticzphp48.png"/>
		<link rel="stylesheet" type="text/css" href="/styles/floorplan.php?v=4">

	</head>';
if(isset($_POST['Weg'])){
	if(isset($_POST['Action'])){
		apcu_store('Weg',$_POST['Action']);
		apcu_store('tWeg',$time);
		if($_POST['Action']==0){
			if(apcu_fetch('spoortrf')=='Off')Schakel('poortrf','On');
			if(apcu_fetch('ssirene')=='On')double('sirene','Off');
		}else{
			if(apcu_fetch('spoortrf')=='On')Schakel('poortrf','Off');
		}
	}
	else{
		if(apcu_fetch('sraamliving')=='Open'){
			echo '<body><div id="message" class="fix dimmer" >
				<br><br>
				<h2>Warning:</h2>
				<h2>Raam Living open!<h2>
				<br><br>
				<form action="floorplan.php" method="post">
					<input type="submit" name="cancel" value="Sluit" class="btn" style="height:200px"/>
				</form>
			</div>
			</body>
		</html>';
			exit;
		}
		if(apcu_fetch('sachterdeur')=='Closed'){
			echo '<div id="message" class="fix dimmer" >
				<br><br>
				<h2>Warning:</h2>
				<h2>Achterdeur open!<h2>
				<br><br>
				<form action="floorplan.php" method="post">
					<input type="submit" name="cancel" value="Sluit" class="btn" style="height:200px"/>
				</form>
			</div>
			</body>
		</html>';
			exit;
		}
		if(apcu_fetch('spoort')=='Open'){
			echo '<body><div id="message" class="fix dimmer" >
				<br><br>
				<h2>Warning:</h2>
				<h2>Poort open!<h2>
				<br><br>
				<form action="floorplan.php" method="post">
					<input type="submit" name="cancel" value="Sluit" class="btn" style="height:200px"/>
				</form>
			</div>
			</body>
		</html>';
			exit;
		}
		echo '<body><div id="message" class="fix confirm">
				<form method="post">
					<input type="hidden" name="Weg" value="true"/>
					<button name="Action" value="2" class="btn huge3">Weg</button>
					<button name="Action" value="1" class="btn huge3">Slapen</button>
					<button name="Action" value="0" class="btn huge3">Thuis</button>
				</form>
			</div>
			</body>
		</html>';
			exit;
	}
}
if(isset($_POST['Naam'])&&!isset($_POST['dimmer'])){
	if(in_array($_POST['Naam'],array('bureeltobi','weg','slapen'))){
		if(!isset($_POST['confirm'])){
			switch($_POST['Naam']){
				case 'weg':$txtoff='Thuis';$txton='Weg';break;
				case 'slapen':$txtoff='Wakker';$txton='Slapen';break;
				case 'bureeltobi':$txtoff='Uit';$txton='Aan';break;
			}
			echo '<body><div id="message" class="fix confirm">
				<form method="post">
					<input type="hidden" name="Actie" value="On"/>
					<input type="hidden" name="Naam" value="'.$_POST['Naam'].'"/>
					<input type="submit" name="confirm" value="'.$txton.'" class="btn huge2"/>
				</form>
				<form method="post">
					<input type="hidden" name="Actie" value="Off"/>
					<input type="hidden" name="Naam" value="'.$_POST['Naam'].'"/>
					<input type="submit" name="confirm" value="'.$txtoff.'" class="btn huge2"/>
				</form>
			</div>
			</body>
		</html>';
			exit;
		}elseif(isset($_POST['confirm']))Schakel($_POST['Naam'],$_POST['Actie']);
	}elseif(!in_array($_POST['Naam'],array('radioluisteren','tvkijken','kodikijken')))Schakel($_POST['Naam'],$_POST['Actie']);


	if($_POST['Naam']=='garage'&&$_POST['Actie']=='Off'&&apcu_fetch('spirgarage')=='On')Udevice('pirgarage',0,'Off');
	elseif($_POST['Naam']=='wasbak'&&$_POST['Actie']=='Off'&&apcu_fetch('spirkeuken')=='On')Udevice('pirkeuken',0,'Off');
	elseif($_POST['Naam']=='werkblad'&&$_POST['Actie']=='Off'&&apcu_fetch('spirkeuken')=='On')Udevice('pirkeuken',0,'Off');
	elseif($_POST['Naam']=='keuken'&&$_POST['Actie']=='Off'&&apcu_fetch('spirkeuken')=='On')Udevice('pirkeuken',0,'Off');
	elseif($_POST['Naam']=='inkom'&&$_POST['Actie']=='Off'&&apcu_fetch('spirinkom')=='On'){Udevice('pirinkom',0,'Off');Udevice('pirhall',0,'Off');}
	elseif($_POST['Naam']=='hall'&&$_POST['Actie']=='Off'&&apcu_fetch('spirhall')=='On'){Udevice('pirhall',0,'Off');Udevice('pirinkom',0,'Off');}
	elseif($_POST['Naam']=='slapen'){apcu_store('sslapen',$_POST['Actie']);file_get_contents('http://127.0.0.1/secure/cronforce.php');}
	elseif($_POST['Naam']=='weg'){
		apcu_store('sweg',$_POST['Actie']);
		if($_POST['Actie']==2)sw('garage','Off');
		if($_POST['Actie']==0)if(apcu_fetch('ssirene')!='Group Off')double('sirene','Off');
		file_get_contents('http://127.0.0.1/secure/cronforce.php');
	}

}
elseif(isset($_POST['dimmer'])){
	if(isset($_POST['dimlevelon_x'])){
		Dim($_POST['Naam'],100);
		apcu_store('dimtime'.$_POST['Naam'],$time);
		apcu_store('dimaction'.$_POST['Naam'],0);
	}elseif(isset($_POST['dimleveloff_x'])){
		Dim($_POST['Naam'],0);
		apcu_store('dimtime'.$_POST['Naam'],$time);
		apcu_store('dimaction'.$_POST['Naam'],0);
	}elseif(isset($_POST['dimsleep_x'])){
		lg('=> '.$user.' => activated dimmer sleep for '.$_POST['Naam']);
		apcu_store('dimaction'.$_POST['Naam'],1);
	}elseif(isset($_POST['dimwake_x'])){
		lg('=> '.$user.' => activated dimmer wake for '.$_POST['Naam']);
		Dim($_POST['Naam'],$_POST['dimwakelevel']+1);
		apcu_store('dimaction'.$_POST['Naam'],2);
	}else{
		Dim($_POST['Naam'],$_POST['dimlevel']);
		apcu_store('dimaction'.$_POST['Naam'],0);
	}
}
if(isset($_POST['Scene'])){
	if(isset($_POST['action'])){
		if($_POST['action']=='Media uit')Udevice('miniliving4l',0,'On');
		elseif($_POST['action']=='Radio')Udevice('miniliving1s',0,'On');
		elseif($_POST['action']=='TV kijken')Udevice('miniliving1l',0,'On');
		elseif($_POST['action']=='Kodi kijken')Udevice('miniliving2l',0,'On');
		elseif($_POST['action']=='Kodi Control'){header("Location: /kodi.php");die("Redirecting to: /kodi.php");}
		elseif($_POST['action']=='Denon Control'){header("Location: /denon.php");die("Redirecting to: /denon.php");}
	}else{
		echo '<body><div id="message" class="fix confirm">
				<form method="post">
					<input type="hidden" name="Scene" value="true"/>
					<input type="submit" name="action" value="Media uit" class="btn huge6"/>
					<input type="submit" name="action" value="Radio" class="btn huge6"/>
					<input type="submit" name="action" value="Kodi kijken" class="btn huge6"/>
					<input type="submit" name="action" value="TV kijken" class="btn huge6"/>
					<input type="submit" name="action" value="Kodi Control" class="btn huge6"/>
					<input type="submit" name="action" value="Denon Control" class="btn huge6"/>
					<input type="submit" name="action" value="Annuleer" class="btn huge6"/>
				</form>
			</div>
			</body>
		</html>';
			exit;
	}
}
echo '<body class="floorplan"><div class="fix clock"><a href=\'javascript:navigator_Go("floorplan.php");\'>'.strftime("%k:%M:%S",$time).'</a></div>
	<div class="fix center zon"><small>
		<table>
			<tr><td>V:</td><td align="right">';
			$consumption=apcu_fetch('consumption');
			if($consumption>5000)echo '<font color="red">'.$consumption.' W</font>';
			elseif($consumption>1000)echo '<font color="orange">'.$consumption.' W</font>';
			else echo $consumption.' W';
			echo '</td></tr>';
			$zon=apcu_fetch('zon');
			if($zon>0) echo '<tr><td>Z:</td><td align="right">'.apcu_fetch('zon').' W</td></tr>';
		echo '</table></small>';
	$regen=apcu_fetch('buien');
	if($regen>0)echo '<a href=\'javascript:navigator_Go("regen.php");\'>Buien: '.$regen.'</a><br>';

	$wind=apcu_fetch('wind');
	if($wind>0){
		echo round($wind,1).' '.apcu_fetch('winddir');
	}
	echo '</div>';
	$icon=apcu_fetch('icon');
	if(!empty($icon)){
		if($udevice=='Mac') echo '<div class="fix weather"><a href="https://darksky.net/details/51.893,3.1125/'.strftime("%Y-%m-%d",$time).'/si24/nl" target="popup" ><img src="https://icons.wxug.com/i/c/k/'.$icon.'.gif"/></a></div>';
		else echo '<div class="fix weather"><a href=\'javascript:navigator_Go("https://darksky.net/details/51.893,3.1125/'.strftime("%Y-%m-%d",$time).'/si24/nl");\'><img src="https://icons.wxug.com/i/c/k/'.$icon.'.gif"/></a></div>';
	}
	echo '
	<div class="fix radioluisteren"><form method="POST"><input type="hidden" name="Scene" value="radioluisteren"><input type="image" src="/images/Amp_';echo apcu_fetch('sdenon')=='On'?'On':'Off';echo '.png" class="i70"></form></div>
	<div class="fix tvkijken"><form method="POST"><input type="hidden" name="Scene" value="tvkijken"><input type="image" src="/images/TV_';echo apcu_fetch('stv')=='On'?'On':'Off';echo '.png" class="i60"></form></div>
	<div class="fix kodikijken">';echo apcu_fetch('skodi')=='Off'?'<form method="POST"><input type="hidden" name="Scene" value="kodikijken"><input type="image" src="/images/Kodi_Off.png" class="i48"></form>':'<form method="POST"><input type="hidden" name="Scene" value="kodikijken"><input type="image" src="/images/Kodi_On.png" class="i48"></form>';echo '</div>
	<div class="fix heatingicon"><a href=\'javascript:navigator_Go("heating.php");\'><img src="/images/Fire_';echo apcu_fetch('sbrander')=='On'?'On':'Off';echo '.png" class="i48"></a></div>
	<div class="fix floorplan2icon"><a href=\'javascript:navigator_Go("floorplan2.php");\'><img src="/images/plus.png" class="i60"/></a></div>
	<div class="fix weg">
		<form method="POST">
			<input type="hidden" name="Weg" value="true"/>';
	$Weg=apcu_fetch('Weg');
	if($Weg==0) echo '<input type="image" src="/images/Thuis.png" id="Weg"/>';
	elseif($Weg==1) echo '<input type="image" src="/images/Slapen.png" id="Weg"/>';
	elseif($Weg==2) echo '<input type="image" src="/images/Weg.png" id="Weg"/>';
	echo '
		</form>
	</div>';


Dimmer('tobi');
Dimmer('zithoek');
Dimmer('eettafel');
Dimmer('kamer');
Dimmer('alex');
Schakelaar('tvled','Light');
Schakelaar('kristal','Light');
Schakelaar('bureel','Light');
Schakelaar('inkom','Light');
Schakelaar('keuken','Light');
Schakelaar('wasbak','Light');
Schakelaar('kookplaat','Light');
Schakelaar('werkblad1','Light');
Schakelaar('lichtbadkamer1','Light');
Schakelaar('lichtbadkamer2','Light');
Schakelaar('voordeur','Light');
Schakelaar('hall','Light');
Schakelaar('garage','Light');
Schakelaar('zolderg','Light');
Schakelaar('terras','Light');
Schakelaar('tuin','Light');
Schakelaar('zolder','Light');
Schakelaar('bureeltobi','Plug');
Schakelaar('badkamervuur','Plug');
Schakelaar('diepvries','Light');
if($Weg==0)Schakelaar('poortrf','Alarm');
//Schakelaar('kerstboom','Kerstboom');
Thermometer('buiten_temp');
Thermometer('living_temp');
Thermometer('badkamer_temp');
Thermometer('kamer_temp');
Thermometer('tobi_temp');
Thermometer('alex_temp');
Thermometer('zolder_temp');
Blinds('zoldertrap');
Luifel('luifel');
if($Weg>0){Secured('zliving');Secured('zkeuken');Secured('zinkom');Secured('zgarage');}
if($Weg==2){Secured('zhalla');Secured('zhallb');}
if(apcu_fetch('spirliving')=='On')Motion('zliving');
if(apcu_fetch('spirkeuken')=='On')Motion('zkeuken');
if(apcu_fetch('spirinkom')=='On')Motion('zinkom');
if(apcu_fetch('spirgarage')=='On')Motion('zgarage');
if(apcu_fetch('spirhall')=='On'){Motion('zhalla');Motion('zhallb');}
if(apcu_fetch('tbelknop')>$eendag)Timestamp('belknop',270);
if(apcu_fetch('tpirgarage')>$eendag)Timestamp('pirgarage',0);
if(apcu_fetch('tpirliving')>$eendag)Timestamp('pirliving',0);
if(apcu_fetch('tpirkeuken')>$eendag)Timestamp('pirkeuken',0);
if(apcu_fetch('tpirinkom')>$eendag)Timestamp('pirinkom',0);
if(apcu_fetch('tpirhall')>$eendag)Timestamp('pirhall',0);
if(apcu_fetch('tachterdeur')>$eendag)Timestamp('achterdeur',270);
if(apcu_fetch('tpoort')>$eendag)Timestamp('poort',90);
if(apcu_fetch('traamliving')>$eendag)Timestamp('raamliving',270);
if(apcu_fetch('traamtobi')>$eendag)Timestamp('raamtobi',270);
if(apcu_fetch('traamalex')>$eendag)Timestamp('raamalex',270);
if(apcu_fetch('traamkamer')>$eendag)Timestamp('raamkamer',90);
if(apcu_fetch('tdeurbadkamer')>$eendag)Timestamp('deurbadkamer',90);
if(apcu_fetch('spoort')=='Open')echo '<div class="fix poort"></div>';
if(apcu_fetch('sachterdeur')=='Closed')echo '<div class="fix achterdeur"></div>';
if(apcu_fetch('sraamliving')=='Open')echo '<div class="fix raamliving"></div>';
if(apcu_fetch('sraamtobi')=='Open')echo '<div class="fix raamtobi"></div>';
if(apcu_fetch('sraamalex')=='Open')echo '<div class="fix raamalex"></div>';
if(apcu_fetch('sraamkamer')=='Open')echo '<div class="fix raamkamer"></div>';
if(apcu_fetch('sdeurbadkamer')=='Open')echo '<div class="fix deurbadkamer"></div>';

$diepvries=apcu_fetch('sdiepvries_temp');
echo $diepvries > -15 ? '<div id="diepvries z0" class="fix diepvries_temp red"> '.$diepvries.'°C</div>'
				: '<div id="diepvries z0" class="fix diepvries_temp">'.$diepvries.'°C</div>';

$power=apcu_fetch('sUsage_grohered');
if($power>0&&$power<10)echo '<div class="fix z0 GroheRed"><img src="images/Plug_On.png" width="28px" height="auto" alt=""/></div>';
elseif($power>0) echo '<div class="fix z0 GroheRed"><img src="images/Plug_Red.png" width="28px" height="auto" alt=""/></div>';

$power=apcu_fetch('sUsage_bureeltobi');
if($power>0)echo '<div class="fix bureeltobikwh z0">'.round($power,0).'W</div>';

echo '<div class="fix floorplanstats">'.$udevice.' | '.$ipaddress.' | '.number_format(((microtime(true)-$start)*1000),3).'</div>';
if(isset($_REQUEST['setdimmer'])){
	$name=$_REQUEST['setdimmer'];
	$stat=apcu_fetch('s'.$name);
	echo '<div id="D'.$name.'" class="fix dimmer" >
		<form method="POST" action="floorplan.php" oninput="level.value = dimlevel.valueAsNumber">
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
					<input type="image" name="dimsleep" value ="100" src="images/Sleepy.png" class="i90"/>
				</div>
				<div class="fix z" style="top:100px;left:265px;">
					<input type="image" name="dimwake" value="100" src="images/Wakeup.png" style="height:90px;width:90px"/>
					<input type="hidden" name="dimwakelevel" value="'.$stat.'">
				</div>
				<div class="fix z" style="top:100px;left:385px;">
					<input type="image" name="dimlevelon" value ="100" src="images/Light_On.png" class="i90"/>
				</div>
				<div class="fix z" style="top:210px;left:10px;">';
			$levels=array(1,2,3,4,5,6,7,8,9,10,12,14,16,18,20,22,24,26,28,30,32,35,40,45,50,55,60,65,70,75,80,85,90,95,100);
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
}elseif(isset($_REQUEST['luifel'])){
	$stat=apcu_fetch('luifel');
	echo '<div id="Luifel" class="fix dimmer" >
		<form method="POST" action="floorplan.php" oninput="level.value = dimlevel.valueAsNumber">
				<div class="fix z" style="top:15px;left:90px;">';
				if($stat==100) echo '<h2>Luifel dicht</h2>';
				else echo '<h2>Luifel '.(100-$stat).'% Open</h2>';
				echo '
					<input type="hidden" name="Naam" value="luifel">
					<input type="hidden" name="dimmer" value="true">
				</div>
				<div class="fix z" style="top:100px;left:100px;">
					<input type="image" name="dimleveloff" value ="100" src="images/arrowgreenup.png" class="i90"/>
				</div>
				<div class="fix z" style="top:100px;left:300px;">
					<input type="image" name="dimlevelon" value ="0" src="images/arrowgreendown.png" class="i90"/>
				</div>
				<div class="fix z" style="top:210px;left:10px;">';
			$levels=array(0,5,10,15,20,25,30,32,34,36,38,40,42,44,46,48,49,50,51,52,54,56,58,60,62,64,66,68,70,75,80,85,90,95,100);
			foreach($levels as $level){
				if($stat!='Off'&&$stat==$level)echo '<button name="dimlevel" value="'. $level.'" class="dimlevel dimlevela">'.(100-$level).'</button>';
				else echo '<button name="dimlevel" value="'.$level.'" class="dimlevel">'.(100-$level).'</button>';
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
echo '<script type="text/javascript">
			function navigator_Go(url) {window.location.assign(url);}
			setTimeout("window.location.href=window.location.href;",';
			echo $local===true?'3950':'15000';
			echo ');
		</script>';
}
else{header("Location: index.php");die("Redirecting to: index.php");}
?>
</body></html>
