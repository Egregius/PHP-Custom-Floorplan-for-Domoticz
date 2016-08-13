<?php //serror_reporting(E_ALL);ini_set("display_errors", "on");
//require_once ('/volume1/web/secure/settings.php');

//Functions for cron and executions
function ios($msg) {global $appleid,$applepass,$appledevice;require_once ("findmyiphone.php");$fmi=new FindMyiPhone($appleid,$applepass);$fmi->playSound($appledevice,$msg);sleep(1);}
function sms($msg,$device) {file_get_contents('http://api.clickatell.com/http/sendmsg?user='.$smsuser.'&password='.$smspassword.'&api_id='.$smsapi.'&to='.$smstofrom.'&text='.urlencode($msg).'&from='.$smstofrom.'');}
function domlog($msg) {global $domoticzurl;file_get_contents($domoticzurl.'json.htm?type=command&param=addlogmessage&message='.urlencode($msg));}
function Schakel($idx,$cmd,$name=NULL) {
	global $domoticzurl,$user,$Usleep,$log,$actions;
	$reply=json_decode(curl($domoticzurl.'json.htm?type=command&param=switchlight&idx='.$idx.'&switchcmd='.$cmd.'&level=0&passcode='),true);
	if($reply['status']=='OK') {$reply='OK';$actions=$actions+1;} else $reply='ERROR';
	if($log) logwrite($user.'	Switch	'.$name.'	'.$cmd.'	'.$reply);
	if($reply=='ERROR') {
		telegram('Switch '.$name.' '.$cmd.' = '.$reply);
		cset('timedeadnodes',$eenuur);
	}
	if($user=="Tobi") telegram('Tobi Schakel '.$idx.' '.$cmd);
	return $reply;
}
function Scene($idx,$name=NULL) {
	global $domoticzurl,$user,$Usleep,$log,$actions;
	$reply=json_decode(curl($domoticzurl.'json.htm?type=command&param=switchscene&idx='.$idx.'&switchcmd=On'),true);
	if($reply['status']=='OK') {$reply='OK';$actions=$actions+1;} else $reply='ERROR';
	if($log) logwrite($user.'	Scene	'.$name.'	'.$cmd.'	'.$reply);
	if($user=="Tobi") telegram('Tobi Scene '.$idx);
	return $reply;
}
function Dim($idx,$level,$name=NULL) {
	global $domoticzurl,$user,$Usleep,$log,$actions;
	if($level>0&&$level<100) $level=$level+1;
	$reply=json_decode(curl($domoticzurl.'json.htm?type=command&param=switchlight&idx='.$idx.'&switchcmd=Set%20Level&level='.$level),true);
	if($reply['status']=='OK') {$reply='OK';$actions=$actions+1;} else $reply='ERROR';
	if($log) logwrite($user.'	Dim	'.$name.' '.$level.'	'.$cmd.'	'.$reply);
	if($user=="Tobi") telegram('Tobi dimmer '.$idx.' '.$cmd);
	return $reply;
}
function Udevice($idx,$nvalue,$svalue,$name=NULL) {
	global $domoticzurl,$user,$Usleep,$log,$actions;
	$reply=json_decode(curl($domoticzurl.'json.htm?type=command&param=udevice&idx='.$idx.'&nvalue='.$nvalue.'&svalue='.$svalue),true);
	if($reply['status']=='OK') {$reply='OK';$actions=$actions+1;} else $reply='ERROR';
	if($log) logwrite($user.'	Udevice	'.$name.' N='.$nvalue.' S='.$svalue.'	'.$reply);
	if($user=="Tobi") telegram('Tobi Udevice '.$idx.' '.$nvalue.' '.$snvalue);
	return $reply;
}
function Textdevice($idx,$text,$name=NULL) {global $domoticzurl,$actions;$reply=json_decode(curl($domoticzurl.'json.htm?type=command&param=udevice&idx='.$idx.'&nvalue=0&svalue='.$text),true);if($reply['status']=='OK') {$reply='OK';$actions=$actions+1;} else $reply='ERROR';return $reply;}
function percentdevice($idx,$value,$name=NULL) {global $domoticzurl,$actions;$reply=json_decode(curl($domoticzurl.'json.htm?type=command&param=udevice&idx='.$idx.'&nvalue=0&svalue='.$value),true);if($reply['status']=='OK') {$reply='OK';$actions=$actions+1;} else $reply='ERROR';return $reply;}
function voorwarmen($temp, $settemp,$seconds) {
	global $Tbuiten;
	if($temp<$settemp) $voorwarmen = ceil(($settemp-$temp) + ($settemp-$Tbuiten)) * $seconds; else $voorwarmen = 0;
	if($voorwarmen>7200) $voorwarmen=7200;
	return $voorwarmen;
}
function resetsecurity($idx,$name=NULL) {
	global $domoticzurl,$log,$actions;
	$reply=json_decode(curl($domoticzurl.'json.htm?type=command&param=resetsecuritystatus&idx='.$idx.'&switchcmd=Normal'),true);
	if($reply['status']=='OK') {$reply='OK';$actions=$actions+1;} else $reply='ERROR';
	if($log) logwrite('Reset security status of  '.$name.' = '.$reply);
	return $reply;
}
function RefreshZwave($node,$name='auto',$device='') {
	global $domoticzurl,$time,$zwaveidx,$vijfmin,$actions;
	$devices=json_decode(curl($domoticzurl.'json.htm?type=openzwavenodes&idx='.$zwaveidx),true);
	//logwrite('Refreshing node '.$node.' '.$device.' '.$name);
	$zwaveurl=$domoticzurl.'ozwcp/refreshpost.html';
	$zwavedata=array('fun'=>'racp','node'=>$node);
	$zwaveoptions = array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query($zwavedata),),);
	$zwavecontext=stream_context_create($zwaveoptions);
	for ($k=1;$k<=5;$k++){
		$result=file_get_contents($zwaveurl,false,$zwavecontext);
		$actions=$actions+1;
		if($result==='OK') {cset('timerefresh-'.$device,$time);break;}
		sleep(1);
	}
	if(cget('timedeadnodes')<$vijfmin) {
	cset('timedeadnodes',$time);
	foreach($devices as $node=>$data) {
		if ($node == "result") {
		foreach($data as $index=>$eltsNode) {
		  if ($eltsNode["State"] == "Dead" && !in_array($eltsNode['NodeID'],array(50))) {
			$actions=$actions+1;
			telegram('Node '.$eltsNode['NodeID'].' '.$eltsNode['Description'].' ('.$eltsNode['Name'].') marked as dead, reviving '.ZwaveHasnodefailed($eltsNode['NodeID']));
			sleep(2);
			Zwavecancelaction();
		  }
		}
	 }
	}
}
	return $result;
}
function ZwaveHasnodefailed	($node) {
	global $domoticzurl;
	echo '1';
	$zwaveurl=$domoticzurl.'ozwcp/admpost.html';
	$zwavedata=array('fun'=>'hnf','node'=>'node'.$node);
	$zwaveoptions = array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query($zwavedata),),);
	$zwavecontext=stream_context_create($zwaveoptions);
	for ($k=1;$k<=5;$k++){
		sleep(1);
		$result=file_get_contents($zwaveurl,false,$zwavecontext);
		if($result=='OK') break;
		sleep(1);
	}
	return $result;
}
function Zwavecancelaction() {
	global $domoticzurl;
	$zwaveurl=$domoticzurl.'ozwcp/admpost.html';
	$zwavedata=array('fun'=>'cancel');
	$zwaveoptions = array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query($zwavedata),),);
	$zwavecontext=stream_context_create($zwaveoptions);
	$result=file_get_contents($zwaveurl,false,$zwavecontext);
}
function Zwavesoftreset() {
	global $domoticzurl;
	$zwaveurl=$domoticzurl.'ozwcp/devpost.html';
	$zwavedata=array('dev'=>'domoticz','fn'=>'sreset','usb'=>1);
	$zwaveoptions = array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query($zwavedata),),);
	$zwavecontext=stream_context_create($zwaveoptions);
	$result=file_get_contents($zwaveurl,false,$zwavecontext);
}
function curl($url){
	$headers = array('Content-Type: application/json',);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$data = curl_exec($ch);curl_close($ch);
    return $data;
}
function ValuePost($command) {
	global $domoticzurl;
	$zwaveurl=$domoticzurl.'ozwcp/valuepost.html';
	$zwaveoptions = array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>$command,),);
	$zwavecontext=stream_context_create($zwaveoptions);
	$result=file_get_contents($zwaveurl,false,$zwavecontext);
	return $result;
}
//Functions for webdesign
function Thermometer($name, $boven, $links) {
	global ${'T'.$name},${'TI'.$name},${'TT'.$name}, $time;
	if(${'T'.$name}>0) $temp = ${'T'.$name}; else $temp = 1;
	$hoogte = $temp * 3;
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
	if(isset(${'T'.$name})) {
		echo '<form action="temp.php" method="POST"><div style="position:absolute;top:'.$boven.'px;left:'.$links.'px;cursor:pointer;z-index:10;" onclick="this.form.submit()">
			<input type="hidden" name="sensor" value="'.${'TI'.$name}.'">
			<input type="hidden" name="naam" value="'.$name.'">
			<div class="tmpbg" style="top:'.number_format($top,0).'px;left:8px;width:26px;height:'.number_format($hoogte,0).'px;background:linear-gradient(to bottom, #'.$tcolor.', #'.$dcolor.');"></div>
			<input type="image" src="images/temp.png" height="100px" width="auto"/>
		</form>';
		echo '<div class="grey" style="top:73px;left:5px;width:30px;align:center;">';
		echo number_format(${'T'.$name},1).'</div></form></div>';
	} else echo '<div class="red" style="position:absolute;top:'.$boven.'px;left:'.$links.'px;">'.$name.'</div>';
}
function Schakelaar($name,$kind,$size,$boven,$links) {
	global ${'S'.$name},${'SI'.$name},${'ST'.$name};
	if(isset(${'S'.$name})) {
		echo '<div style="position:absolute;top:'.$boven.'px;left:'.$links.'px;z-index:500;" title="'.strftime("%a %e %b %k:%M:%S", ${'ST'.$name}).'">		
		<form method="POST"><input type="hidden" name="Schakel" value="'.${'SI'.$name}.'">';
		echo ${'S'.$name}=='Off'?'<input type="hidden" name="Actie" value="On"><input type="hidden" name="Naam" value="'.$name.'"><input type="image" src="images/'.$kind.'_Off.png" height="'.$size.'px" width="auto">' 
					   :'<input type="hidden" name="Actie" value="Off"><input type="hidden" name="Naam" value="'.$name.'"><input type="image" src="images/'.$kind.'_On.png" height="'.$size.'px" width="auto">';
		echo '</form></div>';
	} else echo '<div class="red" style="position:absolute;top:'.$boven.'px;left:'.$links.'px;">'.$name.'</div>';
}
function Dimmer($name,$size,$boven,$links) {
	global ${'D'.$name},${'DI'.$name},${'Dlevel'.$name},${'DT'.$name};
	echo '<div id="D'.$name.'" class="dimmer" style="display:none;">
				<form method="POST" action="floorplan.php" oninput="level.value = dimlevel.valueAsNumber">
    			<div style="position:absolute;top:-5px;left:30px;z-index:1000;"><h2>'.ucwords($name).': '.round(${'Dlevel'.$name},0).'%</h2><input type="hidden" name="Naam" value="'.$name.'"><input type="hidden" name="dimmer" value="'.${'DI'.$name}.'"></div>
				<div style="position:absolute;top:100px;left:30px;z-index:1000;"><input type="image" name="dimleveloff" value ="0" src="images/Light_Off.png" width="90px" height="90px"/></div>
				<div style="position:absolute;top:100px;left:265px;z-index:1000;"><input type="image" name="dimwake" value ="100" src="images/Wakeup.png" width="90px" height="90px"/><input type="hidden" name="dimwakelevel" value="'.${'Dlevel'.$name}.'"></div>
				<div style="position:absolute;top:100px;left:150px;z-index:1000;"><input type="image" name="dimsleep" value ="100" src="images/Sleepy.png" width="90px" height="90px"/></div>
				<div style="position:absolute;top:100px;left:385px;z-index:1000;"><input type="image" name="dimlevelon" value ="100" src="images/Light_On.png" width="90px" height="90px"/></div>
				<div style="position:absolute;top:210px;left:10px;z-index:1000;">';
				$levels=array(1,2,3,4,5,6,7,8,9,10,12,14,16,18,20,22,24,26,28,30,32,35,40,45,50,55,60,65,70,75,80,85,90,95,100);
				foreach($levels as $level) {
					if(${'Dlevel'.$name}==$level) echo '<input type="submit" name="dimlevel" value="'.$level.'"/ style="background-color:#5fed5f;background:linear-gradient(to bottom, #5fed5f, #017a01);">';
					else echo '<input type="submit" style="font-size:300%;padding:0px;text-align:center;" name="dimlevel" value="'.$level.'"/>';
				}
				echo '</div></form>
				<div style="position:absolute;top:5px;right:5px;z-index:1000;"><a href=\'javascript:navigator_Go("");\'><img src="images/close.png" width="72px" height="72px"/></a></div>
			</div>';
	if(isset(${'D'.$name})) {
		echo '<div style="position:absolute;top:'.$boven.'px;left:'.$links.'px;">
		<a href=\'javascript:navigator_Go("#");\'  onclick="toggle_visibility(\'D'.$name.'\');" style="text-decoration:none">';
		echo ${'D'.$name}=='Off'?'<input type="image" src="images/Light_Off.png" height="'.$size.'px" width="auto">'
								:'<input type="image" src="images/Light_On.png" height="'.$size.'px" width="auto"><div style="position:absolute;top:20px;left:14px;width:42px;color:#333;text-align:center">'.${'Dlevel'.$name}.'</div>';
		echo '</a></div>';
	} else echo '<div class="red" style="position:absolute;top:'.$boven.'px;left:'.$links.'px;">'.$name.'</div>';

}
function Smokedetector($name,$size,$boven,$links) {
	global ${'S'.$name},${'SI'.$name},${'ST'.$name};
	echo '<div style="position:absolute;top:'.$boven.'px;left:'.$links.'px;z-index:-10;" title="'.strftime("%a %e %b %k:%M:%S", ${'ST'.$name}).'">
	<form method="POST"><input type="hidden" name="Schakel" value="'.${'SI'.$name}.'"><input type="hidden" name="Naam" value="'.$name.'">';
	echo ${'S'.$name}=='Off'?'<img src="images/smokeoff.png" width="36" height="36">'
	                  :'<input type="hidden" name="Actie" value="Off"><input type="image" src="images/smokeon.png" height="'.$size.'px" width="auto">';
	echo '</form></div>';
	echo ${'SB'.$name}<40?'<div style="position:absolute;top:'.$boven.'px;left:'.$links.'px;width:36px;height:36px;background:rgba(255, 0, 0, 0.7);z-index:-11;"></div>':'';
}
function Timestamp($name,$draai,$boven,$links) {
	global ${'S'.$name},${'ST'.$name};
	if(isset(${'S'.$name})) {
		echo '<div class="stamp" style="top:'.$boven.'px;left:'.$links.'px;font-size:120%;z-index:100;transform:rotate('.$draai.'deg);-webkit-transform:rotate('.$draai.'deg);">'.strftime("%k:%M",${'ST'.$name}).'</div>';
	} else echo '<div class="red" style="position:absolute;top:'.$boven.'px;left:'.$links.'px;">'.$name.'</div>';
}
function Secured($boven, $links, $breed, $hoog) {
	echo '<div class="secured" style="top:'.$boven.'px;left:'.$links.'px;width:'.$breed.'px;height:'.$hoog.'px;"></div>';
}
function Motion($boven, $links, $breed, $hoog) {
	global $Sthuis, $Swakker;
	if($Sweg=='On'||$Sslapen=='On') echo '<div class="motionr" style="top:'.$boven.'px;left:'.$links.'px;width:'.$breed.'px;height:'.$hoog.'px;"></div>';
								 else echo '<div class="motion" style="top:'.$boven.'px;left:'.$links.'px;width:'.$breed.'px;height:'.$hoog.'px;"></div>';
}
function Blinds($name, $size, $boven, $links) {
	global ${'S'.$name},${'SI'.$name},${'ST'.$name};
	if(isset(${'S'.$name})) {
		echo '<div style="position:absolute;top:'.$boven.'px;left:'.$links.'px;z-index:500;" title="'.strftime("%a %e %b %k:%M:%S", ${'ST'.$name}).'">		
		<form method="POST"><input type="hidden" name="Schakel" value="'.${'SI'.$name}.'"><input type="hidden" name="Naam" value="'.$name.'"><input type="hidden" name="Actie" value="Off">';
		echo ${'S'.$name}=='Closed'?'<input type="image" src="images/arrowgreenup.png" height="'.$size.'px" width="auto">':'<input type="image" src="images/arrowup.png" height="'.$size.'px" width="auto">';
		echo '</form><br/>';
		echo '<form method="POST"><input type="hidden" name="Schakel" value="'.${'SI'.$name}.'"><input type="hidden" name="Naam" value="'.$name.'"><input type="hidden" name="Actie" value="On">';
		echo ${'S'.$name}=='Open'?'<input type="image" src="images/arrowgreendown.png" height="'.$size.'px" width="auto">':'<input type="image" src="images/arrowdown.png" height="'.$size.'px" width="auto">';
		echo '</form></div>';
	} else echo '<div class="red" style="position:absolute;top:'.$boven.'px;left:'.$links.'px;">'.$name.'</div>';
}
