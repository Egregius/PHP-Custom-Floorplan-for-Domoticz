<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><title>Floorplan</title>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
<meta name="HandheldFriendly" content="true" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,minimal-ui" />
<link rel="icon" type="image/png" href="images/domoticzphp48.png"/>
<link rel="shortcut icon" href="images/domoticzphp48.png"/>
<link rel="apple-touch-startup-image" href="images/domoticzphp450.png"/>
<link rel="apple-touch-icon" href="images/domoticzphp48.png"/>
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="images/domoticzphp48.png">
<meta name="msapplication-config" content="browserconfig.xml">
<meta name="mobile-web-app-capable" content="yes"><link rel="manifest" href="manifest.json">
<meta name="theme-color" content="#ffffff">
<link href="floorplan.css?v=3" rel="stylesheet" type="text/css" />
</head><body>
<?php 
$start=microtime(true);
$time=$_SERVER['REQUEST_TIME'];$offline=$time-300;$eendag=$time-82800;include "secure/functions.php";
if($authenticated){
if(isset($_POST['Schakel'])){
	switch ($_POST['Schakel']) {
		case 166: Udevice(415,0,'Off');break;
		case 202: Udevice(262,0,'Off');Udevice(290,0,'Off');break;
		case 212: Udevice(315,0,'Off');break;
		case 272: Udevice(290,0,'Off');Udevice(262,0,'Off');break;
	}
	if(Schakel($_POST['Schakel'],$_POST['Actie'],$_POST['Naam'])=='ERROR') echo '<div id="message" class="balloon">'.$_POST['Naam'].' '.$_POST['Actie'].'<br/>ERROR</div>';
}
else if(isset($_POST['Udevice'])){if(Udevice($_POST['Udevice'],$_POST['Naam'])=='ERROR') echo '<div id="message" class="balloon">'.$_POST['Naam'].' '.$_POST['Actie'].'<br/>ERROR</div>'; }
else if(isset($_POST['dimmer'])){
	if(isset($_POST['dimlevelon_x'])) {if(Dim($_POST['dimmer'],100,$_POST['Naam'])=='ERROR') echo '<div id="message" class="balloon">Dimmer '.$_POST['Naam'].' level '.$_POST['dimlevel'].'<br/>ERROR</div>';cset('timedimmer'.$_POST['Naam'],$time);cset('dimmer'.$_POST['Naam'],0);}
	else if(isset($_POST['dimleveloff_x'])) {if(Dim($_POST['dimmer'],0,$_POST['Naam'])=='ERROR') echo '<div id="message" class="balloon">Dimmer '.$_POST['Naam'].' level '.$_POST['dimlevel'].'<br/>ERROR</div>';cset('timedimmer'.$_POST['Naam'],$time);cset('dimmer'.$_POST['Naam'],0);}
	else if(isset($_POST['dimsleep_x'])) {cset('dimmer'.$_POST['Naam'],1);}
	else if(isset($_POST['dimwake_x'])) {if(Dim($_POST['dimmer'],$_POST['dimwakelevel']+1,$_POST['Naam'])=='ERROR') echo '<div id="message" class="balloon">Dimmer '.$_POST['Naam'].' level '.$_POST['dimlevel'].'<br/>ERROR</div>';cset('dimmer'.$_POST['Naam'],2);}
	else {if(Dim($_POST['dimmer'],$_POST['dimlevel'],$_POST['Naam'])=='ERROR') echo '<div id="message" class="balloon">Dimmer '.$_POST['Naam'].' level '.$_POST['dimlevel'].'<br/>ERROR</div>';cset('timedimmer'.$_POST['Naam'],$time);cset('dimmer'.$_POST['Naam'],0);}
	}
else if(isset($_POST['Naam'])){
	switch($_POST['Naam']) {
		case 'radioluisteren': $idx = 504;break;
		case 'tvkijken': $idx = 505;break;
		case 'kodikijken': $idx = 506;break;
		case 'eten': $idx = 507;break;
	}
	if(Schakel($idx,'On',$_POST['Naam'])=='ERROR') echo '<div id="message" class="balloon">Scene '.$_POST['Naam'].' activeren'.'<br/>ERROR</div>';if($_POST['Naam']=='eten') cset('timedimmerEettafel',$time);}
else if(isset($_POST['healnetwork'])) {file_get_contents($domoticzurl.'json.htm?type=command&param=zwavenetworkheal&idx='.$zwaveidx);}
if(isset($_POST['imacpicam1'])) file_get_contents('http://192.168.0.9/cmd_pipe.php?cmd=sy%20wakeimac.sh');
if(isset($_POST['imacpicam2'])) file_get_contents('http://192.168.0.6/cmd_pipe.php?cmd=sy%20wakeimac.sh');
if(isset($_POST['denon'])){$denon_address='http://192.168.0.15';
	$denonmain=simplexml_load_string(file_get_contents($denon_address.'/goform/formMainZone_MainZoneXml.xml?_='.$time,false,$ctx));
	$denonmain=json_encode($denonmain);$denonmain=json_decode($denonmain,TRUE);usleep(10000);
	if($denonmain){
		$denonmain['MasterVolume']['value']=='--'?$setvalue=-80:$setvalue=$denonmain['MasterVolume']['value'];
		$_POST['denon']=='up'?$setvalue=$setvalue+3:$setvalue=$setvalue-3;
		if($setvalue>-10) $setvalue=-10;if($setvalue<-80) $setvalue=-80;
		file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/'.$setvalue.'.0');}}
		
$domoticz=json_decode(file_get_contents($domoticzurl.'json.htm?type=devices&used=true&plan=2',true,$ctx),true);$domotime=microtime(true)-$start; //&plan=2
if($domoticz){
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
			else ${'S'.$name}=$dom['Data'];${'SI'.$name}=$dom['idx'];${'ST'.$name}=strtotime($dom['LastUpdate']);/*${'SB'.$name}=$dom['BatteryLevel'];*/}
	}
	unset($domoticz,$dom);
if(isset($_POST['Schakel'])){
	if($_POST['Schakel']==6||$_POST['Schakel']==48){
		if($Sraamliving=='Open') echo '<script language="javascript">alert("WARNING:Raam living open!")</script>';
		if($Sachterdeur=='Open') echo '<script language="javascript">alert("WARNING:Achterdeur open!")</script>';
		if($Spoort=='Open') echo '<script language="javascript">alert("WARNING:Poort open!")</script>';
	}
	
}
echo '<div style="position:absolute;top:5px;left:260px;width:150px;text-align:right;"><a href=\'javascript:navigator_Go("floorplan.php");\' style="padding:35px 13px 6px 8px;font-size:33px;font-weight:500;color:#CCC;" title="refresh">'.strftime("%k:%M:%S",$time).'</a></div>
<div class="box" style="top:0px;height:240px;">
<div class="box2" style="top:175px;left:12px;"><img src="images/'.cget('weatherimg').'.png" width="60px" height="auto"/></div>
<div class="box2" style="top:220px;left:13px;">'.cget('averagerain').'</div>
<div class="box" style="top:252px;height:573px;">
<form method="POST"><input type="hidden" name="denon" value="up"><input type="image" src="images/arrowup.png" width="48px" height="48px"></form>
<br/><form method="POST"><input type="hidden" name="denon" value="down"><input type="image" src="images/arrowdown.png" width="48px" height="48px"></form>
<br/><a href=\'javascript:navigator_Go("denon.php");\'><img src="images/denon.png" width="48px" height="48px"></a><br/>
<br/><form method="POST"><input type="hidden" name="Naam" value="radioluisteren"><input type="image" src="images/Amp_On.png" width="70px" height="30px"></form>
<br/><form method="POST"><input type="hidden" name="Naam" value="tvkijken"><input type="image" src="images/TV_On.png" width="60px" height="60px"></form>
<br/><form method="POST"><input type="hidden" name="Naam" value="kodikijken"><input type="image" src="images/kodi.png" width="48px" height="48px"></form>
<br/><form method="POST"><input type="hidden" name="Naam" value="eten"><input type="image" src="images/eten.png" width="48px" height="48px"></form>
<br/><a href=\'javascript:navigator_Go("heating.php");\'><img src="images/Fire_On.png" width="48px" height="48px"></a><br/>
<br/><a href=\'javascript:navigator_Go("#");\' onclick="toggle_visibility(\'Plus\');" style="text-decoration:none"><img src="images/plus.png" width="60px" height="60px"/></a>
</div>';

Dimmer('tobi',70,435,115);
Dimmer('zithoek',70,110,105);
Dimmer('eettafel',70,110,245);
Dimmer('kamer',70,545,345);
Dimmer('alex',70,560,115);
Schakelaar('kristal','Light',52,8,88);
Schakelaar('tvled','Light',52,8,147);
Schakelaar('bureel','Light',52,8,206);
Schakelaar('inkom','Light',52,51,349);
Schakelaar('keuken','Light',52,157,390);
Schakelaar('wasbak','Light',40,145,345);
Schakelaar('kookplaat','Light',40,115,386);
Schakelaar('werkblad','Light',40,208,434);
Schakelaar('lichtbadkamer1','Light',70,420,400);
Schakelaar('lichtbadkamer2','Light',40,463,349);
Schakelaar('voordeur','Light',48,54,439);
Schakelaar('hall','Light',52,416,239);	
Schakelaar('hall_auto','Clock',40,420,299);		
Schakelaar('garage','Light',60,305,209);
Schakelaar('zolderg','Light',48,315,140);	
Schakelaar('garage_auto','Clock',40,312,299);
Schakelaar('weg','Home',70,257,417);	
Schakelaar('slapen','Sleepy',70,331,417);
Schakelaar('terras','Light',60,5,10);
//Schakelaar('brander','Fire',48,765,266);
//Schakelaar('kerstboom','Kerstboom',48,172,88);
Schakelaar('zolder','Light',70,710,260);
Schakelaar('bureeltobi','Plug',40,780,410);
Thermometer('buiten',75,20);
//Setpoint('living',50,175,195);
//Setpoint('badkamer',50,431,430);
//Setpoint('kamer',50,571,430);
//Setpoint('tobi',50,462,94);
//Setpoint('alex',50,571,94);
Thermometer('zolder',707,125);
//Radiator('livingZZ',-90,221,77);
//Radiator('badkamerZ',0,403,349);
//Radiator('tobiZ',-90,463,77);
//Radiator('alexZ',-90,583,77);
//Radiator('kamerZ',90,542,456);
Blinds('zoldertrap',50,695,190);
//if($SSirene!='Off') Schakelaar('Sirene','Alarm',96,180,258);
echo '<div id="Plus" class="dimmer" style="display:none;">';
echo '<div style="position:absolute;top:100px;left: 60px;z-index:1000;">';Schakelaar('meldingen','Alarm',48,0,5);echo '<br><br><br>Meldingen</div>';
echo '<div style="position:absolute;top:200px;left: 60px;z-index:1000;"><a href=\'javascript:navigator_Go("films/kodi.php");\'><img src="images/kodi.png" width="60px" height="60px"><br/>Kodi</a></div>';
echo '<div style="position:absolute;top:200px;left:160px;z-index:1000;"><a href=\'javascript:navigator_Go("films/films.php");\'><img src="images/kodi.png" width="60px" height="60px"><br/>Films</a></div>';
echo '<div style="position:absolute;top:200px;left:260px;z-index:1000;"><a href=\'javascript:navigator_Go("films/tobi.php");\'><img src="images/kodi.png" width="60px" height="60px"><br/>Tobi</a></div>';
echo '<div style="position:absolute;top:200px;left:360px;z-index:1000;"><a href=\'javascript:navigator_Go("films/series.php");\'><img src="images/kodi.png" width="60px" height="60px"><br/>Series</a></div>';
echo '<div style="position:absolute;top:350px;left: 60px;z-index:1000;"><form method="POST" action="picam1.php"><input type="image" src="images/Camera.png" width="60px" height="60px"></form><br/>Voordeur</div>';
echo '<div style="position:absolute;top:450px;left: 60px;z-index:1000;"><form method="POST"><input type="hidden" name="imacpicam1" value="imacpicam1"><input type="image" src="images/Camera.png" width="60px" height="60px"></form><br/>Voordeur<br/>iMac</div>';
echo '<div style="position:absolute;top:350px;left:160px;z-index:1000;"><form method="POST" action="picam2.php"><input type="image" src="images/Camera.png" width="60px" height="60px"></form><br/>Alex</div>';
//echo '<div style="position:absolute;top:450px;left: 160px;z-index:1000;"><form method="POST"><input type="hidden" name="imacpicam2" value="imacpicam2"><input type="image" src="images/Camera.png" width="60px" height="60px"></form><br/>PiCam2<br/>iMac</div>';
echo '<div style="position:absolute;top:700px;left:60px;z-index:1000;background:rgba(100,100,100,0.6);"><a href=\'javascript:navigator_Go("history.php");\'><img src="images/log.png" width="60px" height="60px"><br/>History</a></div>';
if($user=='Guy') echo '<div style="position:absolute;top:700px;left:160px;z-index:1000;background:rgba(100,100,100,0.6);"><form method="POST"><input type="hidden" name="healnetwork" value="up"><input type="image" src="images/zwave.png" width="48px" height="48px"></form><br/>Heal<br>network</div>';
echo '<div style="position:absolute;top:700px;left:360px;z-index:1000;"><form method="POST" action="logout.php"><input type="submit" name="logout" value="Logout" class="btn" style="min-width:5em"/></form></div>';
echo '<div style="position:absolute;top:5px;right:5px;z-index:1000;"><a href=""><img src="images/close.png" width="72px" height="72px"/></a></div></div>';

if($Sweg=='On'||$Sslapen=='On'){Secured(52,88,250,196);Secured(50,345,129,57);Secured(255,88,316,141);Secured(114,345,129,134);}
if($Sweg=='On'){Secured(404,212,129,65);Secured(469,214,45,66);}
if($Spirliving!='Off'||$Spirliving!='Off') Motion(52,88,250,196);
if($Spirkeuken!='Off') Motion(114,345,129,134);
if($Spirinkom!='Off') Motion(50,345,129,57);
if($Spirgarage!='Off') Motion(255,88,316,141);
if($Spirhall!='Off'){Motion(404,212,129,65);Motion(469,214,45,66);}
if($STdeurbel>$eendag) Timestamp('deurbel',-90,17,462);
if($STpirgarage>$eendag) Timestamp('pirgarage',0,256,300);
if($STpirliving>$eendag) Timestamp('pirliving',0,230,105);
if($STpirlivingR>$eendag) Timestamp('pirliving',0,230,300);
if($STpirkeuken>$eendag) Timestamp('pirliving',0,115,345);
if($STpirinkom>$eendag) Timestamp('pirinkom',0,89,398);
if($STpirhall>$eendag) Timestamp('pirhall',0,403,215);
if($STachterdeur>$eendag) Timestamp('achterdeur',-90,280,77);
if($STpoort>$eendag) Timestamp('poort',90,315,376);
if($Spoort!='Closed') echo '<div style="position:absolute;top:262px;left:404px;width:25px;height:128px;background:rgba(255,0,0,1);z-index:-10;"></div>';
if($Sachterdeur!='Closed') echo '<div style="position:absolute;top:264px;left:81px;width:30px;height:48px;background:rgba(255,0,0,1);z-index:-10;"></div>';
if($Sraamliving!='Closed') echo '<div style="position:absolute;top:46px;left:81px;width:8px;height:165px;background:rgba(255,0,0,1);z-index:-10;"></div>';
if($Sraamtobi!='Closed') echo '<div style="position:absolute;top:449px;left:81px;width:7px;height:43px;background:rgba(255,0,0,1);z-index:-10;"></div>';
if($Sraamalex!='Closed') echo '<div style="position:absolute;top:569px;left:81px;width:7px;height:43px;background:rgba(255,0,0,1);z-index:-10;"></div>';
if($Sraamkamer!='Closed') echo '<div style="position:absolute;top:586px;left:481px;width:7px;height:43px;background:rgba(255,0,0,1);z-index:-10;"></div>';
if($Sdeurbadkamer!='Closed') echo '<div style="position:absolute;top:421px;left:341px;width:7px;height:46px;background:rgba(255,0,0,1);z-index:-10;"></div>';
$execution= microtime(true)-$start;$phptime=$execution-$domotime;echo '<div style="position:absolute;top:652px;left:90px;width:400px;text-align:left;font-size:12px" >D'.round($domotime,3).' | P'.round($phptime,3).' | T'.round($execution,3).'<br/>';
//print_r($_POST);
echo '</div>';
} else echo '<div style="background:#ddd;"><a href="">Geen verbinding met Domoticz</a></div>';	
} else {
	header("Location: index.php");
	die("Redirecting to: index.php"); 
}
?>
<script type="text/javascript">
function toggle_visibility(id){var e=document.getElementById(id);if(e.style.display=='inherit') e.style.display='none';else e.style.display='inherit';}
setTimeout('window.location.href=window.location.href;',4950);
function navigator_Go(url) {window.location.assign(url);}
</script>
</body></html>
