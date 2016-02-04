#!/usr/bin/php
<?php
//error_reporting(E_ALL);ini_set("display_errors", "on");
if(isset($_REQUEST['photo'])) {
	$url = 'https://api.telegram.org/bot113592115:AAEZ-xCRhO-RBfUqICiJs8q9A_3YIr9irxI/sendPhoto';
	$data = array('chat_id' => '55975443', 'photo' => $_REQUEST['photo']);
}
else if(isset($_REQUEST['text'])) {
	$url = 'https://api.telegram.org/bot113592115:AAEZ-xCRhO-RBfUqICiJs8q9A_3YIr9irxI/sendMessage';
	$data = array('chat_id' => '55975443', 'text' => $_REQUEST['text']);
}
else {
	echo "Nothing sent";
	exit;
}
$options = array(
	'http' => array(
		'method'  => 'POST',
		'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
		'content' => http_build_query($data),
	),
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
//echo '<pre>'.$result.'</pre>';
