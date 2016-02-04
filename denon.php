<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Denon</title>
<link href="css.css" rel="stylesheet" type="text/css" />
<link rel="icon" type="image/png" href="images/denon.png">
<link rel="shortcut icon" href="/favicon.ico" />
<link rel="apple-touch-icon" href="images/denon.png"/>
<link rel="icon" sizes="196x196" href="images/denon.png">
<link rel="icon" sizes="192x192" href="images/denon.png">
<link href="images/denon.png" media="(device-width: 320px)" rel="apple-touch-startup-image">		
<link href="images/denon.png" media="(device-width: 320px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
<link href="images/denon.png" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)"	rel="apple-touch-startup-image">
<link href="images/denon.png" media="(device-width: 768px) and (orientation: portrait)" rel="apple-touch-startup-image">
<link href="images/denon.png" media="(device-width: 768px) and (orientation: landscape)" rel="apple-touch-startup-image">
<link href="images/denon.png" media="(device-width: 768px) and (orientation: portrait) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
<link href="images/denon.png" media="(device-width: 768px) and (orientation: landscape) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image"> 
<script type="text/javascript">
   setTimeout('window.location.href=window.location.href;', 10000);
</script>
<style>
.abutton{border:0px solid #aaa;display:inline-block;padding:7px 0px;text-decoration:none;margin:2px 2px 3px 2px;width:55px;box-shadow:0px 0px 0px 1px #999 inset;border-radius:6px;}
h2{font-size:12px}
</style>
</head>
<body>
<?php
$denon_address = 'http://192.168.0.15';
if(isset($_COOKIE["HomeEgregius"])) {
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
    <div>
    <a href="floorplan.php" class="abutton home gradient" style="padding:5px 0px 5px 0px; margin-bottom:5px;width:100%;">Floorplan</a><br />
  	</div>
    <form method="POST">
       	
    <div class="column">
    <div class="item gradient" id="main">
        <?php if($live==true) echo '<h2>'.$denonmain['RenameZone']['value']; else echo '<h2>Main Zone'; ?>
           
            <?php if($denonmain['ZonePower']['value']=='ON') {
				$currentvolume = 80+$denonmain['MasterVolume']['value'];
				if($currentvolume==80) $currentvolume = 0;
				echo ''.$denonmain['InputFuncSelect']['value'].' @ '.$currentvolume.'';?></h2>
            <button name="action" value="MainZone/index.put.asp?cmd0=PutMasterVolumeBtn/>&cmd1=aspMainZone_WebUpdateStatus/&cmd2=PutMasterVolumeBtn/>" type="submit" class="abutton gradient" >UP</button>
            <button name="action" value="MainZone/index.put.asp?cmd0=PutMasterVolumeBtn/<&cmd1=aspMainZone_WebUpdateStatus/&cmd2=PutMasterVolumeBtn/<" type="submit" class="abutton gradient" >DOWN</button>
            <br/>
            <?php
            for ($k = 9 ; $k < 56; $k++){ 
                if ($k % 5 === 0) {
                    $setvalue = 80-$k;
                    $showvalue = $k;
					if($showvalue == 80) $showvalue = 0;
                    echo '<button name="action" value="MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-'.$setvalue.'.0" type="submit" class="abutton gradient" >'.$showvalue.'</button>'; 
                }
            }
            ?>
            <button name="action" value="MainZone/index.put.asp?cmd0=PutZone_InputFunction/TUNER" class="abutton settings gradient">Tuner</button>
            <button name="action" value="MainZone/index.put.asp?cmd0=PutZone_InputFunction/SAT/CBL" class="abutton settings gradient">TV</button>
            <button name="action" value="MainZone/index.put.asp?cmd0=PutZone_InputFunction/DVD" class="abutton settings gradient">Kodi</button>
            <button name="action" value="MainZone/index.put.asp?cmd0=PutZone_InputFunction/TV" class="abutton settings gradient">iMac</button>
            <!-- <button name="action" value="MainZone/index.put.asp?cmd0=PutZone_InputFunction/IRADIO&cmd1=aspMainZone_WebUpdateStatus/&cmd2=PutNetAudioCommand/CurRight&ZoneName=MAIN+ZONE" class="abutton settings gradient">iRadio</button> -->
        <?php } ?>
        <table width="100%" align="center">
            <tr>
                <td align="right">Power</td>
                <td width="140px">
                    <button name="action" value="MainZone/index.put.asp?cmd0=PutSystem_OnStandby%2FON&cmd1=aspMainZone_WebUpdateStatus%2F" class="abutton gradient">ON</button>
                    <button name="action" value="MainZone/index.put.asp?cmd0=PutSystem_OnStandby%2FSTANDBY&cmd1=aspMainZone_WebUpdateStatus%2F" class="abutton gradient">OFF</button>
                </td>
            </tr>
            <tr>
                <td align="right"><?php if($live==true) echo $denonmain['RenameZone']['value']; else echo '<h2>Zone 2</h2>'; ?></td>
                <td width="140px">
                    <button name="action" value="MainZone/index.put.asp?cmd0=PutZone_OnOff%2FON&cmd1=aspMainZone_WebUpdateStatus%2F" class="abutton gradient">ON</button>
                    <button name="action" value="MainZone/index.put.asp?cmd0=PutZone_OnOff%2FOFF&cmd1=aspMainZone_WebUpdateStatus%2F" class="abutton gradient">OFF</button>
                </td>
            </tr>
        </table>
    </div>
   </div>
    <div class="column">
    
    <div class="item gradient" id="zone2">
        <?php if($live==true) echo '<h2>'.$denonzone2['RenameZone']['value'].'</h2>'; else echo '<h2>Zone 2</h2>'; ?>
           
        <?php if($live==true && $denonzone2['ZonePower']['value']=="ON") {
			echo '<h2>'.$denonzone2['InputFuncSelect']['value'].' @ '. (80+$denonzone2['MasterVolume']['value']).'</h2>';?>
        Volume <button name="action" value="MainZone/index.put.asp?cmd0=PutMasterVolumeBtn/>&cmd1=aspMainZone_WebUpdateStatus/&ZoneName=ZONE2" class="abutton gradient">UP</button>
        <button name="action" value="MainZone/index.put.asp?cmd0=PutMasterVolumeBtn/<&cmd1=aspMainZone_WebUpdateStatus/&ZoneName=ZONE2" class="abutton gradient">DOWN</button><br/><br/>																			
        <?php
        for ($k = 9 ; $k < 56; $k++){ 
            if ($k % 5 === 0) {
                $setvalue = 80-$k;
                $showvalue = $k;
                echo '<button name="action" value="MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-'.$setvalue.'.0&ZoneName=ZONE2" class="abutton gradient">'.$showvalue.'</button>'; 
            }											
        }
		}
        ?>
        <table width="100%" align="center">
            <tr>
                <td align="right"><?php if($live==true) echo $denonzone2['RenameZone']['value']; else echo '<h2>Zone 2</h2>'; ?></td>
                <td width="140px">
                    <button name="action" value="MainZone/index.put.asp?cmd0=PutZone_OnOff%2FON&cmd1=aspMainZone_WebUpdateStatus%2F&ZoneName=ZONE2" class="abutton gradient">ON</button>
                    <button name="action" value="MainZone/index.put.asp?cmd0=PutZone_OnOff%2FOFF&cmd1=aspMainZone_WebUpdateStatus%2F&ZoneName=ZONE2" class="abutton gradient">OFF</button>
                </td>
            </tr>
        </table>
    
    </div>
    <?php if($denonmain['ZonePower']['value']=='ON') { ?>
    <div class="item gradient" id="dialoog">
        Dialoog
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=0" class="abutton gradient" style="width:200px; max-width:98%;">0</button><br/>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=-2.0" class="abutton gradient">-2</button>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=-1.0" class="abutton gradient">-1</button>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=1.0" class="abutton gradient">+1</button>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=2.0" class="abutton gradient">+2</button><br/>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=-4.0" class="abutton gradient">-4</button>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=-3.0" class="abutton gradient">-3</button>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=3.0" class="abutton gradient">+3</button>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=4.0" class="abutton gradient">+4</button><br/>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=-6.0" class="abutton gradient">-6</button>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=-5.0" class="abutton gradient">-5</button>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=5.0" class="abutton gradient">+5</button>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=6.0" class="abutton gradient">+6</button><br/>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=-8.0" class="abutton gradient">-8</button>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=-7.0" class="abutton gradient">-7</button>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=7.0" class="abutton gradient">+7</button>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=8.0" class="abutton gradient">+8</button><br/>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=-10.0" class="abutton gradient">-10</button>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=-9.0" class="abutton gradient">-9</button>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=9.0" class="abutton gradient">+9</button>
        <button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=10.0" class="abutton gradient">+10</button><br/>
    </div>
    </div>
    <div class="column">
    
    <div class="item gradient" id="surround">
        <?php if($live==true) echo $denonmain['selectSurround']['value'];?><br/>
        <button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/STANDARD" class="abutton gradient">Standard</button>
        <button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/STEREO" class="abutton gradient">Stereo</button>
        <button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/MOVIE" class="abutton gradient">Movie</button>
        <button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/MUSIC" class="abutton gradient">Music</button>
        <button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/MATRIX" class="abutton gradient">Matrix</button>
        <button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/VIRTUAL" class="abutton gradient">Virtual</button>
        <button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/GAME" class="abutton gradient">Game</button>
        <button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/DIRECT" class="abutton gradient">Direct</button>
        <button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/PURE DIRECT" class="abutton gradient">Pure Direct</button>
        <button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/DOLBY DIGITAL" class="abutton gradient">Dolby Digital</button>
        <button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/DTS SURROUND" class="abutton gradient">DTS Surround</button>
        <button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/VIDEO GAME" class="abutton gradient">Video Game</button>
        <button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/MCH STEREO" class="abutton gradient" style="width:150px">Multi channel Stereo</button>
    </div>
    <div class="item gradient" id="subwoofer">
        Subwoofer
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=0" class="abutton gradient" style="width:216px; max-width:100%">0</button><br/>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=-2.0" class="abutton gradient">-2</button>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=-1.0" class="abutton gradient">-1</button>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=1.0" class="abutton gradient">+1</button>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=2.0" class="abutton gradient">+2</button><br/>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=-4.0" class="abutton gradient">-4</button>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=-3.0" class="abutton gradient">-3</button>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=3.0" class="abutton gradient">+3</button>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=4.0" class="abutton gradient">+4</button><br/>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=-6.0" class="abutton gradient">-6</button>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=-5.0" class="abutton gradient">-5</button>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=5.0" class="abutton gradient">+5</button>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=6.0" class="abutton gradient">+6</button><br/>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=-8.0" class="abutton gradient">-8</button>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=-7.0" class="abutton gradient">-7</button>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=7.0" class="abutton gradient">+7</button>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=8.0" class="abutton gradient">+8</button><br/>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=-10.0" class="abutton gradient">-10</button>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=-9.0" class="abutton gradient">-9</button>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=9.0" class="abutton gradient">+9</button>
        <button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=10.0" class="abutton gradient">+10</button><br/>
    </div>
    </div>
    </div>
    <?php } ?>
    </div>
    </div>
    </form>
    <div style="clear:both">
    <form method="post" action="logout.php"><input type="submit" name="logout" value="Uitloggen" class="abutton settings gradient"/></form>
    </div>
   </body>
 </html>
    
<?php

} else
        {
            echo("Login Failed.");
            header("Location: login.php");
            die("Redirecting to: login.php");
        }
