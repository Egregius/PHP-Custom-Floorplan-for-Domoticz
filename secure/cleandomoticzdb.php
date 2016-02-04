<?php 
$dbd = new SQLite3('/home/pi/domoticz/domoticz.db');
$clean = strftime("%G-%m-%d %k:%M:%S",time()-43200);
$tables = array('Temperature','Temperature_Calendar');
foreach($tables as $table) {
	$sql = 'delete FROM '.$table.' WHERE "DeviceRowID" not in (select ID from DeviceStatus where Used = 1) OR "DeviceRowID" in (247, 251,255,257,259,261,357)';
	if(!$result = $dbd->exec($sql)){ die('There was an error running the query [' . $db->error . ']');}
	echo $table.' : '. $dbd->changes().'<br>';
	$sql = 'delete FROM '.$table.' WHERE "Date" < "'.$clean.'"';
	if(!$result = $dbd->exec($sql)){ exit('There was an error running the query [' . $db->error . ']');}
	echo $table.' : '. $dbd->changes().'<br>';
}

$tables = array('LightingLog','Meter','Meter_Calendar','MultiMeter','MultiMeter_Calendar','Percentage','Percentage_Calendar');
foreach($tables as $table) {
	$sql = 'delete FROM '.$table.' WHERE "Date" < "'.$clean.'"';
	if(!$result = $dbd->exec($sql)){ exit('There was an error running the query [' . $db->error . ']');}
	echo 'Clean '.$table.' : '. $dbd->changes().'<br>';
}


$sql = 'VACUUM;';
if(!$result = $dbd->exec($sql)){ die('There was an error running the query [' . $db->error . ']');}
