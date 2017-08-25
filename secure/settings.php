<?php
date_default_timezone_set('Europe/Brussels');
$timediff=0;$Usleep=10000;
$authenticated=false;
$home=false;
$log=true;$LogFile='/var/log/floorplanlog.log';
$time=$_SERVER['REQUEST_TIME'];$offline=$time-300;$eendag=$time-82800;
$page=basename($_SERVER['PHP_SELF']);
if(strpos($_SERVER['HTTP_USER_AGENT'],'iPhone')!==false)$udevice='iPhone';
elseif(strpos($_SERVER['HTTP_USER_AGENT'],'iPad')!==false)$udevice='iPad';
elseif(strpos($_SERVER['HTTP_USER_AGENT'],'Macintosh')!==false)$udevice='Mac';
else $udevice='other';
if(substr($_SERVER['REMOTE_ADDR'],0,10)=='192.168.2.')$local=true;else $local=false;
$users=array('Guy'=>'wachtwoord','Kirby'=>'wachtwoord','Tobi'=>'wachtwoord');
if(isset($_POST['logout'])){
	setcookie("cookiename",NULL,time()-86400,'/');
	telegram($user.' logged out',true);
	header("Location:/index.php");
	die("Redirecting to:/index.php");
}
if(getenv('HTTP_CLIENT_IP'))$ipaddress=getenv('HTTP_CLIENT_IP');
		elseif(getenv('HTTP_X_FORWARDED_FOR'))$ipaddress=getenv('HTTP_X_FORWARDED_FOR');
		elseif(getenv('HTTP_X_FORWARDED'))$ipaddress=getenv('HTTP_X_FORWARDED');
		elseif(getenv('HTTP_X_REAL_IP'))$ipaddress=getenv('HTTP_X_REAL_IP');
		elseif(getenv('HTTP_FORWARDED_FOR'))$ipaddress=getenv('HTTP_FORWARDED_FOR');
		elseif(getenv('HTTP_FORWARDED'))$ipaddress=getenv('HTTP_FORWARDED');
		elseif(getenv('REMOTE_ADDR'))$ipaddress=getenv('REMOTE_ADDR');
		else $ipaddress='UNKNOWN';

if(isset($_REQUEST['username'])&&isset($_REQUEST['password'])){
	$subuser=$_REQUEST['username'];
	$subpass=$_REQUEST['password'];
	if($users[$subuser]==$subpass&&strlen($subuser)>=3&&strlen($subuser)<=5&&strlen($subpass)>=7&&strlen($subpass)<=28){
		echo 'OK';
		logwrite(print_r($_SERVER,true));
		koekje($subuser,time()+31536000);
		telegram($subuser.' logged in from '.$ipaddress,true);
		header("Location: ".$_SERVER['REQUEST_URI']);
		die("Redirecting to: ".$_SERVER['REQUEST_URI']);
	}else{
		$msg="Home Failed login attempt: ";
		if(isset($subuser))$msg.=" - USER=".$subuser;
		if(isset($subpass))$msg.=" - PSWD=".$subpass;

		$msg.=" - IP=".$ipaddress;
		if(isset($_SERVER['REQUEST_URI']))$msg.=" - REQUEST_URI=".$_SERVER['REQUEST_URI'];
		if(isset($_SERVER['HTTP_USER_AGENT']))$msg.=" - HTTP_USER_AGENT=".$_SERVER['HTTP_USER_AGENT'];
		logwrite($msg);
		telegram($msg,false);
		echo 'Wrong password!<br>Try again in 10 minutes.<br>After second fail you are blocked for a week!';
	}
}
if(isset($_COOKIE["cookiename"])){
		$user=$_COOKIE["cookiename"];
		$homes=array('Guy','Kirby','Tobi');
		if(in_array($user,$homes)){$authenticated=true;$home=true;$Usleep=80000;}
}else{
	if($_SERVER['PHP_SELF']!='/index.php'){
		header("Location:/index.php");die("Redirecting to:/index.php");}
	echo '<form method="POST">
		Username
		<input type="text" name="username" size="40"/><br/><br/>
		Password <input type="password" name="password" size="40"/><br/><br/>
		<input type="submit" value="inloggen"/>
		</form>';
}
function koekje($user,$expirytime){
	setcookie("cookiename",$user,$expirytime,'/');
}
function telegram($msg,$silent=true,$to=1){
	$msg=str_replace('__',PHP_EOL,$msg);
	$telegrambot='123456789:AAEZ-xCRhO-RBfUqICiJs8q9A_3YIr9irxI';
	$telegramchatid=123456789;
	$telegramchatid2=123456789;
	for($x=1;$x<=10;$x++){
		$result=json_decode(file_get_contents('https://api.telegram.org/bot'.$telegrambot.'/sendMessage?chat_id='.$telegramchatid.'&text='.urlencode($msg).'&disable_notification='.$silent),true);
		if(isset($result['ok']))
			if($result['ok']===true){lg('telegram sent to 1: '.$msg);break;}
			else {lg('telegram sent failed');sleep($x*3);}
	}
	if($to==2)
		for($x=1;$x<=10;$x++){
			$result=json_decode(file_get_contents('https://api.telegram.org/bot'.$telegrambot.'/sendMessage?chat_id='.$telegramchatid2.'&text='.$msg.'&disable_notification='.$silent,true));
			if(isset($result['ok']))
				if($result['ok']===true){lg('telegram sent to 2: '.$msg);break;}
				else lg('telegram sent failed');sleep($x*3);
		}
}
function lg($msg){
	//file_get_contents('http://192.168.2.2:8080/json.htm?type=command&param=addlogmessage&message='.urlencode('=> '.$msg));
	$time    = microtime(true);
	$dFormat = "Y-m-d H:i:s";
	$mSecs   =  $time - floor($time);
	$mSecs   =  substr(number_format($mSecs,3),1);
	$fp = fopen('/var/log/floorplanlog.txt',"a+");
	fwrite($fp, sprintf("%s%s %s \n", date($dFormat), $mSecs, $msg));
	fclose($fp);
}
function logwrite($msg,$msg2=NULL){
	global $LogFile;
	$time=microtime(true);
	$dFormat="Y-m-d H:i:s";
	$mSecs=$time-floor($time);
	$mSecs=substr(number_format($mSecs,3),1);
	$fp=fopen($LogFile,"a+");
	fwrite($fp,sprintf("%s,000 %s %s\n",date($dFormat),$msg,$msg2));
	fclose($fp);
}
function pingDomain($domain,$port){$file=fsockopen($domain,$port,$errno,$errstr,1);$status=0;if(!$file)$status=-1;else{fclose($file);$status=1;}return $status;}
?>
