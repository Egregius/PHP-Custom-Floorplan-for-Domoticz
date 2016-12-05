<?php include('secure/settings.php');if($home===true){
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="HandheldFriendly" content="true" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="viewport" content="width=device-width,height=device-width, initial-scale=0.5, user-scalable=yes, minimal-ui" />
    <title>Denon</title>
    <link rel="icon" type="image/png" href="images/denon.png">
    <link rel="shortcut icon" href="images/denon.png" />
    <link rel="apple-touch-icon" href="images/denon.png"/>
    <link rel="icon" sizes="196x196" href="images/denon.png">
    <link rel="icon" sizes="192x192" href="images/denon.png">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="/manifests/denon.json">
    <link href="images/denon.png" media="(device-width: 320px)" rel="apple-touch-startup-image">
    <link href="images/denon.png" media="(device-width: 320px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
    <link href="images/denon.png" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)"	rel="apple-touch-startup-image">
    <link href="images/denon.png" media="(device-width: 768px) and (orientation: portrait)" rel="apple-touch-startup-image">
    <link href="images/denon.png" media="(device-width: 768px) and (orientation: landscape)" rel="apple-touch-startup-image">
    <link href="images/denon.png" media="(device-width: 768px) and (orientation: portrait) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
    <link href="images/denon.png" media="(device-width: 768px) and (orientation: landscape) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
    <script type="text/javascript">setTimeout(\'window.location.href=window.location.href;\', 4950);</script>
    <link href="/styles/denon.php" rel="stylesheet" type="text/css"/>
  </head>
<body>';
$denon_address='http://192.168.2.4';
	if(isset($_POST['action'])){
		file_get_contents($denon_address.'/'.$_POST['action']);
		if(substr($_POST['action'],28,15)=='PutSurroundMode'||substr($_POST['action'],28,15)=='PutZone_InputFu'){
		usleep(700000);
  }else	usleep(80000);
	}
	$ctx=stream_context_create(array('http'=>array('timeout' =>2,)));
  $denonmain=json_decode(json_encode(simplexml_load_string(file_get_contents($denon_address.'/goform/formMainZone_MainZoneXml.xml?_='.time(),false,$ctx))),TRUE);
	usleep(40000);
  $denonzone2=json_decode(json_encode(simplexml_load_string(file_get_contents($denon_address.'/goform/formMainZone_MainZoneXml.xml?_='.time().'&ZoneName=ZONE2',false,$ctx))),TRUE);
	if(!$denonmain)echo '
  <div class="error">Kon geen verbinding maken met Denon op '.$denon_address.'<br/>Geen real-time info beschikbaar</div>';
    else $live=true;
    ?>

		<div class="navbar">
			<form action="/floorplan.php"><input type="submit" class="btn b7" value="Plan"/></form>
    		<form action="/denon.php"><input type="submit" class="btn btna b7" value="Denon"/></form>
    		<form action="/kodi.php"><input type="submit" class="btn b7" value="Kodi"/></form>
			<form action="/films/films.php"><input type="submit" class="btn b7" value="Films"/></form>
			<form action="/films/tobi.php"><input type="submit" class="btn b7" value="Tobi"/></form>
			<form action="/films/alex.php"><input type="submit" class="btn b7" value="Alex"/></form>
			<form action="/films/series.php"><input type="submit" class="btn b7" value="Series"/></form>
			</div>
		<div class="content">
    			<form method="POST">
    				<div class="box">
    <?php
    $currentvolume = 80+$denonmain['MasterVolume']['value'];if($currentvolume==80) $currentvolume = 0;
			if($denonmain['ZonePower']['value']=='ON') {
            $levels=array(10,12,14,16,18,20,22,24,26,28,30,32,34,36,38,40,41,42,43,44,45,46,47,48,50,52,54,56,58,60);
            if(!in_array($currentvolume, $levels))$levels[]=$currentvolume;
          	asort($levels);
          	$levels=array_slice($levels,0,30);
			foreach($levels as $k) {
				$setvalue = 80-$k;
				$showvalue = $k;
				if($showvalue == 80) $showvalue = 0;
				if($k==$currentvolume)echo '
         				<button name="action" value="MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-'.$setvalue.'.0" type="submit" class="btn volume btna">'.$showvalue.'</button>';
        else echo '
          				<button name="action" value="MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-'.$setvalue.'.0" type="submit" class="btn volume">'.$showvalue.'</button>';
            }
            ?>
            
          			</div>
          			<div class="box">
            <?php
            $input=$denonmain['InputFuncSelect']['value'];
            if($input=="TUNER")echo '
            			<button name="action" value="MainZone/index.put.asp?cmd0=PutZone_InputFunction/TUNER" class="btn b4 btna">RADIO</button>';
            else echo '
            			<button name="action" value="MainZone/index.put.asp?cmd0=PutZone_InputFunction/TUNER" class="btn b4">RADIO</button>';
            if($input=="DIGICORDER")echo '
            			<button name="action" value="MainZone/index.put.asp?cmd0=PutZone_InputFunction/SAT/CBL" class="btn b4 btna">DIGICORDER</button>';
            else echo '
            			<button name="action" value="MainZone/index.put.asp?cmd0=PutZone_InputFunction/SAT/CBL" class="btn b4">DIGICORDER</button>';
            if($input=="KODI")echo '
            			<button name="action" value="MainZone/index.put.asp?cmd0=PutZone_InputFunction/DVD" class="btn b4 btna">KODI</button>';
            else echo '
            			<button name="action" value="MainZone/index.put.asp?cmd0=PutZone_InputFunction/DVD" class="btn b4">KODI</button>';
            if($input=="NETWORK")echo '
            			<button name="action" value="MainZone/index.put.asp?cmd0=PutZone_InputFunction/AUX1" class="btn b4 btna">IMAC</button>';
            else echo '
            			<button name="action" value="MainZone/index.put.asp?cmd0=PutZone_InputFunction/AUX1" class="btn b4">IMAC</button>';
            ?>
            
          			</div>
        <?php }
	if($denonmain['ZonePower']['value']=='ON') { ?>
    		<div class="box right">
        			&nbsp;<b>Dialoog</b> &nbsp;
        				<button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=0.0" class="btn level">0</button>
        				<button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=1.0" class="btn level">1</button>
        				<button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=2.0" class="btn level">2</button>
        				<button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=3.0" class="btn level">3</button>
        				<button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=4.0" class="btn level">4</button>
        				<button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=5.0" class="btn level">5</button>
        				<button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=6.0" class="btn level">6</button>
        				<button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=7.0" class="btn level">7</button>
        				<button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=8.0" class="btn level">8</button>
        				<button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=9.0" class="btn level">9</button>
        				<button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=10.0" class="btn level">10</button>
        				<button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=11.0" class="btn level">11</button>
        				<button name="action" value="SETUP/AUDIO/DIALOGLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listDialogue=12.0" class="btn level">12</button>
      				</div>
      				<div class="box right">
        				<b>Subwoofer</b> &nbsp;
        				<button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=0.0" class="btn level">0</button>
        				<button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=1.0" class="btn level">1</button>
        				<button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=2.0" class="btn level">2</button>
        				<button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=3.0" class="btn level">3</button>
        				<button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=4.0" class="btn level">4</button>
        				<button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=5.0" class="btn level">5</button>
        				<button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=6.0" class="btn level">6</button>
        				<button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=7.0" class="btn level">7</button>
        				<button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=8.0" class="btn level">8</button>
        				<button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=9.0" class="btn level">9</button>
        				<button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=10.0" class="btn level">10</button>
        				<button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=11.0" class="btn level">11</button>
        				<button name="action" value="SETUP/AUDIO/SUBWOOFERLEVEL/s_audio.asp?setPureDirectOn=OFF&setSetupLock=OFF&radioSw=ON&listSWLevel=12.0" class="btn level">12</button>
    				</div>
    				<div class="box"><h1>
        				<?php if($live==true) echo $denonmain['selectSurround']['value'];?></h1><br/>
        				<button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/STANDARD" class="btn b4">Standard</button>
        				<button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/STEREO" class="btn b4">Stereo</button>
        				<button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/MOVIE" class="btn b4">Movie</button>
        				<button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/MUSIC" class="btn b4">Music</button>
        				<button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/MATRIX" class="btn b4">Matrix</button>
        				<button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/VIRTUAL" class="btn b4">Virtual</button>
        				<button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/GAME" class="btn b4">Game</button>
        				<button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/DIRECT" class="btn b4">Direct</button>
        				<button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/PURE DIRECT" class="btn b4">Pure Direct</button>
        				<button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/DOLBY DIGITAL" class="btn b4">Dolby Digital</button>
        				<button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/DTS SURROUND" class="btn b4">DTS Surround</button>
        				<button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/VIDEO GAME" class="btn b4">Video Game</button>
    				</div>
    			</div>
    		</div>
    <?php } ?>
    </div>
    </div>
</div>
    </form>
   </body>
 </html>
<?php
}
