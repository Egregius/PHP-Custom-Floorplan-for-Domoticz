<?PHP
	error_reporting(0);
	header("Content-type: text/html; charset=utf-8");
	require_once "functions.php";
	require_once "findmyiphone.php";
	try {
		$fmi = new FindMyiPhone($appleid, $applepass);
	} catch (Exception $e) {
		print "Error: ".$e->getMessage();
		exit;
	}
	$fmi->printDevices();