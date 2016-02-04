<?php
include('functions.php');
error_reporting(E_ALL);ini_set("display_errors", "on");

if(isset($_COOKIE["HomeEgregius"])) echo "Script to send IOS notifications<br/>";
if(isset($_REQUEST['bericht'])) {
	include ("findmyiphone.php");
	$fmi = new FindMyiPhone($appleid, $applepass);
	$fmi->playSound($device,$_REQUEST['bericht']);
} else {
?>
<form action="#" method="get" id="form"><input type="text" name= "bericht" value="bericht" size="50"/><input type="submit" name="submit" value="Verzenden" /></form>
<?php
}
