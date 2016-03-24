<html><head><title>Heating</title><meta http-equiv="Content-Type" content="text/html;charset=UTF-8" /><meta name="HandheldFriendly" content="true"/><meta name="apple-mobile-web-app-capable" content="yes"><meta name="apple-mobile-web-app-status-bar-style" content="black"><meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,minimal-ui" /><link rel="icon" type="image/png" href="images/domoticzphp48.png"/><link rel="shortcut icon" href="images/domoticzphp48.png"/><link rel="apple-touch-startup-image" href="images/domoticzphp48.png"/><link rel="apple-touch-icon" href="images/domoticzphp48.png"/><meta name="msapplication-TileColor" content="#ffffff"/><meta name="msapplication-TileImage" content="images/domoticzphp48.png"/><meta name="msapplication-config" content="browserconfig.xml"/><meta name="mobile-web-app-capable" content="yes"/><link rel="manifest" href="manifest.json"/><meta name="theme-color" content="#ffffff"/><link rel="stylesheet" href="style.css"/>
<style type="text/css">body{margin:0 auto;}	form, table {display:inline;margin:0px;padding:0px;}</style>
<script type="text/javascript">setTimeout('window.location.href=window.location.href;',14900);</script>
</head>
<?php require_once "/volume1/web/secure/functions.php";$time=$_SERVER['REQUEST_TIME'];$eenuur=$time-3590;$tweeuur=$time-7190;
function setradiator($name, $koudst=false) {
	global $domoticzurl,$user,$Usleep,$ctx,${'T'.$name},${'TI'.$name},${'R'.$name},${'RT'.$name},${'RI'.$name},${'R'.$name.'Z'},${'RI'.$name.'Z'},$time,$tienmin,$halfuur;
	$setpoint = number_format(${'R'.$name},1);
	$datas = json_decode(file_get_contents($domoticzurl.'json.htm?type=graph&sensor=temp&idx='.${'TI'.$name}.'&range=day',true,$ctx),true);
	$datas = array_slice($datas['result'], -3, 3);
	$total = ${'T'.$name};
	foreach($datas as $data) $total = $total + $data['te'];
	$temp = number_format($total / 4,1);
	number_format(${'R'.$name},1)>number_format(${'T'.$name},1)?$warmen=true:$warmen=false;
	number_format(${'T'.$name},1)>$temp?$warment=true:$warment=false;
	$rad = unserialize(cget('temp'.$name));
	if($warmen==true) {
		$setpoint = number_format($rad['1'], 1);
		if($setpoint<${'R'.$name}) $setpoint=${'R'.$name};
		if($koudst==true) $setpoint = number_format($setpoint + 1,1);
		if($warment==false&&$rad['t']<$tienmin) $setpoint = number_format($setpoint + 0.5 , 1);
		if($setpoint>number_format(${'R'.$name},1)+2) $setpoint=number_format(${'R'.$name},1)+2;else if ($setpoint<number_format(${'R'.$name},1)-2) $setpoint=number_format(${'R'.$name},1)-2;
		if($setpoint>24) $setpoint=24.0;else if ($setpoint<4) $setpoint=4.0;
	}
	if($warmen==false) {
		$setpoint = number_format($rad['1'], 1);
		if($setpoint>${'R'.$name}) $setpoint=number_format(${'R'.$name},1);
		if($warment==true&&$rad['t']<$tienmin) $setpoint = number_format($setpoint - 0.5 , 1);
		if($koudst==true) $setpoint = number_format(${'R'.$name} + 1,1);
		if($setpoint>number_format(${'R'.$name},1)+2) $setpoint=number_format(${'R'.$name},1)+2;else if ($setpoint<number_format(${'R'.$name},1)-2) $setpoint=number_format(${'R'.$name},1)-2;
		if($setpoint>24) $setpoint=24.0;else if ($setpoint<4) $setpoint=4.0;
	}
	usleep($Usleep);
	return number_format($setpoint,1);
}
function convertToHoursMins($time, $format = '%01d u %02d min') {if ($time < 1) return;$hours = floor($time / 60);$minutes = ($time % 60);return sprintf($format, $hours, $minutes);}
if($authenticated === true||basename($_SERVER['PHP_SELF'])=='cron.php') {
	if(isset($_POST['Schakel'])) if(Schakel($_POST['Schakel'],$_POST['Actie'],$_POST['Naam'])=='ERROR') echo '<div id="message" class="balloon">'.$_POST['Naam'].' '.$_POST['Actie'].'<br/>ERROR</div>';
	if(isset($_POST['Setpoint'])) {
		if(Udevice($_POST['Setpoint'],0,number_format($_POST['Actie'],1),$_POST['Naam'])=='ERROR') echo '<div id="message" class="balloon">'.$_POST['Naam'].' '.$value.'<br/>ERROR</div>';
		else {cset('setpoint'.$_POST['Naam'],2);cset('temp'.$_POST['Naam'], serialize(array('1' => $_POST['Actie'], 't' => $time)));}
	}
	if(basename($_SERVER['PHP_SELF'])!='cron.php') {
		$domoticz=json_decode(file_get_contents($domoticzurl.'json.htm?type=devices&used=true&plan=6'),true);
		foreach($domoticz['result'] as $dom) {
			isset($dom['Type'])?$Type=$dom['Type']:$Type='None';
			isset($dom['SwitchType'])?$SwitchType=$dom['SwitchType']:$SwitchType='None';
			isset($dom['SubType'])?$SubType=$dom['SubType']:$SubType='None';
			$name=$dom['Name'];
			if($Type=='Temp'){${'T'.$name}=$dom['Temp'];${'TI'.$name}=$dom['idx'];${'TT'.$name}=strtotime($dom['LastUpdate']);}
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
	//heating on/off
		if($Sweg=='On') {if($Sheating!='Off'&&$STheating<$eenuur) {Schakel($SIheating, 'Off','heating');$Sheating = 'Off';}
		} else {if($Sheating!='On') {Schakel($SIheating, 'On','heating');$Sheating = 'On';}}
		//Slaapkamer
		$Setkamer = 8.0;
		$setpointkamer = cget('setpointkamer');
		if($setpointkamer!=0 && $RTkamer < $eenuur) {cset('setpointkamer',0);$setpointkamer=0;}
		if($setpointkamer!=2) {
			if($Tbuiten<15 && $Sraamkamer=='Closed' && $Sheating=='On' && ($STraamkamer<$tweeuur||$time > $achtavond)) {
				$Setkamer = 12.0;
				if($time < $zevenochtend || $time > $achtavond) $Setkamer = 16.0;
			}
			$Setkamer = number_format($Setkamer,1);
			if($Rkamer != $Setkamer) {Udevice($RIkamer,0,$Setkamer,'Rkamer');$Rkamer=$Setkamer;}
		}
		$difkamer = number_format($Tkamer - $Rkamer,1);
		//Slaapkamer tobi
		$Settobi = 8.0;
		$setpointtobi = cget('setpointtobi');
		if($setpointtobi!=0 && $RTtobi < $eenuur) {cset('setpointtobi',0);$setpointtobi=0;}
		if($setpointtobi!=2) {
			if($Tbuiten<15 && $Sraamtobi=='Closed' && $Sheating=='On' && ($STraamtobi<$tweeuur||$time > $achtavond)) {
				$Settobi = 12.0;
				if (date('W')%2==1) {
						 if (date('N') == 3) { if($time > $achtavond) $Settobi = 16.0;}
					else if (date('N') == 4) { if($time < $zevenochtend || $time > $achtavond) $Settobi = 16.0;}
					else if (date('N') == 5) { if($time < $zevenochtend) $Settobi = 16.0;}
				} else {
						 if (date('N') == 3) { if($time > $achtavond) $Settobi = 16.0;}
					else if (in_array(date('N'),array(4,5,6))) { if($time < $zevenochtend || $time > $achtavond) $Settobi = 16.0;}
					else if (date('N') == 7) { if($time < $zevenochtend) $Settobi = 16.0;}
				}
			}
			$Settobi = number_format($Settobi,1);
			if($Rtobi != $Settobi) {Udevice($RItobi,0,$Settobi,'Rtobi');$Rtobi=$Settobi;cset('temptobi', serialize(array('1' => $Settobi, 't' => $time)));}
		}
		$diftobi = number_format($Ttobi - $Rtobi,1);
		//Slaapkamer alex
		$Setalex = 8.0;
		$setpointalex = cget('setpointalex');
		if($setpointalex!=0 && $RTalex < $eenuur) {cset('setpointalex',0);$setpointalex=0;}
		if($setpointalex!=2) {
			if($Tbuiten<17 && $Sraamalex=='Closed' && $Sheating=='On' && ($STraamalex<$tweeuur||$time > $achtavond)) {
				$Setalex = 12.0;
				if($time < strtotime('8:00') || $time > $achtavond) $Setalex = 19.5;
			}
			$Setalex = number_format($Setalex,1);
			if($Ralex != $Setalex) {Udevice($RIalex,0,$Setalex,'Ralex');$Ralex=$Setalex;cset('tempalex', serialize(array('1' => $Setalex, 't' => $time)));}
		}
		$difalex = number_format($Talex - $Ralex,1);
		//badkamer
		$Setbadkamer=14.0;
		$setpointbadkamer = cget('setpointbadkamer');
		if($setpointbadkamer!=0 && $RTbadkamer < $eenuur) {cset('setpointbadkamer',0);$setpointbadkamer=0;}
		if($setpointbadkamer!=2) {
			if($Tbuiten<21 && $Sheating=='On') {
				$Setbadkamer=17.0;
				if(in_array(date('N',$time), array(1,2,3,4,5)) && $time>=strtotime('6:00') && $time<=strtotime('7:20')) $Setbadkamer=21.0;
				else if(in_array(date('N',$time), array(6,7)) && $time>=strtotime('7:30') && $time<=strtotime('9:30')) $Setbadkamer=20.0;
				else if($time>=strtotime('9:30') && $time<=strtotime('23:59') && $Sslapen=='Off') $Setbadkamer=18.0;

			}
			if($Sdeurbadkamer!='Closed' && $STdeurbadkamer < $time - 180) $Setbadkamer=10.0;
			$Setbadkamer = number_format($Setbadkamer,1);
			if($Rbadkamer != $Setbadkamer) {Udevice($RIbadkamer,0,$Setbadkamer,'Rbadkamer');$Rbadkamer=$Setbadkamer;cset('tempbadkamer', serialize(array('1' => $Setbadkamer, 't' => $time)));}
		}
		$difbadkamer = number_format($Tbadkamer - $Rbadkamer,1);
		if(in_array(date('N',$time), array(1,2,3,4,5)) && in_array(date('G',$time), array(4,5,6)) && $Setbadkamer < 21) $Setbadkamer = 21.0;
		//living
		$Setliving=14.0;
		$setpointliving = cget('setpointliving');
		if($setpointliving!=0 && $RTliving < $drieuur) {cset('setpointliving',0);$setpointliving=0;}
		if($setpointliving!=2) {
			if($Tbuiten<20 && $Sheating=='On' && $Sraamliving=='Closed') {
				$Setliving=15.0;
					 if($time>= strtotime('3:00') && $time <  $zevenochtend) $Sslapen=='Off'?$Setliving=20.5:$Setliving=17.0;
				else if($time>= $zevenochtend && $time < strtotime('22:30')) $Sslapen=='Off'?$Setliving=20.5:$Setliving=19.0;
			}
			$Setliving = number_format($Setliving,1);
			if($Rliving != $Setliving) {Udevice($RIliving,0,$Setliving,'Rliving');$Rliving=$Setliving;cset('templiving', serialize(array('1' => $Setliving, 't' => $time)));}
		}
		$difliving = number_format($Tliving - $Rliving,1);
		$kamers=array('living','badkamer','alex','tobi','kamer');
		$bigdif=100;$rooms = 0;
		foreach($kamers as $kamer) {
			${'koud'.$kamer} = false;
			if(${'dif'.$kamer}<$bigdif) {
				$bigdif = ${'dif'.$kamer};
			}
			if(${'dif'.$kamer} <= $bigdif) $rooms = $rooms + 1;
			if(${'dif'.$kamer} < 0) ${'color'.$kamer} = '#1199FF';
			else if(${'dif'.$kamer} > 0) ${'color'.$kamer} = '#FF5511';
			else ${'color'.$kamer} = '#AAAAAA';
			${'Set'.$kamer} = number_format(${'Set'.$kamer},1);
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
			echo '
			<div id="S'.$kamer.'" class="dimmer" style="display:none;">
				<form method="POST" oninput="level.value = Actie.valueAsNumber">
					<input type="hidden" name="Setpoint" value="'.${'RI'.$kamer}.'" >
					<h2>'.ucwords($kamer).'<br/><big>
					<bold>'.number_format(${'T'.$kamer},1,",","").'°C</bold></big></h2>
					<input type="hidden" name="Naam" value="'.$kamer.'">
					<div style="position:absolute;top:200px;left:10px;z-index:1000;">';
			$temps=array(5,8,10,12,14,15,16,17,18,19,20,21,22,23,24);
			if(!in_array(${'R'.$kamer}, $temps)) $temps[] = ${'R'.$kamer};
			asort($temps);
			$temps = array_slice($temps, 0, 15);
			foreach($temps as $temp) {
				if(${'R'.$kamer}==$temp) echo '
					<input type="submit" name="Actie" value="'.$temp.'"/ style="text-align:center;background-color:#5fed5f;background:linear-gradient(to bottom, #5fed5f, #017a01);">';
				else echo '
					<input type="submit" style="text-align:center;" name="Actie" value="'.$temp.'"/>';
			}
			echo '
				</div>
				<div style="position:absolute;top:250px;left:50px;z-index:-1;">';
			echo ${'R'.$kamer}>${'T'.$kamer}?'
					<img src="images/flame.png" height="400px" width="auto">':'
					<img src="images/flamegrey.png" height="400px" width="auto">';
			echo '
				</div>
			</form>
			<form action="temp.php" method="POST">
				<div style="position:absolute;top:650px;left:150px;z-index:10;">
					<input type="hidden" name="sensor" value="'.${'TI'.$kamer}.'"/>
					<input type="hidden" name="naam" value="'.$kamer.'"/>
					<input type="submit" style="width:200px" value="Graph"/>
				</div>
			</form>
			<div style="position:absolute;top:5px;right:5px;z-index:1000;">
				<a href=""><img src="images/close.png" width="72px" height="72px"/></a>
				</div>
			</div>';
			echo '
			<tr style="color:'.${'color'.$kamer}.'" onclick="toggle_visibility(\'S'.$kamer.'\');" >
				<td align="right" line-height="4"><b>'.$kamer.'</b></td>
				<td align="center">'.${'R'.$kamer}.'</td>
				<td align="center">'.number_format(${'T'.$kamer},1).'</td>
				<td align="center">'.${'dif'.$kamer}.'</td>';
			if(${'dif'.$kamer} <= number_format(($bigdif+ 0.2),1) && ${'dif'.$kamer}< 1 ) {
				${'Set'.$kamer} = setradiator($kamer, true);
				echo '<td>&nbsp;&nbsp;'. ${'Set'.$kamer}  .' +</td></tr>';
			} 
			else {
				${'Set'.$kamer} = setradiator($kamer);
				echo '<td>&nbsp;&nbsp;'.${'Set'.$kamer}.'</td></tr>';
			}
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
				<td>'.strftime("%k:%M:%S",$STbrander).'</td>
			</tr>
		</table>
	</body>
</html>';
		if(number_format($RkamerZ,1)!==number_format($Setkamer,1)) {Udevice($RIkamerZ,0,number_format($Setkamer,1),'RkamerZ');cset('tempkamer', serialize(array('1' => $Setkamer, 't' => $time)));}
		if(number_format($RtobiZ,1)!==number_format($Settobi,1)) {Udevice($RItobiZ,0,number_format($Settobi,1),'RtobiZ');cset('temptobi', serialize(array('1' => $Settobi, 't' => $time)));}
		if(number_format($RalexZ,1)!==number_format($Setalex,1)) {Udevice($RIalexZ,0,number_format($Setalex,1),'RalexZ');cset('tempalex', serialize(array('1' => $Setalex, 't' => $time)));}
		if(number_format($RbadkamerZ,1)!==number_format($Setbadkamer,1)) {Udevice($RIbadkamerZ,0,number_format($Setbadkamer,1),'RbadkamerZ');cset('tempbadkamer', serialize(array('1' => $Setbadkamer, 't' => $time)));}
		if(number_format($RlivingZ,1)!==number_format($Setliving,1)) {Udevice($RIlivingZ,0,number_format($Setliving,1),'RlivingZ');cset('temptobi', serialize(array('1' => $Setliving, 't' => $time)));}
		if(number_format($RlivingZZ,1)!==number_format($RlivingZ,1)) {Udevice($RIlivingZZ,0,number_format($Setliving,1),'RlivingZZ');}
		if(number_format($RlivingZE,1)!==number_format($RlivingZ,1)) {Udevice($RIlivingZE,0,number_format($Setliving,1),'RlivingZE');}
		if($RTtobi<$twaalfuur) Udevice($RItobi,0,$Rtobi,'Updatetobi');
		//brander
		if     ($bigdif <= -0.2 && $Sbrander == "Off" && $STbrander < $time-60 ) {Schakel($SIbrander,'On', 'brander');logwrite('1 Brander On OR -0.2, was '.$Sbrander.' for '.($time - $STbrander).' seconds');}
		else if($bigdif <= -0.1 && $Sbrander == "Off" && $STbrander < $time-290) {Schakel($SIbrander,'On', 'brander');logwrite('2 Brander On OR -0.1, was '.$Sbrander.' for '.($time - $STbrander).' seconds');}
		else if($bigdif <= 0    && $Sbrander == "Off" && $STbrander < $time-550) {Schakel($SIbrander,'On', 'brander');logwrite('3 Brander On OR 0, was '.$Sbrander.' for '.($time - $STbrander).' seconds');}
		else if ($bigdif >= 0.1 && $Sbrander == "On"  && $STbrander < $time-60 ) {Schakel($SIbrander,'Off','brander');logwrite('4 Brander Off AND +0.1, was '.$Sbrander.' for '.($time - $STbrander).' seconds');}
		else if ($bigdif >= 0   && $Sbrander == "On"  && $STbrander < $time-(90 * $rooms * 0.65 )) {Schakel($SIbrander,'Off','brander');logwrite('5 Brander Off AND 0, was '.$Sbrander.' for '.($time - $STbrander).' seconds');}
		else if ($bigdif >= -0.1 && $Sbrander == "On"  && $STbrander < $time-(180* $rooms * 0.65 )) {Schakel($SIbrander,'Off','brander');logwrite('6 Brander Off AND -0.1, was '.$Sbrander.' for '.($time - $STbrander).' seconds');}
		else if ($bigdif >= -0.2 && $Sbrander == "On"  && $STbrander < $time-(360* $rooms * 0.65 )) {Schakel($SIbrander,'Off','brander');logwrite('7 Brander Off AND -0.2, was '.$Sbrander.' for '.($time - $STbrander).' seconds');}
		if($user=='Guy') {
			echo '<table>';
			$datas = json_decode(file_get_contents($domoticzurl.'json.htm?type=lightlog&idx=417',true,$ctx),true);
			$status = '';$tijdprev = $time;$totalon = 0;
			if(!empty($datas['result'])) {
				foreach($datas['result'] as $data) {
					if($status!=$data['Status']) {
						$status=$data['Status'];
						$level=$data['Level'];
						$tijd = strtotime($data['Date']);
						if($tijd<$time-86400) break;
						$period = ($tijdprev - $tijd);
						if($status=='On') $totalon = $totalon + $period;
						$tijdprev = $tijd;
						echo '<tr align="right"><td>'.$data['Date'].'</td><td>&nbsp;'.$status.'&nbsp;</td><td>&nbsp;'.convertToHoursMins($period/60).'</td></tr>';
					}
				}
			}
			echo '</table>
				<div style="position:absolute;top:4.55em;left:13.6em;width:265px;">On = '.convertToHoursMins($totalon/60).'</div>';
		}
		if($STbrander < $tweeuur && $Sbrander=='On') {
			if(cget('timetelegrambrander'<$eenuur)) {
				telegram('Brander langer dan 2 uur aan');
				cset('timetelegrambrander',$time);
			}
		}
		
		
		//if($STbrander<$eenuur) Schakel($SIbrander, $Sbrander,'Brander update');
}
