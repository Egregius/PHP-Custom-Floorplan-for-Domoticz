<?php 
require "secure/header.php"; 
require "secure/settings.php"; 
require "secure/functions.php"; 
require "scripts/chart.php";
if($home === true) {
	$db = new mysqli('localhost', 'kodi', 'kodi', 'domotica');
	if($db->connect_errno > 0){ die('Unable to connect to database [' . $db->connect_error . ']');}
	echo '<div style="width:90%">';
	$sensor = 147;
	if(isset($_POST['sensor'])) $sensor=$_POST['sensor'];
	switch($sensor) {
		case 147:$setpoint=12;$radiator=179;$sensornaam = 'living'; break;//Living
		case 246:$setpoint=13;$radiator=13;$sensornaam = 'badkamer'; break;//Badkamer
		case 278:$setpoint=14;$radiator=181;$sensornaam = 'kamer'; break;//Kamer
		case 356:$setpoint=15;$radiator=183;$sensornaam = 'tobi'; break;//Slaapkamer Tobi
		case 293:$setpoint=0;$radiator=0;$sensornaam = 'zolder'; break;//Zolder
		case 244:$setpoint=16;$radiator=203;$sensornaam = 'alex'; break;//Slaapkamer Alex
		case 999:$setpoint=999;$radiator=999;$sensornaam = 'alles'; break;//Slaapkamer Alex
		default:$setpoint=0;$radiator=0;$sensornaam = 'buiten';break;
	}
	$time=time();
	$eendag=$time-86400;$eendagstr=strftime("%Y-%m-%d %H:%M:%S",$eendag);
	$eenweek=$time-86400*7;$eenweekstr=strftime("%Y-%m-%d %H:%M:%S",$eenweek);
	$eenmaand=$time-86400*31;$eenmaandstr=strftime("%Y-%m-%d %H:%M:%S",$eenmaand);
	$sensor = $sensornaam;
	echo '<form method="POST">
		<button name="sensor" value="147" class="btn nav">Living</button>
		<button name="sensor" value="246" class="btn nav">Badk</button>
		<button name="sensor" value="278" class="btn nav">Kamer</button>
		<button name="sensor" value="356" class="btn nav">Tobi</button>
		<button name="sensor" value="244" class="btn nav">Alex</button>
		<button name="sensor" value="293" class="btn nav">Zolder</button>
		<button name="sensor" value="329" class="btn nav">Buiten</button>
		<button name="sensor" value="999" class="btn nav">Alles</button>
	</form>
	<h1>'.$sensornaam.'</h1>';
if($sensor!='alles'){
	$min=$sensor.'_min';
	$max=$sensor.'_max';
	$avg=$sensor.'_avg';
	$query = "SELECT stamp, $sensor from `temp` WHERE stamp > '$eendagstr'";
	if($udevice=='iPad') $args = array('chart'=>'AreaChart','width'=>740,'height'=>200,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graph','colors'=>array('55FF55'),'margins'=>array(0,0,0,45),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'));
	else if($udevice=='Mac') $args = array('chart'=>'AreaChart','width'=>1800,'height'=>800,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#111','chart_div'=>'graph','colors'=>array('55FF55'),'margins'=>array(0,0,0,45),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'));
	else if($udevice=='S4') $args = array('chart'=>'AreaChart','width'=>480,'height'=>200,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graph','colors'=>array('55FF55'),'margins'=>array(0,0,0,45),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'));
	else if($udevice=='Stablet') $args = array('chart'=>'AreaChart','width'=>480,'height'=>200,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graph','colors'=>array('55FF55'),'margins'=>array(0,0,0,45),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'));
	else $args = array('chart'=>'AreaChart','width'=>480,'height'=>200,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graph','colors'=>array('55FF55'),'margins'=>array(0,0,0,45),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'));
	if(!$result = $db->query($query)) { die('There was an error running the query ['.$query .' - ' . $db->error . ']');}
	while ($row = $result->fetch_assoc()) $graph[] = $row;$result->free();
	$chart = array_to_chart($graph,$args);
	echo $chart['script'];
	echo $chart['div'];
	unset($chart);
	echo '<br/>';
	$query = "SELECT stamp, $min, $max, $avg from `temp_hour` where stamp > '$eenweekstr'";
	if($udevice=='iPad') $argshour = array('width'=>740,'height'=>200,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graphhour','colors'=>array('6666FF','FF5555','55FF55'),'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'));
	else if($udevice=='Mac') $argshour = array('width'=>1800,'height'=>800,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graphhour','colors'=>array('6666FF','FF5555','55FF55'),'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'));
	else if($udevice=='S4') $argshour = array('width'=>480,'height'=>200,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graphhour','colors'=>array('6666FF','FF5555','55FF55'),'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'));
	else if($udevice=='Stablet') $argshour = array('width'=>480,'height'=>200,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graphhour','colors'=>array('6666FF','FF5555','55FF55'),'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'));
	else $argshour = array('width'=>480,'height'=>200,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graphhour','colors'=>array('6666FF','FF5555','55FF55'),'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'));
	if(!$result = $db->query($query)) { die('There was an error running the query ['.$query .' - ' . $db->error . ']');}
	while ($row = $result->fetch_assoc()) $graphhour[] = $row;$result->free();
	$charthour = array_to_chart($graphhour,$argshour);
	echo $charthour['script'];
	echo $charthour['div'];
	unset($charthour);
	echo '<br/>';
	$query = "SELECT stamp, $min, $max, $avg from `temp_day` where stamp > '$eenmaandstr'";
	if($udevice=='iPad') $argsday = array('width'=>740,'height'=>200,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graphday','colors'=>array('6666FF','FF5555','55FF55'),'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'));
	else if($udevice=='Mac') $argsday = array('width'=>1800,'height'=>800,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graphday','colors'=>array('6666FF','FF5555','55FF55'),'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'));
	else if($udevice=='S4') $argsday = array('width'=>480,'height'=>200,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graphday','colors'=>array('6666FF','FF5555','55FF55'),'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'));
	else if($udevice=='Stablet') $argsday = array('width'=>480,'height'=>200,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graphday','colors'=>array('6666FF','FF5555','55FF55'),'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'));
	else $argsday = array('width'=>480,'height'=>200,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graphday','colors'=>array('6666FF','FF5555','55FF55'),'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'));
	if(!$result = $db->query($query)) { die('There was an error running the query ['.$query .' - ' . $db->error . ']');}
	while ($row = $result->fetch_assoc()) $graphday[] = $row;$result->free();
	$chartday = array_to_chart($graphday,$argsday);
	echo $chartday['script'];
	echo $chartday['div'];
	unset($chart);
	
	echo '</center></div>';
} else {
	$query = "SELECT * from `temp` WHERE stamp > '$eendagstr'";
	if($udevice=='iPad') $args = array('width'=>740,'height'=>500,'hide_legend'=>false,'responsive'=>true,'background_color'=>'#000','chart_div'=>'graph','colors'=>array('FFFFFF','FF0000','0000FF','33FF33','FFFF44','8888FF','00FFFF'),'margins'=>array(0,0,0,45),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'));
	else if($udevice=='Mac') $args = array('width'=>1800,'height'=>900,'hide_legend'=>false,'responsive'=>false,'background_color'=>'#000','chart_div'=>'graph','colors'=>array('FFFFFF','FF0000','0000FF','33FF33','FFFF44','8888FF','00FFFF'),'margins'=>array(0,110,0,45),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'999999'));
	else if($udevice=='S4') $args = array('width'=>480,'height'=>500,'hide_legend'=>false,'responsive'=>true,'background_color'=>'#000','chart_div'=>'graph','colors'=>array('FFFFFF','FF0000','0000FF','33FF33','FFFF44','8888FF','00FFFF'),'margins'=>array(0,0,0,45),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'));
	else if($udevice=='Stablet') $args = array('width'=>480,'height'=>500,'hide_legend'=>false,'responsive'=>true,'background_color'=>'#000','chart_div'=>'graph','colors'=>array('FFFFFF','FF0000','0000FF','33FF33','FFFF44','8888FF','00FFFF'),'margins'=>array(0,0,0,45),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'));
	else $args = array('width'=>480,'height'=>200,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#000','chart_div'=>'graph','colors'=>array('FFFFFF','FF0000','0000FF','33FF33','FFFF44','8888FF','00FFFF'),'margins'=>array(0,0,0,45),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'));
	if(!$result = $db->query($query)) { die('There was an error running the query ['.$query .' - ' . $db->error . ']');}
	while ($row = $result->fetch_assoc()) $graph[] = $row;$result->free();
	$chart = array_to_chart($graph,$args);
	echo $chart['script'];
	echo $chart['div'];
	unset($chart);
	echo '<br/>';
	$query = "SELECT stamp,buiten_avg as buiten,living_avg as living,badkamer_avg as badkamer,kamer_avg as kamer,tobi_avg as tobi,alex_avg as alex,zolder_avg as zolder from `temp_hour` where stamp > '$eenweekstr'";
	if($udevice=='iPad') $argshour = array('width'=>740,'height'=>200,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#000','chart_div'=>'graphhour','colors'=>array('FFFFFF','FF0000','0000FF','33FF33','FFFF44','8888FF','00FFFF'),'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'));
	else if($udevice=='Mac') $argshour = array('width'=>1800,'height'=>800,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graphhour','colors'=>array('FFFFFF','FF0000','0000FF','33FF33','FFFF44','8888FF','00FFFF'),'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'),'legend_position'=>'bottom');
	else if($udevice=='S4') $argshour = array('width'=>480,'height'=>200,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graphhour','colors'=>array('FFFFFF','FF0000','0000FF','33FF33','FFFF44','8888FF','00FFFF'),'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'),'legend_position'=>'bottom');
	else if($udevice=='Stablet') $argshour = array('width'=>480,'height'=>200,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graphhour','colors'=>array('FFFFFF','FF0000','0000FF','33FF33','FFFF44','8888FF','00FFFF'),'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'),'legend_position'=>'bottom');
	else $argshour = array('width'=>480,'height'=>200,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graphhour','colors'=>array('FFFFFF','FF0000','0000FF','33FF33','FFFF44','8888FF','00FFFF'),'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'),'legend_position'=>'bottom');
	if(!$result = $db->query($query)) { die('There was an error running the query ['.$query .' - ' . $db->error . ']');}
	while ($row = $result->fetch_assoc()) $graphhour[] = $row;$result->free();
	$charthour = array_to_chart($graphhour,$argshour);
	echo $charthour['script'];
	echo $charthour['div'];
	unset($charthour);
	echo '<br/>';
	$query = "SELECT stamp,buiten_avg as buiten,living_avg as living,badkamer_avg as badkamer,kamer_avg as kamer,tobi_avg as tobi,alex_avg as alex,zolder_avg as zolder from `temp_day` where stamp > '$eenmaandstr'";
	if($udevice=='iPad') $argsday = array('width'=>740,'height'=>200,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graphday','colors'=>array('FFFFFF','FF0000','0000FF','33FF33','FFFF44','8888FF','00FFFF'),'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'FFFFFF'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'),'legend_position'=>'bottom');
	else if($udevice=='Mac') $argsday = array('width'=>1800,'height'=>800,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#000','chart_div'=>'graphday','colors'=>array('FFFFFF','FF0000','0000FF','33FF33','FFFF44','8888FF','00FFFF'),'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'FFFFFF'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'),'legend_position'=>'bottom');
	else if($udevice=='S4') $argsday = array('width'=>480,'height'=>200,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graphday','colors'=>array('FFFFFF','FF0000','0000FF','33FF33','FFFF44','8888FF','00FFFF'),'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'FFFFFF'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'),'legend_position'=>'bottom');
	else if($udevice=='Stablet') $argsday = array('width'=>480,'height'=>200,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graphday','colors'=>array('FFFFFF','FF0000','0000FF','33FF33','FFFF44','8888FF','00FFFF'),'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'FFFFFF'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'),'legend_position'=>'bottom');
	else $argsday = array('width'=>480,'height'=>200,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#111','chart_div'=>'graphday','colors'=>array('FFFFFF','FF0000','0000FF','33FF33','FFFF44','8888FF','00FFFF'),'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'FFFFFF'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'),'legend_position'=>'bottom');
	if(!$result = $db->query($query)) { die('There was an error running the query ['.$query .' - ' . $db->error . ']');}
	while ($row = $result->fetch_assoc()) $graphday[] = $row;$result->free();
	$chartday = array_to_chart($graphday,$argsday);
	echo $chartday['script'];
	echo $chartday['div'];
	unset($chart);
}
	$db->close();
} else {
	header("Location: index.php");
	die("Redirecting to: index.php"); 
}

