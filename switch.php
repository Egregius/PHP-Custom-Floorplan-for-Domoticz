<?php
if(isset($_REQUEST['idx']) && isset($_REQUEST['cmd']) && isset($_REQUEST['comment'])) {
	$domoticzurl='http://127.0.0.1:8080/';
	function Schakel($idx,$cmd,$name=NULL) {
		global $domoticzurl;
		$reply=json_decode(file_get_contents($domoticzurl.'json.htm?type=command&param=switchlight&idx='.$idx.'&switchcmd='.$cmd.'&level=0&passcode='),true);
		if($reply['status']=='OK') $reply='OK';else {$reply='ERROR';}
		return $reply;
	}
	echo 'Schakel: '.Schakel($_REQUEST['idx'],$_REQUEST['cmd'],$_REQUEST['comment']);
	
	echo '<hr>VARS:<pre>';
	print_r(get_defined_vars());
	echo '</pre>';
}