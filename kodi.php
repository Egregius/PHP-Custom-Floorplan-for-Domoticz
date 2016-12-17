<?php include('secure/settings.php');if($home===true){//error_reporting(E_ALL);ini_set("display_errors", "on");
  $count=0;$ctx=stream_context_create(array('http'=>array('timeout' => 2)));
  echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="HandheldFriendly" content="true" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="viewport" content="width=device-width,height=device-width, initial-scale=0.5, user-scalable=yes, minimal-ui" />
    <title>Kodi</title>
    <link rel="icon" type="image/png" href="images/kodi.png">
    <link rel="shortcut icon" href="images/kodi.png" />
    <link rel="apple-touch-icon" href="images/kodi.png"/>
    <link rel="icon" sizes="196x196" href="images/kodi.png">
    <link rel="icon" sizes="192x192" href="images/kodi.png">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="/manifests/kodi.json">
    <script type="text/javascript">
       setTimeout(\'window.location.href=window.location.href;\', 4950);
       function navigator_Go(url) {window.location.assign(url);}
    </script>
    <link href="/styles/kodi.php" rel="stylesheet" type="text/css"/>
  </head>
  <body>
  <div class="navbar">
    <form action="/floorplan.php"><input type="submit" class="btn b7" value="Plan"/></form>
    <form action="/denon.php"><input type="submit" class="btn b7" value="Denon"/></form>
    <form action="/kodi.php"><input type="submit" class="btn btna b7" value="Kodi"/></form>
	<form action="/films/films.php"><input type="submit" class="btn b7" value="Films"/></form>
	<form action="/films/tobi.php"><input type="submit" class="btn b7" value="Tobi"/></form>
	<form action="/films/alex.php"><input type="submit" class="btn b7" value="Alex"/></form>
	<form action="/films/series.php"><input type="submit" class="btn b7" value="Series"/></form>
	</div>
	<div class="content">
    <div class="box"><form method="POST">';
  if(isset($_POST['UpdateKodi'])){
    $profile=$_POST['UpdateKodi'];echo 'Wanted profile='.$profile.'<br/>';
    profile:
    $loadedprofile=json_decode(file_get_contents('http://192.168.2.7:1597/jsonrpc?request={"jsonrpc":"2.0","id":"1","method":"Profiles.GetCurrentProfile","id":1}',false,$ctx),true);
    echo 'loadedprofile='.$loadedprofile['result']['label'].'<br/>';
    if($loadedprofile['result']['label']!==$profile){
      file_get_contents('http://192.168.2.7:1597/jsonrpc?request={"jsonrpc":"2.0","id":1,"method":"Player.Stop","params":{"playerid":1}}',false,$ctx);
      usleep(10000);
      $profilereply=file_get_contents('http://192.168.2.7:1597/jsonrpc?request={"jsonrpc":"2.0","id":"1","method":"Profiles.LoadProfile","params":{"profile":"'.$profile.'"},"id":1}',false,$ctx);
      echo 'profilereply='.$profilereply.'</pre><br/>';
      $count=$count + 1;
      if($count>10) die('Die Endless loop');
      sleep(3);
      goto profile;
    }
    else file_get_contents('http://192.168.2.7:1597/jsonrpc?request={"jsonrpc":"2.0","id":1,"method":"Videolibrary.Scan"}',false,$ctx);
  }elseif(isset($_POST['CleanKodi']))
  	file_get_contents('http://192.168.2.7:1597/jsonrpc?request={"jsonrpc":"2.0","id":1,"method":"Videolibrary.Clean"}',false,$ctx);
  elseif(isset($_POST['PauseKodi'])){
  	file_get_contents('http://192.168.2.7:1597/jsonrpc?request={"jsonrpc":"2.0","id":1,"method":"Player.PlayPause","params":{"playerid":1}}',false,$ctx);
  	if($_POST['PauseKodi']=="Paused") file_get_contents('http://127.0.0.1:8084/json.htm?type=command&param=udevice&idx=198&nvalue=0&svalue=On');
  	else file_get_contents('http://127.0.0.1:8084/json.htm?type=command&param=switchlight&idx=61&switchcmd=On&level=0&passcode=');
  }
  elseif(isset($_POST['StopKodi']))
  	file_get_contents('http://192.168.2.7:1597/jsonrpc?request={"jsonrpc":"2.0","id":1,"method":"Player.Stop","params":{"playerid":1}}',false,$ctx);
  elseif(isset($_POST['bigbackward']))
  	file_get_contents('http://192.168.2.7:1597/jsonrpc?request={"jsonrpc":"2.0","id":1,"method":"Player.Seek","params":{"playerid":1,"value":"bigbackward"}}',false,$ctx);
  elseif(isset($_POST['smallbackward']))
  	file_get_contents('http://192.168.2.7:1597/jsonrpc?request={"jsonrpc":"2.0","id":1,"method":"Player.Seek","params":{"playerid":1,"value":"smallbackward"}}',false,$ctx);
  elseif(isset($_POST['smallforward']))
  	file_get_contents('http://192.168.2.7:1597/jsonrpc?request={"jsonrpc":"2.0","id":1,"method":"Player.Seek","params":{"playerid":1,"value":"smallforward"}}',false,$ctx);
  elseif(isset($_POST['bigforward']))
  	file_get_contents('http://192.168.2.7:1597/jsonrpc?request={"jsonrpc":"2.0","id":1,"method":"Player.Seek","params":{"playerid":1,"value":"bigforward"}}',false,$ctx);
  elseif(isset($_POST['PowerOn']))
  	file_get_contents('http://127.0.0.1:8084/json.htm?type=command&param=switchlight&idx=15&switchcmd=On&level=0&passcode=',false,$ctx);
  elseif(isset($_POST['TVKodi']))
  	file_get_contents('http://127.0.0.1:8084/json.htm?type=command&param=udevice&idx=196&nvalue=0&svalue=On',false,$ctx);
  elseif(isset($_POST['audio']))
  	file_get_contents('http://192.168.2.7:1597/jsonrpc?request={"jsonrpc":"2.0","id":1,"method":"Player.SetAudioStream","params":{"playerid":1,"stream":'.$_POST['audio'].'}}',false,$ctx);
  elseif(isset($_POST['subtitle']))
  	file_get_contents('http://192.168.2.7:1597/jsonrpc?request={"jsonrpc":"2.0","id":1,"method":"Player.SetSubtitle","params":{"playerid":1,"subtitle":'.$_POST['subtitle'].'}}',false,$ctx);
  elseif(isset($_POST['Reboot']))
  	file_get_contents('http://192.168.2.7:1597/jsonrpc?request={"jsonrpc":"2.0","id":1,"method":"System.Reboot"}',false,$ctx);
  elseif(isset($_POST['Shutdown']))
  	file_get_contents('http://192.168.2.7:1597/jsonrpc?request={"jsonrpc":"2.0","id":1,"method":"System.Shutdown"}',false,$ctx);
  elseif(isset($_POST['Denon'])){header("Location: ../denon.php");die("Redirecting to: ../denon.php");}
  elseif(isset($_POST['VolumeDOWN']))
  {
    $denonmain=simplexml_load_string(file_get_contents($denon_address.'/goform/formMainZone_MainZoneXml.xml?_='.$time,false,$ctx));
    $denonmain=json_encode($denonmain);$denonmain=json_decode($denonmain,TRUE);usleep(10000);
    if($denonmain)
    {
      $denonmain['MasterVolume']['value']=='--'?$setvalue=-80:$setvalue=$denonmain['MasterVolume']['value'];
      $setvalue=$setvalue-3;
      if($setvalue>-10) $setvalue=-10;if($setvalue<-80) $setvalue=-80;
      file_get_contents('http://192.168.2.4/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/'.$setvalue.'.0');
    }
  }
  elseif(isset($_POST['VolumeUP']))
  {
    $denonmain=simplexml_load_string(file_get_contents($denon_address.'/goform/formMainZone_MainZoneXml.xml?_='.$time,false,$ctx));
    $denonmain=json_encode($denonmain);$denonmain=json_decode($denonmain,TRUE);usleep(10000);
    if($denonmain)
    {
      $denonmain['MasterVolume']['value']=='--'?$setvalue=-80:$setvalue=$denonmain['MasterVolume']['value'];
      $setvalue=$setvalue+3;
      if($setvalue>-10) $setvalue=-10;
      if($setvalue<-80) $setvalue=-80;
      file_get_contents('http://192.168.2.4/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/'.$setvalue.'.0');
    }
  }
  
  $current=json_decode(file_get_contents('http://192.168.2.7:1597/jsonrpc?request={"jsonrpc":"2.0","method":"Player.GetItem","params":{"properties":["title","album","artist","season","episode","duration","showtitle","tvshowid","thumbnail","file","imdbnumber"],"playerid":1},"id":"VideoGetItem"}',false,$ctx),true);
  if(isset($current['result']['item']['file']))
  {
    if(!empty($current['result']['item']['file']))
    {
      $item=$current['result']['item'];
      //print_r($item);
      if($item['episode']>0){
        echo '<h1>'.$item['showtitle'].' S '.$item['season'].' E '.$item['episode'].'</h1>';
        echo '<h1>'.$item['label'].'</h1>';
      }
      else
      {
        echo '<a href="http://www.imdb.com/title/'.$item['imdbnumber'].'" style="color:#f5b324"><h1>'.$item['label'].'</h1></a>';
      }
	$properties=json_decode(file_get_contents('http://192.168.2.7:1597/jsonrpc?request={"jsonrpc":"2.0","method":"Player.GetProperties","id":1,"params":{"playerid":1,"properties":["playlistid","speed","position","totaltime","time","audiostreams","currentaudiostream","subtitleenabled","subtitles","currentsubtitle"]}}',false,$ctx),true);
      //echo '<pre>';print_r($properties);echo '</pre>';
    if(!empty($properties['result']))
    {
      $prop=$properties['result'];
      $point=$prop['time'];
      $total=$prop['totaltime'];
      $passedtime=$point['hours'].':';
      $point['minutes']<10?$passedtime.='0'.$point['minutes'].':':$passedtime.=$point['minutes'].':';
      $point['seconds']<10?$passedtime.='0'.$point['seconds']:$passedtime.=$point['seconds'];
      $totaltime=$total['hours'].':';
      $total['minutes']<10?$totaltime.='0'.$total['minutes'].':':$totaltime.=$total['minutes'].':';
      $total['seconds']<10?$totaltime.='0'.$total['seconds']:$totaltime.=$total['seconds'];
      echo '<table align="center">
      <tr>
      	<td>Passed</td>
      	<td><h2>'.$passedtime.'</h2></td><td>Runtime</td><td><h2>'.$totaltime.'</h2></td>
      </tr>
      <tr>
      	<td>Remaining</td>
      	<td><h2>'.strftime("%k:%M:%S",(strtotime($totaltime)-strtotime($passedtime)-3600)).'</h2></td>
      	<td>End at</td>
      	<td><h2>'.strftime("%k:%M:%S",($time+strtotime($totaltime)-strtotime($passedtime))).'</h2></td>
      </tr>
      </table>
      </div>';
      echo '<div class="box">';
      echo $prop['speed']==1
      	?'<input type="submit" name="PauseKodi" value="Playing" class="btn b2"/>'
      	:'<input type="submit" name="PauseKodi" value="Paused" class="btn b2"/>';
      echo '        <input type="submit" name="StopKodi" value="STOP" class="btn b2"/>';
      if($prop['speed']==1) echo '<br>
      	<input type="submit" name="bigbackward" value="<<" class="btn b4"/>
      	<input type="submit" name="smallbackward" value="<" class="btn b4"/>
      	<input type="submit" name="smallforward" value=">" class="btn b4"/>
      	<input type="submit" name="bigforward" value=">>" class="btn b4"/>
      	';
      echo '
            </div>';
      echo '<div class="box">';
      $stream=0;
  		foreach($prop['audiostreams'] as $audio)
  		{
  			echo $audio['index']===$prop['currentaudiostream']['index']
  				?'<button type="submit" name="audio" value="'.$audio['index'].'" class="btn btna b2">'.$audio['name'].'</button>'
  				:'<button type="submit" name="audio" value="'.$audio['index'].'" class="btn b2">'.$audio['name'].'</button>';
  			$stream=$stream + 1;
  		}
      echo '</div>';
      echo '<div class="box">';
      $subtitlenumber=0;
  		foreach($prop['subtitles'] as $subtitle)
  		{
  			echo $subtitle['index']===$prop['currentsubtitle']['index']
  				?'<button type="submit" name="subtitle" value="'.$subtitle['index'].'" class="btn btna b2">'.lang($subtitle['language']).' '.$subtitle['name'].'</button>'
  				:'<button type="submit" name="subtitle" value="'.$subtitle['index'].'" class="btn b2">'.lang($subtitle['language']).' '.$subtitle['name'].'</button>';
  			$subtitlenumber=$subtitlenumber + 1;
  		}
  	}
      echo '</div>';


    }
    else echo '<div class="box">Nothing playing</div>';

  }
  else echo '<div class="box">Kodi offline</div>';
  echo '<div class="box">
          <input type="submit" name="VolumeDOWN" value="Down" class="btn b3"/>
          <input type="submit" name="Denon" value="Denon" class="btn b3"/>
          <input type="submit" name="VolumeUP" value="Up" class="btn b3"/>
        </div>';
  echo '<div class="box">Update Library:<br/>
          <input type="submit" name="UpdateKodi" value="Wij" class="btn b3"/>
          <input type="submit" name="UpdateKodi" value="Tobi" class="btn b3"/>
          <input type="submit" name="UpdateKodi" value="Alex" class="btn b3"/>
        </div>';
  echo '<div class="box">
          <input type="submit" name="PowerOn" value="Power On" class="btn b4"/>
          <input type="submit" name="TVKodi" value="TV Kodi" class="btn b4"/>
          <input type="submit" name="Reboot" value="Reboot" class="btn b4"/>
          <input type="submit" name="Shutdown" value="Shutdown" class="btn b4"/>
        </div>
    </div>';
}
else
{
	header("Location: index.php");
	die("Redirecting to: index.php");
}
function lang($lang){
	switch($lang){
		case 'dut': $taal='&nbsp;NL&nbsp;';break;
		case 'eng': $taal='&nbsp;EN&nbsp;';break;
		case 'fre': $taal='&nbsp;FR&nbsp;';break;
		case '': $taal='N/A';break;
		default: $taal=$lang;
	}
   return $taal;
}
