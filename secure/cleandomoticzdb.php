<?php 
$db = new SQLite3('/volume1/@appstore/domoticz/var/domoticz.db');
$clean = strftime("%G-%m-%d %k:%M:%S",time()-86400);
$tables = array('LightingLog','Meter','Meter_Calendar','MultiMeter','MultiMeter_Calendar','Percentage','Percentage_Calendar','Rain','Rain_Calendar','Temperature','Temperature_Calendar','UV','UV_Calendar','Wind','Wind_Calendar');
foreach($tables as $table) {
	$query=$db->exec("DELETE FROM $table WHERE DeviceRowID not in (select ID from DeviceStatus where Used = 1)");
	if ($query) {
		$rows = $db->changes();
		if($rows>0) echo $rows." rows removed from $table<br/>";
	}
	$query=$db->exec("DELETE FROM $table WHERE Date < '$clean'");
	if ($query) {
		$rows=$db->changes();
		if($rows>0) echo $rows." rows removed from $table<br/>";
	}
}
$tables = array('Temperature','Temperature_Calendar','Meter','Meter_Calendar','LightingLog');
foreach($tables as $table) {
	$query=$db->exec("DELETE FROM $table");
	if ($query) {
		$rows=$db->changes();
		if($rows>0) echo $rows." rows removed from $table<br/>";
	}
}

$sql = 'VACUUM;';
if(!$result = $db->exec($sql)){ die('There was an error running the query [' . $db->error . ']');}