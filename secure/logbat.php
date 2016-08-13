<?php error_reporting(E_ALL);ini_set("display_errors", "on");
$ctx = stream_context_create(array('http'=>array('timeout' => 7,)));
$domoticz=json_decode(file_get_contents('http://127.0.0.1:8084/json.htm?type=devices&used=true&plan=6',true,$ctx),true);//$domotime=microtime(true)-$start; //&plan=2
$stamp = strftime("%Y-%m-%d",time());
$db = new mysqli('localhost', 'kodi', 'kodi', 'domotica');
if($db->connect_errno > 0){ die('Unable to connect to database [' . $db->connect_error . ']');}
if($domoticz){
	foreach($domoticz['result'] as $dom) {
		if($dom['BatteryLevel']>=0 && $dom['BatteryLevel'] <= 100) {
			$device = $dom['Name'];
			$value = $dom['BatteryLevel'];
			$query = "INSERT INTO `bat` (`stamp`,`device`,`value`) VALUES ('$stamp','$device','$value') ON DUPLICATE KEY update `value` = '$value';";
			if(!$result = $db->query($query)) { die('There was an error running the query ['.$query .' - ' . $db->error . ']');}
		}
	}
	unset($domoticz,$dom);
}
$db->close();