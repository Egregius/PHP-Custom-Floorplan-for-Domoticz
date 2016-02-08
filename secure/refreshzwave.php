#!/usr/bin/php
<?php
include 'functions.php';
$devices=json_decode(file_get_contents($domoticzurl.'json.htm?type=openzwavenodes&idx='.$zwaveidx),true);
function RefreshZwave2($node) {
	global $domoticzurl;
	$zwaveurl=$domoticzurl.'ozwcp/refreshpost.html';
	$zwavedata=array('fun'=>'racp','node'=>$node);
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
function ZwaveHasnodefailed	($node) {
	global $domoticzurl;
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
if(!empty($argv[1])&&!empty($argv[2])) {
	RefreshZwave2($argv[1],$argv[2]);
	//$mc->set('RefreshZwave'.$argv[1], $time);
}
else {
	logwrite("RefreshZwave: no idx or name defined");
}
sleep(3);
if($mc->get('deadnodes')<$eenmin) {
	foreach($devices as $node=>$data) {
		if ($node == "result") {
		foreach($data as $index=>$eltsNode) {
		  if ($eltsNode["State"] == "Dead") {
			telegram('Node '.$eltsNode['NodeID'].' '.$eltsNode['Description'].' ('.$eltsNode['Name'].') marked as dead, reviving '.ZwaveHasnodefailed($eltsNode['NodeID']));
			sleep(2);
			Zwavecancelaction();
			//shell_exec('/var/www/secure/reboot.sh');
		  }
		}
	 }
	}
	$mc->set('deadnodes',$time);
}
