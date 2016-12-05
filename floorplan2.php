<?php require "secure/settings.php";require "secure/functions2.php";if($home){
echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Floorplan2</title>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/><meta name="HandheldFriendly" content="true"/><meta name="apple-mobile-web-app-capable" content="yes"><meta name="apple-mobile-web-app-status-bar-style" content="black"><meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,minimal-ui"/><link rel="icon" type="image/png" href="images/domoticzphp48.png"/><link rel="shortcut icon" href="images/domoticzphp48.png"/><link rel="apple-touch-startup-image" href="images/domoticzphp450.png"/><link rel="apple-touch-icon" href="images/domoticzphp48.png"/><meta name="msapplication-TileColor" content="#ffffff"><meta name="msapplication-TileImage" content="images/domoticzphp48.png"><meta name="msapplication-config" content="browserconfig.xml"><meta name="mobile-web-app-capable" content="yes"><link rel="manifest" href="manifest.json"><meta name="theme-color" content="#ffffff">
<link rel="stylesheet" type="text/css" href="/styles/floorplan2.php">
</head><body>';
if(isset($_POST['Schakel'])){
	if(Schakel($_POST['Schakel'],$_POST['Actie'],$_POST['Naam'])=='ERROR') echo '<div id="message" class="balloon">'.$_POST['Naam'].' '.$_POST['Actie'].'<br/>ERROR</div>';
	if(isset($_POST['halfuurwater'])) cset('water',1800);
	}
else if(isset($_POST['Udevice'])){if(Udevice($_POST['Udevice'],$_POST['Naam'])=='ERROR') echo '<div id="message" class="balloon">'.$_POST['Naam'].' '.$_POST['Actie'].'<br/>ERROR</div>'; }
$domoticz=json_decode(file_get_contents('http://plan:plan@127.0.0.1:8084/json.htm?type=devices&used=true&plan=5',true,$ctx),true);$domotime=microtime(true)-$start; //&plan=2
if($domoticz){foreach($domoticz['result'] as $dom) {
		(isset($dom['Type'])?$Type=$dom['Type']:$Type='None');
		(isset($dom['SwitchType'])?$SwitchType=$dom['SwitchType']:$SwitchType='None');
		(isset($dom['SubType'])?$SubType=$dom['SubType']:$SubType='None');
		$name=$dom['Name'];
		if($Type=='Temp'){${'T'.$name}=$dom['Temp'];${'TI'.$name}=$dom['idx'];${'TT'.$name}=strtotime($dom['LastUpdate']);}
		else if($Type=='Temp + Humidity + Baro'){${'T'.$name}=$dom['Temp'];${'TI'.$name}=$dom['idx'];${'TT'.$name}=strtotime($dom['LastUpdate']);}
		else if($SwitchType=='Dimmer'){${'DI'.$name}=$dom['idx'];$dom['Status']=='Off'?${'D'.$name}='Off':${'D'.$name}='On';$dom['Status']=='Off'?${'Dlevel'.$name}=0:${'Dlevel'.$name}=$dom['Level'];${'DT'.$name}=strtotime($dom['LastUpdate']);}
		else if($Type=='Usage'&&$dom['SubType']=='Electric') ${'P'.$name}=substr($dom['Data'],0,-5);
		else if($Type=='Radiator 1'||$Type=='Thermostat') {${'RI'.$name}=$dom['idx'];${'R'.$name}=$dom['Data'];${'RT'.$name}=strtotime($dom['LastUpdate']);}
		else {
			if(substr($dom['Data'],0,2)=='On') ${'S'.$name}='On';
			else if(substr($dom['Data'],0,3)=='Off') ${'S'.$name}='Off';
			else if(substr($dom['Data'],0,4)=='Open') ${'S'.$name}='Open';
			else ${'S'.$name}=$dom['Data'];${'SI'.$name}=$dom['idx'];${'ST'.$name}=strtotime($dom['LastUpdate']);/*${'SB'.$name}=$dom['BatteryLevel'];*/
			if($name=='achterdeur') {if($Sachterdeur=='Open') $Sachterdeur='Closed';else $Sachterdeur='Open';}
		}

	}
	unset($domoticz,$dom);
echo '<div class="fix center clock"><a href=\'javascript:navigator_Go("floorplan2.php");\'><h2>'.strftime("%k:%M:%S",$time).'</h2></a></div>
<div class="fix box box1" style="top:120px;left:10px;height:90px;width:470px">';
Schakelaar('tv','TV');
Schakelaar('kodi','Kodi');
echo '<div class="fix center z1 kodicontrol"><a href=\'javascript:navigator_Go("kodi.php");\'><img src="/images/kodi.png" class="i48"/><br/>Kodi<br/>Control</a></div>
<div class="fix center z1 films"><a href=\'javascript:navigator_Go("films/films.php");\'><img src="/images/kodi.png" class="i48"/><br/>Films</a></div>
<div class="fix center z1 filmstobi"><a href=\'javascript:navigator_Go("films/tobi.php");\'><img src="/images/kodi.png" class="i48"/><br/>Tobi</a></div>
<div class="fix center z1 series"><a href=\'javascript:navigator_Go("films/series.php");\'><img src="/images/kodi.png" class="i48"/><br/>Series</a></div>
</div>
<div class="fix box box2" style="top:230px;left:10px;height:160px;width:470px">
	<div class="fix center z1 picam1">
		<a href=\'javascript:navigator_Go("picam1/index.php");\'>
			<img src="/images/Camera.png" class="i48"><br/>
			Voordeur<br/>Oprit
		</a>
	</div>
	<div class="fix center z1 picam2">
		<a href=\'javascript:navigator_Go("picam2/index.php");\'>
			<img src="/images/Camera.png" class="i48"><br/>
			Alex
		</a>
	</div>';
Schakelaar('meldingen','Alarm');
Schakelaar('sony','Ampgrey');
if($Ssirene!='Off')Schakelaar('sirene','Alarm');
$items=array('SDliving','SDkamer','SDbadkamer','SDtobi','SDalex','SDzolder');foreach($items as $item)if(${'S'.$item}!='Off')Smokedetector($item);
echo '</div>
<div class="fix box box3" style="top:410px;left:10px;height:250px;width:470px">';
Schakelaar('regenpomp','Light');
if($STregenpomp>$eendag)echo '<div class="fix" style="top:83px;left:32px;">'.strftime("%H:%M",$STregenpomp).'</div>';
Schakelaar('zwembadfilter','Light');
if($STzwembadfilter>$eendag)echo '<div class="fix" style="top:83px;left:159px;">'.strftime("%H:%M",$STzwembadfilter).'</div>';
Schakelaar('zwembadwarmte','Light');
if($STzwembadwarmte>$eendag)echo '<div class="fix" style="top:83px;left:285px;">'.strftime("%H:%M",$STzwembadwarmte).'</div>';
Schakelaar('water','Light');
if($STwater>$eendag)echo '<div class="fix" style="top:83px;left:410px;">'.strftime("%H:%M",$STwater).'</div>';
//Sunscreen('luifel');
echo '</div>
<div class="fix z1" style="top:5px;left:5px;"><a href=\'javascript:navigator_Go("floorplan.php");\'><img src="/images/close.png" width="72px" height="72px"/></a></div></div>
<div class="fix z1" style="top:550px;left:20px;"><a href=\'javascript:navigator_Go("budget/index.php");\' class="btn"><img src="/images/euro.png" width="48px" height="48px"/><br/>Budget</a></div></div>
<div class="fix z1" style="top:550px;left:110px;"><a href=\'javascript:navigator_Go("verbruik/index.php");\' class="btn"><img src="/images/verbruik.png" width="48px" height="48px"/><br/>Verbruik</a></div></div>
</div>
<div class="clear"></div>
<div class="fix box box4" style="top:680px;left:10px;height:150px;width:470px">&nbsp;'.$udevice.'<br/>&nbsp'.$_SERVER['HTTP_USER_AGENT'].'
<div class="fix z1 logout"><form method="POST"><input type="submit" name="logout" value="Logout" class="btn" style="min-width:4em;padding:0px;margin:0px;width:50px;height:39px;"/></form><br/><br/></div></div>';
} else echo '<div style="background:#ddd;"><a href="">Geen verbinding met Domoticz</a></div>';
}?>
<script type="text/javascript">
function toggle_visibility(id){var e=document.getElementById(id);if(e.style.display=='inherit') e.style.display='none';else e.style.display='inherit';}
setTimeout('window.location.href=window.location.href;',4950);
function navigator_Go(url) {window.location.assign(url);}
</script>
</body></html>
