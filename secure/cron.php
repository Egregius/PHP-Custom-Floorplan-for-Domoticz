<?php //error_reporting(E_ALL);ini_set("display_errors", "on");
$authenticated=false;
include "functions.php";
$time=$_SERVER['REQUEST_TIME'];$starttime=microtime(true);
$all=false;
if(isset($_GET['all'])) if($_GET['all']==1) $all=true;
if(isset($argv[1])) if($argv[1]=='all') $all=true;
if($all){$domoticz=json_decode(file_get_contents($domoticzurl.'json.htm?type=devices&used=true'),true);}
else {$domoticz=json_decode(file_get_contents($domoticzurl.'json.htm?type=devices&used=true&plan=3'),true);}
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
			if($Shall_auto=='On') {
				if($Swasbak=='Off') Schakel($SIwasbak, 'On','lamp wasbak');
				if($Sbureel=='Off') Schakel($SIbureel, 'On','lamp bureel');
			}
			if($Sdenon=='Off') Udevice(437,1,'On','Radio luisteren');
		}
		if($Spirkeuken!='Off' && $Swasbak=='Off' && $Swerkblad=='Off' && $Skookplaat=='Off' && $Shall_auto=='On' &&$STwasbak < $tweesec) 
			Schakel($SIwasbak, 'On', 'wasbak door pir');

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
	if($Sdeurbel=='On') {telegram('Beweging op oprit');Schakel($SIdeurbel,'Off', 'Reset deurbel');}
	if(($Sweg=='On'||$Sslapen=='On') && $STweg<$driemin && $Smeldingen=='On') {
		if($Spoort!='Closed') {$msg='Poort open om '.strftime("%H:%M:%S", $STpoort);$sirene=true;if($mc->get('alertpoort')<$eenmin) {$mc->set('alertpoort', $time);telegram($msg);ios($msg);if($sms==true) sms($msg);}}
		if($Sachterdeur!='Closed') {$msg='Achterdeur open om '.strftime("%H:%M:%S", $STachterdeur);$sirene=true;if($mc->get('alertAchterdeur')<$eenmin) {$mc->set('alertAchterdeur', $time);telegram($msg);ios($msg);if($sms==true) sms($msg);}}
		if($Sraamliving!='Closed') {$msg='raam living open om '.strftime("%H:%M:%S", $STraamliving);$sirene=true;if($mc->get('alertraamliving')<$eenmin) {$mc->set('alertraamliving', $time);telegram($msg);ios($msg);if($sms==true) sms($msg);}}
		if($Spirgarage!='Off'&&$STslapen<$driemin) {$msg='Beweging gedecteerd in garage om '.strftime("%H:%M:%S", $STpirgarage);$sirene=true;if($mc->get('alertpirgarage')<$eenmin) {$mc->set('alertpirgarage', $time);telegram($msg);ios($msg);if($sms==true) sms($msg);}}

		/*if(($Spirliving!='Off'||$SpirlivingR!='Off'||$Spirkeuken!='Off')&&$STslapen<$driemin) {
			//file_get_contents('http://picam1:8080/0/action/snapshot');
			Dim($DIzithoek,100,'living alarm');
			Dim($DIeettafel,100,'living alarm');
			Schakel($SIbureel, 'On', 'living alarm');
			Schakel($SIwerkblad, 'On', 'living alarm');
			Schakel($SIkookplaat, 'On', 'living alarm');
			Schakel($SIwasbak, 'On', 'living alarm');
			$msg='Beweging gedecteerd in living om '.strftime("%H:%M:%S", $STpirliving);
			$sirene=true;
			//file_get_contents('http://picam1:8080/0/action/snapshot');
			if($mc->get('alertpirliving')<$time-90) {
				$mc->set('alertpirliving', $time);
				telegram($msg);
				ios($msg);
				if($sms==true) sms($msg);
			}
		}*/
		if($Spirinkom!='Off'&&$STslapen<$driemin) {$msg='Beweging gedecteerd in inkom om '.strftime("%H:%M:%S", $STpirinkom);$sirene=true;if($mc->get('alertinkom')<$time-90) {$mc->set('alertpirinkom', $time);telegram($msg);ios($msg);if($sms==true) sms($msg);}}
	}
	if($Sweg=='On' && $STweg<$driemin && $Smeldingen=='On') {
		if($Spirhall!='Off') {$msg='Beweging gedecteerd in hall om '.strftime("%H:%M:%S", $STpirhall);$sirene=true;if($mc->get('telegrampirhall')<$time-90) {$mc->set('alertpirhall', $time);telegram($msg);ios($msg);if($sms==true) sms($msg);}}
	}
	/*if($STdeurbel>$vijfsec) {
		$msg='Deurbel';
		if($mc->get('alertDeurbel')<$eenmin) {
			$mc->set('alertDeurbel',$time);
			ios($msg);
		}
		//Udevice($SIdeurbel,0,'Off','deurbel');
		if($Shall_auto=='On') {
			Schakel($SIvoordeur, 'On','licht voordeur');
			$mc->set('Bellichtvoordeur',2);
		}

	}*/
	//minimote living
	$ctx = stream_context_create(array('http'=>array('timeout' => 2,)));
	if($Sminiliving1s=='On'&&$STminiliving1s>$tweesec) {
		if($Sdenon!='On') Schakel($SIdenon,'On','mini Denon');
		Schakel(48,'On','mini TV');
		if($Shall_auto=='On') {
			Schakel(51,'On','mini Kristal');
			Schakel(52,'On','mini TVled');
			if($Sbureel!='On') Schakel($SIbureel,'On','mini Bureel');
		}
		file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutZone_OnOff%2FON&cmd1=aspMainZone_WebUpdateStatus%2F',false, $ctx);
		file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutZone_InputFunction/SAT/CBL',false, $ctx);
		file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-50.0',false, $ctx);
		Schakel(53,'On','mini Subwoofer');
		Udevice($SIminiliving1s,0,'Off');
	}
	if($Sminiliving1l=='On'&&$STminiliving1l>$tweesec) {
		Schakel($SIdenon,'On','mini Denon');
		Schakel(48,'Off','mini TV');
		Schakel(51,'Off','mini Kristal');
		Schakel(52,'Off','mini TVLed');
		file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutZone_OnOff%2FON&cmd1=aspMainZone_WebUpdateStatus%2F',false, $ctx);
		file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-55.0',false, $ctx);
		file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutZone_InputFunction/TUNER',false, $ctx);
		Schakel(53,'On','mini Subwoofer');
		Udevice($SIminiliving1l,0,'Off');
	}
	if($Sminiliving2s=='On'&&$STminiliving2s>$tweesec) {
		Schakel($SIdenon,'On','mini Denon');
		Schakel(48,'On','mini TV');
		Schakel(54,'On','mini Kodi');
		if($Shall_auto=='On') {
			Schakel(51,'On','mini Kristal');
			Schakel(52,'On','mini TVLed');
			if($Sbureel!='On') Schakel($SIbureel,'On','mini Bureel');
		}
		file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutZone_OnOff%2FON&cmd1=aspMainZone_WebUpdateStatus%2F',false, $ctx);
		file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-45.0',false, $ctx);
		file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutZone_InputFunction/DVD',false, $ctx);
		Schakel(53,'On','mini Subwoofer');
		Udevice($SIminiliving2s,0,'Off');
	}
	if($Sminiliving2l=='On'&&$STminiliving2l>$tweesec) {
		file_get_contents($domoticzurl.'json.htm?type=command&param=switchlight&idx=55&switchcmd=Toggle');
		Udevice($SIminiliving2l,0,'Off');
	}
	if($Sminiliving3s=='On'&&$STminiliving3s>$tweesec) {
		$denonmain=simplexml_load_string(file_get_contents($denon_address.'/goform/formMainZone_MainZoneXml.xml?_='.$time,false,$ctx));
		$denonmain=json_encode($denonmain);$denonmain=json_decode($denonmain,TRUE);usleep(10000);
		if($denonmain){
			$denonmain['MasterVolume']['value']=='--'?$setvalue=-55:$setvalue=$denonmain['MasterVolume']['value'];
			$setvalue=$setvalue-3;
			if($setvalue>-10) $setvalue=-10;if($setvalue<-80) $setvalue=-80;
			file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/'.$setvalue.'.0');}
		Udevice($SIminiliving3s,0,'Off');
	}
	if($Sminiliving3l=='On'&&$STminiliving3l>$tweesec) {
		Dim(425,9,'mini Eettafel');
		Schakel(48,'Off','mini TV');
		Schakel(51,'Off','mini kristal');
		Schakel(52,'Off','mini TVled');
		file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutZone_OnOff%2FON&cmd1=aspMainZone_WebUpdateStatus%2F',false, $ctx);
		file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-55.0',false, $ctx);
		file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutZone_InputFunction/TUNER',false, $ctx);
		Schakel(53,'On','mini Subwoofer');
		Udevice($SIminiliving3l,0,'Off');
	}
	if($Sminiliving4s=='On'&&$STminiliving4s>$tweesec) {
		$denonmain=simplexml_load_string(file_get_contents($denon_address.'/goform/formMainZone_MainZoneXml.xml?_='.$time,false,$ctx));
		$denonmain=json_encode($denonmain);$denonmain=json_decode($denonmain,TRUE);usleep(10000);
		if($denonmain){
			$denonmain['MasterVolume']['value']=='--'?$setvalue=-55:$setvalue=$denonmain['MasterVolume']['value'];
			$setvalue=$setvalue+3;
			if($setvalue>-10) $setvalue=-10;if($setvalue<-80) $setvalue=-80;
			file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/'.$setvalue.'.0');}
		Udevice($SIminiliving4s,0,'Off');
	}
	if($Sminiliving4l=='On'&&$STminiliving4l>$tweesec) {
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
	if($Sminihall1s=='On'&&$STminihall1s>$tweesec) {
		if($Sslapen!='On') Schakel($SIslapen, 'On');
		if($Shall=='On') Schakel($SIhall,'Off');
		if($Sbureel=='On') Schakel($SIbureel,'Off');
		if($Sgarage=='On') Schakel($SIgarage,'Off');
		if($Swasbak=='On') Schakel($Swasbak,'Off');
		Schakel(48,'Off');
		Schakel(52,'Off');
		
		Udevice($SIminihall1s,0,'Off');
	}
	if($Sminihall1l=='On'&&$STminihall1l>$tweesec) {
		Schakel($SIslapen,'Off','mini hall_auto');
		Schakel($SIhall,'On','mini hall_auto');
		Schakel(223,'On','mini hall_auto');
		Dim(280,100,'mini hall_auto');
		Dim(285,100,'mini hall_auto');
		Udevice($SIminihall1l,0,'Off');
	}
	if($Sminihall2s=='On'&&$STminihall2s>$tweesec) {
		Schakel(446,'Off','mini hall');
		Udevice($SIminihall2s,0,'Off');
	}
	if($Sminihall2l=='On'&&$STminihall2l>$tweesec) {
		Udevice($SIminihall2l,0,'Off');
	}
	if($Sminihall3s=='On'&&$STminihall3s>$tweesec) {
		if($Sslapen=='On') Schakel($SIslapen, 'Off', 'mini hall');
		if($Shall_auto=='On' && $Shall=='Off') Schakel($SIhall,'On');
		Udevice($SIminihall3s,0,'Off');
	}
	if($Sminihall3l=='On'&&$STminihall3l>$tweesec) {
		Schakel(223,'Off','mini hall_auto');
		Dim(280,0,'mini hall_auto');
		Dim(285,0,'mini hall_auto');
		if($time<$zonop+1800 && $time>$zononder-1800) Schakel($SIhall_auto,'On','hall auto');
		else Schakel($SIhall,'Off','mini hall_auto');
		Udevice($SIminihall3l,0,'Off');
	}
	if($Sminihall4s=='On'&&$STminihall4s>$tweesec) {
		Schakel(446,'On','mini hall');
		Udevice($SIminihall4s,0,'Off');
	}
	if($Sminihall4l=='On'&&$STminihall4l>$tweesec) {
		Dim(285,25,'mini hall');
		$mc->set('dimmertobi',1);
		Udevice($SIminihall4l, 'Off');
	}
	
	//Refresh Zwave node
	if($STkeukenzolderg>$tweesec) RefreshZwave(20,'KeukenZolder');
	if($STwasbakkookplaat>$tweesec) RefreshZwave(21,'WasbakKookplaat');
	if($STwerkbladtuin>$tweesec) RefreshZwave(22,'WerkbladTuin');
	if($STinkomvoordeur>$tweesec) RefreshZwave(23,'InkomVoordeur');
	if($STgarageterras>$tweesec) RefreshZwave(24,'TerrasGarage');
	if($STlichtbadkamer>$tweesec) RefreshZwave(25,'LichtBadkamer');
	if($SThallzolder>$tweesec) RefreshZwave(35,'HallZolder');

	if($STweg>$tweesec||$STslapen>$tweesec) shell_exec('/usr/bin/php /var/www/secure/cron.php all > /dev/null 2>&1 &');
	if($all) {
		if($mc->get('domoticzconnection')!=1) {$mc->set('domoticzconnection',1); telegram('Verbinding met Domoticz hersteld');}
		//meldingen
		if($Smeldingen=='On') {
			$items=array('living','badkamer','tobi','alex','zolder');
			$avg=0;
			foreach($items as $item) $avg=$avg+${'T'.$item};
			$avg=$avg / 6;
			foreach($items as $item) {
				if(${'T'.$item}>$avg + 5 && ${'T'.$item} > 25) {$msg='T '.$item.'='.${'T'.$item}.'°C. AVG='.round($avg,1).'°C';
					if($mc->get('alerttemp'.$item)<$tienmin) {telegram($msg);ios($msg);if($sms==true) sms($msg);$mc->set('alerttemp'.$item, $time);}
				}
				if(${'SSD'.$item}!='Off') {
					$msg='Rook gedecteerd in '.$item.'!';
					telegram($msg);
					ios($msg);
					if($sms==true) sms($msg);
					if(${'STSD'.$item}<$tweemin) resetsecurity(${'SISD'.$item},$item);
				}
			}
			//if($Pbureeltobi>500) {if($mc->get('alertpowerbureeltobi')<$eenuur) {$msg='Verbruik bureel tobi='.$Pbureeltobi;telegram($msg);$mc->set('alertpowerbureeltobi', $time);}}
		}
		//Sleep dimmers
		$items = array('eettafel','zithoek','tobi','kamer');
		foreach($items as $item) {
			if(${'D'.$item}!='Off') {
				$action = $mc->get('dimmer'.$item);
				if($action == 1) {
					$level = floor(${'Dlevel'.$item}*0.95);
					Dim(${'DI'.$item},$level,$item);
					if($level==0) $mc->set('dimmer'.$item,0);
				} else if($action == 2&&date('i')%2==1) {
					$level = ${'Dlevel'.$item}+1;
					if($level>30) $level = 30;
					Dim(${'DI'.$item},$level,$item);
					if($level==30) $mc->set('dimmer'.$item,0);
				}
			}
		}
		//heating on/off
		if($Sweg=='On') {if($Sheating!='Off'&&$STheating<$eenuur) {Schakel($SIheating, 'Off','heating');$Sheating = 'Off';}
		} else {if($Sheating!='On') {Schakel($SIheating, 'On','heating');$Sheating = 'On';}}
		// 0 = auto, 1 = voorwarmen, 2 = manueel
		//living
		$Set=16.0;
		$setpointliving = $mc->get('setpointliving');
		if($setpointliving!=0 && $RTliving < $tweeuur) {$mc->set('setpointliving',0);$setpointliving=0;}
		if($setpointliving!=2) {
			if($Tbuiten<20 && $Sheating=='On'/* && $Sraamliving=='Closed'*/) {
					 if($time>= strtotime('3:00') && $time <  strtotime('7:00')) $Sslapen=='Off'?$Set=19.0:$Set=16.0;
				else if($time>= strtotime('7:00') && $time <  strtotime('8:30')) $Sslapen=='Off'?$Set=19.0:$Set=19.0;
				else if($time>= strtotime('8:30') && $time < strtotime('19:00')) $Sslapen=='Off'?$Set=20.0:$Set=19.0;
				else if($time>=strtotime('19:00') && $time < strtotime('22:00')) $Sslapen=='Off'?$Set=20.0:$Set=19.0;
			}
			if($Rliving != $Set) {Udevice($RIliving,0,$Set,'Rliving');$Rliving=$Set;}
			if($RTliving < $drieuur) Udevice($RIliving,0,$Set,'Rliving');
		}
		$Set = setradiator($Tliving, $Rliving);
		if($RlivingZZ!=$Set) {Udevice($RIlivingZZ,0,$Set,'RlivingZZ');}
		if($RlivingZE!=$Set) {Udevice($RIlivingZE,0,$Set,'RlivingZE');}
		if($RlivingZB!=$Set) {Udevice($RIlivingZB,0,$Set,'RlivingZB');}
		//badkamer
		$Set=15.0;
		$setpointbadkamer = $mc->get('setpointbadkamer');
		if($setpointbadkamer!=0 && $RTbadkamer < $eenuur) {$mc->set('setpointbadkamer',0);$setpointbadkamer=0;}
		if($setpointbadkamer!=2) {
			if($Tbuiten<21 && $Sheating=='On') {
				if(in_array(date('N',$time), array(1,2,3,4,5)) && $time>=strtotime('5:00') && $time<=strtotime('6:00')) $Set=19.0;
				else if(in_array(date('N',$time), array(1,2,3,4,5)) && $time>=strtotime('6:00') && $time<=strtotime('7:20')) $Set=21.0;
				else if(in_array(date('N',$time), array(6,7)) && $time>=strtotime('7:30') && $time<=strtotime('9:30')) $Set=20.0;
				else if($time>=strtotime('9:30') && $time<=strtotime('23:59') && $Sslapen=='Off') $Set=17.0;

			}
			if($Sdeurbadkamer!='Closed' && $STdeurbadkamer < $time - 180) $Set=14.0;
			if($Rbadkamer != $Set) {Udevice($RIbadkamer,0,$Set,'Rbadkamer');$Rbadkamer=$Set;}
			if($RTbadkamer < $drieuur) Udevice($RIbadkamer,0,$Set,'Rbadkamer');
		}
		$Set = setradiator($Tbadkamer, $Rbadkamer);
		if(in_array(date('N',$time), array(1,2,3,4,5)) && in_array(date('G',$time), array(4,5,6)) && $Set < 21) $Set = 21.0;
		if($RbadkamerZ!=$Set) {Udevice($RIbadkamerZ,0,$Set,'RbadkamerZ');}
		//Slaapkamer
		$Set = 4.0;
		$setpointkamer = $mc->get('setpointkamer');
		if($setpointkamer!=0 && $RTkamer < $eenuur) {$mc->set('setpointkamer',0);$setpointkamer=0;}
		if($setpointkamer!=2) {
			if($Tbuiten<15 && $Sraamkamer=='Closed' && $Sheating=='On' ) {
				$Set = 12.0;
				if($time < strtotime('7:00') || $time > strtotime('20:00')) $Set = 16.0;
			}
		}
		if($Rkamer != $Set) {Udevice($RIkamer,0,$Set,'Rkamer');$Rkamer=$Set;}
		if($RTkamer < $drieuur) Udevice($RIkamer,0,$Set,'Rkamer');
		$Set = setradiator($Tkamer, $Rkamer);
		if($RkamerZ!=$Set) {Udevice($RIkamerZ,0,$Set,'RkamerZ');}
		//Slaapkamer tobi
		$Set = 4.0;
		$setpointtobi = $mc->get('setpointtobi');
		if($setpointtobi!=0 && $RTtobi < $eenuur) {$mc->set('setpointtobi',0);$setpointtobi=0;}
		if($setpointtobi!=2) {
			if($Tbuiten<15 && $Sraamtobi=='Closed' && $Sheating=='On') {
				$Set = 12.0;
				if (date('W')%2==1) {
						 if (date('N') == 3) { if($time > strtotime('20:00')) $Set = 16.0;}
					else if (date('N') == 4) { if($time < strtotime('7:00') || $time > strtotime('20:00')) $Set = 16.0;}
					else if (date('N') == 5) { if($time < strtotime('7:00')) $Set = 16.0;}
				} else {
						 if (date('N') == 3) { if($time > strtotime('20:00')) $Set = 16.0;}
					else if (in_array(date('N'),array(4,5,6))) { if($time < strtotime('8:00') || $time > strtotime('20:00')) $Set = 16.0;}
					else if (date('N') == 7) { if($time < strtotime('7:00')) $Set = 16.0;}
				}
			}
			if($Rtobi != $Set) {Udevice($RItobi,0,$Set,'Rtobi');$Rtobi=$Set;}
			if($RTtobi < $time - 8600) Udevice($RItobi,0,$Set,'Rtobi');
		}
		$Set = setradiator($Ttobi, $Rtobi);
		if($RtobiZ!=$Set) {Udevice($RItobiZ,0,$Set,'RtobiZ');}
		//Slaapkamer alex
		$Set = 4.0;
		$setpointalex = $mc->get('setpointalex');
		if($setpointalex!=0 && $RTalex < $eenuur) {$mc->set('setpointalex',0);$setpointalex=0;}
		if($setpointalex!=2) {
			if($Tbuiten<15 && $Sraamalex=='Closed' && $Sheating=='On') {
				//$Set = 10.0;
			}
			if($Ralex != $Set) {Udevice($RIalex,0,$Set,'Ralex');$Ralex=$Set;}
			if($RTalex < $time - 8600) Udevice($RIalex,0,$Set,'Ralex');
		}
		$Set = setradiator($Talex, $Ralex);
		if($RalexZ!=$Set) {Udevice($RIalexZ,0,$Set,'RalexZ');}
		//brander
		if (($Tliving < $Rliving-0.2 || $Tbadkamer < $Rbadkamer-0.2 || $Tkamer < $Rkamer-0.2 || $Ttobi < $Rtobi-0.2 || $Talex < $Ralex-0.2 ) && $Sbrander == "Off") Schakel($SIbrander, 'On', 'brander');
		else if(($Tliving < $Rliving || $Tbadkamer < $Rbadkamer || $Tkamer < $Rkamer || $Ttobi < $Rtobi || $Talex < $Ralex ) && $Sbrander == "Off" && $STbrander < $time-250) Schakel($SIbrander, 'On', 'brander');
		else if($Tliving > $Rliving-0.2 && $Tbadkamer > $Rbadkamer-0.2 && $Tkamer > $Rkamer-0.2 && $Ttobi > $Rtobi-0.2 && $Talex > $Ralex-0.2 && $Sbrander == "On" && $STbrander < $time-130) Schakel($SIbrander, 'Off', 'brander');
		else if($Tliving > $Rliving && $Tbadkamer > $Rbadkamer && $Tkamer > $Rkamer && $Ttobi > $Rtobi && $Talex > $Ralex && $Sbrander == "On") Schakel($SIbrander, 'Off', 'brander');
		//if($STbrander<$eenuur) Schakel($SIbrander, $Sbrander,'Brander update');
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
		if($Spirliving!='Off'&&$STpirliving<$eenmin) Schakel($SIpirliving,'Off','PIR living');

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
		if($STpirkeuken<$driemin&&$Spirkeuken=='Off'&&$Swasbak=='On'&&$Swerkblad=='Off'&&$Skookplaat=='Off')
			Schakel($SIwasbak,'Off','pir keuken');
		if($Svoordeur=='On'&&$STvoordeur<$tienmin) Schakel($SIvoordeur, 'Off', 'Voordeur uit');
		//slapen-weg bij geen beweging
		if($STpirliving<$time-14400&&$STpirlivingR<$time-14400&&$STpirgarage<$time-14400&&$STpirinkom<$time-14400&&$STpirhall<$time-14400&&$STslapen<$time-14400&&$STweg<$time-14400&&$Sweg=='Off'&&$Sslapen=="Off") {Schakel($SIslapen,'On','slapen');telegram('slapen ingeschakeld na 4 uur geen beweging');}
		if($STpirliving<$twaalfuur&&$STpirlivingR<$twaalfuur&&$STpirgarage<$twaalfuur&&$STpirinkom<$twaalfuur&&$STpirhall<$twaalfuur&&$STslapen<$time-28800&&$STweg<$twaalfuur&&$Sweg=='Off'&&$Sslapen=="On") {Schakel($SIslapen, 'Off','slapen');Schakel($SIweg, 'On','weg');telegram('weg ingeschakeld na 12 uur geen beweging');}

		//meldingen inschakelen indien langer dan 3 uur uit.
		if($Smeldingen!='On' && $STmeldingen<$drieuur) Schakel($SImeldingen, 'On','meldingen');
		//Alles uitschakelen
		if($Sweg=='On'||$Sslapen=='On') {
			if($Sbureel!='Off'&&$STbureel<$tienmin) Schakel($SIbureel, 'Off', 'bureel');
			if($Swerkblad!='Off'&&$STwerkblad<$tienmin) Schakel($SIwerkblad, 'Off', 'werkblad');
			if($Skookplaat!='Off'&&$STkookplaat<$tienmin) Schakel($SIkookplaat, 'Off', 'kookplaat');
			if($Svoordeur!='Off'&&$STvoordeur<$tienmin) Schakel($SIvoordeur, 'Off', 'voordeur');
			if($Skeuken!='Off'&&$STkeuken<$tienmin) Schakel($SIkeuken, 'Off', 'keuken');
			if($Swasbak!='Off'&&$STwasbak<$tienmin) Schakel($SIwasbak, 'Off', 'wasbak');
			if($Szolderg!='Off'&&$STzolderg<$tienmin) Schakel($SIzolderg, 'Off', 'zolderg');
			if($Stv!='Off'&&$STtv<$tienmin) Schakel($SItv, 'Off', 'tv');
			if($Stvled!='Off'&&$STtvled<$tienmin) Schakel($SItvled, 'Off', 'tvled');
			if($Skristal!='Off'&&$STkristal<$tienmin) Schakel($SIkristal, 'Off', 'kristal');
			if($Sdenon!='Off') {
				Schakel($SIdenon, 'Off', 'denon');
				file_get_contents($denon_address.'/MainZone/index.put.asp?cmd0=PutSystem_OnStandby%2FSTANDBY&cmd1=aspMainZone_WebUpdateStatus%2F');
			}
			if($Ssubwoofer!='Off'&&$STsubwoofer<$tienmin) Schakel($SIsubwoofer, 'Off', 'subwoofer');
			if($Sterras!='Off'&&$STterras<$tienmin) Schakel($SIterras, 'Off', 'terras');
			if($Szolder!='Off'&&$STzodler<$tienmin) Schakel($SIzolder, 'Off', 'zolder');
			if($Sbureeltobi!='Off'&&$STbureeltobi<$tienmin) Schakel($SIbureeltobi, 'Off', '');
			if($Deettafel!='Off'&&$STeettafel<$tienmin) Schakel($DIeettafel, 'Off', 'eettafel');
			if($Dzithoek!='Off'&&$STzithoek<$tienmin) Schakel($DIzithoek, 'Off', 'zithoek');

			$items = array('living','badkamer','kamer','tobi','alex');
			foreach ($items as $item) {
				${'setpoint'.$item} = $mc->get('setpoint'.$item);
				if(${'setpoint'.$item}!=0&&${'RT'.$item}<$eenuur) $mc->set('setpoint'.$item,0);
			}
		}
		if($Sweg=='On') {
			if($Slichtbadkamer1!='Off'&&$STlichtbadkamer1<$tienmin) Schakel($SIlichtbadkamer1, 'Off', 'lichtbadkamer1');
			if($Slichtbadkamer2!='Off'&&$STlichtbadkamer2<$tienmin) Schakel($SIlichtbadkamer2, 'Off', 'lichtbadkamer2');
			if($Dkamer!='Off'&&$DTkamer<$tienmin) Schakel($DIkamer, 'Off', 'kamer');
			if($Dtobi!='Off'&&$DTtobi<$tienmin) Schakel($DItobi, 'Off', 'tobi');
		}
		/*
		$rpimem=number_format(get_server_memory_usage(),2);
		$rpicpu=number_format(get_server_cpu_usage(),2);
		if(date('G') == 3) {
			if($rpimem>70) {
				telegram('PiDomoticz '.$rpimem.'% memory usage, clearing');
				shell_exec('/var/www/secure/freememory.sh');
				sleep(5);
				$rpimem=number_format(get_server_memory_usage(),2);
			}
			if($rpimem>70||$rpicpu>2){
				telegram('Rebooting Domoticz: '.$rpimem.' memory, '.$rpicpu.' cpu');
				if($rpimem>90) shell_exec('/var/www/secure/reboot');
			}
		} else {
			if($rpimem>70) {
				telegram('PiDomoticz '.$rpimem.'% memory usage, clearing');
				shell_exec('/var/www/secure/freememory.sh');
				sleep(5);
				$rpimem=number_format(get_server_memory_usage(),2);
			}
			if($rpimem>90||$rpicpu>2){
				telegram('Rebooting Domoticz: '.$rpimem.' memory, '.$rpicpu.' cpu');
				$rpimem=number_format(get_server_memory_usage(),2);
				if($rpimem>90) shell_exec('/var/www/secure/reboot');
			}
		}*/
		$rains=file_get_contents('http://gps.buienradar.nl/getrr.php?lat=50.892880&lon=3.112568');
		$rains=str_split($rains, 11);$totalrain=0;$aantal=0;
		foreach($rains as $rain) {$aantal=$aantal+1;$totalrain=$totalrain+substr($rain,0,3);$averagerain=round($totalrain/$aantal,0);if($aantal==12) break;}
		if($averagerain>=0) $mc->set('averagerain',$averagerain);
		$openweathermap=file_get_contents('http://api.openweathermap.org/data/2.5/weather?id=2787491&APPID=ac3485bbf1a02a81d2525db615021d&units=metric');
		$openweathermap=json_decode($openweathermap,true);
		if(isset($openweathermap['weather']['0']['icon'])) {
			$mc->set('weatherimg',$openweathermap['weather']['0']['icon']);
			$newtemp = round(($openweathermap['main']['temp']+$Tbuiten)/2,1);
			if($newtemp!=$Tbuiten) Udevice(22,0,$newtemp,'Buiten = '.$openweathermap['main']['temp']);
		}
		//KODI
		if($Skodi=='On'&&$STkodi<$driemin) {
			$status = pingDomain('192.168.0.7', 1597);
			if(is_int($status)) {
				sleep(5);
				$status2 = pingDomain('192.168.0.7', 1597);
				if(is_int($status2)) Schakel($SIkodi, 'Off','kodi');
			}
		}
		//diskstation
		if($Sdiskstation=='On'&&$STdiskstation<$vijfmin) {
			$status = pingDomain('192.168.0.10', 1600);
			if(is_int($status)) {
				sleep(5);
				$status2 = pingDomain('192.168.0.10', 1600);
				if(is_int($status2)) Schakel($SIdiskstation, 'Off','diskstation');
			}
		} else if($Sdiskstation=='Off' &&($Sbureeltobi=='On'||$Skodi=='On')) shell_exec('wakeonlan 00:11:32:2c:b7:21');
		$min=date('i');
		switch (true) {
			case($min==1||$min==31): RefreshZwave(7,'cron');break;
			case($min==2||$min==32): RefreshZwave(8,'cron');break;
			case($min==3||$min==33): RefreshZwave(9,'cron');break;
			case($min==4||$min==34): RefreshZwave(11,'cron');break;
			case($min==5||$min==35): RefreshZwave(20,'cron');break;
			case($min==6||$min==36): RefreshZwave(21,'cron');break;
			case($min==9||$min==39): RefreshZwave(22,'cron');break;
			case($min==10||$min==40): RefreshZwave(23,'cron');break;
			case($min==11||$min==41): RefreshZwave(24,'cron');break;
			case($min==12||$min==42): RefreshZwave(25,'cron');break;
			case($min==13||$min==43): RefreshZwave(35,'cron');break;
			case($min==14||$min==44): RefreshZwave(36,'cron');break;
			case($min==15||$min==45): RefreshZwave(37,'cron');break;
			case($min==16||$min==46): RefreshZwave(79,'cron');break;
			case($min==17||$min==47): RefreshZwave(80,'cron');break;
			case($min==18||$min==48): RefreshZwave(84,'cron');break;
		}
		if($mc->get('gcal')<$vijfmin) {
			if(php_sapi_name()=='cli')include('gcal/gcal.php');
			$mc->set('gcal', $time);
		}
		unset($rain,$rains,$avg,$Set,$openweathermap,$Type,$SwitchType,$SubType,$name,$client,$results,$optParams,$service,$event,$calendarId);
	} //END ALL
$execution= microtime(true)-$starttime;$phptime=$execution-$domotime;
unset($domoticz,$dom,$applepass,$appleid,$appledevice,$domoticzurl,$smsuser,$smsapi,$smspassword,$smstofrom,$user,$users,$db,$http_response_header,$_SERVER,$_FILES,$_COOKIE,$_POST);
//End Acties
} else {
	if($all) {
		$domoticzconnection = $mc->get('domoticzconnection');
		$domoticzconnection = $domoticzconnection + 1;
		$mc->set('domoticzconnection',$domoticzconnection);
		if($domoticzconnection==2) telegram('Geen verbinding met Domoticz');
		//else if($domoticzconnection>9) {telegram('Domoticzonnection = '.$domoticzconnection.', rebooting domoticz');shell_exec('/var/www/secure/reboot.sh');}
	}
}
if($authenticated) 	echo '<hr>Number of vars: '.count(get_defined_vars()).'<br/><pre>';print_r(get_defined_vars());echo '</pre>';
if($actions>=1) {
	if(isset($argv[1])&&$arg[1]=="All") $msg='D'.number_format($domotime,2).'|P'.number_format($phptime,2).'|T'.number_format($execution,2).'|M'.$rpimem.'|C'.$rpicpu.'|'.$argv[1].' -> '.$actions.' actions';
	else if(isset($argv[1])) $msg='D'.number_format($domotime,2).'|P'.number_format($phptime,2).'|T'.number_format($execution,2).'|'.$argv[1].' -> '.$actions.' actions';
	else $msg='D'.number_format($domotime,2).'|P'.number_format($phptime,2).'|T'.number_format($execution,2).'| -> '.$actions.' actions';
	logwrite($msg);
}