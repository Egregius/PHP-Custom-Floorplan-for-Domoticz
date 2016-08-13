<?php error_reporting(E_ALL);ini_set("display_errors","on");setlocale(LC_ALL,'nl_NL.UTF-8');setlocale(LC_ALL,'nld_nld');date_default_timezone_set('Europe/Brussels');
$db = new mysqli('localhost', 'kodi', 'kodi', 'domotica');
if($db->connect_errno > 0){ die('Unable to connect to database [' . $db->connect_error . ']');}

$time=time();
$eendag=$time-86400;
$eendagstr=strftime("%Y-%m-%d %H:%M:%S",$eendag);
	
$sql = "select left(stamp,13) as stamp,min(buiten) as buiten_min,max(buiten) as buiten_max,avg(buiten) as buiten_avg,min(living) as living_min,max(living) as living_max,avg(living) as living_avg,min(badkamer) as badkamer_min,max(badkamer) as badkamer_max,avg(badkamer) as badkamer_avg,min(kamer) as kamer_min,max(kamer) as kamer_max,avg(kamer) as kamer_avg,min(tobi) as tobi_min,max(tobi) as tobi_max,avg(tobi) as tobi_avg,min(alex) as alex_min,max(alex) as alex_max,avg(alex) as alex_avg,min(zolder) as zolder_min,max(zolder) as zolder_max,avg(zolder) as zolder_avg from temp	group by left(stamp,13)	order by stamp desc limit 0,100";
	if(!$result = $db->query($sql)) { die('There was an error running the query ['.$sql .' - ' . $db->error . ']');}
	$values=array();
	while ($row = $result->fetch_assoc()) $values[]=$row;$result->free();
	foreach($values as $value) {
		$stamp=$value['stamp'];
		$buiten_min=$value['buiten_min'];$buiten_max=$value['buiten_max'];$buiten_avg=$value['buiten_avg'];
		$living_min=$value['living_min'];$living_max=$value['living_max'];$living_avg=$value['living_avg'];
		$badkamer_min=$value['badkamer_min'];$badkamer_max=$value['badkamer_max'];$badkamer_avg=$value['badkamer_avg'];
		$kamer_min=$value['kamer_min'];$kamer_max=$value['kamer_max'];$kamer_avg=$value['kamer_avg'];
		$tobi_min=$value['tobi_min'];$tobi_max=$value['tobi_max'];$tobi_avg=$value['tobi_avg'];
		$alex_min=$value['alex_min'];$alex_max=$value['alex_max'];$alex_avg=$value['alex_avg'];
		$zolder_min=$value['zolder_min'];$zolder_max=$value['zolder_max'];$zolder_avg=$value['zolder_avg'];
		$query = "INSERT INTO `temp_hour` (`stamp`,`buiten_min`,`buiten_max`,`buiten_avg`,`living_min`,`living_max`,`living_avg`,`badkamer_min`,`badkamer_max`,`badkamer_avg`,`kamer_min`,`kamer_max`,`kamer_avg`,`tobi_min`,`tobi_max`,`tobi_avg`,`alex_min`,`alex_max`,`alex_avg`,`zolder_min`,`zolder_max`,`zolder_avg`) VALUES ('$stamp','$buiten_min','$buiten_max','$buiten_avg','$living_min','$living_max','$living_avg','$badkamer_min','$badkamer_max','$badkamer_avg','$kamer_min','$kamer_max','$kamer_avg','$tobi_min','$tobi_max','$tobi_avg','$alex_min','$alex_max','$alex_avg','$zolder_min','$zolder_max','$zolder_avg') ON DUPLICATE KEY UPDATE `buiten_min`='$buiten_min',`buiten_max`='$buiten_max',`buiten_avg`='$buiten_avg',`living_min`='$living_min',`living_max`='$living_max',`living_avg`='$living_avg',`badkamer_min`='$badkamer_min',`badkamer_max`='$badkamer_max',`badkamer_avg`='$badkamer_avg',`kamer_min`='$kamer_min',`kamer_max`='$kamer_max',`kamer_avg`='$kamer_avg',`tobi_min`='$tobi_min',`tobi_max`='$tobi_max',`tobi_avg`='$tobi_avg',`alex_min`='$alex_min',`alex_max`='$alex_max',`alex_avg`='$alex_avg',`zolder_min`='$zolder_min',`zolder_max`='$zolder_max',`zolder_avg`='$zolder_avg';";
		if(!$result = $db->query($query)) { die('There was an error running the query ['.$query .' - ' . $db->error . ']');}
	}
