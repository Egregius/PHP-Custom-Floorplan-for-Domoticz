<?php 
$domoticzurl='http://127.0.0.1:8080/';
$zwaveidx=7;
$applepass='password';
$appledevice='appledeviceid';
$appleid='appleid';
$telegrambot='113592115:AZEZ-xCRrO-RBfUsICiJs3q9A_3YIr9irxI';
$telegramchatid=54975423;
$sms=true;
$smsuser='clickateluser';
$smspassword='clickatelpassword';
$smsapi=1234567;
$smstofrom=32475123456;
$log=true;
$calendarId = 'uxe9pqcanqhmyc2u1aeacs16p8@group.calendar.google.com'; //primary
$LogFile = '/var/log/floorplan.log';
$Usleep=150000;
$denon_address = 'http://192.168.0.15';

$authenticated=false;
$token='XqyK2QFyTy8aoYgowzHRsdtkaEbBVgQcqtrf';

$users=array('Guy','Kirby','Tobi');$db=new SQLite3('/var/www/secure/database.db');
if(isset($_COOKIE["HomeEgregius"])&&isset($_COOKIE["HomeEgregiustoken"])) {
	if($_COOKIE["HomeEgregiustoken"]===$token) {
		$user = $_COOKIE["HomeEgregius"];
		if(in_array($user,$users)) $authenticated=true;
		if($user=="Tobi") {
			if(date("N", $_SERVER['REQUEST_TIME'])==1) $authenticated=false;
			if(date("N", $_SERVER['REQUEST_TIME'])==2) $authenticated=false;
			if(date("N", $_SERVER['REQUEST_TIME'])==3 && $_SERVER['REQUEST_TIME']<strtotime('11:00')) $authenticated=false;
			if(date("N", $_SERVER['REQUEST_TIME'])==5) {
				if(date("W", $_SERVER['REQUEST_TIME']) %2 == 0 && $_SERVER['REQUEST_TIME']>strtotime('18:00')) $authenticated=false;
			}
			if(date("N", $_SERVER['REQUEST_TIME'])==6 && date("W", $_SERVER['REQUEST_TIME']) %2 == 1) $authenticated=false;
			if(date("N", $_SERVER['REQUEST_TIME'])==7) {
				if($_SERVER['REQUEST_TIME']>strtotime('20:15')) $authenticated=false;
				if(date("W", $_SERVER['REQUEST_TIME']) %2 == 1) $authenticated=false;
			}
		}
	}
} else $user = 'cron';