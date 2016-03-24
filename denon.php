<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="HandheldFriendly" content="true" /><meta name="apple-mobile-web-app-capable" content="yes"><meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,minimal-ui" />
<title>Denon</title>
<link href="style.css?v=7" rel="stylesheet" type="text/css" />
<link rel="icon" type="image/png" href="images/denon.png">
<link rel="shortcut icon" href="/favicon.ico" />
<link rel="apple-touch-icon" href="images/denon.png"/>
<link rel="icon" sizes="196x196" href="images/denon.png">
<link rel="icon" sizes="192x192" href="images/denon.png">
<meta name="mobile-web-app-capable" content="yes"><link rel="manifest" href="manifest.json">
<link href="images/denon.png" media="(device-width: 320px)" rel="apple-touch-startup-image">		
<link href="images/denon.png" media="(device-width: 320px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
<link href="images/denon.png" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)"	rel="apple-touch-startup-image">
<link href="images/denon.png" media="(device-width: 768px) and (orientation: portrait)" rel="apple-touch-startup-image">
<link href="images/denon.png" media="(device-width: 768px) and (orientation: landscape)" rel="apple-touch-startup-image">
<link href="images/denon.png" media="(device-width: 768px) and (orientation: portrait) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
<link href="images/denon.png" media="(device-width: 768px) and (orientation: landscape) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image"> 
<script type="text/javascript">
   setTimeout('window.location.href=window.location.href;', 12000);
   function navigator_Go(url) {window.location.assign(url);}
