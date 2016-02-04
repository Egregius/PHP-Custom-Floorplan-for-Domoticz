<?php
require '/var/www/secure/gcal/autoload.php';
define('APPLICATION_NAME', 'Domoticz');
define('CREDENTIALS_PATH', '/var/www/secure/gcal/gcal.token.json');
define('CLIENT_SECRET_PATH', '/var/www/secure/gcal/gcal.json');
define('SCOPES', implode(' ', array(Google_Service_Calendar::CALENDAR_READONLY)));
if (php_sapi_name() != 'cli') {throw new Exception('This application must be run on the command line.');}
function getClient() {
  $client = new Google_Client();
  $client->setApplicationName(APPLICATION_NAME);
  $client->setScopes(SCOPES);
  $client->setAuthConfigFile(CLIENT_SECRET_PATH);
  $client->setAccessType('offline');
  $credentialsPath = CREDENTIALS_PATH;
  if (file_exists($credentialsPath)) {
    $accessToken = file_get_contents($credentialsPath);
  } else {
    $authUrl = $client->createAuthUrl();
    printf("Open the following link in your browser:\n%s\n", $authUrl);
    print 'Enter verification code: ';
    $authCode = trim(fgets(STDIN));
    $accessToken = $client->authenticate($authCode);
    if(!file_exists(dirname($credentialsPath))) {
      mkdir(dirname($credentialsPath), 0700, true);
    }
    file_put_contents($credentialsPath, $accessToken);
    printf("Credentials saved to %s\n", $credentialsPath);
  }
  $client->setAccessToken($accessToken);

  if ($client->isAccessTokenExpired()) {
    $client->refreshToken($client->getRefreshToken());
    file_put_contents($credentialsPath, $client->getAccessToken());
  }
  return $client;
}
$client = getClient();
$service = new Google_Service_Calendar($client);
$optParams = array('maxResults' => 5,'orderBy' => 'startTime','singleEvents' => TRUE,'timeMin' => date('c'),);
$results = $service->events->listEvents($calendarId, $optParams);

if (count($results->getItems()) > 0) {
  foreach ($results->getItems() as $event) {
    if(isset($event->start->dateTime)) $start = strtotime($event->start->dateTime);
    if (empty($start)) {
      $start = strtotime($event->start->date);
    }
	$datetime = strftime("%a %e %b %k:%M:%S", $start);
	if($start<=$vijfsec){
		$user='GCal';
		logwrite('GCal Event: '.$event->getSummary().' at '.$datetime);
		$item = explode(" ", $event->getSummary());
		$action = strtolower($item[0]);
		if($action=="licht") $action = "schakel";
		else if($action=="dim") $action = "dimmer";
		else if($action=="opstaan") $action = "wake";
		else if($action=="slaap") $action = "sleep";
		$place = strtolower($item[1]);
		if(isset($item[2])) {
			$detail = strtolower($item[2]);
			if($detail=="on") $detail = "On";
			else if($detail=="off") $detail = "Off";
			else if($detail=="aan") $detail = "On";
			else if($detail=="uit") $detail = "Off";
		}
		if ($action=="wake") {
			if(${'Dlevel'.$place}<30&&$mc->get('dimmer'.$place)!=2) {$mc->set('dimmer'.$place,2);Dim(${'DI'.$place},${'Dlevel'.$place}+1,$place);}
		} else if($action=="sleep") {
			if(${'Dlevel'.$place}>1&&$mc->get('dimmer'.$place)!=1) {$mc->set('dimmer'.$place,1);Dim(${'DI'.$place},${'Dlevel'.$place}-1,$place);}
		} else if($action=="dimmer") {
			if(${'Dlevel'.$place}!=$detail) Dim(${'DI'.$place},$detail,'GCal '.$place.' ');
		} else if($action=="schakel") {
			if(${'S'.$place}!=$detail) Schakel(${'SI'.$place},$detail,'GCal '.$place.' ');
		} else if($action=="setpoint") {
			$mc->set('setpoint'.${'RI'.$place},2);
			if(${'R'.$place}!=$detail) Udevice(${'RI'.$place},0,$detail,'GCal '.$place.' ');
		}
	}
  }
}