#!/usr/bin/php
<?php error_reporting(E_ALL);ini_set("display_errors", "on");
$domoticzurl='http://127.0.0.1:8080/';
$zwaveidx=7;
file_get_contents($domoticzurl.'json.htm?type=openzwavenodes&idx='.$zwaveidx);

function ValuePost($command) {
	global $domoticzurl;
	$zwaveurl=$domoticzurl.'ozwcp/valuepost.html';
	$zwaveoptions = array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>$command,),);
	$zwavecontext=stream_context_create($zwaveoptions);
	$result=file_get_contents($zwaveurl,false,$zwavecontext);
	return $result;
}

echo ValuePost('85-CONFIGURATION-config-list-1-62=Green illumination');