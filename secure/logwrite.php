<?php
if(isset($_REQUEST['device'])){
	if($_REQUEST['device']=='temp') {
		$stamp=sprintf("%s",date("Y-m-d H:i"));
		$db=new mysqli('localhost','kodi','kodi','domotica');
		if($db->connect_errno > 0){ die('Unable to connect to database [' . $db->connect_error . ']');}
		$buiten=$_REQUEST['buiten']; 
		$living=$_REQUEST['living']; 
		$badkamer=$_REQUEST['badkamer']; 
		$kamer=$_REQUEST['kamer']; 
		$tobi=$_REQUEST['tobi']; 
		$alex=$_REQUEST['alex']; 
		$zolder=$_REQUEST['zolder']; 
		$query="INSERT INTO `temp` (`stamp`,`buiten`,`living`,`badkamer`,`kamer`,`tobi`,`alex`,`zolder`) VALUES ('$stamp','$buiten','$living','$badkamer','$kamer','$tobi','$alex','$zolder');";
		if(!$result = $db->query($query)) { die('There was an error running the query ['.$query .' - ' . $db->error . ']');}
		$db->close();
	}
	elseif(isset($_REQUEST['value'])&&isset($_REQUEST['table'])) logwrite($_REQUEST['device'],$_REQUEST['value'],$_REQUEST['table']);
}

function logwrite($device,$value,$table) {
	$time=microtime(true);$dFormat="Y-m-d H:i:s";$mSecs=$time-floor($time);$mSecs=substr(number_format($mSecs,3),1);
	$stamp = sprintf("%s%s",date($dFormat),$mSecs);
	$db = new mysqli('localhost','kodi','kodi','domotica');
	if($db->connect_errno > 0){ die('Unable to connect to database [' . $db->connect_error . ']');}
	$query = "INSERT INTO `$table` (`stamp`,`device`,`value`) VALUES ('$stamp','$device','$value');";
	if(!$result = $db->query($query)) { die('There was an error running the query ['.$query .' - ' . $db->error . ']');}
	$db->close();
}