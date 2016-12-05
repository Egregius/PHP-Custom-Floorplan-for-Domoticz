<?php
function lg($msg){curl('http://127.0.0.1:8084/json.htm?type=command&param=addlogmessage&message='.urlencode($msg));}
function Schakel($idx,$cmd,$name=NULL){global $user;lg('>>> '.$user.' switched '.$idx.' '.$name.' '.$cmd);curl('http://127.0.0.1:8084/json.htm?type=command&param=switchlight&idx='.$idx.'&switchcmd='.$cmd.'&level=0&passcode=');}
function Scene($idx,$name=NULL){global $user;lg('>>> '.$user.'	Scene	'.$name.'	'.$cmd.'	'.$reply);curl('http://127.0.0.1:8084/json.htm?type=command&param=switchscene&idx='.$idx.'&switchcmd=On');}
function Dim($idx,$level,$name=NULL){global $user;lg('>>> '.$user.'	Dimmer '.$idx.'	'.$name.'	'.$level);if($level>0&&$level<100) $level=$level+1;curl('http://127.0.0.1:8084/json.htm?type=command&param=switchlight&idx='.$idx.'&switchcmd=Set%20Level&level='.$level);}
function Udevice($idx,$nvalue,$svalue,$name=NULL){global $user;lg('>>> '.$user.'	UDevice '.$idx.'	'.$nvalue.'	'.$level.' '.$svalue);curl('http://127.0.0.1:8084/json.htm?type=command&param=udevice&idx='.$idx.'&nvalue='.$nvalue.'&svalue='.$svalue);}
function Thermometer($name){
	global $weer;
	if(isset($weer[$name]))$temp=$weer[$name];
	//($temp>0)?$temp=$temp:$temp=1;
	$hoogte=$temp*3;if($hoogte>88)$hoogte=88;elseif($hoogte<20)$hoogte=20;
	$top=88-$hoogte;if($top<0)$top=0;$top=$top+5;
	switch($temp){
		case $temp>=22:$tcolor='F00';$dcolor='55F';break;
		case $temp>=20:$tcolor='D12';$dcolor='44F';break;
		case $temp>=18:$tcolor='B24';$dcolor='33F';break;
		case $temp>=15:$tcolor='93B';$dcolor='22F';break;
		case $temp>=10:$tcolor='64D';$dcolor='11F';break;
		default:$tcolor='55F';$dcolor='00F';}
	echo '
	<a href=\'javascript:navigator_Go("temp.php?sensor=998");\'>
		<div class="fix '.$name.'" >
			<div class="fix tmpbg" style="top:'.number_format($top,0).'px;height:'.number_format($hoogte,0).'px;background:linear-gradient(to bottom, #'.$tcolor.', #'.$dcolor.');">
			</div>
			<img src="/images/temp.png" height="100px" width="auto"/>
			<div class="fix center" style="top:73px;left:5px;width:30px;">
				'.number_format($temp,1).'
			</div>
		</div>
	</a>';
}
function Schakelaar($name,$kind){global ${'S'.$name},${'SI'.$name};
	echo '
	<div class="fix '.$name.'">
		<form method="POST">
			<input type="hidden" name="Schakel" value="'.${'SI'.$name}.'"/>
			<input type="hidden" name="Naam" value="'.$name.'"/>';
	echo ${'S'.$name}=='Off'?'
			<input type="hidden" name="Actie" value="On"/>
			<input type="image" src="/images/'.$kind.'_Off.png" id="'.$name.'"/>'
		:'
			<input type="hidden" name="Actie" value="Off">
			<input type="image" src="/images/'.$kind.'_On.png" id="'.$name.'"/>';
	echo '
		</form>
	</div>';
}
function Dimmer($name){
	global ${'D'.$name},${'DI'.$name},${'Dlevel'.$name},${'DT'.$name};
	echo '
	<form method="POST">
		<a href="floorplan.php?setdimmer='.$name.'">
		<div class="fix z '.$name.'">
			<input type="hidden" name="setdimmer" value="'.$name.'"/>';
	echo ${'D'.$name}=='Off'?'
			<input type="image" src="/images/Light_Off.png" class="i70"/>'
	:'
			<input type="image" src="/images/Light_On.png" class="i70"/>
			<div class="fix center dimmerlevel">
				'.${'Dlevel'.$name}.'
			</div>';
	echo '
		</div>
		</a>
	</form>';
}
function Timestamp($name,$draai){global ${'ST'.$name};echo '
	<div class="fix stamp r'.$draai.' t'.$name.'">
		'.strftime("%k:%M",${'ST'.$name}).'
	</div>';}
function Secured($name){echo '
	<div class="fix secured '.$name.'">
	</div>';}
function Motion($name){global $Sweg,$Sslapen;echo ($Sweg=='On'||$Sslapen=='On')?'
	<div class="fix motionr '.$name.'">
	</div>'
	:'
	<div class="fix motion '.$name.'">
	</div>';}
function Blinds($name){global ${'S'.$name},${'SI'.$name};
	echo '
	<div class="fix z '.$name.'">
		<form method="POST">
			<input type="hidden" name="Schakel" value="'.${'SI'.$name}.'"/>
			<input type="hidden" name="Naam" value="'.$name.'"/>
			<input type="hidden" name="Actie" value="Off"/>';
	echo ${'S'.$name}=='Closed'?'
			<input type="image" src="/images/arrowgreenup.png" class="i48"/>':'
			<input type="image" src="/images/arrowup.png" class="i48"/>';
	echo '
		</form><br/>
	<form method="POST">
		<input type="hidden" name="Schakel" value="'.${'SI'.$name}.'"/>
		<input type="hidden" name="Naam" value="'.$name.'"/>
		<input type="hidden" name="Actie" value="On"/>';
	echo ${'S'.$name}=='Open'?'
		<input type="image" src="/images/arrowgreendown.png" class="i48"/>':'
		<input type="image" src="/images/arrowdown.png" class="i48"/>';
	echo '
	</form>
</div>';
}
