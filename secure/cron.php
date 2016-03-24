<?php error_reporting(E_ALL);ini_set("display_errors", "on");
$authenticated=false;$all=false;
include "functions.php";
$time=$_SERVER['REQUEST_TIME'];$starttime=microtime(true);
if(isset($_GET['all'])) if($_GET['all']==1) $all=true;
if(isset($argv[1])) if($argv[1]=='all') $all=true;
if($all){$domoticz=json_decode(file_get_contents($domoticzurl.'json.htm?type=devices&used=true'),true);}
else {$domoticz=json_decode(file_get_contents($domoticzurl.'json.htm?type=devices&used=true&plan=3'),true);} //&plan=3
$domotime=microtime(true)-$starttime;
if($domoticz) {
	foreach($domoticz['result'] as $dom) {
		isset($dom['Type'])?$Type=$dom['Type']:$Type='None';
		isset($dom['SwitchType'])?$SwitchType=$dom['SwitchType']:$SwitchType='None';
		isset($dom['SubType'])?$SubType=$dom['SubType']:$SubType='None';
		$name=$dom['Name'];
		if($Type=='Temp'){${'T'.$name}=$dom['Temp'];${'TI'.$name}=$dom['idx'];${'TT'.$name}=strtotime($dom['LastUpdate']);}
		else if($SwitchType=='Dimmer'){${'DI'.$name}=$dom['idx'];$dom['Status']=='Off'?${'D'.$name}='Off':${'D'.$name}='On';$dom['Status']=='Off'?${'Dlevel'.$name}=0:${'Dlevel'.$name}=$dom['Level'];${'DT'.$name}=strtotime($dom['LastUpdate']);}
		else if($Type=='Usage'&&$dom['SubType']=='Electric') ${'P'.$name}=substr($dom['Data'],0,-5);
		else if($Type=='Radiator 1'||$Type=='Thermostat') {${'RI'.$name}=$dom['idx'];${'R'.$name}=$dom['Data'];${'RT'.$name}=strtotime($dom['LastUpdate']);}
		else {
			if(substr($dom['Data'],0,2)=='On') ${'S'.$name}='On';
			else if(substr($dom['Data'],0,3)=='Off') ${'S'.$name}='Off';
			else if(substr($dom['Data'],0,4)=='Open') ${'S'.$name}='Open';
			else ${'S'.$name}=$dom['Data'];
			${'SI'.$name}=$dom['idx'];
			${'ST'.$name}=strtotime($dom['LastUpdate']);
		}
	}
	echo 'cron';
	//Zon op / zon onder
	$zonop=strtotime($domoticz['Sunrise']);$zononder=strtotime($domoticz['Sunset']);
	//Automatische lichten inschakelen
	if($Sslapen=='Off') {
		if(($Spirgarage!='Off'||$Spoort!='Closed')&&$Sgarage=='Off'&&$Sgarage_auto=='On'&&$STgarage<$tweesec) 
			Schakel($SIgarage, 'On', 'licht garage');
		if($Spirinkom!='Off'&&$Sinkom=='Off'&&$Shall_auto=='On') {
			Schakel($SIinkom, 'On', 'licht inkom');
			Schakel($SIhall, 'On','licht hall');
		}
		if($Spirhall!='Off'&&($Shall=='Off'||$Sinkom=='Off')&&$Shall_auto=='On' &&$SThall<$tweesec&&$STinkom<$tweesec) {
			Schakel($SIhall, 'On','licht hall');
			Schakel($SIinkom, 'On','licht inkom');
		}
		if(($Spirliving!='Off'||$SpirlivingR!='Off')&&$Sdenon=='Off') {
			$Usleep=1500000;
			if($Shall_auto=='On') {
				if($Skeuken=='Off') Schakel($SIkeuken, 'On','lamp wasbak');
				if($Sbureel=='Off') Schakel($SIbureel, 'On','lamp bureel');
			}
			Schakel(504,'On','Radio luisteren');
		}
		if($Spirkeuken!='Off' && $Skeuken=='Off' && $Swasbak=='Off' && $Swerkblad=='Off' && $Skookplaat=='Off' && $Shall_auto=='On' &&$STwasbak < $tweesec) 
			Schakel($SIkeuken, 'On', 'wasbak door pir');

		if($Sdeurbadkamer=='Open'&&$STdeurbadkamer>$time-10&&$STlichtbadkamer1<$eenmin&&$Slichtbadkamer1!='On'&&$STlichtbadkamer2<$eenmin&&$Slichtbadkamer2=='Off'&&$Shall_auto=='On') 
			Schakel($SIlichtbadkamer1, 'On','badkamer1 door deur');
	} else {
		if($Spirinkom!='Off'&&$Sinkom=='Off'&&$Shall_auto=='On')
			Schakel($SIinkom, 'On','licht inkom');
		if($Spirhall!='Off'&&$Sinkom=='Off'&&$Shall_auto=='On')
			Schakel($SIinkom, 'On','licht inkom');
		if($Sdeurbadkamer=='Open'&&$STdeurbadkamer>$time-10&&$STlichtbadkamer2<$eenmin&&$Slichtbadkamer2!='On'&&$STlichtbadkamer1<$eenmin&&$Slichtbadkamer1=='Off') {
			if($time > strtotime('6:00') && $time < strtotime('12:00')) Schakel($SIlichtbadkamer1, 'On','licht badkamer1'); else Schakel($SIlichtbadkamer2, 'On','badkamer2 door deur'); 
		}
	}
	//meldingen
	$sirene=false;
	if(($Sweg=='On'||$Sslapen=='On') && $STweg<$driemin && $Smeldingen=='On') {
		if($Spoort!='Closed') {$msg='Poort open om '.strftime("%H:%M:%S", $STpoort);$sirene=true;if(cget('alertpoort')<$eenmin) {cset('alertpoort', $time);telegram($msg);ios($msg);if($sms===true) sms($msg);}}
		if($Sachterdeur!='Closed') {$msg='Achterdeur open om '.strftime("%H:%M:%S", $STachterdeur);$sirene=true;if(cget('alertAchterdeur')<$eenmin) {cset('alertAchterdeur', $time);telegram($msg);ios($msg);if($sms===true) sms($msg);}}
		if($Sraamliving!='Closed') {$msg='raam living open om '.strftime("%H:%M:%S", $STraamliving);$sirene=true;if(cget('alertraamliving')<$eenmin) {cset('alertraamliving', $time);telegram($msg);ios($msg);if($sms===true) sms($msg);}}
		if($Spirgarage!='Off'&&$STslapen<$driemin) {$msg='Beweging gedecteerd in garage om '.strftime("%H:%M:%S", $STpirgarage);$sirene=true;if(cget('alertpirgarage')<$eenmin) {cset('alertpirgarage', $time);telegram($msg);ios($msg);if($sms===true) sms($msg);}}
		if($Spirinkom!='Off'&&$STslapen<$driemin) {$msg='Beweging gedecteerd in inkom om '.strftime("%H:%M:%S", $STpirinkom);$sirene=true;if(cget('alertinkom')<$time-90) {cset('alertpirinkom', $time);telegram($msg);ios($msg);if($sms===true) sms($msg);}}
	}
	if($Sweg=='On' && $STweg<$driemin && $Smeldingen=='On') {
		if($Spirhall!='Off') {$msg='Beweging gedecteerd in hall om '.strftime("%H:%M:%S", $STpirhall);$sirene=true;if(cget('telegrampirhall')<$time-90) {cset('alertpirhall', $time);telegram($msg);ios($msg);if($sms===true) sms($msg);}}
	}
	if($Sweg=='Off' && $Sslapen=='Off' && $Scamvoordeur=='On') {
		Schakel($SIcamvoordeur,'Off');
		if(strftime("%M",$time) != 0 && strftime("%S",$time) != 23) {
			if($STdeurbel < $time) Schakel($SIdeurbel,'On');$Sdeurbel=='On';
			if($Shall_auto=='On') Schakel(203,'On');
			telegram('Beweging voordeur');
			shell_exec('/volume1/web/secure/picam1.sh');
		}
	}
	if($Sdeurbel=='On') {telegram('Deurbel');Schakel($SIdeurbel,'Off');}
	
	//minimote living
	$ctx = stream_context_create(array('http'=>array('timeout' => 2,)));
	if($Sradioluisteren=='On') {$Sminiliving1l='On';$STminiliving1l=$time;Schakel($SIradioluisteren,'Off');}
	if($Stvkijken=='On') {$Sminiliving1s='On';$STminiliving1s=$time;Schakel($SItvkijken,'Off');}
	if($Skodikijken=='On') {$Sminiliving2s='On';$STminiliving2s=$time;Schakel($SIkodikijken,'Off');}
	if($Seten=='On') {$Sminiliving3l='On';$STminiliving3l=$time;Schakel($SIeten,'Off');}
	if($Sminiliving1s=='On'/*&&$STminiliving1s>$tweesec*/) {
		if($Sdenon!='On') Schakel($SIdenon,'On','mini Denon');
		if($Stv!='On') Schakel(48,'On','mini TV');
		if($Shall_auto=='On') {
			if($Skristal!='On') Schakel(51,'On','mini Kristal');
			if($Stvled!='On') Schakel(52,'On','mini TVled');
		}
		file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutZone_OnOff%2FON&cmd1=aspMainZone_WebUpdateStatus%2F',false, $ctx);
		usleep($Usleep*2);
		file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutZone_InputFunction/SAT/CBL',false, $ctx);
		usleep($Usleep*2);
		file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-50.0',false, $ctx);
		if($Ssubwoofer!='On') Schakel(53,'On','mini Subwoofer');
		Udevice($SIminiliving1s,0,'Off');
	}
	if($Sminiliving1l=='On'/*&&$STminiliving1l>$tweesec*/) {
		if($Sdenon!='On') Schakel($SIdenon,'On','mini Denon');
		if($Stv!='Off') Schakel(48,'Off','mini TV');
		if($Skristal!='Off') Schakel(51,'Off','mini Kristal');
		if($Stvled!='Off') Schakel(52,'Off','mini TVLed');
		file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutZone_OnOff%2FON&cmd1=aspMainZone_WebUpdateStatus%2F',false, $ctx);
		usleep($Usleep*2);
		file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-55.0',false, $ctx);
		usleep($Usleep*2);
		file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutZone_InputFunction/TUNER',false, $ctx);
		if($Ssubwoofer!='On') Schakel(53,'On','mini Subwoofer');
		Udevice($SIminiliving1l,0,'Off');
	}
	if($Sminiliving2s=='On'/*&&$STminiliving2s>$tweesec*/) {
		if($Sdenon!='On') Schakel($SIdenon,'On','mini Denon');
		if($Stv!='On') Schakel(48,'On','mini TV');
		if($Skodi!='On') Schakel(54,'On','mini Kodi');
		if($Shall_auto=='On') {
			Schakel(51,'On','mini Kristal');
			Schakel(52,'On','mini TVLed');
		}
		file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutZone_OnOff%2FON&cmd1=aspMainZone_WebUpdateStatus%2F',false, $ctx);
		usleep($Usleep*2);
		file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutZone_InputFunction/DVD',false, $ctx);
		usleep($Usleep*2);
		file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-40.0',false, $ctx);
		if($Ssubwoofer!='On') Schakel(53,'On','mini Subwoofer');
		Udevice($SIminiliving2s,0,'Off');
	}
	if($Sminiliving2l=='On'/*&&$STminiliving2l>$tweesec*/) {
		file_get_contents($domoticzurl.'json.htm?type=command&param=switchlight&idx=55&switchcmd=Toggle');
		Udevice($SIminiliving2l,0,'Off');
	}
	if($Sminiliving3s=='On'/*&&$STminiliving3s>$tweesec*/) {
		$denonmain=simplexml_load_string(file_get_contents($denon_address.'/goform/formMainZone_MainZoneXml.xml?_='.$time,false,$ctx));
		$denonmain=json_encode($denonmain);$denonmain=json_decode($denonmain,TRUE);usleep(10000);
		if($denonmain){
			$denonmain['MasterVolume']['value']=='--'?$setvalue=-55:$setvalue=$denonmain['MasterVolume']['value'];
			$setvalue=$setvalue-3;
			if($setvalue>-10) $setvalue=-10;if($setvalue<-80) $setvalue=-80;
			file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/'.$setvalue.'.0');}
		Udevice($SIminiliving3s,0,'Off');
	}
	if($Sminiliving3l=='On'/*&&$STminiliving3l>$tweesec*/) {
		Dim(425,9,'mini Eettafel');
		Schakel(48,'Off','mini TV');
		Schakel(51,'Off','mini kristal');
		Schakel(52,'Off','mini TVled');
		file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutZone_OnOff%2FON&cmd1=aspMainZone_WebUpdateStatus%2F',false, $ctx);
		usleep($Usleep*2);
		file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-55.0',false, $ctx);
		usleep($Usleep*2);
		file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutZone_InputFunction/TUNER',false, $ctx);
		if($Ssubwoofer!='On') Schakel(53,'On','mini Subwoofer');
		Udevice($SIminiliving3l,0,'Off');
	}
	if($Sminiliving4s=='On'/*&&$STminiliving4s>$tweesec*/) {
		$denonmain=simplexml_load_string(file_get_contents($denon_address.'/goform/formMainZone_MainZoneXml.xml?_='.$time,false,$ctx));
		$denonmain=json_encode($denonmain);$denonmain=json_decode($denonmain,TRUE);usleep(10000);
		if($denonmain){
			$denonmain['MasterVolume']['value']=='--'?$setvalue=-55:$setvalue=$denonmain['MasterVolume']['value'];
			$setvalue=$setvalue+3;
			if($setvalue>-10) $setvalue=-10;if($setvalue<-80) $setvalue=-80;
			file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/'.$setvalue.'.0');}
		Udevice($SIminiliving4s,0,'Off');
	}
	if($Sminiliving4l=='On'/*&&$STminiliving4l>$tweesec*/) {
		if($Spirkeuken!='Off') Udevice($SIpirkeuken,0,'Off');
		if($Spirgarage!='Off') Udevice($SIpirgarage,0,'Off');
		if($Spirinkom!='Off') Udevice($SIpirinkom,0,'Off');
		if($Spirhall!='Off') Udevice($SIpirhall,0,'Off');
		Schakel(74,'Off','mini Zithoek');
		Schakel(425,'Off','mini Eettafel');
		Schakel(184,'Off','mini Werkblad');
		Schakel(176,'Off','mini Kookplaat');
		Schakel(175,'Off','mini Wasbak');
		Schakel(166,'Off','mini Keuken');
		Schakel(212,'Off','mini garage');
		Schakel(202,'Off','mini inkom');
		Schakel(272,'Off','mini hall');
		Udevice($SIminiliving4l,0,'Off');
	}
	//minimote hall
	if($Sminihall1s=='On'/*&&$STminihall1s>$tweesec*/) {
		if($Sslapen!='On') Schakel($SIslapen, 'On');
		if($Shall=='On') Schakel($SIhall,'Off');
		if($Sbureel=='On') Schakel($SIbureel,'Off');
		if($Sgarage=='On') Schakel($SIgarage,'Off');
		if($Swasbak=='On') Schakel($Swasbak,'Off');
		if($Skeuken=='On') Schakel($SIkeuken,'Off');
		Schakel(48,'Off');
		Schakel(52,'Off');
		Schakel(176,'Off');
		Schakel(203,'Off');
		Schakel(52,'Off');
		Schakel(52,'Off');
		Schakel(52,'Off');
		Dim(425,0);
		Dim(74,0);
		Udevice($SIminihall1s,0,'Off');
	}
	if($Sminihall1l=='On'/*&&$STminihall1l>$tweesec*/) {
		Schakel($SIslapen,'Off');
		Schakel($SIhall,'On');
		Schakel(223,'On');
		Dim(280,100);
		Dim(285,100);
		Dim(473,100);
		Udevice($SIminihall1l,0,'Off');
	}
	if($Sminihall2s=='On'/*&&$STminihall2s>$tweesec*/) {
		Schakel(446,'Off');
		Udevice($SIminihall2s,0,'Off');
	}
	if($Sminihall2l=='On'/*&&$STminihall2l>$tweesec*/) {
		Dim(473,3);
		//cset('dimmeralex',1);
		Udevice($SIminihall2l,0,'Off');
	}
	if($Sminihall3s=='On'/*&&$STminihall3s>$tweesec*/) {
		if($Sslapen=='On') Schakel($SIslapen, 'Off', 'mini hall');
		if($Shall_auto=='On' && $Shall=='Off') Schakel($SIhall,'On');
		Udevice($SIminihall3s,0,'Off');
	}
	if($Sminihall3l=='On'/*&&$STminihall3l>$tweesec*/) {
		Schakel(223,'Off');
		Dim(280,0);
		Dim(285,0);
		Dim(473,0);
		if($time<$zonop+1800 && $time>$zononder-1800) Schakel($SIhall_auto,'On');
		else Schakel($SIhall,'Off');
		Udevice($SIminihall3l,0,'Off');
	}
	if($Sminihall4s=='On'/*&&$STminihall4s>$tweesec*/) {
		Schakel(446,'On');
		Udevice($SIminihall4s,0,'Off');
	}
	if($Sminihall4l=='On'/*&&$STminihall4l>$tweesec*/) {
		Dim(285,18);
		cset('dimmertobi',1);
		Udevice($SIminihall4l, 'Off');
	}
	
	//Refresh Zwave node
	if($STkeukenzolderg>$vijfsec) RefreshZwave(20,'KeukenZolder');
	if($STwasbakkookplaat>$vijfsec) RefreshZwave(21,'WasbakKookplaat');
	if($STwerkbladtuin>$vijfsec) RefreshZwave(22,'WerkbladTuin');
	if($STinkomvoordeur>$vijfsec) RefreshZwave(23,'InkomVoordeur');
	if($STgarageterras>$vijfsec) RefreshZwave(24,'TerrasGarage');
	if($STlichtbadkamer>$vijfsec) RefreshZwave(25,'LichtBadkamer');
	if($SThallzolder>$vijfsec) RefreshZwave(35,'HallZolder');

	if($STweg>$tweesec||$STslapen>$tweesec) shell_exec('/usr/bin/php /var/www/secure/cron.php all > /dev/null 2>&1 &');
	if($Sscripttest!='Off') Schakel($SIscripttest, 'Off', 'reset');
	if($all) {
		//meldingen
		if($Smeldingen=='On') {
			$items=array('living','badkamer','tobi','alex','zolder');
			$avg=0;
			foreach($items as $item) $avg=$avg+${'T'.$item};
			$avg=$avg / 6;
			foreach($items as $item) {
				if(${'T'.$item}>$avg + 5 && ${'T'.$item} > 25) {$msg='T '.$item.'='.${'T'.$item}.'°C. AVG='.round($avg,1).'°C';
					if(cget('alerttemp'.$item)<$tienmin) {telegram($msg);ios($msg);if($sms===true) sms($msg);cset('alerttemp'.$item, $time);}
				}
				if(${'SSD'.$item}!='Off') {
					$msg='Rook gedecteerd in '.$item.'!';
					telegram($msg);
					ios($msg);
					if($sms===true) sms($msg);
					if(${'STSD'.$item}<$tweemin) resetsecurity(${'SISD'.$item},$item);
				}
			}
		}
		//Sleep dimmers
		$items = array('eettafel','zithoek','tobi','kamer','alex');
		foreach($items as $item) {
			if(${'D'.$item}!='Off' && ${'DT'.$item}<$eenmin) {
				$action = cget('dimmer'.$item);
				if($action == 1) {
					$level = floor(${'Dlevel'.$item}*0.95);
					Dim(${'DI'.$item},$level,$item);
					if($level==0) cset('dimmer'.$item,0);
				} else if($action == 2&&date('i')%2==1) {
					$level = ${'Dlevel'.$item}+1;
					if($level>30) $level = 30;
					Dim(${'DI'.$item},$level,$item);
					if($level==30) cset('dimmer'.$item,0);
				}
			}
		}
		//Denon - Subwoofer
		if (date('i')%10==0) {
			$denonstatus = json_decode(json_encode(simplexml_load_string(file_get_contents($denon_address.'/goform/formMainZone_MainZoneXml.xml?_='.$time,false, stream_context_create(array('http'=>array('timeout' => 2,)))))), TRUE);
			if($Sdenon=='On') {
				if($Ssubwoofer!='On') Schakel($SIsubwoofer,'On','subwoofer');
				if($denonstatus['Power']['value']!='ON') file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutSystem_OnStandby%2FON&cmd1=aspMainZone_WebUpdateStatus%2F');
			}
			else if($Sdenon=='Off') {
				if($Ssubwoofer!='Off') Schakel($SIsubwoofer,'Off','subwoofer');
				if($denonstatus['Power']['value']!='OFF') file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutSystem_OnStandby%2FSTANDBY&cmd1=aspMainZone_WebUpdateStatus%2F');
			}
		}
		//PIRS resetten
		if($SpirlivingR!='Off'&&$STpirlivingR<$eenmin) Schakel($SIpirlivingR,'Off','PIR livingR');

		//Automatische lichten uitschakelen
		if($STgarage_auto < $time-7200) {
			if($time>$zonop+10800 && $time<$zononder-10800) {if($Sgarage_auto=='On' && $Sgarage=='Off') Schakel($SIgarage_auto,'Off','licht garage');}
			else if($Sgarage_auto=='Off') Schakel($SIgarage_auto,'On','licht garage');}
		if($SThall_auto < $time-7200) {
			if($time>$zonop+1800 && $time<$zononder-1800) {if($Shall_auto=='On' && $Shall=='Off' && $Sinkom=='Off') Schakel($SIhall_auto,'Off','hall auto');}
			else if($Shall_auto=='Off') Schakel($SIhall_auto,'On','hall auto');}
		if($Spirgarage=='Off'&&$Spoort=='Closed'&&$STpirgarage<$driemin&&$STpoort<$driemin&&$STgarage<$eenmin&&$Sgarage=='On'&&$Sgarage_auto=='On') Schakel($SIgarage,'Off','licht garage');
		if($STpirinkom<$tweemin&&$STpirhall<$tweemin&&$STinkom<$tweemin&&$SThall<$tweemin&&$Shall_auto=='On') {
			if($Sinkom=='On') Schakel($SIinkom,'Off','licht inkom');
			if($Shall=='On') Schakel($SIhall,'Off','licht hall');
		}
		if($STpirkeuken<$tweemin&&$STkeuken<$tweemin&&$Spirkeuken=='Off'&&$Skeuken=='On'&&$Swerkblad=='Off'&&$Swasbak=='Off'&&$Skookplaat=='Off')
			Schakel($SIkeuken,'Off','pir keuken');
		if($Svoordeur=='On'&&$STvoordeur<$tienmin) Schakel($SIvoordeur, 'Off', 'Voordeur uit');
		//slapen-weg bij geen beweging
		if($STpirliving<$time-14400&&$STpirlivingR<$time-14400&&$STpirgarage<$time-14400&&$STpirinkom<$time-14400&&$STpirhall<$time-14400&&$STslapen<$time-14400&&$STweg<$time-14400&&$Sweg=='Off'&&$Sslapen=="Off") {Schakel($SIslapen,'On','slapen');telegram('slapen ingeschakeld na 4 uur geen beweging');}
		if($STpirliving<$twaalfuur&&$STpirlivingR<$twaalfuur&&$STpirgarage<$twaalfuur&&$STpirinkom<$twaalfuur&&$STpirhall<$twaalfuur&&$STslapen<$time-28800&&$STweg<$twaalfuur&&$Sweg=='Off'&&$Sslapen=="On") {Schakel($SIslapen, 'Off','slapen');Schakel($SIweg, 'On','weg');telegram('weg ingeschakeld na 12 uur geen beweging');}

		//meldingen inschakelen indien langer dan 3 uur uit.
		if($Smeldingen!='On' && $STmeldingen<$drieuur) Schakel($SImeldingen, 'On','meldingen');
		//Alles uitschakelen
		if($Sweg=='On'||$Sslapen=='On') {
			if($STweg>$eenmin||$STslapen>$eenmin) $uit = $eenmin; else $uit = $tienmin;
			if($Sbureel!='Off'&&$STbureel<$uit) Schakel($SIbureel, 'Off', 'bureel');
			if($Swerkblad!='Off'&&$STwerkblad<$uit) Schakel($SIwerkblad, 'Off', 'werkblad');
			if($Skookplaat!='Off'&&$STkookplaat<$uit) Schakel($SIkookplaat, 'Off', 'kookplaat');
			if($Svoordeur!='Off'&&$STvoordeur<$uit) Schakel($SIvoordeur, 'Off', 'voordeur');
			if($Skeuken!='Off'&&$STkeuken<$uit) Schakel($SIkeuken, 'Off', 'keuken');
			if($Swasbak!='Off'&&$STwasbak<$uit) Schakel($SIwasbak, 'Off', 'wasbak');
			if($Szolderg!='Off'&&$STzolderg<$uit) Schakel($SIzolderg, 'Off', 'zolderg');
			if($Stv!='Off'&&$STtv<$uit) Schakel($SItv, 'Off', 'tv');
			if($Stvled!='Off'&&$STtvled<$uit) Schakel($SItvled, 'Off', 'tvled');
			if($Skristal!='Off'&&$STkristal<$uit) Schakel($SIkristal, 'Off', 'kristal');
			if($Sdenon!='Off') {
				Schakel($SIdenon, 'Off', 'denon');
				file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutSystem_OnStandby%2FSTANDBY&cmd1=aspMainZone_WebUpdateStatus%2F');
			}
			if($Ssubwoofer!='Off'&&$STsubwoofer<$uit) Schakel($SIsubwoofer, 'Off', 'subwoofer');
			if($Sterras!='Off'&&$STterras<$uit) Schakel($SIterras, 'Off', 'terras');
			if($Szolder!='Off'&&$STzodler<$uit) Schakel($SIzolder, 'Off', 'zolder');
			if($Sbureeltobi!='Off'&&$STbureeltobi<$uit) Schakel($SIbureeltobi, 'Off', '');
			if($Sinkom!='Off'&&$STinkom<$uit) Schakel($SIinkom, 'Off', '');
			if($Shall!='Off'&&$SThall<$uit) Schakel($SIhall, 'Off', '');
			if($Deettafel!='Off'&&$DTeettafel<$uit) Schakel($DIeettafel, 'Off', 'eettafel');
			if($Dzithoek!='Off'&&$DTzithoek<$uit) Schakel($DIzithoek, 'Off', 'zithoek');
			if($Skodi!='Off'&&$STkodi<$uit) file_get_contents('http://192.168.0.7:1597/jsonrpc?request={"jsonrpc":"2.0","id":1,"method":"System.Shutdown"}',false,$ctx);
			$items = array('living','badkamer','kamer','tobi','alex');
			foreach ($items as $item) {
				${'setpoint'.$item} = cget('setpoint'.$item);
				if(${'setpoint'.$item}!=0&&${'RT'.$item}<$eenuur) cset('setpoint'.$item,0);
			}
		}
		if($Sweg=='On') {
			if($Slichtbadkamer1!='Off'&&$STlichtbadkamer1<$uit) Schakel($SIlichtbadkamer1, 'Off', 'lichtbadkamer1');
			if($Slichtbadkamer2!='Off'&&$STlichtbadkamer2<$uit) Schakel($SIlichtbadkamer2, 'Off', 'lichtbadkamer2');
			if($Dkamer!='Off'&&$DTkamer<$uit) Schakel($DIkamer, 'Off', 'kamer');
			if($Dtobi!='Off'&&$DTtobi<$uit) Schakel($DItobi, 'Off', 'tobi');
			if($Dalex!='Off'&&$DTalex<$uit) Schakel($DIalex, 'Off', 'alex');
		}
		//KODI
		if($Skodi=='On'&&$STkodi<$vijfmin) {
			$status = pingDomain('192.168.0.7', 1597);
			if($status!=1) {
				cset('timekodi',$time);
				sleep(10);
				$status2 = pingDomain('192.168.0.7', 1597);
				if($status2!=1) Schakel($SIkodi, 'Off','kodi');
			}
		}
			$refresh = $halfuur;
			if(cget('timerefresh7' )<$refresh + rand(0,600)) RefreshZwave(7 ,'cron');
			if(cget('timerefresh8' )<$refresh + rand(0,600)) RefreshZwave(8 ,'cron');
			if(cget('timerefresh9' )<$refresh + rand(0,600)) RefreshZwave(9 ,'cron');
			if(cget('timerefresh11')<$refresh + rand(0,600)) RefreshZwave(11,'cron');
			if(cget('timerefresh20')<$refresh + rand(0,600)) RefreshZwave(20,'cron');
			if(cget('timerefresh21')<$refresh + rand(0,600)) RefreshZwave(21,'cron');
			if(cget('timerefresh22')<$refresh + rand(0,600)) RefreshZwave(22,'cron');
			if(cget('timerefresh23')<$refresh + rand(0,600)) RefreshZwave(23,'cron');
			if(cget('timerefresh24')<$refresh + rand(0,600)) RefreshZwave(24,'cron');
			if(cget('timerefresh25')<$refresh + rand(0,600)) RefreshZwave(25,'cron');
			if(cget('timerefresh35')<$refresh + rand(0,600)) RefreshZwave(35,'cron');
			if(cget('timerefresh36')<$refresh + rand(0,600)) RefreshZwave(36,'cron');
			if(cget('timerefresh37')<$refresh + rand(0,600)) RefreshZwave(37,'cron');
			if(cget('timerefresh79')<$refresh + rand(0,600)) RefreshZwave(79,'cron');
			if(cget('timerefresh80')<$refresh + rand(0,600)) RefreshZwave(80,'cron');
			if(cget('timerefresh84')<$refresh + rand(0,600)) RefreshZwave(84,'cron');
			if(cget('timerefresh89')<$refresh + rand(0,600)) RefreshZwave(89,'cron');

		require ('/volume1/web/heating.php');
		//if(cget('timegcal')<$vijfmin) {
			$rains=file_get_contents('http://gps.buienradar.nl/getrr.php?lat=50.892880&lon=3.112568');
			$rains=str_split($rains, 11);$totalrain=0;$aantal=0;
			foreach($rains as $rain) {$aantal=$aantal+1;$totalrain=$totalrain+substr($rain,0,3);$averagerain=round($totalrain/$aantal,0);if($aantal==12) break;}
			if($averagerain>=0) cset('averagerain',$averagerain);
			$openweathermap=file_get_contents('http://api.openweathermap.org/data/2.5/weather?id=2787891&APPID=ac3485b0bf1a02a81d2525db6515021d&units=metric');
			$openweathermap=json_decode($openweathermap,true);
			if(isset($openweathermap['weather']['0']['icon'])) {
				cset('weatherimg',$openweathermap['weather']['0']['icon']);
				$newtemp = round(($openweathermap['main']['temp']+$Tbuiten)/2,1);
				if($newtemp!=$Tbuiten) Udevice(22,0,number_format($newtemp,1,".","."),'Buiten = '.$openweathermap['main']['temp']);
			}
			//if(php_sapi_name()=='cli') include('gcal/gcal.php'); 
		//	cset('timegcal', $time);
		//}
		unset($rain,$rains,$avg,$Set,$openweathermap,$Type,$SwitchType,$SubType,$name,$client,$results,$optParams,$service,$event,$calendarId,$items,$item,$setpointkamer,$setpointtobi,$setpointalex,$Setkamer,$Settobi,$Setalex,$Setbadkamer);
	} //END ALL
$execution= microtime(true)-$starttime;$phptime=$execution-$domotime;
unset($domoticz,$dom,$applepass,$appleid,$appledevice,$domoticzurl,$smsuser,$smsapi,$smspassword,$smstofrom,$user,$users,$db,$http_response_header,$_SERVER,$_FILES,$_COOKIE,$_POST,$ctx,$zonop,$zononder,$mc);
//End Acties
} else {
	if(cget('timetelegramconnection')<$halfuur) {telegram('Geen verbinding met Domoticz');cset('timetelegramconnection',$time);}

}
//if($authenticated) 	echo '<hr>Number of vars: '.count(get_defined_vars()).'<br/><pre>';print_r(get_defined_vars());echo '</pre>';
/*if($actions>=0) {
	if(isset($argv[1])&&$arg[1]=="All") $msg='D'.number_format($domotime,2).'|P'.number_format($phptime,2).'|T'.number_format($execution,2).'|M'.$rpimem.'|C'.$rpicpu.'|'.$argv[1].' -> '.$actions.' actions';
	else if(isset($argv[1])) $msg='D'.number_format($domotime,2).'|P'.number_format($phptime,2).'|T'.number_format($execution,2).'|'.$argv[1].' -> '.$actions.' actions';
	else $msg='D'.number_format($domotime,2).'|P'.number_format($phptime,2).'|T'.number_format($execution,2).'| -> '.$actions.' actions';
	logwrite($msg);
}*/
