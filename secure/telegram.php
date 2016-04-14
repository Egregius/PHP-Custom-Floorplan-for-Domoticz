#!/usr/bin/php
<?php
error_reporting(E_ALL);ini_set("display_errors", "on");
$silent=false;$type='';
if(isset($_REQUEST['silent'])) $silent = $_REQUEST['silent'];
if(isset($_REQUEST['text'])) {$type='text';$content = $_REQUEST['text'];}
if(isset($_REQUEST['photo'])) {$type='photo';$content = $_REQUEST['photo'];}
if(isset($_REQUEST['video'])) {$type='video';$content = $_REQUEST['video'];$silent=true;}
if(!empty($argv[1])&&!empty($argv[2])) {$type=$argv[1];$content = $argv[2];}
if(!empty($argv[1])&&$argv[1]=="snapshot") {$type='photo';$content = "/run/pikrellcam/mjpeg.jpg";$silent=true;}
echo '<pre>REQUEST=';print_r($_REQUEST);echo '</pre>';
if(isset($type)&&isset($content)) {
	echo 'Type = '.$type.'<br/>Content = '.$content.'<br/>';
	
	$telegrambot='113592115:AAEZ-xCRhO-RBfUqICiJs8q9A_3YIr9irxI';
	$telegramchatid=55975443;
	
	$bot_url    = "https://api.telegram.org/bot$telegrambot/";
	
	if($type=="text") {
		$url        = $bot_url . "sendMessage?chat_id=" . $telegramchatid ;
		$post_fields = array('chat_id'   => $telegramchatid,
			'text'     => $content,
			'disable_notification' => $silent
		);
	}
	else if($type=="photo") {
		$url        = $bot_url . "sendPhoto?chat_id=" . $telegramchatid ;
		$post_fields = array('chat_id'   => $telegramchatid,
			'photo'     => new CURLFile(realpath($content)),
			'disable_notification' => true
		);
	}
	else if($type=="video") {
		$url        = $bot_url . "sendVideo?chat_id=" . $telegramchatid ;
		$post_fields = array('chat_id'   => $telegramchatid,
			'video'     => new CURLFile(realpath($content)),
			'disable_notification' => true
		);
	}

	//echo 'URL = '.$url.'<br/>';
	//echo '<pre>post_fields=';print_r($post_fields);echo '</pre>';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Content-Type:multipart/form-data"
		));
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
		$output = json_decode(curl_exec($ch),true);
		//echo '<pre>Output=';print_r($output);echo '</pre>';
}
