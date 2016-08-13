<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head><title>Floorplan</title><meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/><meta name="HandheldFriendly" content="true"/><meta name="apple-mobile-web-app-capable" content="yes"><meta name="apple-mobile-web-app-status-bar-style" content="black"><meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,minimal-ui"/><link rel="icon" type="image/png" href="images/domoticzphp48.png"/><link rel="shortcut icon" href="images/domoticzphp48.png"/><link rel="apple-touch-startup-image" href="images/domoticzphp450.png"/><link rel="apple-touch-icon" href="images/domoticzphp48.png"/><meta name="msapplication-TileColor" content="#ffffff"><meta name="msapplication-TileImage" content="images/domoticzphp48.png"><meta name="msapplication-config" content="browserconfig.xml"><meta name="mobile-web-app-capable" content="yes"><link rel="manifest" href="manifest.json"><meta name="theme-color" content="#ffffff"><link href="floorplan.css" rel="stylesheet" type="text/css" /></head><body>
<?php require "secure/settings.php";require "secure/functions.php";if($authenticated){
if(isset($_POST['Schakel'])){
	switch ($_POST['Schakel']) {
		case 283: Udevice(152,0,'Off');break;
		case 88: Udevice(249,0,'Off');Udevice(247,0,'Off');break;
		case 123: Udevice(247,0,'Off');Udevice(249,0,'Off');break;
		case 164: Udevice(154,0,'Off');break;
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
		case 'radioluisteren': $idx = 72;break;
		case 'tvkijken': $idx = 73;break;
		case 'kodikijken': $idx = 74;break;
		case 'eten': $idx = 75;break;
	}
	if(Schakel($idx,'On',$_POST['Naam'])=='ERROR') echo '<div id="message" class="balloon">Scene '.$_POST['Naam'].' activeren'.'<br/>ERROR</div>';
	if($_POST['Naam']=='eten') cset('timedimmerEettafel',$time);
}
$domoticz=json_decode(file_get_contents($domoticzurl.'json.htm?type=devices&used=true&plan=2',true,$ctx),true);//$domotime=microtime(true)-$start; //&plan=2
if($domoticz){
	foreach($domoticz['result'] as $dom) {
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
if(isset($_POST['Schakel'])){
	if($_POST['Schakel']==6||$_POST['Schakel']==48){
		if($Sraamliving=='Open') echo '<script language="javascript">alert("WARNING:Raam living open!")</script>';
		if($Sachterdeur=='Open') echo '<script language="javascript">alert("WARNING:Achterdeur open!")</script>';
		if($Spoort=='Open') echo '<script language="javascript">alert("WARNING:Poort open!")</script>';
	}
}
echo '<div style="position:absolute;top:5px;left:260px;width:150px;text-align:right;"><a href=\'javascript:navigator_Go("floorplan.php");\' style="padding:35px 13px 6px 8px;font-size:33px;font-weight:500;color:#CCC;" title="refresh">'.strftime("%k:%M:%S",$time).'</a></div>
<div class="box" style="top:0px;height:248px;width:80px;z-index:100;">';
$weer=unserialize(cget('weer'));
	//$temp=cget('buiten_temp');
	$temp=$weer['buiten_temp'];
	$hoogte=$temp*3;
	if($hoogte>88) $hoogte=88;else if ($hoogte<20) $hoogte=20;
	$top=88-$hoogte;if($top<0) $top=0;
	$top=$top+5;
	switch ($temp) {
		case $temp>=22:$tcolor='F00';$dcolor='55F';break;
		case $temp>=20:$tcolor='D12';$dcolor='44F';break;
		case $temp>=18:$tcolor='B24';$dcolor='33F';break;
		case $temp>=15:$tcolor='93B';$dcolor='22F';break;
		case $temp>=10:$tcolor='64D';$dcolor='11F';break;
		default:$tcolor='55F';$dcolor='00F';}
		echo '<form action="temp.php" method="POST"><div style="position:absolute;top:100px;left:20px;cursor:pointer;z-index:10;" onclick="this.form.submit()">
			<input type="hidden" name="sensor" value="buiten_temp">
			<input type="hidden" name="naam" value="buiten_temp">
			<div class="tmpbg" style="top:'.number_format($top,0).'px;left:8px;width:26px;height:'.number_format($hoogte,0).'px;background:linear-gradient(to bottom, #'.$tcolor.', #'.$dcolor.');"></div>
			<input type="image" src="images/temp.png" height="100px" width="auto"/>
		</form>';
		echo '<div class="grey" style="top:73px;left:5px;width:32px;align:center;">';
		echo $temp.'</div></form></div>';

echo '<div style="position:absolute;top:194px;left:3px;z-index:100;"><center>
	'.number_format((filter_var($Szon, FILTER_SANITIZE_NUMBER_INT)/100),0,'.','.').' Watt<br/>
	Regen:'.number_format($weer['buien'],0).'%<br/>
	Wind:'.number_format($weer['wind'],0).'m/s<br/>
	Wolken:'.number_format($weer['wolken'],0).' %<br/>
	</center>
</div>
<div class="box" style="top:260px;height:565px;">
<br/><a href=\'javascript:navigator_Go("denon.php");\'><img src="images/denon.png" width="48px" height="48px"></a><br/>
<br/><br/><form method="POST"><input type="hidden" name="Naam" value="radioluisteren"><input type="image" src="images/Amp_';echo $Sdenon=='On'?'On':'Off';echo '.png" width="70px" height="30px"></form>
<br/><br/><form method="POST"><input type="hidden" name="Naam" value="tvkijken"><input type="image" src="images/TV_';echo $Stv=='On'?'On':'Off';echo '.png" width="60px" height="60px"></form>
<br/><br/><form method="POST"><input type="hidden" name="Naam" value="kodikijken"><input type="image" src="images/Kodi_';echo $Skodi=='On'?'On':'Off';echo '.png" width="48px" height="48px"></form>
<br/><br/><form method="POST"><input type="hidden" name="Naam" value="eten"><input type="image" src="images/eten.png" width="48px" height="48px"></form>
<br/><br/><a href=\'javascript:navigator_Go("heating.php");\'><img src="images/Fire_';echo $Sbrander=='On'?'On':'Off';echo '.png" width="48px" height="48px"></a><br/>
<br/><br/><br/><a href=\'javascript:navigator_Go("floorplan2.php");\' onclick="toggle_visibility(\'Plus\');" style="text-decoration:none"><img src="images/plus.png" width="60px" height="60px"/></a>
</div>';
Dimmer('tobi',70,435,135);
Dimmer('zithoek',70,110,105);
Dimmer('eettafel',70,110,245);
Dimmer('kamer',70,545,345);
Dimmer('alex',70,560,135);
Schakelaar('kristal','Light',52,8,88);
Schakelaar('tvled','Light',52,8,147);
Schakelaar('bureel','Light',52,8,206);
Schakelaar('inkom','Light',52,51,349);
Schakelaar('keuken','Light',52,157,390);
Schakelaar('wasbak','Light',40,145,345);
Schakelaar('kookplaat','Light',40,115,386);
Schakelaar('werkblad','Light',40,208,434);
Schakelaar('lichtbadkamer1','Light',70,420,390);
Schakelaar('lichtbadkamer2','Light',40,463,349);
Schakelaar('voordeur','Light',48,54,439);
Schakelaar('hall','Light',52,410,252);	
Schakelaar('garage','Light',60,305,209);
Schakelaar('zolderg','Light',48,315,140);	
Schakelaar('lichten_auto','Clock',40,312,299);
Schakelaar('weg','Home',70,257,417);	
Schakelaar('slapen','Sleepy',70,331,417);
Schakelaar('terras','Light',48,5,16);
Schakelaar('tuin','Light',48,59,16);
Schakelaar('zolder','Light',70,710,260);
Schakelaar('bureeltobi','Plug',40,730,400);
Schakelaar('badkamervuur','Plug',28,408,348);
Thermometer('living_temp',120,190);
Thermometer('badkamer_temp',403,440);
Thermometer('kamer_temp',530,440);
Thermometer('tobi_temp',433,90);
Thermometer('alex_temp',544,90);
Thermometer('zolder_temp',690,125);
Blinds('zoldertrap',50,675,190);
if($Sweg=='On'||$Sslapen=='On'){Secured(52,88,250,196);Secured(50,345,129,57);Secured(255,88,316,141);Secured(114,345,129,134);}
if($Sweg=='On'){Secured(404,212,129,65);Secured(469,214,45,66);}
if($Spirliving!='Off'||$Spirliving!='Off') Motion(52,88,250,196);
if($Spirkeuken!='Off') Motion(114,345,129,134);
if($Spirinkom!='Off') Motion(50,345,129,57);
if($Spirgarage!='Off') Motion(255,88,316,141);
if($Spirhall!='Off'){Motion(404,212,129,65);Motion(469,214,45,66);}
if($STbelknop>$eendag) Timestamp('belknop',-90,17,462);
if($STpirgarage>$eendag) Timestamp('pirgarage',0,256,300);
if($STpirliving>$eendag) Timestamp('pirliving',0,230,300);
if($STpirlivingR>$eendag) Timestamp('pirlivingR',0,230,105);
if($STpirkeuken>$eendag) Timestamp('pirkeuken',0,115,345);
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
