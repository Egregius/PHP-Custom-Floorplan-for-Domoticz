<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><title>Floorplan</title>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
<meta name="HandheldFriendly" content="true" /><meta name="apple-mobile-web-app-capable" content="yes"><meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,minimal-ui" />
<link rel="icon" type="image/png" href="images/domoticzphp48.png">
<link rel="shortcut icon" href="images/domoticzphp48.png" /><link rel="apple-touch-startup-image" href="images/domoticzphp450.png">
<link rel="apple-touch-icon" href="images/domoticzphp48.png" />
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="images/domoticzphp48.png">
<meta name="msapplication-config" content="browserconfig.xml">
<meta name="mobile-web-app-capable" content="yes"><link rel="manifest" href="manifest.json">
<meta name="theme-color" content="#ffffff">
<link href="style.css?v=3" rel="stylesheet" type="text/css" />
<script type="text/javascript">function navigator_Go(url) {window.location.assign(url);}</script>
</head><body>
<?php 
include "secure/functions.php";
if($authenticated){
	echo '<div class="navbar"><a href=\'javascript:navigator_Go("floorplan.php");\' class="btn nav">Floorplan</a>';
	$domoticz=json_decode(file_get_contents($domoticzurl.'json.htm?type=devices&used=true&plan=2',true,$ctx),true);//$domotime=microtime(true)-$start; //&plan=2
	if($domoticz){
		$idx = 252;
		if(isset($_POST['switch'])) $idx = $_POST['switch'];
		echo '<form method="POST"><select name="switch" onchange="this.form.submit()" class="btn nav">';
		foreach($domoticz['result'] as $dom) {
			(isset($dom['Type'])?$Type=$dom['Type']:$Type='None');
			(isset($dom['SwitchType'])?$SwitchType=$dom['SwitchType']:$SwitchType='None');
			(isset($dom['SubType'])?$SubType=$dom['SubType']:$SubType='None');
			$name=$dom['Name'];
			if($Type=='Temp + Humidity'||$Type=='Temp'){${'T'.$name}=$dom['Temp'];${'TI'.$name}=$dom['idx'];${'TT'.$name}=strtotime($dom['LastUpdate']);}
			else if($SwitchType=='Dimmer'){${'DI'.$name}=$dom['idx'];$dom['Status']=='Off'?${'D'.$name}='Off':${'D'.$name}='On';$dom['Status']=='Off'?${'Dlevel'.$name}=0:${'Dlevel'.$name}=$dom['Level'];${'DT'.$name}=strtotime($dom['LastUpdate']);}
			else if($Type=='Usage'&&$dom['SubType']=='Electric') ${'P'.$name}=substr($dom['Data'],0,-5);
			else if($Type=='Radiator 1'||$Type=='Thermostat') {${'RI'.$name}=$dom['idx'];${'R'.$name}=$dom['Data'];${'RT'.$name}=strtotime($dom['LastUpdate']);}
			else {
				if(substr($dom['Data'],0,2)=='On') ${'S'.$name}='On';
				else if(substr($dom['Data'],0,3)=='Off') ${'S'.$name}='Off';
				else if(substr($dom['Data'],0,4)=='Open') ${'S'.$name}='Open';
				else ${'S'.$name}=$dom['Data'];${'SI'.$name}=$dom['idx'];${'ST'.$name}=strtotime($dom['LastUpdate']);/*${'SB'.$name}=$dom['BatteryLevel'];*/
				echo $idx==$dom['idx']?'<option value="'.$dom['idx'].'" selected>'.$name.'</option>':'<option value="'.$dom['idx'].'">'.$name.'</option>';
			}
		}
		unset($domoticz,$dom);
		echo '</select></form></div><div class="clear"></div>';
		//echo '<pre>';print_r($domoticz['result']);echo '</pre>';

			$datas = json_decode(file_get_contents($domoticzurl.'json.htm?type=lightlog&idx='.$idx,true,$ctx),true);
			echo '<table><tr align="right"><td width="200px">Date</td><td width="100px">Status</td><td width="100px">Level</td><td width="100px">Period</td></tr>';
			$status='';$level=999;
			if(!empty($datas['result'])) {
					foreach($datas['result'] as $data) {
					if($status!=$data['Status']||$level!=$data['Level']) {
						$status=$data['Status'];
						$level=$data['Level'];
						$tijd = strtotime($data['Date']);
						$period = ($time - $tijd);
						$time = $tijd;
						echo '<tr align="right"><td>'.$data['Date'].'</td><td>'.$status.'</td><td>'.$level.'</td><td>'.convertToHoursMins($period/60).'</td></tr>';
					}
				}
			}
			echo '</table>';
		
			
	}
}
function convertToHoursMins($time, $format = '%01d u %02d min') {
	if ($time < 1) {
		return;
	}
	$hours = floor($time / 60);
	$minutes = ($time % 60);
	return sprintf($format, $hours, $minutes);
}
