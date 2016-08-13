<?php 
require_once "/volume1/web/secure/settings.php";
require_once "/volume1/web/secure/functions.php";
?>
<html><head><meta http-equiv="Content-Type" content="text/html;charset=UTF-8" /><meta name="HandheldFriendly" content="true"/><meta name="apple-mobile-web-app-capable" content="yes"><meta name="apple-mobile-web-app-status-bar-style" content="black"><meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,minimal-ui" /><link rel="icon" type="image/png" href="images/domoticzphp48.png"/><link rel="shortcut icon" href="images/domoticzphp48.png"/><link rel="apple-touch-startup-image" href="images/domoticzphp48.png"/><link rel="apple-touch-icon" href="images/domoticzphp48.png"/><meta name="msapplication-TileColor" content="#ffffff"/><meta name="msapplication-TileImage" content="images/domoticzphp48.png"/><meta name="msapplication-config" content="browserconfig.xml"/><meta name="mobile-web-app-capable" content="yes"/><link rel="manifest" href="manifest.json"/><meta name="theme-color" content="#ffffff"/><link rel="stylesheet" href="style.css"/>
<style type="text/css">body{margin:0 auto;}	form, table {display:inline;margin:0px;padding:0px;}</style>
<script type="text/javascript">setTimeout('window.location.href=window.location.href;',2900);</script>
</head>
<?php 
function convertToHoursMins($time, $format = '%01d u %02d min') {if ($time < 1) return;$hours = floor($time / 60);$minutes = ($time % 60);return sprintf($format, $hours, $minutes);}
if($authenticated === true||basename($_SERVER['PHP_SELF'])=='pass2php.time.php') {
if(isset($_POST['Schakel'])) if(Schakel($_POST['Schakel'],$_POST['Actie'],$_POST['Naam'])=='ERROR') echo '<div id="message" class="balloon">'.$_POST['Naam'].' '.$_POST['Actie'].'<br/>ERROR</div>';
if(isset($_POST['Setpoint'])) {
	if(Udevice($_POST['Setpoint'],0,number_format($_POST['Actie'],1),$_POST['Naam'])=='ERROR') echo '<div id="message" class="balloon">'.$_POST['Naam'].' '.$value.'<br/>ERROR</div>';
	else cset('setpoint'.$_POST['Naam'],2);
}
{
	$domoticz=json_decode(file_get_contents($domoticzurl.'json.htm?type=devices&used=true&plan=4'),true);
	foreach($domoticz['result'] as $dom) {
		isset($dom['Type'])?$Type=$dom['Type']:$Type='None';
		isset($dom['SwitchType'])?$SwitchType=$dom['SwitchType']:$SwitchType='None';
		isset($dom['SubType'])?$SubType=$dom['SubType']:$SubType='None';
		$name=$dom['Name'];
		if($Type=='Temp'){${'T'.$name}=$dom['Temp'];${'TI'.$name}=$dom['idx'];${'TT'.$name}=strtotime($dom['LastUpdate']);}
		else if($Type=='Temp + Humidity + Baro'){$Wtemp=$dom['Temp'];$Whum=$dom['Humidity'];$Wforecast=$dom['ForecastStr'];$Wbarometer=$dom['Barometer'];}
		else if($SwitchType=='Dimmer'){${'DI'.$name}=$dom['idx'];$dom['Status']=='Off'?${'D'.$name}='Off':${'D'.$name}='On';$dom['Status']=='Off'?${'Dlevel'.$name}=0:${'Dlevel'.$name}=$dom['Level'];${'DT'.$name}=strtotime($dom['LastUpdate']);}
		else if($Type=='Usage'&&$dom['SubType']=='Electric') ${'P'.$name}=substr($dom['Data'],0,-5);
		else if($Type=='Radiator 1'||$Type=='Thermostat') {${'RI'.$name}=$dom['idx'];${'R'.$name}=$dom['Data'];${'RT'.$name}=strtotime($dom['LastUpdate']);}
		else {
			if(substr($dom['Data'],0,2)=='On') ${'S'.$name}='On';
			else if(substr($dom['Data'],0,3)=='Off') ${'S'.$name}='Off';
			else if(substr($dom['Data'],0,4)=='Open') ${'S'.$name}='Open';
			else ${'S'.$name}=$dom['Data'];
			${'SI'.$name}=$dom['idx'];
			${'ST'.$name}=strtotime($dom['LastUpdate']);
		}
	}
	require_once "/volume1/web/secure/header.php"; 
	echo '
	<script type="text/javascript">
		function toggle_visibility(id){var e=document.getElementById(id);if(e.style.display==\'inherit\') e.style.display=\'none\';else e.style.display=\'inherit\';}
		function navigator_Go(url) {window.location.assign(url);}
		setTimeout(\'window.location.href=window.location.href;\',19950);
	</script>
	<style>
	tr{}
	td{line-height:2;font-size:18px;}
	@media only screen and (min-device-width : 768px) and (max-device-width : 1024px)  {
		td{line-height:1.5;}
	}
	</style>';
}
	$difkamer = number_format($Tkamer_temp - $Rkamer_set,1);
	$diftobi = number_format($Ttobi_temp - $Rtobi_set,1);
	$difalex = number_format($Talex_temp - $Ralex_set,1);
	$difbadkamer = number_format($Tbadkamer_temp - $Rbadkamer_set,1);
	$difliving = number_format($Tliving_temp - $Rliving_set,1);
	$kamers=array('living','badkamer','tobi','alex','kamer');
	$kamersgas=array('living','alex');
	$bigdif=100;$rooms = 0;
	foreach($kamers as $kamer) {
		${'koud'.$kamer} = false;
		if(in_array($kamer, $kamersgas)) {
			if(${'dif'.$kamer}<$bigdif) $bigdif = ${'dif'.$kamer};
			if(${'dif'.$kamer} <= $bigdif) $rooms = $rooms + 1;
		}
		if(${'dif'.$kamer} < 0) ${'color'.$kamer} = '#1199FF';
		else if(${'dif'.$kamer} > 0) ${'color'.$kamer} = '#FF5511';
		else ${'color'.$kamer} = '#AAAAAA';
		${'Set'.$kamer} = number_format(${'R'.$kamer.'_set'},1);
	}
	echo '<a href=\'javascript:navigator_Go("temp.php");\' class="btn nav">Graph</a><a href=\'javascript:navigator_Go("heating.php");\' class="btn nav">'.strftime("%k:%M:%S",$time).'</a><br/>
<table>
	<tr>
		<td width="70px"></td>
		<td width="40px" align="center"><b>setpoint</b></td>
		<td width="40px" align="center"><b>temp</b></td>
		<td width="40px" align="center"><b>diff</b></td>
		<td>&nbsp;&nbsp;<b>Radiator</b></td>
	</tr>';
	foreach($kamers as $kamer) {
		if($kamer!="badkamer") {
		echo '
		<div id="S'.$kamer.'" class="dimmer" style="display:none;">
			<form method="POST" oninput="level.value = Actie.valueAsNumber">
				<input type="hidden" name="Setpoint" value="'.${'RI'.$kamer.'_set'}.'" >
				<h2>'.ucwords($kamer).'<br/><big>
				<bold>'.number_format(${'T'.$kamer.'_temp'},1,",","").'°C</bold></big></h2>
				<input type="hidden" name="Naam" value="'.$kamer.'">
				<div style="position:absolute;top:200px;left:10px;z-index:1000;">';
		$temps=array(5,8,10,12,14,15,16,17,18,19,20,21,22,23,24);
		if(!in_array(${'R'.$kamer.'_set'}, $temps)) $temps[] = ${'R'.$kamer.'_set'};
		asort($temps);
		$temps = array_slice($temps, 0, 15);
		foreach($temps as $temp) {
			if(${'R'.$kamer.'_set'}==$temp) echo '
				<input type="submit" name="Actie" value="'.$temp.'"/ style="text-align:center;background-color:#5fed5f;background:linear-gradient(to bottom, #5fed5f, #017a01);">';
			else { echo '
				<input type="submit" style="text-align:center;" name="Actie" value="'.$temp.'"/>';}
		}
		echo '
			</div>
			<div style="position:absolute;top:250px;left:50px;z-index:-1;">';
		echo ${'R'.$kamer.'_set'}>${'T'.$kamer.'_temp'}?'
				<img src="images/flame.png" height="400px" width="auto">':'
				<img src="images/flamegrey.png" height="400px" width="auto">';
		echo '
			</div>
		</form>
		<form action="temp.php" method="POST">
			<div style="position:absolute;top:650px;left:150px;z-index:10;">
				<input type="hidden" name="sensor" value="'.${'TI'.$kamer.'_temp'}.'"/>
				<input type="hidden" name="naam" value="'.$kamer.'"/>
				<input type="submit" style="width:200px" value="Graph"/>
			</div>
		</form>
		<div style="position:absolute;top:5px;right:5px;z-index:1000;">
			<a href=\'javascript:navigator_Go("heating.php");\'><img src="images/close.png" width="72px" height="72px"/></a>
			</div>
		</div>';
	}
		echo '
		<tr id='.$kamer.' style="color:'.${'color'.$kamer}.'" onclick="toggle_visibility(\'S'.$kamer.'\');" >
			<td align="right" line-height="4"><b>'.$kamer.'</b></td>
			<td align="center">'.${'R'.$kamer.'_set'}.'</td>
			<td align="center">'.number_format(${'T'.$kamer.'_temp'},1).'</td>
			<td align="center">'.${'dif'.$kamer}.'</td>';
		
		if(${'dif'.$kamer} <= number_format(($bigdif+ 0.2),1) && ${'dif'.$kamer}< 1 ) {
			if(in_array($kamer, $kamersgas)) echo '<td>&nbsp;&nbsp;'. ${'R'.$kamer.'Z'}.' +</td>';
		} 
		else {
			if(in_array($kamer, $kamersgas)) echo '<td>&nbsp;&nbsp;'.${'R'.$kamer.'Z'}.'</td>';
		}
		if($kamer=='badkamer') echo '<td>&nbsp;&nbsp;'.$Sbadkamervuur.'</td>';
		echo '</tr>';
	}
	echo '
		<tr>
			<td align="right" height="100">Heating</td>
			<td>
				<form method="POST">
					<input type="hidden" name="Schakel" value="'.$SIheating.'">';
	echo $Sheating=='Off'?'
					<input type="hidden" name="Actie" value="On">
					<input type="hidden" name="Naam" value="heating">
					<input type="image" src="images/Fire_Off.png" height="48px" width="auto">' 
				   :'
					<input type="hidden" name="Actie" value="Off">
					<input type="hidden" name="Naam" value="heating">
					<input type="image" src="images/Fire_On.png" height="48px" width="auto">';
	echo '
				</form>
			</td>
			<td align="right" height="100">Brander</td>
			<td>
				<form method="POST">
					<input type="hidden" name="Schakel" value="'.$SIbrander.'">';
	echo $Sbrander=='Off'?'
					<input type="hidden" name="Actie" value="On">
					<input type="hidden" name="Naam" value="brander">
					<input type="image" src="images/Fire_Off.png" height="48px" width="auto">' 
	:'
					<input type="hidden" name="Actie" value="Off"><input type="hidden" name="Naam" value="brander"><input type="image" src="images/Fire_On.png" height="48px" width="auto">';
	echo '
				</form>
			</td>
			<td>';
			if($STbrander>$eendag) echo strftime("%k:%M:%S",$STbrander);
			echo '</td>
		</tr>';
		echo '
		<tr>
		<td align="right" height="70">Vestiaire</td>
			<td>
				<form method="POST">
					<input type="hidden" name="Schakel" value="'.$SIfanvestiaire.'">';
	echo $Sfanvestiaire=='Off'?'
					<input type="hidden" name="Actie" value="On">
					<input type="hidden" name="Naam" value="fanvestiaire">
					<input type="image" src="images/Fan_Off.png" height="48px" width="auto">' 
	:'
					<input type="hidden" name="Actie" value="Off"><input type="hidden" name="Naam" value="brander"><input type="image" src="images/Fan_On.png" height="48px" width="auto">';
	echo '
				</form>
			</td>
		</tr>';
		echo '
	</table>
</body>
</html>';
	if($user=='Guy') {
		echo '<table>';
		$datas = json_decode(file_get_contents($domoticzurl.'json.htm?type=lightlog&idx=131',true,$ctx),true);
		$status = '';$tijdprev = $time;$totalon = 0;
		if(!empty($datas['result'])) {
			foreach($datas['result'] as $data) {
				if($status!=$data['Status']) {
					$status=$data['Status'];
					$level=$data['Level'];
					$tijd = strtotime($data['Date']);
					if($tijd<$twaalfuur) break;
					$period = ($tijdprev - $tijd);
					if($status=='On') $totalon = $totalon + $period;
					$tijdprev = $tijd;
					echo '<tr align="right"><td>'.$data['Date'].'</td><td>&nbsp;'.$status.'&nbsp;</td><td>&nbsp;'.convertToHoursMins($period/60).'</td></tr>';
				}
			}
		}
		echo '</table>
			<div style="position:absolute;top:4.55em;left:13.6em;width:265px;">Dif= '.$bigdif.'</div>';
	}
}