$sql = "select left(stamp,10) as stamp,min(buiten) as buiten_min,max(buiten) as buiten_max,avg(buiten) as buiten_avg,min(living) as living_min,max(living) as living_max,avg(living) as living_avg,min(badkamer) as badkamer_min,max(badkamer) as badkamer_max,avg(badkamer) as badkamer_avg,min(kamer) as kamer_min,max(kamer) as kamer_max,avg(kamer) as kamer_avg,min(tobi) as tobi_min,max(tobi) as tobi_max,avg(tobi) as tobi_avg,min(alex) as alex_min,max(alex) as alex_max,avg(alex) as alex_avg,min(zolder) as zolder_min,max(zolder) as zolder_max,avg(zolder) as zolder_avg from temp	group by left(stamp,10)	order by stamp desc limit 0,100";
	if(!$result = $db->query($sql)) { die('There was an error running the query ['.$sql .' - ' . $db->error . ']');}
	$values=array();
	while ($row = $result->fetch_assoc()) $values[]=$row;$result->free();
	foreach($values as $value) {
		$stamp=$value['stamp'];
		$buiten_min=$value['buiten_min'];$buiten_max=$value['buiten_max'];$buiten_avg=$value['buiten_avg'];
		$living_min=$value['living_min'];$living_max=$value['living_max'];$living_avg=$value['living_avg'];
		$badkamer_min=$value['badkamer_min'];$badkamer_max=$value['badkamer_max'];$badkamer_avg=$value['badkamer_avg'];
		$kamer_min=$value['kamer_min'];$kamer_max=$value['kamer_max'];$kamer_avg=$value['kamer_avg'];
		$tobi_min=$value['tobi_min'];$tobi_max=$value['tobi_max'];$tobi_avg=$value['tobi_avg'];
		$alex_min=$value['alex_min'];$alex_max=$value['alex_max'];$alex_avg=$value['alex_avg'];
		$zolder_min=$value['zolder_min'];$zolder_max=$value['zolder_max'];$zolder_avg=$value['zolder_avg'];
		$query = "INSERT INTO `temp_day` (`stamp`,`buiten_min`,`buiten_max`,`buiten_avg`,`living_min`,`living_max`,`living_avg`,`badkamer_min`,`badkamer_max`,`badkamer_avg`,`kamer_min`,`kamer_max`,`kamer_avg`,`tobi_min`,`tobi_max`,`tobi_avg`,`alex_min`,`alex_max`,`alex_avg`,`zolder_min`,`zolder_max`,`zolder_avg`) VALUES ('$stamp','$buiten_min','$buiten_max','$buiten_avg','$living_min','$living_max','$living_avg','$badkamer_min','$badkamer_max','$badkamer_avg','$kamer_min','$kamer_max','$kamer_avg','$tobi_min','$tobi_max','$tobi_avg','$alex_min','$alex_max','$alex_avg','$zolder_min','$zolder_max','$zolder_avg') ON DUPLICATE KEY UPDATE `buiten_min`='$buiten_min',`buiten_max`='$buiten_max',`buiten_avg`='$buiten_avg',`living_min`='$living_min',`living_max`='$living_max',`living_avg`='$living_avg',`badkamer_min`='$badkamer_min',`badkamer_max`='$badkamer_max',`badkamer_avg`='$badkamer_avg',`kamer_min`='$kamer_min',`kamer_max`='$kamer_max',`kamer_avg`='$kamer_avg',`tobi_min`='$tobi_min',`tobi_max`='$tobi_max',`tobi_avg`='$tobi_avg',`alex_min`='$alex_min',`alex_max`='$alex_max',`alex_avg`='$alex_avg',`zolder_min`='$zolder_min',`zolder_max`='$zolder_max',`zolder_avg`='$zolder_avg';";
		if(!$result = $db->query($query)) { die('There was an error running the query ['.$query .' - ' . $db->error . ']');}
	}
	//$sql = "delete from temp where stamp < '$eendagstr'";
	//if(!$result = $db->query($sql)) { die('There was an error running the query ['.$sql .' - ' . $db->error . ']');}
	
	echo '<hr>';
