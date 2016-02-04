<?php error_reporting(E_ALL);ini_set("display_errors", "on");
$domoticzurl='http://127.0.0.1:8080/';
$zwaveidx=7;
$n = '';
$devices=json_decode(file_get_contents($domoticzurl.'json.htm?type=openzwavenodes&idx='.$zwaveidx),true);
$topos = json_decode(file_get_contents($domoticzurl.'json.htm?type=command&param=zwavenetworkinfo&idx='.$zwaveidx),true);
foreach($devices['result'] as $device) {
	$id='';
	$id=$device['NodeID'];
	${'n'.$id}=$device['Name'];
	${'d'.$id}=$device['Description'];
	foreach($topos['result']['mesh'] as $topo) {
		$mid='';
		$mid=$topo['nodeID'];
		if($id==$mid) ${'m'.$id}=$topo['seesNodes'];
	}
}

echo '<table border="1" cellpadding="10"><thead><tr><td></td><td></td><td></td></tr></thead><tbody>';
foreach($devices['result'] as $device) {
	$id='';
	$id=$device['NodeID'];
	echo '<tr><td>'.${'d'.$id}.'</td><td>'.${'n'.$id}.'</td><td>';
	$mesh = array();
	$mesh = explode(",",rtrim(${'m'.$id},","));
	foreach($mesh as $sees) {
		if(isset($sees)) echo ${'n'.$sees}.', ';
	}
	echo '</td></tr>';
	unset($mesh,$sees);
}
echo '</table>';
unset($devices,$topos,$device,$topo,$_COOKIE,$_FILES,$domoticzurl,$zwaveidx,$id,$mid);

echo '<hr>VARS:<pre>';
print_r(get_defined_vars());
echo '</pre>';