</script>
<style>
.abutton{border:0px solid #aaa;display:inline-block;padding:7px 0px;text-decoration:none;margin:2px 2px 3px 2px;width:55px;box-shadow:0px 0px 0px 1px #999 inset;border-radius:6px;}
h2{font-size:12px}
</style>
</head>
<body style="max-width:100%;">
<?php
$denon_address = 'http://192.168.0.15';
include('secure/settings.php');
if($home===true) {
	if(isset($_POST['action'])) {
		file_get_contents($denon_address.'/'.$_POST['action']);
		if(substr($_POST['action'],28,15) == 'PutSurroundMode' || substr($_POST['action'],28,15) == 'PutZone_InputFu') {
		usleep(700000);
		} else {
		usleep(70000);
		}
	}

	$ctx = stream_context_create(array('http'=>array('timeout' => 2,)));
	$denonmain = simplexml_load_string(file_get_contents($denon_address.'/goform/formMainZone_MainZoneXml.xml?_='.time(),false, $ctx));
    $denonmain = json_encode($denonmain);
    $denonmain = json_decode($denonmain, TRUE);
	usleep(40000);
    $denonzone2 = simplexml_load_string(file_get_contents($denon_address.'/goform/formMainZone_MainZoneXml.xml?_='.time().'&ZoneName=ZONE2',false, $ctx));
    $denonzone2 = json_encode($denonzone2);
    $denonzone2 = json_decode($denonzone2, TRUE);
	if(!$denonmain) echo '<div class="error">Kon geen verbinding maken met Denon op '.$denon_address.'<br/>Geen real-time info beschikbaar</div>';
    else $live = true;
    ?>
    <div align="center">
    <a href='javascript:navigator_Go("floorplan.php");' class="btn" style="width:38%;">Floorplan</a>
    <a href='javascript:navigator_Go("films/kodi.php");' class="btn" style="width:38%;">Kodi</a>
    <a href='javascript:navigator_Go("films/films.php");' class="btn" style="width:38%;">Films</a>
    <a href='javascript:navigator_Go("films/series.php");' class="btn" style="width:38%;">Series</a>
  	</div>
    <form method="POST">
    <div align="center"><br/><a href='javascript:navigator_Go("denon.php");'>
        <?php if($live==true) echo '<h1>'.$denonmain['RenameZone']['value']; else echo '<h1>Main Zone'; ?>
           
            <?php if($denonmain['ZonePower']['value']=='ON') {
				$currentvolume = 80+$denonmain['MasterVolume']['value'];
				if($currentvolume==80) $currentvolume = 0;
				echo ''.$denonmain['InputFuncSelect']['value'].' @ '.$currentvolume.'';?></h1></a><br/>
            <?php
            $levels=array(12,16,18,20,22,24,26,28,30,32,34,36,38,40,42,44,46,48,50,55);
			foreach($levels as $k) {
				$setvalue = 80-$k;
				$showvalue = $k;
				if($showvalue == 80) $showvalue = 0;
				echo '<button name="action" value="MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-'.$setvalue.'.0" type="submit" class="btn" style="min-width:18.2%">'.$showvalue.'</button>'; 
            }
            ?>
            <br/><br/>
            <button name="action" value="MainZone/index.put.asp?cmd0=PutZone_InputFunction/TUNER" class="btn" style="min-width:23%">Tuner</button>
            <button name="action" value="MainZone/index.put.asp?cmd0=PutZone_InputFunction/SAT/CBL" class="btn" style="min-width:22%">TV</button>
            <button name="action" value="MainZone/index.put.asp?cmd0=PutZone_InputFunction/DVD" class="btn" style="min-width:23%">Kodi</button>
            <button name="action" value="MainZone/index.put.asp?cmd0=PutZone_InputFunction/AUX1" class="btn" style="min-width:23%">iMac</button><br/><br/>
            <!-- <button name="action" value="MainZone/index.put.asp?cmd0=PutZone_InputFunction/IRADIO&cmd1=aspMainZone_WebUpdateStatus/&cmd2=PutNetAudioCommand/CurRight&ZoneName=MAIN+ZONE" class="abutton settings gradient">iRadio</button> -->
        <?php } ?>
       <!-- <table width="100%" align="center">
            <tr>
                <td align="right">Power</td>
                <td width="300px">
                    <button name="action" value="MainZone/index.put.asp?cmd0=PutSystem_OnStandby%2FON&cmd1=aspMainZone_WebUpdateStatus%2F" class="btn" style="width:110px">ON</button>
                    <button name="action" value="MainZone/index.put.asp?cmd0=PutSystem_OnStandby%2FSTANDBY&cmd1=aspMainZone_WebUpdateStatus%2F" class="btn" style="width:110px">OFF</button>
                </td>
            
                <td align="right"><?php //if($live==true) echo $denonmain['RenameZone']['value']; else echo '<h2>Zone 2</h2>'; ?></td>
                <td width="300px">
                    <button name="action" value="MainZone/index.put.asp?cmd0=PutZone_OnOff%2FON&cmd1=aspMainZone_WebUpdateStatus%2F" class="btn" style="width:110px">ON</button>
                    <button name="action" value="MainZone/index.put.asp?cmd0=PutZone_OnOff%2FOFF&cmd1=aspMainZone_WebUpdateStatus%2F" class="btn" style="width:110px">OFF</button>
                </td>
            </tr>
        </table> -->
  
        <?php //if($live==true) echo '<h1>'.$denonzone2['RenameZone']['value'].'</h1>'; else echo '<h1>Zone 2</h1>'; ?>
           
        <?php if($live==true && $denonzone2['ZonePower']['value']=="ON") {
			echo '<h2>'.$denonzone2['InputFuncSelect']['value'].' @ '. (80+$denonzone2['MasterVolume']['value']).'</h2>';?>
        Volume <button name="action" value="MainZone/index.put.asp?cmd0=PutMasterVolumeBtn/>&cmd1=aspMainZone_WebUpdateStatus/&ZoneName=ZONE2" class="btn">UP</button>
        <button name="action" value="MainZone/index.put.asp?cmd0=PutMasterVolumeBtn/<&cmd1=aspMainZone_WebUpdateStatus/&ZoneName=ZONE2" class="btn">DOWN</button><br/><br/>																			
        <?php
        for ($k = 9 ; $k < 56; $k++){ 
            if ($k % 5 === 0) {
                $setvalue = 80-$k;
                $showvalue = $k;
                echo '<button name="action" value="MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-'.$setvalue.'.0&ZoneName=ZONE2" class="btn">'.$showvalue.'</button>'; 
            }											
        }
		}
        ?><!--
        <table width="100%" align="center">
            <tr>
                <td align="right"><?php //if($live==true) echo $denonzone2['RenameZone']['value']; else echo '<h2>Zone 2</h2>'; ?></td>
                <td width="240px">
                    <button name="action" value="MainZone/index.put.asp?cmd0=PutZone_OnOff%2FON&cmd1=aspMainZone_WebUpdateStatus%2F&ZoneName=ZONE2" class="btn">ON</button>
                    <button name="action" value="MainZone/index.put.asp?cmd0=PutZone_OnOff%2FOFF&cmd1=aspMainZone_WebUpdateStatus%2F&ZoneName=ZONE2" class="btn">OFF</button>
                </td>
            </tr>
        </table>
    -->
    <?php if($denonmain['ZonePower']['value']=='ON') { ?>
    <div align="right">
        &nbsp;Dialoog &nbsp;
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=2.0" class="btn" style="min-width:13%">2</button>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=3.0" class="btn" style="min-width:13%">3</button>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=4.0" class="btn" style="min-width:13%">4</button>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=5.0" class="btn" style="min-width:13%">5</button>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=6.0" class="btn" style="min-width:13%">6</button>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=7.0" class="btn" style="min-width:13%">7</button>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=8.0" class="btn" style="min-width:13%">8</button>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=9.0" class="btn" style="min-width:13%">9</button>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=10.0" class="btn" style="min-width:13%">10</button>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=11.0" class="btn" style="min-width:13%">11</button>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=12.0" class="btn" style="min-width:13%">12</button><br/>
        Subwoofer &nbsp;
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=2.0" class="btn" style="min-width:13%">2</button>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=3.0" class="btn" style="min-width:13%">3</button>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=4.0" class="btn" style="min-width:13%">4</button>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=5.0" class="btn" style="min-width:13%">5</button>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=6.0" class="btn" style="min-width:13%">6</button>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=7.0" class="btn" style="min-width:13%">7</button>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=8.0" class="btn" style="min-width:13%">8</button>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=9.0" class="btn" style="min-width:13%">9</button>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=10.0" class="btn" style="min-width:13%">10</button>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=11.0" class="btn" style="min-width:13%">11</button>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=12.0" class="btn" style="min-width:13%">12</button><br/>
    </div>
    <div align="center"><h1>
        <?php if($live==true) echo $denonmain['selectSurround']['value'];?></h1><br/>
        <button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/STANDARD" class="btn" style="min-width:6em">Standard</button>
        <button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/STEREO" class="btn" style="min-width:6em">Stereo</button>
        <button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/MOVIE" class="btn" style="min-width:6em">Movie</button>
        <button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/MUSIC" class="btn" style="min-width:6em">Music</button>
        <button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/MATRIX" class="btn" style="min-width:6em">Matrix</button>
        <button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/VIRTUAL" class="btn" style="min-width:6em">Virtual</button>
        <button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/GAME" class="btn" style="min-width:6em">Game</button>
        <button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/DIRECT" class="btn" style="min-width:6em">Direct</button>
        <button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/PURE DIRECT" class="btn">Pure Direct</button>
        <button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/DOLBY DIGITAL" class="btn">Dolby Digital</button>
        <button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/DTS SURROUND" class="btn">DTS Surround</button>
        <button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/VIDEO GAME" class="btn">Video Game</button>
        <button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/MCH STEREO" class="btn" style="width:50%">Multi channel Stereo</button>
    </div>
    </div>
    </div>
    <?php } ?>
    </div>
    </div>
    </form>
   </body>
 </html>
<?php
}
