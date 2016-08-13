#!/usr/bin/php
<?php error_reporting(E_ALL);ini_set("display_errors","on");date_default_timezone_set('Europe/Brussels');
define('api',"http://127.0.0.1:8084/");define('denon','http://192.168.0.4');define('ctx',stream_context_create(array('http'=>array('timeout' => 7,))));
define('applepass','applepass');define('appledevice','942FIdWFh848REeBNfnZk0sD/ZHxYptWlD2zoKvGC1VYH82kSRqROHYVNeUzmWV');define('appleid','you@me.com');
define('sms',true);define('smsuser','smsuser');define('smspassword','smspassword');define('smsapi',123456);define('smstofrom',32479123456);
$time=$_SERVER['REQUEST_TIME'];define('time',$time);define('eensec',$time-1);define('tweesec',$time-2);define('driesec',$time-3);define('vijfsec',$time-5);define('tiensec',$time-10);define('dertigsec',$time-29);
define('eenmin',$time-57);define('tweemin',$time-118);define('driemin',$time-178);define('vijfmin',$time-298);define('tienmin',$time-598);define('halfuur',$time-1795);
define('eenuur',$time-3598);define('tweeuur',$time-7198);define('drieuur',$time-10795);define('vieruur',$time-14395);define('achtuur',$time-28795);define('twaalfuur',$time-43190);define('eendag',$time-82800);
define('zevenochtend',strtotime('7:00'));define('achtochtend',strtotime('8:00'));define('tienochtend',strtotime('10:00'));define('achtavond',strtotime('20:00'));
$t=microtime(true);$micro=sprintf("%03d",($t-floor($t))*1000);define('stamp',strftime("%Y-%m-%d %H:%M:%S.", $t).$micro);
$c=json_decode(base64_decode($argv[1]),true);$s=json_decode(base64_decode($argv[2]),true);$i=json_decode(base64_decode($argv[3]),true);$t=json_decode(base64_decode($argv[4]),true);$a=$s[key($c)];$devidx=$i[key($c)];
$events=array(1=>'pirliving',2=>'poort',3=>'weg',4=>'slapen',5=>'meldingen',6=>'cron',9=>'camvoordeur',10=>'denon',12=>'cron',13=>'cron',14=>'cron',15=>'cron',16=>'cron',18=>'miniliving1s',19=>'miniliving2s',20=>'miniliving3s',21=>'miniliving4s',22=>'miniliving1l',23=>'miniliving2l',24=>'miniliving3l',25=>'miniliving4l',59=>'belknop',72=>'miniliving1l',73=>'miniliving1s',74=>'miniliving2s',75=>'miniliving3l',76=>'deurbel',78=>'werkbladtuin',87=>'hallzolder',98=>'lichtbadkamer',107=>'badkamervuur',122=>'inkomvoordeur',141=>'SDliving',147=>'living_temp',148=>'raamliving',150=>'pirliving',152=>'pirkeuken',153=>'pirkeuken',154=>'pirgarage',158=>'water',163=>'garageterras',172=>'achterdeur',196=>'SDalex',205=>'minihall1s',206=>'minihall2s',207=>'minihall3s',208=>'minihall4s',209=>'minihall1l',210=>'minihall2l',211=>'minihall3l',212=>'minihall4l',216=>'SDkamer',225=>'SDbadkamer',231=>'deurbadkamer',238=>'SDzolder',246=>'badkamer_temp',247=>'pirinkom',249=>'pirhall',282=>'wasbakkookplaat',308=>'alex',320=>'keukenzolderg',330=>'cron',350=>'SDtobi',379=>'sirene',412=>'cron',437=>'remotewater',483=>'wasbakkookplaat');
$idxs=array(1=>'pirlivingR',88=>'licht_hall',123=>'licht_inkom',124=>'licht_voordeur',175=>'livingZE',177=>'livingZZ',179=>'livingZ',181=>'kamerZ',183=>'tobiZ',203=>'AlexZ',244=>'alex_temp',278=>'kamer_temp',293=>'zolder_temp',356=>'tobi_temp',412=>'zon',449=>'regenpomp',491=>'wasbak');
if(!$m = xsMemcached::Connect('127.0.0.1', 11211)){die('Failed to connect to Memcached server.');}
$msg= stamp.' > '.$devidx.'='.$a;if(isset($idxs[$devidx]))$msg.=' > '.$idxs[$devidx];if(isset($events[$devidx]))$msg.=' > '.$events[$devidx];else $msg.=' - ';$msg.=PHP_EOL;print $msg;
if(isset($events[$devidx]))call_user_func($events[$devidx]);
function achterdeur(){global $a,$s,$i,$t;if($a=="On"){if(($s['weg']=='On'||$s['slapen']=='On')&&$s['meldingen']=='On'){if(cget('timealertachterdeur')<eenmin){sw($i['sirene'],'On');$msg='Achterdeur open om '.$t['achterdeur'];cset('timealertachterdeur',time);telegram($msg,false,'channel');ios($msg);if(sms===true)sms($msg);}}}}
function alex(){global $a,$i;if(filter_var($a,FILTER_SANITIZE_NUMBER_INT)==20) sl($i['alex'],0,'dimmer Alex');logwrite(__FUNCTION__,$a,'log');}
function alles($action,$uit=0){global $s,$i,$t;
	print stamp ." ===== ALLES ===== $action".PHP_EOL;
	if($action=='On'){
		$items=array('eettafel','zithoek','kamer','tobi');foreach($items as $item)if($s[$item]!='On')sl($i[$item],100,$item);
		$items=array('bureel','tvled','kristal','wasbak','keuken','kookplaat','werkblad','inkom','hall','lichtbadkamer1');foreach($items as $item)if($s[$item]!='On')sw($i[$item],'On',$item);
	}elseif($action=='Off'){
		$items=array('denon','bureel','tv','tvled','kristal','eettafel','zithoek','garage','terras','tuin','voordeur','keuken','werkblad','wasbak','kookplaat','sony');foreach($items as $item)if($s[$item]!='Off'&&strtotime($t[$item])<time-$uit)sw($i[$item],'Off',$item);
		$items=array('lichtbadkamer1','lichtbadkamer2','badkamervuur');foreach($items as $item)if($s[$item]!='Off'&&strtotime($t[$item])<time-$uit)sw($i[$item],'Off',$item);
	}elseif($action=='Slapen'){
		$items=array('hall','bureel','denon','tv','tvled','kristal','eettafel','zithoek','garage','terras','tuin','voordeur','keuken','werkblad','wasbak','kookplaat','GroheRed');foreach($items as $item)if($s[$item]!='Off')sw($i[$item],'Off',$item);
		$items=array('pirkeuken','pirgarage','pirinkom','pirhall');foreach($items as $item)if($s[$item]!='Off')ud($i[$item],0,'Off');
	}
}
function badkamer_temp(){cron();}
function badkamervuur(){global $a;RefreshZwave(9,'switch','badkamervuur');logwrite(__FUNCTION__,$a,'log');}
function belknop(){global $a,$s,$i;
	if($a=="On"&&$s['meldingen']=='On'){
		if(cget('timetelegramdeurbel')<eenmin){
			cset('timetelegramdeurbel',time);
			if($s['weg']=='Off'&&$s['slapen']=='Off')sw($i['deurbel'],'On','deurbel');
			if($s['slapen']=='Off')telegram('Deurbel',false,'channel');else telegram('Deurbel',true,'channel');
			file_get_contents('http://192.168.0.11/telegram.php?snapshot=true',false,ctx);
			file_get_contents('http://192.168.0.11/fifo_command.php?cmd=record%20on%205%2055',false,ctx);
			if($s['weg']=='Off'&&$s['slapen']=='Off'&&cget('timetelegrammotionvoordeur')<tweemin)shell_exec('/volume1/web/secure/picam1.sh &');
			if($s['zon']=0)sw($i['voordeur'],'On');
		}
		ud($i['belknop'],0,'Off','belknop reset');
	}
	logwrite(__FUNCTION__,$a,'log');
}
function camvoordeur(){global $a,$s,$i;if($s['meldingen']=='On'&&$s['slapen']=='Off'){if(cget('timetelegrammotionvoordeur')<tweemin){cset('timetelegrammotionvoordeur',time);telegram('Motion Cam Voordeur',false,'channel');if($s['weg']=='Off')shell_exec('/volume1/web/secure/picam1.sh');if($s['zon']=0)sw($i['voordeur'],'On');}}logwrite(__FUNCTION__,$a,'log');}
function denon(){global $a,$s,$i;if($a=="On"){if($s['denonpower']=='Off'){sw($i['denonpower'],'On','denonpower');sleep(20);}for($x=0;$x<=10;$x++){sleep(1);$denon=json_decode(json_encode(simplexml_load_string(file_get_contents(denon.'/goform/formMainZone_MainZoneXml.xml?_='.time(),false, ctx))),TRUE);if($denon['ZonePower']['value']!='ON'){file_get_contents(denon.'/MainZone/index.put.asp?cmd0=PutZone_OnOff%2FON&cmd1=aspMainZone_WebUpdateStatus%2F',false,ctx);sleep(1);file_get_contents(denon.'/MainZone/index.put.asp?cmd0=PutZone_OnOff%2FON&cmd1=aspMainZone_WebUpdateStatus%2F&ZoneName=ZONE2',false,ctx);sleep(1);file_get_contents(denon.'/MainZone/index.put.asp?cmd0=PutZone_InputFunction/TUNER',false,ctx);} else break;}}else{file_get_contents(denon.'/MainZone/index.put.asp?cmd0=PutZone_OnOff%2FOFF&cmd1=aspMainZone_WebUpdateStatus%2F&ZoneName=ZONE2',false,ctx);sleep(1);file_get_contents(denon.'/MainZone/index.put.asp?cmd0=PutZone_OnOff%2FOFF&cmd1=aspMainZone_WebUpdateStatus%2F',false,ctx);sleep(5);sw($i['denonpower'],'Off','denonpower');}}
function deurbadkamer(){global $a,$s,$i;if($a=="Open"){if($s['slapen']=='Off'||(time>strtotime('6:00')&&time<strtotime('12:00'))){if($s['lichtbadkamer1']=='Off')sw($i['lichtbadkamer1'],'On','lichtbadkamer1');if($s['lichtbadkamer2']=='On')sw($i['lichtbadkamer2'],'Off','lichtbadkamer2');}else{if($s['lichtbadkamer2']=='Off')sw($i['lichtbadkamer2'],'On','lichtbadkamer2');if($s['lichtbadkamer1']=='On')sw($i['lichtbadkamer1'],'Off','lichtbadkamer1');}}cron();}
function deurbel(){global $a,$i;if($a=="On")sw($i['deurbel'],'Off','Deurbel reset');logwrite(__FUNCTION__,$a,'log');}
function garageterras(){RefreshZwave(24,'switch','garageterras');}
function hallzolder(){RefreshZwave(7,'switch','hallzolder');}
function inkomvoordeur(){RefreshZwave(12,'switch','inkomvoordeur');}
function keukenzolderg(){RefreshZwave(49,'switch','keukenzolderg');}
function lichtbadkamer(){cron();RefreshZwave(8,'switch','lichtbadkamer');}
function living_temp(){cron();}
function minihall1s(){global $a,$s,$i;if($a=="On"){alles('Slapen');if($s['slapen']=='Off')sw($i['slapen'],'On','slapen');}if($s['lichten_auto']=='Off')sw($i['lichten_auto'],'On','lichten auto aan');if($s['luifel']!='Open')sw($i['luifel'],'Off','zonneluifel dicht');}
function minihall2s(){global $i;sw($i['zoldertrap'],'Off','zoldertrap open');}
function minihall3s(){global $s,$i;if($s['hall']=='Off')sw($i['hall'],'On','hall');if($s['slapen']=='On')sw($i['slapen'],'Off','slapen');if($s['zon']<600&&$s['hall']=='Off')sw($i['hall'],'On','hall');}
function minihall4s(){global $i;sw($i['zoldertrap'],'On','zoldertrap toe');}
function minihall1l(){global $s,$i;if($s['slapen']=='On')sw($i['slapen'],'Off','wakker');if($s['hall']=='Off')sw($i['hall'],'On','hall');if($s['lichtbadkamer1']=='Off')sw($i['lichtbadkamer1'],'On','lichtbadkamer1');if($s['kamer']!='On')sl($i['kamer'],100,'dimmer kamer');if($s['tobi']!='On')sl($i['tobi'],100,'dimmer Tobi');if($s['alex']!='On')sl($i['alex'],100,'dimmer Alex');}
function minihall2l(){global $i;sl($i['alex'],2,'Alex');}
function minihall3l(){global $s,$i;$items=array('lichtbadkamer1','lichtbadkamer2','kamer','tobi','alex');foreach($items as $item) if($s[$item]!='Off')sw($i[$item],'Off',$item);}
function minihall4l(){global $i;sl($i['tobi'],18,'dimmer Tobi');cset('dimmertobi',1);}
function miniliving1s(){global $s,$i;
	if($s['denon']=='Off'){sw($i['denon'],'On','Denon');usleep(800000);}
	if($s['tv']=='Off')sw($i['tv'],'On','TV');
	if($s['zon']<100){
		if($s['kristal']=='Off')sw($i['kristal'],'On','kristal');
		if($s['tvled']=='Off')sw($i['tvled'],'On','tvled');
	} elseif($s['zon']>300){
		if($s['kristal']=='On')sw($i['kristal'],'Off','kristal');
		if($s['tvled']=='On')sw($i['tvled'],'Off','tvled');
	}
	file_get_contents(denon.'/MainZone/index.put.asp?cmd0=PutZone_InputFunction/SAT/CBL',false,ctx);usleep(800000);
	file_get_contents(denon.'/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-42.0',false,ctx);
}
function miniliving2s(){global $s,$i;if($s['kodi']=='Off')sw($i['kodi'],'On','Kodi');if($s['denon']=='Off')sw($i['denon'],'On','Denon');if($s['tv']=='Off')sw($i['tv'],'On','TV');if($s['zon']<100){if($s['kristal']=='Off')sw($i['kristal'],'On','kristal');if($s['tvled']=='Off')sw($i['tvled'],'On','tvled');}elseif($s['zon']>300){if($s['kristal']=='On')sw($i['kristal'],'Off','kristal');if($s['tvled']=='On')sw($i['tvled'],'Off','tvled');}file_get_contents(denon.'/MainZone/index.put.asp?cmd0=PutZone_InputFunction/DVD',false,ctx);usleep(800000);file_get_contents(denon.'/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-36.0',false,ctx);}
function miniliving3s(){$denon=json_decode(json_encode(simplexml_load_string(file_get_contents(denon.'/goform/formMainZone_MainZoneXml.xml?_='.time,false,ctx))),TRUE);if($denon){$denon['MasterVolume']['value']=='--'?$setvalue=-55:$setvalue=$denon['MasterVolume']['value'];$setvalue=$setvalue-3;if($setvalue>-10)$setvalue=-10;if($setvalue<-80)$setvalue=-80;$volume=80+$setvalue;usleep(100000);file_get_contents(denon.'/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/'.$setvalue.'.0',false,ctx);}}
function miniliving4s(){$denon=json_decode(json_encode(simplexml_load_string(file_get_contents(denon.'/goform/formMainZone_MainZoneXml.xml?_='.time,false,ctx))),TRUE);if($denon){$denon['MasterVolume']['value']=='--'?$setvalue=-55:$setvalue=$denon['MasterVolume']['value'];$setvalue=$setvalue+3;if($setvalue>-10)$setvalue=-10;if($setvalue<-80)$setvalue=-80;$volume=80+$setvalue;usleep(100000);file_get_contents(denon.'/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/'.$setvalue.'.0',false,ctx);}}
function miniliving1l(){global $s,$i;if($s['denon']=='Off'){sw($i['denon'],'On','Denon');usleep(800000);}if($s['tv']=='On')sw($i['tv'],'Off','TV');if($s['zon']>0){if($s['kristal']=='On')sw($i['kristal'],'Off','Kristal');if($s['tvled']=='On')sw($i['tvled'],'Off','tvled');}file_get_contents(denon.'/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-50.0',false,ctx);usleep(800000);file_get_contents(denon.'/MainZone/index.put.asp?cmd0=PutZone_InputFunction/TUNER',false,ctx);}
function miniliving2l(){global $i;sw($i['bureel'],'Toggle','Bureel');}
function miniliving3l(){global $s,$i;sl($i['eettafel'],9,'dimmer eettafel');if($s['tv']=='On')sw($i['tv'],'Off','TV');if($s['kristal']=='On')sw($i['kristal'],'Off','kristal');if($s['tvled']=='On')sw($i['tvled'],'Off','tvled');file_get_contents(denon.'/MainZone/index.put.asp?cmd0=PutZone_OnOff%2FON&cmd1=aspMainZone_WebUpdateStatus%2F',false,ctx);usleep(800000);file_get_contents(denon.'/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-55.0',false,ctx);usleep(800000);file_get_contents(denon.'/MainZone/index.put.asp?cmd0=PutZone_InputFunction/TUNER',false,ctx);}
function miniliving4l(){global $a,$s,$i;$items=array('pirkeuken','pirgarage','pirinkom','pirhall');foreach($items as $item)if($s[$item]!='Off')ud($i[$item],0,'Off',$item);$items=array('eettafel','zithoek','garage','inkom','hall','keuken','werkblad','wasbak','kookplaat');foreach($items as $item)if($s[$item]!='Off')sw($i[$item],'Off',$item);}
function pirgarage(){global $a,$s,$i,$t;
	if($a=="On"&&(time < tienochtend||time > achtavond||$s['zon']<1200)&&$s['garage']=='Off')sw($i['garage'],'On','garage');
	if(($s['weg']=='On'||$s['slapen']=='On')&&$s['meldingen']=='On'&&strtotime($t['weg'])<driemin&&strtotime($t['slapen'])<driemin){
		if(cget('timealertpirgarage')<eenmin){sw($i['sirene'],'On');$msg='Beweging garage om '.strftime("%H:%M:%S",time);cset('timealertpirgarage',time);telegram($msg,false,'channel');ios($msg);if(sms===true)sms($msg);}
}}
function pirhall(){global $a,$s,$i,$t;
	if($a=="On"){
		if($s['slapen']=='Off'&&$s['hall']=='Off'&&(time < achtochtend||$s['zon']<100))sw($i['hall'],'On','hal');
		if($s['inkom']=='Off'&&(time<achtochtend||$s['zon']<200))sw($i['inkom'],'On','inkom');
		if($s['weg']=='On'&&$s['meldingen']=='On'&&strtotime($t['weg'])<driemin){
			if(cget('timealertpirhall')<eenmin){sw($i['sirene'],'On','SIRENE');$msg='Beweging hall om '.strftime("%H:%M:%S",time);cset('timealertpirhall',time);telegram($msg,false,'channel');ios($msg);if(sms===true)sms($msg);}
}}}
function pirinkom(){global $a,$s,$i,$t;
	if($a=="On"){
		if($s['inkom']=='Off'&&(time<achtochtend||$s['zon']<100))sw($i['inkom'],'On','inkom');
		if($s['slapen']=='Off'&&$s['hall']=='Off'&&(time<achtochtend||$s['zon']<150))sw($i['hall'],'On','hall');
		if(($s['weg']=='On'||$s['slapen']=='On')&&$s['meldingen']=='On'&&strtotime($t['weg'])<driemin&&strtotime($t['slapen'])<driemin){
			if(cget('timealertpirinkom')<eenmin){sw($i['sirene'],'On','SIRENE');$msg='Beweging inkom om '.strftime("%H:%M:%S",time);cset('timealertpirinkom',time);telegram($msg,false,'channel');ios($msg);if(sms===true)sms($msg);}
}}}
function pirkeuken(){global $a,$s,$i,$t;
	if($a=="On"){
		if($s['keuken']=='Off'&&$s['wasbak']=='Off'&&$s['werkblad']=='Off'&&$s['kookplaat']=='Off'&&$s['zon']<400)sw($i['wasbak'],'On','wasbak');
		if(($s['weg']=='On'||$s['slapen']=='On')&&$s['meldingen']=='On'&&strtotime($t['weg'])<driemin&&strtotime($t['slapen'])<driemin){
			if(cget('timealertpirkeuken')<eenmin){sw($i['sirene'],'On','SIRENE');$msg='Beweging keuken om '.strftime("%H:%M:%S",time);cset('timealertpirkeuken',time);telegram($msg,false,'channel');ios($msg);if(sms===true)sms($msg);}
		}
	}
}
function pirliving(){global $a,$s,$i,$t;
	if($a=="On"){
		if($s['denon']=='Off'&&$s['weg']=='Off'&&$s['slapen']=='Off'){
			if($s['zon']<100){if($s['wasbak']=='Off')sw($i['wasbak'],'On','wasbak');if($s['bureel']=='Off')sw($i['bureel'],'On','bureel');}
			miniliving1l();
		}
		if(($s['weg']=='On'||$s['slapen']=='On')&&$s['meldingen']=='On'&&strtotime($t['weg'])<driemin&&strtotime($t['slapen'])<driemin){
			if(cget('timealertpirliving')<eenmin){sw($i['sirene'],'On','SIRENE');cset('timealertpirliving',time);$msg='Beweging living om '.strftime("%H:%M:%S",time);telegram($msg,false,'channel');ios($msg);if(sms===true)sms($msg);}
		}
	}
}
function poort(){global $a,$s,$i,$t;
	if($a=="On"){
		if($s['zon']<600&&$s['garage']=='Off') sw($i['garage'],'On','garage');
		if(($s['weg']=='On'||$s['slapen']=='On')&&$s['meldingen']=='On'&&strtotime($t['weg'])<driemin){
			if(cget('timealertpoort')<eenmin){sw($i['sirene'],'On','SIRENE');$msg='Poort open om '.$t['poort'];cset('timealertpoort',time);telegram($msg,false,'channel');ios($msg);if(sms===true)sms($msg);}
		}
	}
}
function raamliving(){global $a,$s,$i,$t;
	if($a=="On"){
		if(($s['weg']=='On'||$s['slapen']=='On')&&$s['meldingen']=='On'){
			if(cget('timealertraamliving')<eenmin){sw($i['sirene'],'On','SIRENE');$msg='Raam living open om '.$t['raamliving'];cset('timealertraamliving',time);telegram($msg,false,'channel');ios($msg);if(sms===true)sms($msg);}
		}
	}
}
function SDalex(){global $a,$i;if($a=="On"){$msg='Rook gedecteerd bij Alex!';telegram($msg,false,'channel');ios($msg);if(sms===true)sms($msg);resetsecurity($i['SDalex'],'Alex');}}
function SDbadkamer(){global $a,$i;if($a=="On"){$msg='Rook gedecteerd in badkamer!';telegram($msg,false,'channel');ios($msg);if(sms===true)sms($msg);resetsecurity($i['SDbadkamer'],'Badkamer');}}
function SDkamer(){global $a,$i;if($a=="On"){$msg='Rook gedecteerd in slaapkamer!';telegram($msg,false,'channel');ios($msg);if(sms===true)sms($msg);resetsecurity($i['SDkamer'],'Slaapkamer');}}
function SDliving(){global $a,$i;if($a=="On"){$msg='Rook gedecteerd in living!';telegram($msg,false,'channel');ios($msg);if(sms===true)sms($msg);resetsecurity($i['SDliving'],'Living');}}
function SDtobi(){global $a,$i;if($a=="On"){$msg='Rook gedecteerd bij Tobi!';telegram($msg,false,'channel');ios($msg);if(sms===true)sms($msg);resetsecurity($i['SDtobi'],'Tobi');}}
function SDzolder(){global $a,$i;if($a=="On"){$msg='Rook gedecteerd op zolder!';telegram($msg,false,'channel');ios($msg);if(sms===true)sms($msg);resetsecurity($i['SDzolder'],'Zolder');}}
function remotewater(){global $a,$i;if($a=="On") sw($i['water'],'On','water aan');else sw($i['water'],'Off','water uit');}
function sirene(){global $a,$i;if($a=="On"){sw($i['deurbel'],'On','Deurbel sirene');sleep(2);sw($i['sirene'],'Off','sirene');}}
function slapen(){global $a,$s,$i;if($a=="On")alles('Slapen');if($s['lichten_auto']=='Off')sw($i['lichten_auto'],'On','lichten auto aan');if($s['luifel']!='Open')sw($i['luifel'],'Off','zonneluifel dicht');}
function wasbakkookplaat(){RefreshZwave(61,'switch','wasbakkookplaat');}
function water(){global $a,$i;
	//$sleep=filter_var(cget('water'),FILTER_SANITIZE_NUMBER_INT);
	//if($a=="On"){sleep($sleep);sw($i['water'],'Off','function water');}
	//else{if($sleep!=5)cset('water',5);}
}
function weg(){global $a,$s,$i;if($a=="On")alles('Off');else{if($s['poortrf']=='Off')sw($i['poortrf'],'On','Poort RF');}}
function werkbladtuin(){RefreshZwave(22,'switch','werkbladtuin');}
function cron(){global $a,$s,$i,$t;
$weer=unserialize(cget('weer'));
$buienradar=$weer['buien'];$buiten_temp=$weer['buiten_temp'];$wind=$weer['wind'];$windrichting=$weer['windrichting'];$wolken=$weer['wolken'];
if($s['weg']=='On'){if($s['heating']!='Off'&&strtotime($t['heating']) < eenuur){sw($i['heating'],'Off','heating');$s['heating']='Off';}}
//else {if($s['heating']!='On'){sw($i['heating'],'On','heating');$s['heating']='On';}}
$Setkamer=8.0;$setpointkamer=cget('setpointkamer');
if($setpointkamer!=0&&strtotime($t['kamer_set'])<eenuur){cset('setpointkamer',0);$setpointkamer=0;}
if($setpointkamer!=2){
	if($buiten_temp<15&&$s['raamkamer']=='Closed'&&$s['heating']=='On'&&(strtotime($t['raamkamer'])<tweeuur||time>achtavond)){
		$Setkamer=12.0;if(time<zevenochtend||time>achtavond)$Setkamer=16.0;
	}
	if($s['kamer_set']!=$Setkamer){ud($i['kamer_set'],0,$Setkamer,'Rkamer_set');$s['kamer_set']=$Setkamer;}
}	
$Settobi=8.0;$setpointtobi=cget('setpointtobi');
if($setpointtobi!=0&&strtotime($t['tobi_set'])<eenuur){cset('setpointtobi',0);$setpointtobi=0;}
if($setpointtobi!=2){
	if($buiten_temp<15&&$s['raamtobi']=='Closed'&&$s['heating']=='On'&&(strtotime($t['raamtobi'])<tweeuur||time>achtavond)){
		$Settobi=12.0;
		if(date('W')%2==1){
				 if(date('N')==3)if(time>achtavond)$Settobi=16.0;
			elseif(date('N')==4) if(time<zevenochtend||time>achtavond) $Settobi=16.0;
			elseif(date('N')==5) if(time<zevenochtend)$Settobi=16.0;
		}else{
				 if(date('N')==3) if(time>achtavond)$Settobi=16.0;
			elseif(in_array(date('N'),array(4,5,6)))if(time<zevenochtend||time>achtavond) $Settobi=16.0;
			elseif(date('N')==7) if(time<zevenochtend)$Settobi=16.0;
		}
	}
	if($s['tobi_set']!=$Settobi){ud($i['tobi_set'],0,$Settobi,'Rtobi_set');$s['tobi_set']=$Settobi;}
}
$Setalex=8.0;$setpointalex=cget('setpointalex');
if($setpointalex!=0&&strtotime($t['alex_set'])<achtuur){cset('setpointalex',0);$setpointalex=0;}
if($setpointalex!=2){
	if($buiten_temp<17&&$s['raamalex']=='Closed'&&$s['heating']=='On'&&(strtotime($t['raamalex'])<tweeuur||time>achtavond)){
		$Setalex=12.0;if(time<strtotime('8:00')||time>achtavond)$Setalex=18.5;
	}
	if($s['alex_set']!=$Setalex){ud($i['alex_set'],0,$Setalex,'Ralex_set');$s['alex_set']=$Setalex;}
}
$Setliving=14.0;$setpointliving=cget('setpointliving');
if($setpointliving!=0&&strtotime($t['living_set'])<drieuur){cset('setpointliving',0);$setpointliving=0;}
if($setpointliving!=2){if($buiten_temp<20&&$s['heating']=='On'&&$s['raamliving']=='Closed'){$Setliving=17.0;if(time>=strtotime('3:00')&&time<zevenochtend)$s['slapen']=='Off'?$Setliving=20.5:$Setliving=17.0;elseif(time>=zevenochtend&&time<strtotime('22:30'))$s['slapen']=='Off'?$Setliving=20.5:$Setliving=17.0;}if($s['living_set']!= $Setliving){ud($i['living_set'],0,$Setliving,'Rliving_set');$s['living_set']=$Setliving;}}
if($s['deurbadkamer']=="Open"){if($s['badkamer_set']!=10.0&&(strtotime($t['deurbadkamer'])<eenmin||$s['lichtbadkamer']=='Off')){ud($i['badkamer_set'],0,10,'badkamer_set 10 deur open');$s['badkamer_set']=10.0;}
}elseif($s['deurbadkamer']=="Closed"){if($s['lichtbadkamer']=='On'&&$s['badkamer_set']!=22.0){ud($i['badkamer_set'],0,22.0,'badkamer_set 22 deur dicht en licht aan');$s['badkamer_set']=22.0;}elseif($s['lichtbadkamer']=='Off'&&$s['badkamer_set']!=15.0){ud($i['badkamer_set'],0,15.0,'badkamer_set 15 deur dicht en licht uit');$s['badkamer_set']=15.0;}}
$kamers=array('living','badkamer','tobi','alex','kamer');$kamersgas=array('living','alex');
$bigdif=100;$brander=$s['brander'];$Tbrander=strtotime($t['brander']);$Ibrander=$i['brander'];
foreach($kamers as $kamer){${'dif'.$kamer}=number_format($s[$kamer.'_temp']-$s[$kamer.'_set'],1);if(in_array($kamer,$kamersgas))if(${'dif'.$kamer}<$bigdif)$bigdif=${'dif'.$kamer};${'Set'.$kamer}=number_format($s[$kamer.'_set'],1);}
foreach($kamers as $kamer){if(${'dif'.$kamer}<=number_format(($bigdif+ 0.2),1)&&${'dif'.$kamer}<1)${'RSet'.$kamer}=setradiator($kamer,${'dif'.$kamer},true,$s[$kamer.'_set']);else ${'RSet'.$kamer}=setradiator($kamer,${'dif'.$kamer},false,$s[$kamer.'_set']);}
if($s['kamerZ']!=$RSetkamer)ud($i['kamerZ'],0,$RSetkamer,'RkamerZ');
if($s['tobiZ']!=$RSettobi)ud($i['tobiZ'],0,$RSettobi,'RtobiZ');
if($s['alexZ']!=$RSetalex)ud($i['alexZ'],0,$RSetalex,'RalexZ');
//if($s['badkamerZ']!=$RSetbadkamer)ud($i['badkamerZ'],0,$RSetbadkamer,'RbadkamerZ');
if($s['livingZ']!=$RSetliving)ud($i['livingZ'],0,$RSetliving,'RlivingZ');
if($s['livingZZ']!=$RSetliving)ud($i['livingZZ'],0,$RSetliving,'RlivingZZ');
if($s['livingZE']!=$RSetliving)ud($i['livingZE'],0,$RSetliving,'RlivingZE');
if($bigdif<=-0.3&&$brander=="Off"&& $Tbrander<time-60)sw($Ibrander,'On','brander dif = '.$bigdif);
elseif($bigdif<=-0.2&&$brander=="Off"&& $Tbrander<time-120)sw($Ibrander,'On','brander dif = '.$bigdif);
elseif($bigdif<=-0.1&&$brander=="Off"&& $Tbrander<time-180)sw($Ibrander,'On','brander dif = '.$bigdif);
elseif($bigdif<=0&&$brander=="Off"&& $Tbrander<time-240)sw($Ibrander,'On','brander dif = '.$bigdif);
elseif($bigdif>=0.1&&$brander=="On"&& $Tbrander<time)sw($Ibrander,'Off','brander dif = '.$bigdif);
elseif($bigdif>=0&&$brander=="On"&& $Tbrander<time-60)sw($Ibrander,'Off','brander dif = '.$bigdif);
elseif($bigdif>=-0.1&&$brander=="On"&& $Tbrander<time-120)sw($Ibrander,'Off','brander dif = '.$bigdif);
elseif($bigdif>=-0.2&&$brander=="On"&& $Tbrander<time-180)sw($Ibrander,'Off','brander dif = '.$bigdif);
elseif($bigdif>=-0.3&&$brander=="On"&& $Tbrander<time-240)sw($Ibrander,'Off','brander dif = '.$bigdif);
$badkvuur=$s['badkamervuur'];$Tbadkvuur=strtotime($t['badkamervuur']);$Ibadkvuur=$i['badkamervuur'];
if($difbadkamer<=-0.2&&$badkvuur=="Off"&&$Tbadkvuur<time-180)sw($Ibadkvuur,'On','badkamervuur dif = '.$difbadkamer);
elseif($difbadkamer<=-0.1&&$badkvuur=="Off"&&$Tbadkvuur<time-240)sw($Ibadkvuur,'On','badkamervuur dif = '.$difbadkamer);
elseif($difbadkamer<=0&&$badkvuur=="Off"&&$Tbadkvuur<time-360)sw($Ibadkvuur,'On','badkamervuur dif = '.$difbadkamer);
elseif($difbadkamer>=0.1&&$badkvuur=="On"&&$Tbadkvuur<time)sw($Ibadkvuur,'Off','badkamervuur dif = '.$difbadkamer);
elseif($difbadkamer>=0&&$badkvuur=="On"&&$Tbadkvuur<time-45)sw($Ibadkvuur,'Off','badkamervuur dif = '.$difbadkamer);
elseif($difbadkamer>=-0.1&&$badkvuur=="On"&&$Tbadkvuur<time-90)sw($Ibadkvuur,'Off','badkamervuur dif = '.$difbadkamer);
elseif($difbadkamer>=-0.3&&$badkvuur=="On"&&$Tbadkvuur<time-150)sw($Ibadkvuur,'Off','badkamervuur dif = '.$difbadkamer);
/* ==== Zonneluifel ==== */
$maxbuien=20;$maxwolken=80;$zonopen=1500;$zontoe=200;
if($windrichting>240&&$windrichting<300) $maxwind=6;
else $maxwind=8;
/*if($s['luifel']!='Open'&&($wind>=$maxwind||$buienradar>=$maxbuien||$wolken>=$maxwolken||$s['zon']<$zontoe)){
	print stamp.'  --- Luifel: Wind='.$wind.'|Buien='.round($buienradar,0).'|Zon='.$s['zon'].'|Luifel='.$s['luifel'].'|Last='.$t['luifel'].PHP_EOL;
	if($wind>=$maxwind){
		$msg='Luifel dicht, wind '.$wind .'m/s @ '.$windrichting.'°';
		sw($i['luifel'],'Off',$msg);
		telegram($msg,true,'channel');
		if(strtotime($t['luifel'])<eenuur)sw($i['luifel'],'Off',$msg.' repeat');
	}elseif($buienradar>=$maxbuien){
		$msg='Luifel dicht, '.round($buienradar,0) .'% regen voorspeld';
		sw($i['luifel'],'Off',$msg);
		telegram($msg,true,'channel');
		if(strtotime($t['luifel'])<eenuur)sw($i['luifel'],'Off',$msg.' repeat');
	}elseif($wolken>=$maxwolken){
		$msg='Luifel dicht, '.round($wolken,0) .'% wolken';
		sw($i['luifel'],'Off',$msg);
		telegram($msg,true,'channel');
		if(strtotime($t['luifel'])<eenuur)sw($i['luifel'],'Off',$msg.' repeat');
	}elseif($s['zon']<$zontoe){
		$msg='Luifel dicht, geen zon meer';
		sw($i['luifel'],'Off',$msg);
		telegram($msg,true,'channel');
		if(strtotime($t['luifel'])<eenuur)sw($i['luifel'],'Off',$msg.' repeat');
	}
}elseif($s['luifel']!='Closed'&&time>strtotime('10:25')&&$wind<$maxwind-1&&$buienradar<$maxbuien-1&&$wolken<$maxwolken-5&&$s['living_temp']>20&&$s['zon']>$zonopen&&strtotime($t['luifel'])<tienmin){
	print stamp.'  --- Luifel: Wind='.$wind.'|Buien='.round($buienradar,0).'|Zon='.$s['zon'].'|Luifel='.$s['luifel'].'|Last='.$t['luifel'].PHP_EOL;
		$msg='Luifel open '.stamp;
		sw($i['luifel'],'On',$msg);
		telegram($msg,true,'channel');
		$s['luifel']=='Open';
}
if(strtotime($t['luifel'])<eenuur){$msg='Repeat luifel '.$s['luifel'];sw($i['luifel'],$s['luifel'],$msg.' repeat');telegram($msg,true,'Guy');}
*/
if(cget('time-cron')<eenmin){cset('time-cron',time);
	$items=array('eettafel','zithoek','tobi','kamer','alex');foreach($items as $item){if($s[$item]!='Off'){if(strtotime($t[$item])<dertigsec){$action=cget('dimmer'.$item);if($action==1){$level=filter_var($s[$item],FILTER_SANITIZE_NUMBER_INT);$level=floor($level*0.95);if($level<2)$level=0;if($level==20)$level=19;sl($i[$item],$level,$item);if($level==0)cset('dimmer'.$item,0);}elseif($action==2){$level=filter_var($s[$item],FILTER_SANITIZE_NUMBER_INT);$level=$level+2;if($level==20)$level=21;if($level>30)$level=30;sl($i[$item],$level,$item);if($level==30)cset('dimmer'.$item,0);}}}}
	//Water duiven
	//if(time>strtotime('5:00')&&time<strtotime('6:30')&&strtotime($t['water'])<(time-rand(300,900)))sw($i['water'],'On','duiven wegjagen');	
	//if(time>strtotime('6:30')&&time<strtotime('21:30')&&strtotime($t['water'])<(time-rand(900,3600)))sw($i['water'],'On','duiven wegjagen');	
	//if(time>strtotime('21:30')&&time<strtotime('22:50')&&strtotime($t['water'])<(time-rand(300,900)))sw($i['water'],'On','duiven wegjagen');	
	/*if(time>strtotime('22:00')&&time<strtotime('22:30')){
		if($s['water']=='Off'&&$buienradar<2)sw($i['water'],'On','planten');
	}elseif(time>strtotime('22:30')&&$s['water']=='On')sw($i['water'],'Off','planten');
	*/
	if($s['pirlivingR']!='Off'&&strtotime($t['pirlivingR'])<eenmin) sw($i['pirlivingR'],'Off','Reset pirlivingR');
	if($s['pirgarage']=='Off'&&$s['poort']=='Closed'&&strtotime($t['pirgarage'])<driemin&&strtotime($t['poort'])<driemin&&strtotime($t['garage'])<eenmin&&$s['garage']=='On'&&$s['lichten_auto']=='On')sw($i['garage'],'Off','licht garage');
	if(strtotime($t['pirinkom'])<tweemin&&strtotime($t['pirhall'])<tweemin&&strtotime($t['inkom']) < tweemin&&strtotime($t['hall'])<tweemin&&$s['lichten_auto']=='On'){
		if($s['inkom']=='On')sw($i['inkom'],'Off','licht inkom');
		if($s['hall']=='On')sw($i['hall'],'Off','licht hall');
	}
	if(strtotime($t['pirkeuken'])<tweemin&&strtotime($t['wasbak'])<tweemin&&$s['pirkeuken']=='Off'&&$s['wasbak']=='On'&&$s['werkblad']=='Off'&&$s['keuken']=='Off'&&$s['kookplaat']=='Off')sw($i['wasbak'],'Off','wasbak pir keuken');
	if($s['weg']=='Off'&&$s['slapen']=='Off'){
		$items=array('GroheRed','poortrf','denonpower');
		foreach($items as $item)if($s[$item]=='Off')sw($i[$item],'On',$item);
	}
	if(time>=strtotime('7:00')&&time<=strtotime('23:45')) $fc=json_decode(curl('https://api.forecast.io/forecast/2e43c1ed64ff79afc81f329285103310/50.1925552,3.8140885?units=si'),true);
	if(isset($fc['currently'])){
		if($fc['currently']['temperature']!=0) {$newtemp=round($fc['currently']['temperature']+2.1,1);}
		else {$newtemp=round($buiten_temp,1);}
		$newbuien=$fc['currently']['precipProbability']*100;
		$newwolken=round($fc['currently']['cloudCover']*100,0);
		$newwind=round($fc['currently']['windSpeed'],0);$newwindrichting=round($fc['currently']['windBearing'],0);
	} else $openweathermap=json_decode(curl('http://api.openweathermap.org/data/2.5/weather?id=2790729&APPID=ac3425b0bf0a02a80d2525db6215021d&units=metric'),true);
	if(isset($openweathermap['main'])){
		if($openweathermap['main']['temp']!=0) {$newtemp=round($openweathermap['main']['temp']+1.3,1);}
		else {$newtemp=round($buiten_temp,1);}
		$newwind=round($openweathermap['wind']['speed'],0);$newwindrichting=round($openweathermap['wind']['deg'],0);
		$newwolken=round(($wolken+$openweathermap['clouds']['all'])/2,0);
	}
	$rains=curl('http://gps.buienradar.nl/getrr.php?lat=50.1925552&lon=3.8140885');
	$rains=str_split($rains,11);$totalrain=0;$aantal=0;
	foreach($rains as $rain){$aantal=$aantal+1;$totalrain=$totalrain+substr($rain,0,3);$buien=$totalrain/$aantal;if($aantal==7)break;}
	if(isset($newbuien)) {$newbuienradar=round(max($newbuien,$buien),0);if($newbuienradar>100)$newbuienradar=100;} else $newbuienradar=round($buien);
	if(isset($newtemp)) $weer['buiten_temp']=$newtemp;
	if(isset($newbuienradar)) $weer['buien']=$newbuienradar;
	if(isset($newwind)) $weer['wind']=$newwind;
	if(isset($newwindrichting)) $weer['windrichting']=$newwindrichting;
	if(isset($newwolken)) $weer['wolken']=$newwolken;
	$uweer=serialize($weer);cset('weer',$uweer);
	curl('http://127.0.0.1/secure/logwrite.php?device=temp&buiten='.$buiten_temp.'&living='.$s['living_temp'].'&badkamer='.$s['badkamer_temp'].'&kamer='.$s['kamer_temp'].'&tobi='.$s['tobi_temp'].'&alex='.$s['alex_temp'].'&zolder='.$s['zolder_temp']);
	$regenpomp=$s['regenpomp'];$Tregenpomp=strtotime($t['regenpomp']);
	if($buienradar>0)$pomppauze=14400/$buienradar;else $pomppauze=14400;
	if($regenpomp=='On'&&$Tregenpomp<eenmin)sw($i['regenpomp'],'Off','regenpomp');elseif($regenpomp=='Off'&&$Tregenpomp<time-$pomppauze)sw($i['regenpomp'],'On','regenpomp');
	$items=array(3=>'media',7=>'hallzolder',8=>'lichtbadkamer',9=>'badkamervuur',12=>'inkomvoordeur',13=>'brander',15=>'bureeltobi',22=>'werkbladtuin',23=>'water',24=>'garageterras',49=>'keukenzolderg',56=>'grohered',57=>'fanvestiaire',58=>'tuinpomp',59=>'zwembad',61=>'wasbakkookplaat',65=>'media');
	foreach($items as $item => $name)if(cget('timerefresh-'.$name)<time-rand(1800,2700)){RefreshZwave($item,'time',$name);break;}
if(date('i',time)%5==0){
	if($s['voordeur']=='On'&&strtotime($t['voordeur'])<tienmin)sw($i['voordeur'],'Off','Voordeur uit');
	if($s['lichten_auto']=='Off') if(strtotime($t['lichten_auto'])<drieuur)sw($i['lichten_auto'],'Off','lichten_auto aan');
	if($s['weg']=='On'||$s['slapen']=='On'){
		$lastoff=cget('timelastoff');
		if($lastoff<tienmin){
			if(strtotime($t['weg'])>eenmin||strtotime($t['slapen'])>eenmin)$uit=60;else $uit=600;
			if($s['weg']=='On')alles('Off',$uit);
			if($s['slapen']=='On')alles('Slapen',$uit);
			$items=array('living','badkamer','kamer','tobi','alex');
			foreach($items as $item){${'setpoint'.$item}=cget('setpoint'.$item);if(${'setpoint'.$item}!=0&&strtotime($t[$item])<eenuur)cset('setpoint'.$item,0);}
			cset('timelastoff',time);
		}
		if(strtotime($t['weg'])<eenmin)if($s['poortrf']=='On')sw($i['poortrf'],'Off','Poort uit');
	}
	if($s['kodi']=='On'&&strtotime($t['kodi'])<vijfmin){$devcheck='Kodi';if(pingDomain('192.168.0.7',1597)==1){$prevcheck=cget('check'.$devcheck);if($prevcheck>0)cset('check'.$devcheck,0);}else{$check=cget('check'.$devcheck)+1;if($check>0)cset('check'.$devcheck,$check);if($check==3)sw($i['kodi'],'Off','kodi');}}
	$devcheck='PiCam1-Voordeur';if(pingDomain('192.168.0.11',80)==1){$prevcheck=cget('check'.$devcheck);if($prevcheck>=3)telegram($devcheck.' online',true,'channel');if($prevcheck>0)cset('check'.$devcheck,0);}else{$check=cget('check'.$devcheck)+1;if($check>0)cset('check'.$devcheck,$check);if($check==3)telegram($devcheck.' Offline',true,'channel');if($check%100==0)telegram($devcheck.' nog steeds Offline',true,'channel');}
	$devcheck='PiCam2-Alex';if(pingDomain('192.168.0.12',80)==1){$prevcheck=cget('check'.$devcheck);if($prevcheck>=3)telegram($devcheck.' online',true,'channel');if($prevcheck>0)cset('check'.$devcheck,0);}else{$check=cget('check'.$devcheck)+1;if($check>0)cset('check'.$devcheck,$check);if($check==3)telegram($devcheck.' Offline',true,'channel');if($check%100==0)telegram($devcheck.' nog steeds Offline',true,'channel');}
	$devcheck='PiCam3-Oprit';if(pingDomain('192.168.0.13',80)==1){$prevcheck=cget('check'.$devcheck);if($prevcheck>=3)telegram($devcheck.' online',true,'channel');if($prevcheck>0)cset('check'.$devcheck,0);}else{$check=cget('check'.$devcheck)+1;if($check>0)cset('check'.$devcheck,$check);if($check==3)telegram($devcheck.' Offline',true,'channel');if($check%100==0)telegram($devcheck.' nog steeds Offline',true,'channel');}
	$devcheck='PiHole-DNS';if(pingDomain('192.168.0.2',53)==1){$prevcheck=cget('check'.$devcheck);if($prevcheck>=3)telegram($devcheck.' online',true,'channel');if($prevcheck>0)cset('check'.$devcheck,0);}else{$check=cget('check'.$devcheck)+1;if($check>0)cset('check'.$devcheck,$check);if($check==3)telegram($devcheck.' Offline',true,'channel');if($check%100==0)telegram($devcheck.' nog steeds Offline',true,'channel');}
	$devcheck='PiHole-WWW';if(pingDomain('192.168.0.2',80)==1){$prevcheck=cget('check'.$devcheck);if($prevcheck>=3)telegram($devcheck.' online',true,'channel');if($prevcheck>0)cset('check'.$devcheck,0);}else{$check=cget('check'.$devcheck)+1;if($check>0)cset('check'.$devcheck,$check);if($check==3)telegram($devcheck.' Offline',true,'channel');if($check%100==0)telegram($devcheck.' nog steeds Offline',true,'channel');}
	if(($s['weg']=='On'||$s['slapen']=='On')&&$s['meldingen']=='On'&&strtotime($t['weg'])<driemin&&strtotime($t['slapen'])<driemin){$devices=array('raamliving','pirliving','pirlivingR','pirkeuken','pirgarage','pirinkom');foreach($devices as $device){if(!in_array($s[$device],array('Closed','Off'))){if(cget('timealert'.$device)<eenmin){sw($i['sirene'],'On','SIRENE');cset('timealert'.$device,time);$msg=$device.' '.$s[$device].' om '.$t[$device];telegram($msg,true,'channel');ios($msg);if(sms===true)sms($msg);}}}}
	if(strtotime($t['tobi_set'])<eendag)ud($i['tobi_set'],0,$s['tobi_set'],'Update tobi');
	if(strtotime($t['living_set'])<eendag)ud($i['living_set'],0,$s['living_set'],'Update living');
	if(strtotime($t['kamer_set'])<eendag)ud($i['kamer_set'],0,$s['kamer_set'],'Update kamer');
	if(strtotime($t['alex_set'])<eendag)ud($i['alex_set'],0,$s['alex_set'],'Update alex');
	if(cget('timenetworkheal')<twaalfuur&&time>strtotime('15:00')){curl(api.'json.htm?type=command&param=zwavenetworkheal&idx=4');cset('timenetworkheal',time);}
	$items=array('brander','badkamervuur');foreach($items as $item)if($s[$item]!='Off'&&strtotime($t[$item])<eenuur)sw($i[$item],$s[$item],$item);
	if($s['meldingen']=='Off'&&strtotime($t['meldingen'])<drieuur)sw($i['meldingen'],'On','meldingen');
	if(strtotime($t['pirliving'])<vieruur&&strtotime($t['pirlivingR'])<vieruur&&strtotime($t['pirgarage'])<vieruur&&strtotime($t['pirinkom'])<vieruur&&strtotime($t['pirhall'])<vieruur&&strtotime($t['slapen'])<vieruur&&strtotime($t['weg'])<vieruur&&$s['weg']=='Off'&&$s['slapen']=="Off"){
		sw($i['slapen'],'On','wakker1');
		if($s['slapen']=='Off')telegram('slapen ingeschakeld na 4 uur geen beweging',false,'channel');else telegram('slapen ingeschakeld na 4 uur geen beweging',true,'channel');
	}
	if(strtotime($t['pirliving'])<twaalfuur&&strtotime($t['pirlivingR'])<twaalfuur&&strtotime($t['pirgarage'])<twaalfuur&&strtotime($t['pirinkom'])<twaalfuur&&strtotime($t['pirhall'])<twaalfuur&&strtotime($t['slapen'])<twaalfuur&&strtotime($t['weg'])<twaalfuur&&$s['weg']=='Off'&&$s['slapen']=="On"){
		sw($i['slapen'],'Off','wakker2');sw($i['weg'],'On','weg');
		if($s['slapen']=='Off')telegram('weg ingeschakeld na 12 uur geen beweging',false,'channel');else telegram('weg ingeschakeld na 12 uur geen beweging',true,'channel');
	}
	if($buienradar<2&&$s['regenpomp']=='On')sw($i['regenpomp'],'Off','regenpomp');
	if($s['zwembadfilter']=='On'){if(strtotime($t['zwembadfilter']) < vieruur&&time>strtotime("18:00")&&$s['zwembadwarmte']=='Off')sw($i['zwembadfilter'],'Off','zwembadfilter');}
	else{if(strtotime($t['zwembadfilter'])<vieruur&&time>strtotime("12:00")&&time<strtotime("16:00"))sw($i['zwembadfilter'],'On','zwembadfilter');}
	if($s['zwembadwarmte']=='On'){
		if(strtotime($t['zwembadwarmte'])<eendag)sw($i['zwembadwarmte'],'Off','warmtepomp zwembad');
		if($s['zwembadfilter']=='Off')sw($i['zwembadfilter'],'On','zwembadfilter');
	}
	if($s['meldingen']=='On'){
		$items=array('living_temp','badkamer_temp','kamer_temp','tobi_temp','alex_temp','zolder_temp');
		$avg=0;foreach($items as $item) $avg=$avg+$s[$item];$avg=$avg/6;
		foreach($items as $item){$temp=$s[$item];if($temp>$avg+5&&$temp>25){$msg='T '.$item.'='.$temp.'°C. AVG='.round($avg,1).'°C';if(cget('timealerttemp'.$item)<eenuur){telegram($msg,false,'channel');ios($msg);if(sms===true)sms($msg);cset('timealerttemp'.$item,time);}}if(strtotime($t[$item])<vieruur){if(cget('timealerttempupd'.$item)<twaalfuur){telegram($item.' not updated');cset('timealerttempupd'.$item,time);}}}
	}
	$devices=array('tobiZ','alexZ','livingZ','livingZZ','livingZE','kamerZ');
	foreach($devices as $device){if(strtotime($t[$device])<vieruur){if(cget('timealert'.$device)<twaalfuur){telegram($device.' geen communicatie');cset('timealert'.$device,time);}}}
	if($s['weg']=='Off'&&$s['slapen']=='Off'){
		if(($buiten_temp>$s['kamer_temp']&&$buiten_temp>$s['tobi_temp']&&$buiten_temp>$s['alex_temp'])&&$buiten_temp>22&&($s['kamer_temp']>20||$s['tobi_temp']>20||$s['alex_temp']>20)&&($s['raamkamer']=='Open'||$s['raamtobi']=='Open'||$s['raamalex']=='Open')) if((int)cget('timeramen') < twaalfuur){telegram('Ramen boven dicht doen, te warm buiten. Buiten = '.$buiten_temp.',kamer = '.$s['kamer_temp'].', Tobi = '.$s['tobi_temp'].', Alex = '.$s['alex_temp'],false,'channel');cset('timeramen',time);}
		elseif(($buiten_temp<=$s['kamer_temp']||$buiten_temp<=$s['tobi_temp']||$buiten_temp<=$s['alex_temp'])&&($s['kamer_temp']>20||$s['tobi_temp']>20||$s['alex_temp']>20)&&($s['raamkamer']=='Closed'||$s['raamkamer']=='Closed'||$s['raamkamer']=='Closed')) if((int)cget('timeramen') < twaalfuur){telegram('Ramen boven open doen, te warm binnen. Buiten = '.$buiten_temp.',kamer = '.$s['kamer_temp'].', Tobi = '.$s['tobi_temp'].', Alex = '.$s['alex_temp'],false,'channel');cset('timeramen',time);}
	}include('gcal/gcal.php');
}}}
function sw($idx,$action="",$info=""){$t=microtime(true);$micro=sprintf("%03d",($t-floor($t))*1000);$stamp=strftime("%Y-%m-%d %H:%M:%S.", $t).$micro;print $stamp."          Switch ".$idx." (".ucfirst($info).") ".strtoupper($action).PHP_EOL;if(empty($action)) curl(api."json.htm?type=command&param=switchlight&idx=".$idx."&switchcmd=Toggle");else curl(api."json.htm?type=command&param=switchlight&idx=".$idx."&switchcmd=".$action);usleep(500000);}
function sl($idx,$level,$info=""){$t=microtime(true);$micro=sprintf("%03d",($t-floor($t))*1000);$stamp=strftime("%Y-%m-%d %H:%M:%S.", $t).$micro;print $stamp."        Set Level ".$idx." ".ucfirst($info)." ".$level.PHP_EOL;curl(api . "json.htm?type=command&param=switchlight&idx=".$idx."&switchcmd=Set%20Level&level=".$level);usleep(500000);}
function ud($idx,$nvalue,$svalue,$info=""){$t=microtime(true);$micro=sprintf("%03d",($t-floor($t))*1000);$stamp=strftime("%Y-%m-%d %H:%M:%S.", $t).$micro;if(!in_array($idx, array(395,532,534))) print $stamp."  --- UPDATE ".$idx." ".$info." ".$nvalue." ".$svalue.PHP_EOL;curl(api.'json.htm?type=command&param=udevice&idx='.$idx.'&nvalue='.$nvalue.'&svalue='.$svalue);usleep(500000);}
function setradiator($name,$dif,$koudst=false,$set){$setpoint=$set-ceil($dif)*3;if($koudst==true)$setpoint=$set+3;if($setpoint>25)$setpoint=25.0;elseif($setpoint<4)$setpoint=4.0;return $setpoint;}
function telegram($msg,$silent=true,$to='Guy'){if($to=='channel') $telegramchatid=-1011051395180;else $telegramchatid=159714413;$result=curl('https://api.telegram.org/bot111592115:AAEZ-xCRhO-RBfUqICiJs1q1A_13YIr1irxI/sendMessage?chat_id='.$telegramchatid.'&text='.$msg.'&disable_notification='.$silent);$time=microtime(true);$mSecs=$time-floor($time);$mSecs=substr(number_format($mSecs,3),1);$fp=fopen('/volume1/files/telegramlog.txt',"a+");fwrite($fp, sprintf("%s%s %s\n", date("Y-m-d H:i:s"),$mSecs,$result));fclose($fp);usleep(50000);}
function ios($msg){require_once("findmyiphone.php");$fmi=new FindMyiPhone(appleid,applepass);$fmi->playSound(appledevice,$msg);}
function sms($msg){curl('http://api.clickatell.com/http/sendmsg?user='.$smsuser.'&password='.$smspassword.'&api_id='.$smsapi.'&to='.$smstofrom.'&text='.urlencode($msg).'&from='.$smstofrom.'');usleep(500000);}
function pingDomain($domain,$port){$file=fsockopen($domain,$port,$errno,$errstr,3);$status=0;if(!$file) $status=-1;else {fclose($file);$status=1;}return $status;}
function RefreshZwave($node,$name='auto',$device=''){cset('timerefresh-'.$device,time);$devices=json_decode(file_get_contents(api.'json.htm?type=openzwavenodes&idx=4'),true);for($k=1;$k<=5;$k++){ControllerBusy(20);$result=file_get_contents(api.'ozwcp/refreshpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>'racp','node'=>$node)),),)));if($result==='OK'){cset('timerefresh-'.$device,time);break;}sleep(1);}if(cget('timedeadnodes')<vijfmin){cset('timedeadnodes',time);foreach($devices as $node=>$data){if($node=="result"){foreach($data as $index=>$eltsNode){if($eltsNode["State"]=="Dead"&&!in_array($eltsNode['NodeID'],array(31,50,53,55,60))){telegram('Node '.$eltsNode['NodeID'].' '.$eltsNode['Description'].' ('.$eltsNode['Name'].') marked as dead, reviving '.ZwaveCommand($eltsNode['NodeID'],'HasNodeFailed'));ControllerBusy(5);ZwaveCommand(1,'Cancel');}}}}}}
function Zwavecancelaction(){file_get_contents(api.'ozwcp/admpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>'cancel')),),)));}
function ZwaveCommand($node,$command){$cm=array('AssignReturnRoute'=>'assrr','DeleteAllReturnRoutes'=>'delarr','NodeNeighbourUpdate'=>'reqnnu','RefreshNodeInformation'=>'refreshnode','RequestNetworkUpdate'=>'reqnu','HasNodeFailed'=>'hnf','Cancel'=>'cancel');	$cm=$cm[$command];for($k=1;$k<=5;$k++){$result=file_get_contents(api.'ozwcp/admpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>$cm,'node'=>'node'.$node)),),)));if($result=='OK')break;sleep(1);}return $result;}
function ControllerBusy($retries){for ($k=1;$k<=$retries;$k++){$result=file_get_contents(api.'ozwcp/poll.xml');$p=xml_parser_create();xml_parse_into_struct($p,$result,$vals,$index);xml_parser_free($p);foreach($vals as $val){if($val['tag']=='ADMIN'){$result=$val['attributes']['ACTIVE'];break;}}if($result=='false')break;if($k==$retries){ZwaveCommand(1,'Cancel');break;}sleep(1);}}
function logwrite($device,$value,$table){$t=microtime(true);$micro=sprintf("%03d",($t-floor($t))*1000);$stamp=strftime("%Y-%m-%d %H:%M:%S.", $t).$micro;if(!in_array($device, array('buiten_temp'))) print $stamp."          ".ucfirst($table)." ($device) $value".PHP_EOL;curl('http://127.0.0.1/secure/logwrite.php?device='.$device.'&value='.$value.'&table='.$table);}
function curl($url){dl("curl.so");$headers=array('Content-Type: application/json',);$ch=curl_init();curl_setopt($ch,CURLOPT_URL,$url);curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);$data=curl_exec($ch);curl_close($ch);return $data;}
function cset($key,$value){if(!$m=xsMemcached::Connect('127.0.0.1', 11211)){die('Memcache failed to connect.');}$m->Set($key,$value);}
function cget($key){if(!$m=xsMemcached::Connect('127.0.0.1', 11211)){die('Memcache failed to connect.');}return $m->Get($key);}
class xsMemcached{private $Host;private $Port;private $Handle;public static function Connect($Host,$Port,$Timeout=5){$Ret=new self();$Ret->Host=$Host;$Ret->Port=$Port;$ErrNo=$ErrMsg=NULL;if(!$Ret->Handle=@fsockopen($Ret->Host,$Ret->Port,$ErrNo,$ErrMsg,$Timeout))return false;return $Ret;}public function Set($Key,$Value,$TTL=0){return $this->SetOp($Key,$Value,$TTL,'set');}public function Get($Key){$this->WriteLine('get '.$Key);$Ret='';$Header=$this->ReadLine();if($Header=='END'){$Ret=0;$this->SetOp($Key,0,0,'set');return $Ret;}while(($Line=$this->ReadLine())!='END')$Ret.=$Line;if($Ret=='')return false;$Header=explode(' ',$Header);if($Header[0]!='VALUE'||$Header[1]!=$Key) throw new Exception('unexcpected response format');$Meta=$Header[2];$Len=$Header[3];return $Ret;}public function Quit(){$this->WriteLine('quit');}private function SetOp($Key,$Value,$TTL,$Op){$this->WriteLine($Op.' '.$Key.' 0 '.$TTL.' '.strlen($Value));$this->WriteLine($Value);return $this->ReadLine()=='STORED';}private function WriteLine($Command,$Response=false){fwrite($this->Handle,$Command."\r\n");if($Response)return $this->ReadLine();return true;}private function ReadLine(){return rtrim(fgets($this->Handle),"\r\n");}private function __construct(){}}
