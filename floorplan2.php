<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><title>Floorplan2</title><meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/><meta name="HandheldFriendly" content="true"/><meta name="apple-mobile-web-app-capable" content="yes"><meta name="apple-mobile-web-app-status-bar-style" content="black"><meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,minimal-ui"/><link rel="icon" type="image/png" href="images/domoticzphp48.png"/><link rel="shortcut icon" href="images/domoticzphp48.png"/><link rel="apple-touch-startup-image" href="images/domoticzphp450.png"/><link rel="apple-touch-icon" href="images/domoticzphp48.png"/><meta name="msapplication-TileColor" content="#ffffff"><meta name="msapplication-TileImage" content="images/domoticzphp48.png"><meta name="msapplication-config" content="browserconfig.xml"><meta name="mobile-web-app-capable" content="yes"><link rel="manifest" href="manifest.json"><meta name="theme-color" content="#ffffff"><link href="floorplan.css" rel="stylesheet" type="text/css"/><style>body{background-image:none;}</style></head><body>
<?php require "secure/settings.php";require "secure/functions2.php";if($authenticated){
if(isset($_POST['Schakel'])){
	if(Schakel($_POST['Schakel'],$_POST['Actie'],$_POST['Naam'])=='ERROR') echo '<div id="message" class="balloon">'.$_POST['Naam'].' '.$_POST['Actie'].'<br/>ERROR</div>';
	if(isset($_POST['halfuurwater'])) cset('water',1800);
	}
else if(isset($_POST['Udevice'])){if(Udevice($_POST['Udevice'],$_POST['Naam'])=='ERROR') echo '<div id="message" class="balloon">'.$_POST['Naam'].' '.$_POST['Actie'].'<br/>ERROR</div>'; }
else if(isset($_POST['imacpicam1']))shell_exec('/volume1/web/secure/picam1.sh');
else if(isset($_POST['imacsleep']))shell_exec('/volume1/web/secure/imacsleep.sh');
$domoticz=json_decode(file_get_contents($domoticzurl.'json.htm?type=devices&used=true&plan=5',true,$ctx),true);$domotime=microtime(true)-$start; //&plan=2
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
echo '<div style="position:absolute;top:5px;left:0px;width:99%;text-align:center;"><a href=\'javascript:navigator_Go("floorplan2.php");\' style="padding:35px 13px 6px 8px;font-size:33px;font-weight:500;color:#CCC;" title="refresh">'.strftime("%k:%M:%S",$time).'</a></div>';

echo '<div class="box" style="top:120px;left:10px;height:90px;width:470px">';
Schakelaar('tv','TV',48,10,10);
Schakelaar('kodi','Kodi',48,10,85);
echo '<div style="position:absolute;top:10px;left:160px;z-index:1000;"><a href=\'javascript:navigator_Go("films/kodi.php");\'><img src="images/kodi.png" width="48px" height="48px"><br/>Kodi<br/>Control</a></div>';
echo '<div style="position:absolute;top:10px;left:235px;z-index:1000;"><a href=\'javascript:navigator_Go("films/films.php");\'><img src="images/kodi.png" width="48px" height="48px"><br/>Films</a></div>';
echo '<div style="position:absolute;top:10px;left:310px;z-index:1000;"><a href=\'javascript:navigator_Go("films/tobi.php");\'><img src="images/kodi.png" width="48px" height="48px"><br/>Tobi</a></div>';
echo '<div style="position:absolute;top:10px;left:385px;z-index:1000;"><a href=\'javascript:navigator_Go("films/series.php");\'><img src="images/kodi.png" width="48px" height="48px"><br/>Series</a></div>';
echo '</div>';

echo '<div class="box" style="top:230px;left:10px;height:160px;width:470px">';
echo '<div style="position:absolute;top:5px;left: 10px;z-index:1000;"><form method="POST" action="picam1/index.php"><input type="image" src="images/Camera.png" width="48px" height="48px"></form>Voordeur<br/>Oprit</div>';
echo '<div style="position:absolute;top:5px;left: 90px;z-index:1000;"><form method="POST" action="picam2/index.php"><input type="image" src="images/Camera.png" width="48px" height="48px"></form>Alex</div>';
echo '<div style="position:absolute;top:85px;left: 10px;z-index:1000;"><form method="POST"><input type="hidden" name="imacpicam1" value="imacpicam1"><input type="image" src="images/Camera.png" width="48px" height="48px"></form>Voordeur<br/>iMac</div>';
echo '<div style="position:absolute;top:85px;left:310px;z-index:1000;"><form method="POST"><input type="hidden" name="imacsleep" value="imacsleep"><input type="image" src="images/imacsleep_On.png" width="48px" height="48px"></form>iMac<br/>sleep now</div>';
Schakelaar('meldingen','Alarm',60,5,385);
Schakelaar('sony','Ampgrey',60,5,285);
Schakelaar('imacslapen','imacsleep',60,85,385);
if($Ssirene!='Off') Schakelaar('sirene','Alarm',60,85,385);
$items=array('SDliving','SDkamer','SDbadkamer','SDtobi','SDalex','SDzolder');foreach($items as $item) if(${'S'.$item}!='Off') Smokedetector($item,60,85,385);
echo '</div>';

echo '<div class="box" style="top:410px;left:10px;height:250px;width:470px">';
Schakelaar('regenpomp','Light',60,10,0);
if($STregenpomp>$eendag) {echo '<div style="position:absolute;top:83px;left:34px;">';echo strftime("%H:%M",$STregenpomp);echo '</div>';}
Schakelaar('zwembadfilter','Light',60,10,125);
if($STzwembadfilter>$eendag) {echo '<div style="position:absolute;top:83px;left:159px;">';echo strftime("%H:%M",$STzwembadfilter);echo '</div>';}
Schakelaar('zwembadwarmte','Light',60,10,250);
if($STzwembadwarmte>$eendag) {echo '<div style="position:absolute;top:83px;left:285px;">';echo strftime("%H:%M",$STzwembadwarmte);echo '</div>';}
Schakelaar('water','Light',60,10,375);
if($STwater>$eendag) {echo '<div style="position:absolute;top:83px;left:410px;">';echo strftime("%H:%M",$STwater);echo '</div>';}
echo '<div style="position:absolute;top:100px;left:375px;width:102px;text-align:center;z-index:500;">		
		<form method="POST"><input type="hidden" name="Schakel" value="'.$SIwater.'"><input type="hidden" name="halfuurwater" value="halfuurwater">';
		echo $Swater=='Off'?'<input type="hidden" name="Actie" value="On"><input type="hidden" name="Naam" value="water"><input type="image" src="images/Light_Off.png" height="60px" width="auto">' 
					   :'<input type="hidden" name="Actie" value="Off"><input type="hidden" name="Naam" value="water"><input type="image" src="images/Light_On.png" height="60px" width="auto">';
		echo '<br/>water<br/>30min</form></div>';

Sunscreen('luifel',60,175,50);
echo '</div>';

echo '<div style="position:absolute;top:5px;left:5px;z-index:1000;"><a href=\'javascript:navigator_Go("floorplan.php");\'><img src="images/close.png" width="72px" height="72px"/></a></div></div>';
echo '<div style="position:absolute;top:5px;right:5px;z-index:1000;"><a href=\'javascript:navigator_Go("log.php");\'><img src="images/log.png" width="72px" height="72px"/></a></div></div>';


echo '</div>';
echo '<div class="clear"></div><br/><br/><br/>
<div>&nbsp;&nbsp;<form method="POST"><input type="submit" name="logout" value="Logout" class="btn" style="min-width:4em;padding:0px;margin:0px;width:50px;height:39px;"/></form><br/><br/></div>';
} else echo '<div style="background:#ddd;"><a href="">Geen verbinding met Domoticz</a></div>';	
}
?>
<script type="text/javascript">
function toggle_visibility(id){var e=document.getElementById(id);if(e.style.display=='inherit') e.style.display='none';else e.style.display='inherit';}
setTimeout('window.location.href=window.location.href;',4950);
function navigator_Go(url) {window.location.assign(url);}
</script>
</body></html>
