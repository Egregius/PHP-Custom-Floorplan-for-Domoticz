<?php
error_reporting(E_ALL);
ini_set("display_errors","on");
date_default_timezone_set('Europe/Brussels');
define('time',$_SERVER['REQUEST_TIME']);
$c=json_decode(base64_decode($_REQUEST['c']),true);
$s=json_decode(base64_decode($_REQUEST['s']),true);
$i=json_decode(base64_decode($_REQUEST['i']),true);
$t=json_decode(base64_decode($_REQUEST['t']),true);
$r=str_replace(" ","_",key($c));
if(function_exists(key($c)))
	key($c)();
elseif(function_exists($r))
	$r();
function achterdeur()
{
	global $s,$i,$t;
	if($s['achterdeur']!="Open")
	{
		if(($s['weg']=='On'||$s['slapen']=='On')&&$s['meldingen']=='On')
		{
			if(cget('timealertachterdeur')<time-57)
			{
				sw($i['sirene'],'On');
				$msg='Achterdeur open om '.$t['achterdeur'];
				cset('timealertachterdeur',time);
				telegram($msg,false);
				ios($msg);
			}
		}
	}
}
function alex_set()
{
	verwarming();
}
function alex_temp()
{
	verwarming();
}
function alles($action,$uit=0)
{
	global $s,$i,$t;
	if($action=='On')
	{
		$items=array('eettafel','zithoek','kamer','tobi');
		foreach($items as $item)
			if($s[$item]!='On')
				sl($i[$item],100,$item,300000);
		$items=array('bureel','tvled','kristal','wasbak','keuken','kookplaat','werkblad','inkom','hall','lichtbadkamer1');
		foreach($items as $item)
			if($s[$item]!='On')
				sw($i[$item],'On',$item,300000);
	}
	elseif($action=='Off')
	{
		$items=array('denon','bureel','tv','tvled','kristal','eettafel','zithoek','garage','terras','tuin','voordeur','keuken','werkblad','wasbak','kookplaat','sony','kamer','tobi','alex');
		foreach($items as $item)
			if($s[$item]!='Off'&&strtotime($t[$item])<time-$uit)
				sw($i[$item],'Off',$item,3000000);
		$items=array('lichtbadkamer1','lichtbadkamer2','badkamervuur');
		foreach($items as $item)
			if($s[$item]!='Off'&&strtotime($t[$item])<time-$uit)
				sw($i[$item],'Off',$item,3000000);
	}
	elseif($action=='Slapen')
	{
		$items=array('hall','bureel','denon','tv','tvled','kristal','eettafel','zithoek','garage','terras','tuin','voordeur','keuken','werkblad','wasbak','kookplaat','inkom');
		foreach($items as $item)
			if($s[$item]!='Off')
				sw($i[$item],'Off',$item,2000000);
		$items=array('pirkeuken','pirgarage','pirinkom','pirhall');
		foreach($items as $item)
			if($s[$item]!='Off')
				ud($i[$item],0,'Off',3000000);
	}
}
function badkamer_temp()
{
	global $s,$i,$t;
	if($s['deurbadkamer']=="Open")
	{
		if($s['badkamer_set']!=12&&(strtotime($t['deurbadkamer'])<time-57||$s['lichtbadkamer']=='Off'))
		{
			ud($i['badkamer_set'],0,12,'badkamer_set 12 deur open');$s['badkamer_set']=12.0;
		}
	}
	elseif($s['deurbadkamer']=="Closed"&&$s['heating']=='On')
	{
		if($s['lichtbadkamer']=='On'&&$s['badkamer_set']!=22.5)
		{
			ud($i['badkamer_set'],0,22.5,'badkamer_set 22.5 deur dicht en licht aan');$s['badkamer_set']=22.5;
		}
		elseif($s['lichtbadkamer']=='Off'&&$s['badkamer_set']!=15)
		{
			ud($i['badkamer_set'],0,15,'badkamer_set 15 deur dicht en licht uit');$s['badkamer_set']=15.0;
		}
	}
	$difbadkamer=number_format($s['badkamer_temp']-$s['badkamer_set'],1);
	$timebadkvuur=time-strtotime($t['badkamervuur']);
	if 		($difbadkamer<=-0.2&&$s['badkamervuur']=="Off"&&$timebadkvuur>180)double($i['badkamervuur'],'On','badkamervuur dif = '.$difbadkamer.' was off for '.convertToHours($timebadkvuur));
	elseif($difbadkamer<=-0.1&&$s['badkamervuur']=="Off"&&$timebadkvuur>240)double($i['badkamervuur'],'On','badkamervuur dif = '.$difbadkamer.' was off for '.convertToHours($timebadkvuur));
	elseif($difbadkamer<=	0&&$s['badkamervuur']=="Off"&&$timebadkvuur>360)double($i['badkamervuur'],'On','badkamervuur dif = '.$difbadkamer.' was off for '.convertToHours($timebadkvuur));
	elseif($difbadkamer>=0	&&$s['badkamervuur']=="On"&&$timebadkvuur>30)	double($i['badkamervuur'],'Off','badkamervuur dif = '.$difbadkamer.' was on for '.convertToHours($timebadkvuur));
	elseif($difbadkamer>=-0.2&&$s['badkamervuur']=="On"&&$timebadkvuur>120)	double($i['badkamervuur'],'Off','badkamervuur dif = '.$difbadkamer.' was on for '.convertToHours($timebadkvuur));
	elseif($difbadkamer>=-0.4&&$s['badkamervuur']=="On"&&$timebadkvuur>180)	double($i['badkamervuur'],'Off','badkamervuur dif = '.$difbadkamer.' was on for '.convertToHours($timebadkvuur));
}
function belknop()
{
	global $s,$i;
	if($s['belknop']=="On"&&$s['meldingen']=='On')
	{
		if(cget('timetelegramdeurbel')<time-57)
		{
			cset('timetelegramdeurbel',time);
			if($s['weg']=='Off'&&$s['slapen']=='Off')
				sw($i['deurbel'],'On','deurbel');
			if($s['slapen']=='Off')
			{
				telegram('Deurbel',false,'Kirby');
				ios('Deurbel');
			}
			else 
				telegram('Deurbel',true,'Kirby');
			$ctx=stream_context_create(array('http'=>array('timeout'=>3)));
			file_get_contents('http://192.168.2.11/telegram.php?snapshot=true',false,$ctx);
			file_get_contents('http://192.168.2.11/fifo_command.php?cmd=record%20on%205%2055',false,$ctx);
			if($s['zon']<=10)
				sw($i['voordeur'],'On');
		}
    }
}
function denon()
{
	global $s,$i;
	if($s['denon']=="On")
	{
		$ctx=stream_context_create(array('http'=>array('timeout'=>3)));
		for($x=0;$x<=20;$x++)
		{
			sleep(1);
			$denon=json_decode(json_encode(simplexml_load_string(file_get_contents('http://192.168.2.4/goform/formMainZone_MainZoneXml.xml?_='.time(),false,$ctx))),TRUE);
			if($denon['ZonePower']['value']!='ON')
			{
				file_get_contents('http://192.168.2.4/MainZone/index.put.asp?cmd0=PutZone_OnOff%2FON&cmd1=aspMainZone_WebUpdateStatus%2F',false,$ctx);
				sleep(1);
				file_get_contents('http://192.168.2.4/MainZone/index.put.asp?cmd0=PutZone_InputFunction/TUNER',false,$ctx);
				sleep(1);
				file_get_contents('http://192.168.2.4/MainZone/index.put.asp?cmd0=PutZone_OnOff%2FON&cmd1=aspMainZone_WebUpdateStatus%2F&ZoneName=ZONE2',false,$ctx);
			}
			else break;
		}
	}
}
function deurbadkamer()
{
	global $s,$i;
	if($s['deurbadkamer']=="Open")
	{
		if($s['slapen']=='Off'||(time>strtotime('6:00')&&time<strtotime('12:00')))
		{
			if($s['lichtbadkamer1']=='Off')
				sw($i['lichtbadkamer1'],'On','lichtbadkamer1');
			if($s['lichtbadkamer2']=='On')
				sw($i['lichtbadkamer2'],'Off','lichtbadkamer2');
		}
		else
		{
			if($s['lichtbadkamer2']=='Off')
				sw($i['lichtbadkamer2'],'On','lichtbadkamer2');
			if($s['lichtbadkamer1']=='On')
				sw($i['lichtbadkamer1'],'Off','lichtbadkamer1');
		}
	}
	badkamer_temp();
}
function inkomvoordeur()
{
	global $s,$t;
	if(strtotime($t['inkom'])<time-5&&strtotime($t['voordeur'])<time-5)
		RefreshZwave(8);
}
function kamer_set()
{
	verwarming();
}
function kamer_temp()
{
	verwarming();
}
function keukenzolderg()
{
	global $s,$t;
	if(strtotime($t['keuken'])<time-5&&strtotime($t['zolderg'])<time-5)
		RefreshZwave(5);
}
function lichtbadkamer()
{
	global $s,$t;
	zon();
	if(strtotime($t['lichtbadkamer1'])<time-5&&strtotime($t['lichtbadkamer1'])<time-5)
	{
		sleep(5);
		RefreshZwave(11);
	}
}
function living_set()
{
	verwarming();
}
function living_temp()
{
	verwarming();
}
function minihall1s()
{
	global $s,$i;
	if($s['minihall1s']=="On")
	{
		alles('Slapen');
		if($s['slapen']=='Off')
			sw($i['slapen'],'On','slapen');
	}
	if($s['lichten_auto']=='Off')
		sw($i['lichten_auto'],'On','lichten auto aan');
	/*if($s['luifel']!='Open')sw($i['luifel'],'Off','zonneluifel dicht');*/
}
function minihall2s()
{
	global $i;
	sw($i['zoldertrap'],'Off','zoldertrap open');
}
function minihall3s()
{
	global $s,$i;
	if($s['hall']=='Off')
		sw($i['hall'],'On','hall');
	if($s['slapen']=='On')
		sw($i['slapen'],'Off','slapen');
}
function minihall4s()
{
	global $i;
	sw($i['zoldertrap'],'On','zoldertrap toe');
}
function minihall1l()
{
	minihall2l();
}
function minihall2l()
{
	global $i;
	sl($i['alex'],2,'Alex');
}
function minihall3l()
{
	minihall4l();
}
function minihall4l()
{
	global $i;
	sl($i['tobi'],18,'dimmer Tobi');
	cset('dimmertobi',1);
}
function miniliving1s()
{
	global $s,$i;
	if($s['denon']=='Off')
		sw($i['denon'],'On','Denon',100000);
	if($s['tv']=='Off')
		sw($i['tv'],'On','TV',100000);
	if($s['zon']=0&&$s['tvled']=='Off')
		sw($i['tvled'],'On','tvled',1000);
	elseif($s['tv']=='On'&&$s['tvled']=='Off')
		sw($i['tvled'],'On','tvled',1000);
	elseif($s['tv']=='On'&&$s['tvled']=='On')
		sw($i['tvled'],'Off','tvled',1000);
	$ctx=stream_context_create(array('http'=>array('timeout'=>3)));
	file_get_contents('http://192.168.2.4/MainZone/index.put.asp?cmd0=PutZone_InputFunction/SAT/CBL',false,$ctx);
	usleep(800000);
	file_get_contents('http://192.168.2.4/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-42.0',false,$ctx);
}
function miniliving2s()
{
	global $s,$i;
	if($s['kodi']=='Off')
		sw($i['kodi'],'On','Kodi');
	if($s['denon']=='Off')
		sw($i['denon'],'On','Denon');
	if($s['tv']=='Off')
		sw($i['tv'],'On','TV');
	if($s['zon']<100)
	{
		if($s['tvled']=='Off')
			sw($i['tvled'],'On','tvled');
	}
	elseif($s['zon']>300)
	{
		if($s['kristal']=='On')
			sw($i['kristal'],'Off','kristal');
		if($s['tvled']=='On')
			sw($i['tvled'],'Off','tvled');
	}
	$ctx=stream_context_create(array('http'=>array('timeout'=>3)));
	file_get_contents('http://192.168.2.4/MainZone/index.put.asp?cmd0=PutZone_InputFunction/DVD',false,$ctx);
	usleep(800000);
	file_get_contents('http://192.168.2.4/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-40.0',false,$ctx);
}
 
function miniliving3s()
{
	$ctx=stream_context_create(array('http'=>array('timeout'=>3)));
	$denon=json_decode(json_encode(simplexml_load_string(file_get_contents('http://192.168.2.4/goform/formMainZone_MainZoneXml.xml?_='.time,false,$ctx))),TRUE);
	if($denon)
	{
		$denon['MasterVolume']['value']=='--'?$setvalue=-55:$setvalue=$denon['MasterVolume']['value'];
		$setvalue=$setvalue-3;
		if($setvalue>-10)
			$setvalue=-10;
		if($setvalue<-80)
			$setvalue=-80;
		$volume=80+$setvalue;
		usleep(100000);
		file_get_contents('http://192.168.2.4/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/'.$setvalue.'.0',false,$ctx);
	}
}
function miniliving4s()
{
	$ctx=stream_context_create(array('http'=>array('timeout'=>3)));
	$denon=json_decode(json_encode(simplexml_load_string(file_get_contents('http://192.168.2.4/goform/formMainZone_MainZoneXml.xml?_='.time,false,$ctx))),TRUE);
	if($denon)
	{
		$denon['MasterVolume']['value']=='--'?$setvalue=-55:$setvalue=$denon['MasterVolume']['value'];
		$setvalue=$setvalue+3;
		if($setvalue>-10)
			$setvalue=-10;
		elseif($setvalue<-80)
			$setvalue=-80;
		$volume=80+$setvalue;
		usleep(100000);
		file_get_contents('http://192.168.2.4/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/'.$setvalue.'.0',false,$ctx);
	}
}
function miniliving1l()
{
	global $s,$i;
	if($s['denon']=='Off')
		sw($i['denon'],'On','Denon',100000);
	if($s['tv']=='On')
		sw($i['tv'],'Off','TV',100000);
	if($s['zon']>0)
	{
		if($s['kristal']=='On')
			sw($i['kristal'],'Off','Kristal');
		if($s['tvled']=='On')
			sw($i['tvled'],'Off','tvled');
	}
	sleep(1);
	$ctx=stream_context_create(array('http'=>array('timeout'=>3)));
	file_get_contents('http://192.168.2.4/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-50.0',false,$ctx);
	usleep(800000);
	file_get_contents('http://192.168.2.4/MainZone/index.put.asp?cmd0=PutZone_InputFunction/TUNER',false,$ctx);
}
function miniliving2l()
{
	global $i;
	sw($i['bureel'],'Toggle','Bureel');
}
function miniliving3l()
{
	global $s,$i;
	sl($i['eettafel'],9,'dimmer eettafel');
	if($s['tv']=='On')
		sw($i['tv'],'Off','TV');
	if($s['kristal']=='On')
		sw($i['kristal'],'Off','kristal');
	if($s['tvled']=='On')
		sw($i['tvled'],'Off','tvled');
	$ctx=stream_context_create(array('http'=>array('timeout'=>3)));
	file_get_contents('http://192.168.2.4/MainZone/index.put.asp?cmd0=PutZone_OnOff%2FON&cmd1=aspMainZone_WebUpdateStatus%2F',false,$ctx);
	usleep(800000);
	file_get_contents('http://192.168.2.4/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-55.0',false,$ctx);
	usleep(800000);
	file_get_contents('http://192.168.2.4/MainZone/index.put.asp?cmd0=PutZone_InputFunction/TUNER',false,$ctx);
}
function miniliving4l()
{
	global $s,$i;
	$items=array('pirkeuken','pirgarage','pirinkom','pirhall');
	foreach($items as $item)
		if($s[$item]!='Off')
			ud($i[$item],0,'Off',$item);
	$items=array('eettafel','zithoek','garage','inkom','hall','keuken','werkblad','wasbak','kookplaat');
	foreach($items as $item)
		if($s[$item]!='Off')
			sw($i[$item],'Off',$item);
}
function alarm($naam,$slapen=true)
{
	global $s,$i,$t;
	if(($s['weg']=='On'||($s['slapen']=='On'&&$slapen==true))&&$s['meldingen']=='On'&&strtotime($t['weg'])<time-178&&strtotime($t['slapen'])<time-178)
	{
		if(cget('timealert'.$naam)<time-57)
		{
			sw($i['sirene'],'On');
			$msg='Beweging '.$naam.' om '.strftime("%H:%M:%S",time);
			cset('timealert'.$naam,time);
			telegram($msg,false);
			ios($msg);
		}
	}
}
function pirgarage()
{
	global $s,$i;
	if($s['pirgarage']=="On")
	{
		if((time<strtotime('10:30')||time>strtotime('18:30')||$s['zon']<1200)&&$s['garage']=='Off')
			sw($i['garage'],'On','garage');
		alarm('garage');
	}
}
function pirhall()
{
	global $s,$i;
	if($s['pirhall']=="On")
	{
		if($s['slapen']=='Off'&&$s['hall']=='Off'&&(time<strtotime('8:00')||$s['zon']<100))
			sw($i['hall'],'On','hal',100000);
			if($s['inkom']=='Off'&&(time<strtotime('8:00')||$s['zon']<200))
				sw($i['inkom'],'On','inkom');
		alarm('hall',false);
	}
}
function pirinkom()
{
	global $s,$i;
	if($s['pirinkom']=="On")
	{
		if($s['inkom']=='Off'&&(time<strtotime('8:00')||$s['zon']<100))
			sw($i['inkom'],'On','inkom',100000);
		if($s['slapen']=='Off'&&$s['hall']=='Off'&&(time<strtotime('8:00')||$s['zon']<150))
			sw($i['hall'],'On','hall');alarm('inkom');
	}
}
function pirkeuken()
{
	global $s,$i;
	if($s['pirkeuken']=="On")
	{
		if($s['keuken']=='Off'&&$s['wasbak']=='Off'&&$s['werkblad']=='Off'&&$s['kookplaat']=='Off'&&$s['zon']<500)
			sw($i['wasbak'],'On','wasbak');alarm('keuken');
	}
}
function pirliving()
{
	global $s,$i;
	if($s['pirliving']=="On")
	{
		if($s['denon']=='Off'&&$s['weg']=='Off'&&$s['slapen']=='Off')
		{
			if($s['zon']<100)
			{
				if($s['wasbak']=='Off')
					sw($i['wasbak'],'On','wasbak',300000);
				if($s['bureel']=='Off')
					sw($i['bureel'],'On','bureel',300000);
			}
			miniliving1l();
		}
		alarm('living');
	}
}
function poort()
{
	global $s,$i;
	if($s['poort']=="On")
	{
		if($s['zon']<1000&&$s['garage']=='Off')
			sw($i['garage'],'On','garage');
		alarm('poort');
	}
}
function raamliving()
{
	global $s;
	if($s['raamliving']=="On")
		alarm('raamliving');
}
function remoteslapen()
{
	global $s,$i;
	if($s['remoteslapen']=="On")
	{
		$kamer=filter_var($s['kamer'],FILTER_SANITIZE_NUMBER_INT);
		if($s['slapen']=='Off'&&$kamer!=16)
			sl($i['kamer'],17);
		elseif($s['slapen']=='Off'&&$kamer==16)
		{
			sl($i['kamer'],13);
			minihall1s();
		}
		elseif($s['slapen']=='On'&&$kamer==12)
		{
			sl($i['kamer'],11);
			cset('dimmerkamer',1);
		}
	}
	else minihall3s();
}
function remoteweg()
{
	weg();
}
function remotezolder()
{
	global $s;
	$s['remotezolder']=='On'?minihall2s():minihall4s();
}
 
function SD($naam)
{
	global $i;
	$msg='Rook gedecteerd bij '.$naam.'!';
	telegram($msg,false,'Kirby');
	ios($msg);
	resetsecurity($i['SD'.$naam],$naam);
}
function SDalex()
{
	global $s;
	if($s['SDalex']=="On")
		SD('alex');
}
function SDbadkamer()
{
	global $s;
	if($s['SDbadkamer']=="On")
		SD('badkamer');
}
function SDkamer()
{
	global $s;
	if($s['SDkamer']=="On")
		SD('kamer');
}
function SDliving()
{
	global $s;
	if($s['SDliving']=="On")
		SD('living');
}
function SDtobi()
{
	global $s;
	if($s['SDtobi']=="On")
		SD('tobi');
}
function SDzolder()
{
	global $s;
	if($s['SDzolder']=="On")
		SD('zolder');
}
function sirene()
{
	global $s,$i;
	if($s['sirene']=="On")
	{
		sw($i['deurbel'],'On','Deurbel sirene');
		sleep(2);
		sw($i['sirene'],'Off','sirene');
	}
}
function slapen()
{
	global $s,$i;
	if($s['slapen']=="On")
	{
		if($s['achterdeur']!='Open')
		{
			sw($i['deurbel'],'On');
			telegram('Opgelet: Achterdeur open!',false,'Kirby');
		}
		if($s['raamliving']!='Closed')
		{
			sw($i['deurbel'],'On');
			telegram('Opgelet: Raam Living open!',false,'Kirby');
		}
		if($s['poort']!='Closed')
		{
			sw($i['deurbel'],'On');
			telegram('Opgelet: Poort open!',false,'Kirby');
		}
		alles('Slapen');
		double($i['GroheRed'],'Off');
		double($i['badkamervuur'],'Off');
		/*if($s['luifel']!='Open')sw($i['luifel'],'Off','zonneluifel dicht');*/
	}
	if($s['lichten_auto']=='Off')
		sw($i['lichten_auto'],'On','lichten auto aan');
	
}
function sony()
{
	global $s;
	RefreshZwave(23,'switch','sony',$s['meldingen']);
}
function tobi_set()
{
	verwarming();
}
function tobi_temp()
{
	verwarming();
}
function verwarming()
{
	if(cget('time-verwarming')<time-10)
	{
		cset('time-verwarming',time);
		global $s,$i,$t;
		$weer=unserialize(cget('weer'));
		$buienradar=$weer['buien'];
		$buiten_temp=$weer['buiten_temp'];
		$wind=$weer['wind'];
		if($s['weg']=='On')
		{
			if($s['heating']!='Off'&&strtotime($t['heating'])<time-3598)
			{
				sw($i['heating'],'Off','heating');
				$s['heating']='Off';
			}
		}
		else
		{
			if($s['heating']!='On')
			{
				sw($i['heating'],'On','heating');
				$s['heating']='On';
			}
		}
		$Setkamer=12;
		$setpointkamer=cget('setpointkamer');
		if($setpointkamer!=0&&strtotime($t['kamer_set'])<time-3598)
		{
			cset('setpointkamer',0);
			$setpointkamer=0;
		}
		if($setpointkamer!=2)
		{
			if($buiten_temp<14&&$s['raamkamer']=='Closed'&&$s['heating']=='On'&&(strtotime($t['raamkamer'])<time-7198||time>strtotime('21:00')))
			{
				$Setkamer=12.0;if(time<strtotime('5:00')||time>strtotime('21:00'))
					$Setkamer=16;
			}
			if($s['kamer_set']!=$Setkamer)
			{
				ud($i['kamer_set'],0,$Setkamer,'Rkamer_set');
				$s['kamer_set']=$Setkamer;
			}
		}
		$Settobi=12;
		$setpointtobi=cget('setpointtobi');
		if($setpointtobi!=0&&strtotime($t['tobi_set'])<time-3598)
		{
			cset('setpointtobi',0);
			$setpointtobi=0;
		}
		if($setpointtobi!=2)
		{
			if($buiten_temp<14&&$s['raamtobi']=='Closed'&&$s['heating']=='On'&&(strtotime($t['raamtobi'])<time-7198||time>strtotime('21:00')))
			{
				$Settobi=12.0;
				if(date('W')%2==1)
				{
					if(date('N')==3)
						if(time>strtotime('21:00'))
							$Settobi=16;
					elseif(date('N')==4)
						if(time<strtotime('5:00')||time>strtotime('21:00'))
							$Settobi=16;
					elseif(date('N')==5)
						if(time<strtotime('5:00'))
							$Settobi=16;
				}
				else
				{
					if(date('N')==3)
					{
						if(time>strtotime('21:00'))$Settobi=16;
					}
					elseif(in_array(date('N'),array(4,5,6)))
					{
						if(time<strtotime('5:00')||time>strtotime('21:00'))
							$Settobi=16;
					}
					elseif(date('N')==7)
					{
						if(time<strtotime('5:00'))
							$Settobi=16;
					}
				}
			}
			if(isset($s['tobi_set'])&&$s['tobi_set']!=$Settobi)
			{
				ud($i['tobi_set'],0,$Settobi,'Rtobi_set');
				$s['tobi_set']=$Settobi;
			}
		}
		$Setalex=12;
		$setpointalex=cget('setpointalex');
		if($setpointalex!=0&&strtotime($t['alex_set'])<time-28795)
		{
			cset('setpointalex',0);
			$setpointalex=0;
		}
		if($setpointalex!=2)
		{
			if($buiten_temp<17&&$s['raamalex']=='Closed'&&$s['heating']=='On'&&(strtotime($t['raamalex'])<time-7198||time>strtotime('20:00')))
			{
				$Setalex=12;
				if(time<strtotime('6:00')||time>strtotime('20:00'))
					$Setalex=18.5;
			}
			if($s['alex_set']!=$Setalex)
			{
				ud($i['alex_set'],0,$Setalex,'Ralex_set');
				$s['alex_set']=$Setalex;
			}
		}
		$Setliving=14;
		$setpointliving=cget('setpointliving');
		if($setpointliving!=0&&strtotime($t['living_set'])<time-10795)
		{
			cset('setpointliving',0);
			$setpointliving=0;
		}
		if($setpointliving!=2)
		{
			if($buiten_temp<20&&$s['heating']=='On'&&$s['raamliving']=='Closed')
			{
				$Setliving=17;
				if(time>=strtotime('5:00')&&time<strtotime('5:12'))$s['slapen']=='On'?$Setliving=17.0:$Setliving=18.0;
				elseif(time>=strtotime('5:12')&&time<strtotime('5:24'))$s['slapen']=='On'?$Setliving=17.3:$Setliving=18.2;
				elseif(time>=strtotime('5:24')&&time<strtotime('5:36'))$s['slapen']=='On'?$Setliving=17.6:$Setliving=18.4;
				elseif(time>=strtotime('5:36')&&time<strtotime('5:48'))$s['slapen']=='On'?$Setliving=17.9:$Setliving=18.6;
				elseif(time>=strtotime('5:48')&&time<strtotime('6:00'))$s['slapen']=='On'?$Setliving=18.2:$Setliving=18.8;
				elseif(time>=strtotime('6:00')&&time<strtotime('6:12'))$s['slapen']=='On'?$Setliving=18.4:$Setliving=19.0;
				elseif(time>=strtotime('6:12')&&time<strtotime('6:24'))$s['slapen']=='On'?$Setliving=18.6:$Setliving=19.2;
				elseif(time>=strtotime('6:24')&&time<strtotime('6:36'))$s['slapen']=='On'?$Setliving=18.8:$Setliving=19.4;
				elseif(time>=strtotime('6:36')&&time<strtotime('6:48'))$s['slapen']=='On'?$Setliving=19.0:$Setliving=19.6;
				elseif(time>=strtotime('6:48')&&time<strtotime('7:00'))$s['slapen']=='On'?$Setliving=19.2:$Setliving=19.8;
				elseif(time>=strtotime('7:00')&&time<strtotime('7:20'))$s['slapen']=='On'?$Setliving=19.4:$Setliving=20.0;
				elseif(time>=strtotime('7:20')&&time<strtotime('8:10'))$s['slapen']=='On'?$Setliving=19.6:$Setliving=20.0;
				elseif(time>=strtotime('8:10')&&time<strtotime('8:20'))$s['slapen']=='On'?$Setliving=19.8:$Setliving=20.0;
				elseif(time>=strtotime('8:20')&&time<strtotime('8:30'))$s['slapen']=='On'?$Setliving=19.8:$Setliving=20.2;
				elseif(time>=strtotime('8:30')&&time<strtotime('19:55'))$s['slapen']=='On'?$Setliving=20.0:$Setliving=20.5;
				elseif(time>=strtotime('19:55')&&time<strtotime('21:00'))$s['slapen']=='On'?$Setliving=20.0:$Setliving=20.0;
				elseif(time>=strtotime('21:00')&&time<strtotime('23:00'))$s['slapen']=='On'?$Setliving=20.0:$Setliving=19.5;
			}
			if($s['living_set']!= $Setliving)
			{
				ud($i['living_set'],0,$Setliving,'Rliving_set');
				$s['living_set']=$Setliving;
			}
		}
		$kamers=array('living','tobi','alex','kamer');
		$bigdif=100;
		$timebrander=time-strtotime($t['brander']);
		foreach($kamers as $kamer)
		{
			${'dif'.$kamer}=number_format($s[$kamer.'_temp']-$s[$kamer.'_set'],1);
			if(${'dif'.$kamer}>9.9)
				${'dif'.$kamer}=9.9;
			if(${'dif'.$kamer}<$bigdif&&$kamer!='kamer')
				$bigdif=${'dif'.$kamer};
			${'Set'.$kamer}=$s[$kamer.'_set'];
		}
		foreach($kamers as $kamer)
		{
			if(${'dif'.$kamer}<=number_format(($bigdif+ 0.2),1)&&${'dif'.$kamer}<2)
				${'RSet'.$kamer}=setradiator($kamer,${'dif'.$kamer},true,$s[$kamer.'_set']);
			else
				${'RSet'.$kamer}=setradiator($kamer,${'dif'.$kamer},false,$s[$kamer.'_set']);
			if(${'dif'.$kamer}<=$bigdif)
				$coldest=$kamer;
			else
				$coldest='';
		}
		if($s['kamerZ']!=$RSetkamer)
		{
			lg('Danfoss KamerZ was '.$s['kamerZ'].',nieuw='.$RSetkamer);
			ud($i['kamerZ'],0,$RSetkamer,'RkamerZ',2000000);
		}
		if($s['tobiZ'] !=$RSettobi)
		{
			lg('Danfoss tobiZ was '.$s['tobiZ'].',nieuw='.$RSettobi);
			ud($i['tobiZ'],0,$RSettobi,'RtobiZ',2000000);
		}
		if($s['alexZ'] !=$RSetalex)
		{
			lg('Danfoss alexZ was '.$s['alexZ'].',nieuw='.$RSetalex);
			ud($i['alexZ'],0,$RSetalex,'RalexZ',2000000);
		}
		//if($s['badkamerZ']!=$RSetbadkamer)ud($i['badkamerZ'],0,$RSetbadkamer,'RbadkamerZ');
		if($s['livingZ'] !=$RSetliving)
		{
			lg('Danfoss livingZ was '.$s['livingZ'].',nieuw='.$RSetliving);
			ud($i['livingZ'], 0,$RSetliving,'RlivingZ',2000000);
		}
		if($s['livingZZ']!=$RSetliving)
		{
			lg('Danfoss livingZZ was '.$s['livingZZ'].',nieuw='.$RSetliving);
			ud($i['livingZZ'],0,$RSetliving,'RlivingZZ',2000000);
		}
		if($s['livingZE']!=$RSetliving)
		{
			lg('Danfoss livingZE was '.$s['kamerZ'].',nieuw='.$RSetliving);
			ud($i['livingZE'],0,$RSetliving,'RlivingZE',2000000);
		}
		if 		($bigdif<=-0.6&&$s['brander']=="Off"&&$timebrander>60)	double($i['brander'],'On', 'brander dif = '.$bigdif.' in '.$coldest.', was off for '.convertToHours($timebrander));
		elseif($bigdif<=-0.5&&$s['brander']=="Off"&&$timebrander>120)	double($i['brander'],'On', 'brander dif = '.$bigdif.' in '.$coldest.', was off for '.convertToHours($timebrander));
		elseif($bigdif<=-0.4&&$s['brander']=="Off"&&$timebrander>180)	double($i['brander'],'On', 'brander dif = '.$bigdif.' in '.$coldest.', was off for '.convertToHours($timebrander));
		elseif($bigdif<=-0.3&&$s['brander']=="Off"&&$timebrander>300)	double($i['brander'],'On', 'brander dif = '.$bigdif.' in '.$coldest.', was off for '.convertToHours($timebrander));
		elseif($bigdif<=-0.2&&$s['brander']=="Off"&&$timebrander>450)	double($i['brander'],'On', 'brander dif = '.$bigdif.' in '.$coldest.', was off for '.convertToHours($timebrander));
		elseif($bigdif<=-0.1&&$s['brander']=="Off"&&$timebrander>600)	double($i['brander'],'On', 'brander dif = '.$bigdif.' in '.$coldest.', was off for '.convertToHours($timebrander));
		elseif($bigdif<= 0	&&$s['brander']=="Off"&&$timebrander>2400)	double($i['brander'],'On', 'brander dif = '.$bigdif.' in '.$coldest.', was off for '.convertToHours($timebrander));
		elseif($bigdif>  0	&&$s['brander']=="On" &&$timebrander>30)	double($i['brander'],'Off','brander dif = '.$bigdif.', was on for '.convertToHours($timebrander));
		elseif($bigdif>= 0	&&$s['brander']=="On" &&$timebrander>120)	double($i['brander'],'Off','brander dif = '.$bigdif.' in '.$coldest.', was on for '.convertToHours($timebrander));
		elseif($bigdif>=-0.1&&$s['brander']=="On" &&$timebrander>180)	double($i['brander'],'Off','brander dif = '.$bigdif.' in '.$coldest.', was on for '.convertToHours($timebrander));
		elseif($bigdif>=-0.2&&$s['brander']=="On" &&$timebrander>240)	double($i['brander'],'Off','brander dif = '.$bigdif.' in '.$coldest.', was on for '.convertToHours($timebrander));
		elseif($bigdif>=-0.3&&$s['brander']=="On" &&$timebrander>300)	double($i['brander'],'Off','brander dif = '.$bigdif.' in '.$coldest.', was on for '.convertToHours($timebrander));
		elseif($bigdif>=-0.4&&$s['brander']=="On" &&$timebrander>360)	double($i['brander'],'Off','brander dif = '.$bigdif.' in '.$coldest.', was on for '.convertToHours($timebrander));
		elseif($bigdif>=-0.5&&$s['brander']=="On" &&$timebrander>420)	double($i['brander'],'Off','brander dif = '.$bigdif.' in '.$coldest.', was on for '.convertToHours($timebrander));
		elseif($bigdif>=-0.6&&$s['brander']=="On" &&$timebrander>900)	double($i['brander'],'Off','brander dif = '.$bigdif.' in '.$coldest.', was on for '.convertToHours($timebrander));
		}
}
function wasbakkookplaat()
{
	global $t;
	if(strtotime($t['wasbak'])<time-5&&strtotime($t['kookplaat'])<time-5)
		RefreshZwave(61);
}
function water()
{
	global $s;
	RefreshZwave(19,'switch','water',$s['meldingen']);
}
function weg()
{
	global $s,$i;
	if($s['weg']=="On")
	{
		if($s['achterdeur']!='Open')
		{
			sw($i['deurbel'],'On','',0);
			telegram('Opgelet: Achterdeur open!',false,'Kirby');
		}
		if($s['raamliving']!='Closed')
		{
			sw($i['deurbel'],'On','',0);
			telegram('Opgelet: Raam Living open!',false,'Kirby');
		}
		alles('Off');
		double($i['GroheRed'],'Off');
		double($i['badkamervuur'],'Off');
	}
	else
	{
		if($s['poortrf']=='Off')
			sw($i['poortrf'],'On','Poort RF',0);
	}
}
function werkbladtuin()
{
	global $t;
	if(strtotime($t['werkblad'])<time-5&&strtotime($t['werkblad2'])<time-5)
		RefreshZwave(22);
}
function zonneluifel()
{
	global $s,$i,$t;
	$weer=unserialize(cget('weer'));
	$buienradar=$weer['buien'];
	$buiten_temp=$weer['buiten_temp'];
	$wind=$weer['wind'];
	$maxbuien=20;
	$maxwolken=80;
	$zonopen=1500;
	$zontoe=200;
	if(in_array($weer['wind_dir'],array('W','S','SE')))
		$maxwind=6;
	else
		$maxwind=8;
	if($s['luifel']!='Open'&&($wind>=$maxwind||$buienradar>=$maxbuien||$s['zon']<$zontoe))
	{
		lg('  --- Luifel: Wind='.$wind.'|Buien='.round($buienradar,0).'|Zon='.$s['zon'].'|Luifel='.$s['luifel'].'|Last='.$t['luifel']);
		if($wind>=$maxwind)
		{
			sw($i['luifel'],'Off');
			if(strtotime($t['luifel'])<time-3598)
				sw($i['luifel'],'Off');
		}
		elseif($buienradar>=$maxbuien)
		{
			sw($i['luifel'],'Off');
			if(strtotime($t['luifel'])<time-3598)
				sw($i['luifel'],'Off');
			}
		elseif($s['zon']<$zontoe)
		{
			sw($i['luifel'],'Off');
			if(strtotime($t['luifel'])<time-3598)
				sw($i['luifel'],'Off');
		}
	}
	elseif($s['luifel']!='Closed'&&time>strtotime('10:25')&&$wind<$maxwind-1&&$buienradar<$maxbuien-1&&$s['living_temp']>22&&$s['zon']>$zonopen&&strtotime($t['luifel'])<time-598)
	{
		lg('  --- Luifel: Wind='.$wind.'|Buien='.round($buienradar,0).'|Zon='.$s['zon'].'|Luifel='.$s['luifel'].'|Last='.$t['luifel']);
		sw($i['luifel'],'On',$msg);
		$s['luifel']=='Open';
	}
}
function zon()
{
	global $s,$i,$t;
	$weer=unserialize(cget('weer'));
	$buienradar=$weer['buien'];
	$buiten_temp=$weer['buiten_temp'];
	$wind=$weer['wind'];
	if(cget('time-wunderground')<time-(86400/500))
	{
		$wu=json_decode(curl('http://api.wunderground.com/api/c123456789e9413e/conditions/q/BX/Beitem.json'),true);
		if(isset($wu['current_observation']))
		{
			cset('time-wunderground',time);
			$lastobservation=cget('time-observation');
			if(isset($wu['current_observation']['estimated']['estimated']))
			{
				lg('Wunderground '.number_format($wu['current_observation']['feelslike_c'],1).'	'.number_format($wu['current_observation']['temp_c'],1).'	'.number_format($wu['current_observation']['wind_kph'],1).' '.number_format($wu['current_observation']['wind_gust_kph'],1).' ESTIMATED');
				goto exitwunderground;
			}
			elseif($wu['current_observation']['observation_epoch']<=$lastobservation)
			{
				lg('Wunderground '.number_format($wu['current_observation']['feelslike_c'],1).'	'.number_format($wu['current_observation']['temp_c'],1).'	'.number_format($wu['current_observation']['wind_kph'],1).' '.number_format($wu['current_observation']['wind_gust_kph'],1).' OBSERVATION OLDER THAN 1 HOUR');
				goto exitwunderground;
			}
			else
			{
				cset('time-observation',$wu['current_observation']['observation_epoch']);
			}
			if(!isset($weer['buiten_temp']))
				$weer['buiten_temp']=$wu['current_observation']['feelslike_c'];
			elseif($wu['current_observation']['feelslike_c']>$weer['buiten_temp']+0.2)
				$weer['buiten_temp']=$weer['buiten_temp']+0.2;
			elseif($wu['current_observation']['feelslike_c']<$weer['buiten_temp']-0.2)
				$weer['buiten_temp']=$weer['buiten_temp']-0.2;
			else
				$weer['buiten_temp']=round($wu['current_observation']['feelslike_c'],1);
			lg('Wunderground '.number_format($wu['current_observation']['feelslike_c'],1).'	'.number_format($wu['current_observation']['temp_c'],1).'	'.number_format($wu['current_observation']['wind_kph'],1).' '.number_format($wu['current_observation']['wind_gust_kph'],1).' Newtemp='.$weer['buiten_temp']);
			$weer['wind']=round(max(array($wu['current_observation']['wind_kph'],$wu['current_observation']['wind_gust_kph'])),0);
			$weer['wind_dir']=$wu['current_observation']['wind_dir'];
			$weer['icon']=str_replace('http://','https://',$wu['current_observation']['icon_url']);
		}
		exitwunderground:
		$rains=curl('http://gadgets.buienradar.nl/data/raintext/?lat=51.89&lon=4.11');
		$rains=str_split($rains,11);
		$totalrain=0;
		$aantal=0;
		foreach($rains as $rain)
		{
			$aantal=$aantal+1;
			$totalrain=$totalrain+substr($rain,0,3);
			if($aantal==7)
				break;
		}
		$newbuienradar=pow(10,((($totalrain/7)-109)/32));
		if(isset($newbuienradar))
			$weer['buien']=$newbuienradar;
		$uweer=serialize($weer);
		cset('weer',$uweer);
	}
	if(cget('time-cron')<time-57)
	{
		cset('time-cron',time);
		verwarming();
		badkamer_temp();
		$items=array('eettafel','zithoek','tobi','kamer','alex');
		foreach($items as $item)
		{
			if($s[$item]!='Off')
			{
				if(strtotime($t[$item])<time-29)
				{
					$action=cget('dimmer'.$item);
					if($action==1)
					{
						$level=filter_var($s[$item],FILTER_SANITIZE_NUMBER_INT);
						$level=floor($level*0.95);
						if($level<2)
							$level=0;
						if($level==20)
							$level=19;
						sl($i[$item],$level,$item);
						if($level==0)
							cset('dimmer'.$item,0);
					}
					elseif($action==2)
					{
						$level=filter_var($s[$item],FILTER_SANITIZE_NUMBER_INT);
						$level=$level+2;
						if($level==20)
							$level=21;
						if($level>30)
							$level=30;
						sl($i[$item],$level,$item);
						if($level==30)
							cset('dimmer'.$item,0);
					}
				}
			}
		}
		if($s['water']=='On'&&strtotime($t['water']<time-3598))
			sw($i['water'],'Off');
		if($s['pirlivingR']!='Off'&&strtotime($t['pirlivingR'])<time-57)
			sw($i['pirlivingR'],'Off','Reset pirlivingR');
		if($s['pirgarage']=='Off'&&strtotime($t['pirgarage'])<time-178&&strtotime($t['poort'])<time-178&&strtotime($t['garage'])<time-178&&$s['garage']=='On'&&$s['lichten_auto']=='On')
			sw($i['garage'],'Off','licht garage');
		if(strtotime($t['pirinkom'])<time-118&&strtotime($t['pirhall'])<time-118&&strtotime($t['inkom'])<time-118&&strtotime($t['hall'])<time-118&&$s['lichten_auto']=='On')
		{
			if($s['inkom']=='On')
				sw($i['inkom'],'Off','licht inkom');
			if($s['hall']=='On')
				sw($i['hall'],'Off','licht hall');
		}
		if(strtotime($t['pirkeuken'])<time-118&&strtotime($t['wasbak'])<time-118&&$s['pirkeuken']=='Off'&&$s['wasbak']=='On'&&$s['werkblad']=='Off'&&$s['keuken']=='Off'&&$s['kookplaat']=='Off')
			sw($i['wasbak'],'Off','wasbak pir keuken');
		if($s['weg']=='Off'&&$s['slapen']=='Off')
		{
			if($s['GroheRed']=='Off')
				if(strtotime($t['slapen'])<time-900)
					double($i['GroheRed'],'On',$item);
			if($s['poortrf']=='Off')
				if(strtotime($t['slapen'])<time-900)
					double($i['poortrf'],'On',$item);
		}
		$items=array('living_temp','badkamer_temp','kamer_temp','tobi_temp','alex_temp','zolder_temp');
		foreach($items as $item)
			$weer[$item]=$s[$item];
		$uweer=serialize($weer);
		cset('weer',$uweer);
		$stamp=sprintf("%s",date("Y-m-d H:i"));
		$db=new mysqli('localhost','kodi','kodi','domotica');
		if($db->connect_errno>0)
			die('Unable to connect to database [' . $db->connect_error . ']');
		$living=$s['living_temp'];
		$badkamer=$s['badkamer_temp'];
		$kamer=$s['kamer_temp'];
		$tobi=$s['tobi_temp'];
		$alex=$s['alex_temp'];
		$zolder=$s['zolder_temp'];
		$s_living=$s['living_set'];
		$s_badkamer=$s['badkamer_set'];
		$s_kamer=$s['kamer_set'];$s_tobi=$s['tobi_set'];
		$s_alex=$s['alex_set'];
		if($s['brander']=='On')
			$brander=1;
		else
			$brander=0;
		if($s['badkamervuur']=='On')
			$badkamervuur=1;
		else
			$badkamervuur=0;
		$query="INSERT IGNORE INTO `temp` (`stamp`,`buiten`,`living`,`badkamer`,`kamer`,`tobi`,`alex`,`zolder`,`s_living`,`s_badkamer`,`s_kamer`,`s_tobi`,`s_alex`,`brander`,`badkamervuur`) VALUES ('$stamp','$buiten_temp','$living','$badkamer','$kamer','$tobi','$alex','$zolder','$s_living','$s_badkamer','$s_kamer','$s_tobi','$s_alex','$brander','$badkamervuur');";
		if(!$result=$db->query($query))
			die('There was an error running the query ['.$query .' - ' . $db->error . ']');
		$db->close();
		$Tregenpomp=strtotime($t['regenpomp']);
		if($buienradar>0)
		{
			$pomppauze=3600/max(array(1,($buienradar*20)));
			if($pomppauze>10800)$pomppauze=10800;
		}
		else
			$pomppauze=3600;
		if($s['regenpomp']=='On'&&$Tregenpomp<time-57)
			sw($i['regenpomp'],'Off','regenpomp off, was on for '.convertToHours(time-$Tregenpomp));
		elseif($s['regenpomp']=='Off'&&$Tregenpomp<time-$pomppauze)
			sw($i['regenpomp'],'On','regenpomp on, was off for '.convertToHours(time-$Tregenpomp));
		if($s['voordeur']=='On'&&strtotime($t['voordeur'])<time-598)
			sw($i['voordeur'],'Off','Voordeur uit');
		if($s['lichten_auto']=='Off')
			if(strtotime($t['lichten_auto'])<time-10795)
				sw($i['lichten_auto'],'Off','lichten_auto aan');
		if($s['weg']=='On'||$s['slapen']=='On')
		{
			$lastoff=cget('timelastoff');
			if($lastoff<time-598)
			{
				cset('timelastoff',time);
				if(strtotime($t['weg'])>time-57||strtotime($t['slapen'])>time-57)
					$uit=60;
				else
					$uit=600;
				if($s['weg']=='On')
					alles('Off',$uit);
				if($s['slapen']=='On')
					alles('Slapen',$uit);
				$items=array('living','badkamer','kamer','tobi','alex');
				foreach($items as $item)
				{
					${'setpoint'.$item}=cget('setpoint'.$item);
					if(${'setpoint'.$item}!=0&&strtotime($t[$item])<time-3598)
						cset('setpoint'.$item,0);
					}
				$items=array('tobi','living','kamer','alex');
				foreach($items as $item)
					if(strtotime($t[$item.'_set'])<time-86398)
						ud($i[$item.'_set'],0,$s[$item.'_set'],'Update '.$item);
			}
			if(strtotime($t['weg'])<time-57)
				if($s['poortrf']=='On')
					sw($i['poortrf'],'Off','Poort uit');
		}
		if($s['kodi']=='On'&&strtotime($t['kodi'])<time-298)
		{
			$devcheck='Kodi';
			if(pingDomain('192.168.2.7',1597)==1)
			{
				$prevcheck=cget('check'.$devcheck);
				if($prevcheck>0)
					cset('check'.$devcheck,0);
			}
			else
			{
				$check=cget('check'.$devcheck)+1;
				if($check>0)
					cset('check'.$devcheck,$check);
				if($check>=5)
					sw($i['kodi'],'Off','kodi');
			}
		}
		$devcheck='PiCam1-Voordeur';
		if(pingDomain('192.168.2.11',80)==1)
		{
			$prevcheck=cget('check'.$devcheck);
			if($prevcheck>=3)
				telegram($devcheck.' online',true,'Kirby');
			if($prevcheck>0)
				cset('check'.$devcheck,0);
		}
		else
		{
			$check=cget('check'.$devcheck)+1;
			if($check>0)
				cset('check'.$devcheck,$check);
			if($check==3)
				telegram($devcheck.' Offline',true,'Kirby');
			if($check%100==0)
				telegram($devcheck.' nog steeds Offline',true,'Kirby');
		}
		$devcheck='PiCam2-Alex';
		if(pingDomain('192.168.2.12',80)==1)
		{
			$prevcheck=cget('check'.$devcheck);
			if($prevcheck>=3)
				telegram($devcheck.' online',true,'Kirby');
				if($prevcheck>0)
					cset('check'.$devcheck,0);
		}
		else
		{
			$check=cget('check'.$devcheck)+1;
			if($check>0)
				cset('check'.$devcheck,$check);
			if($check==3)
				telegram($devcheck.' Offline',true,'Kirby');
			if($check%100==0)
				telegram($devcheck.' nog steeds Offline',true,'Kirby');
		}
		$devcheck='PiCam3-Oprit';
		if(pingDomain('192.168.2.13',80)==1)
		{
			$prevcheck=cget('check'.$devcheck);
			if($prevcheck>=3)
				telegram($devcheck.' online',true);
			if($prevcheck>0)
				cset('check'.$devcheck,0);
		}
		else
		{
			$check=cget('check'.$devcheck)+1;
			if($check>0)
				cset('check'.$devcheck,$check);
			if($check==3)
				telegram($devcheck.' Offline',true);
			if($check%100==0)
				telegram($devcheck.' nog steeds Offline',true);
		}
		$devcheck='PiHole-DNS';
		if(pingDomain('192.168.2.2',53)==1)
		{
			$prevcheck=cget('check'.$devcheck);
			if($prevcheck>=3)
				telegram($devcheck.' online',true);
				if($prevcheck>0)
					cset('check'.$devcheck,0);
		}
		else
		{
			$check=cget('check'.$devcheck)+1;
			if($check>0)
				cset('check'.$devcheck,$check);
				if($check==3)
					telegram($devcheck.' Offline',true);
				if($check%100==0)
					telegram($devcheck.' nog steeds Offline',true);
		}
		$devcheck='PiHole-WWW';
		if(pingDomain('192.168.2.2',80)==1)
		{
			$prevcheck=cget('check'.$devcheck);
			if($prevcheck>=3)
				telegram($devcheck.' online',true);
			if($prevcheck>0)
				cset('check'.$devcheck,0);
		}
		else
		{
			$check=cget('check'.$devcheck)+1;
			if($check>0)
				cset('check'.$devcheck,$check);
				if($check==3)
					telegram($devcheck.' Offline',true);
				if($check%100==0)
					telegram($devcheck.' nog steeds Offline',true);
		}
		$items=array('brander','badkamervuur');
		foreach($items as $item)
			if($s[$item]!='Off'&&strtotime($t[$item])<time-3598)
				sw($i[$item],$s[$item],$item);
		if($s['meldingen']=='Off'&&strtotime($t['meldingen'])<time-10795)
			sw($i['meldingen'],'On','meldingen');
		if(strtotime($t['pirliving'])<time-14395&&strtotime($t['pirlivingR'])<time-14395&&strtotime($t['pirgarage'])<time-14395&&strtotime($t['pirinkom'])<time-14395&&strtotime($t['pirhall'])<time-14395&&strtotime($t['slapen'])<time-14395&&strtotime($t['weg'])<time-14395&&$s['weg']=='Off'&&$s['slapen']=="Off")
		{
			sw($i['slapen'],'On','wakker1');
			if($s['slapen']=='Off')
				telegram('slapen ingeschakeld na 4 uur geen beweging',false,'Kirby');
			else
				telegram('slapen ingeschakeld na 4 uur geen beweging',true,'Kirby');
		}
		if(strtotime($t['pirliving'])<time-43190&&strtotime($t['pirlivingR'])<time-43190&&strtotime($t['pirgarage'])<time-43190&&strtotime($t['pirinkom'])<time-43190&&strtotime($t['pirhall'])<time-43190&&strtotime($t['slapen'])<time-43190&&strtotime($t['weg'])<time-43190&&$s['weg']=='Off'&&$s['slapen']=="On")
		{
			sw($i['slapen'],'Off','wakker2');
			sw($i['weg'],'On','weg');
			if($s['slapen']=='Off')
				telegram('weg ingeschakeld na 12 uur geen beweging',false,'Kirby');
			else
				telegram('weg ingeschakeld na 12 uur geen beweging',true,'Kirby');
		}
 
		//if($s['zwembadfilter']=='On'){if(strtotime($t['zwembadfilter']) < time-14395&&time>strtotime("18:00")&&$s['zwembadwarmte']=='Off')sw($i['zwembadfilter'],'Off','zwembadfilter');}
		//else{if(strtotime($t['zwembadfilter'])<time-14395&&time>strtotime("12:00")&&time<strtotime("16:00"))sw($i['zwembadfilter'],'On','zwembadfilter');}
		//if($s['zwembadwarmte']=='On'){
		//	if(strtotime($t['zwembadwarmte'])<time-86398)sw($i['zwembadwarmte'],'Off','warmtepomp zwembad');
		//	if($s['zwembadfilter']=='Off')sw($i['zwembadfilter'],'On','zwembadfilter');
		//}
		if($s['meldingen']=='On')
		{
			$items=array('living_temp','badkamer_temp','kamer_temp','tobi_temp','alex_temp','zolder_temp');
			$avg=0;
			foreach($items as $item)
				$avg=$avg+$s[$item];
			$avg=$avg/6;
			foreach($items as $item)
			{
				$temp=$s[$item];
				if($temp>$avg+5&&$temp>25)
				{
					$msg='T '.$item.'='.$temp.'°C. AVG='.round($avg,1).'°C';
					if(cget('timealerttemp'.$item)<time-3598)
					{
						telegram($msg,false,'Kirby');
						ios($msg);
						cset('timealerttemp'.$item,time);
					}
				}
				if(strtotime($t[$item])<time-21590)
				{
					if(cget('timealerttempupd'.$item)<time-43190)
					{
						telegram($item.' not updated');
						cset('timealerttempupd'.$item,time);
					}
				}
			}
		}
		$devices=array('tobiZ','alexZ','livingZ','livingZZ','livingZE','kamerZ');
		foreach($devices as $device)
		{
			if(strtotime($t[$device])<time-3598)
			{
				if(cget('timealert'.$device)<time-43190)
				{
					telegram($device.' geen communicatie',true);
					cset('timealert'.$device,time);
				}
			}
		}
		if($s['weg']=='Off'&&$s['slapen']=='Off')
		{
			if(($buiten_temp>$s['kamer_temp']&&$buiten_temp>$s['tobi_temp']&&$buiten_temp>$s['alex_temp'])&&$buiten_temp>22&&($s['kamer_temp']>20||$s['tobi_temp']>20||$s['alex_temp']>20)&&($s['raamkamer']=='Open'||$s['raamtobi']=='Open'||$s['raamalex']=='Open'))
				if((int)cget('timeramen')<time-43190)
				{
					telegram('Ramen boven dicht doen, te warm buiten. Buiten = '.$buiten_temp.',kamer = '.$s['kamer_temp'].', Tobi = '.$s['tobi_temp'].', Alex = '.$s['alex_temp'],false,'Kirby');
					cset('timeramen',time);
				}
			elseif(($buiten_temp<=$s['kamer_temp']||$buiten_temp<=$s['tobi_temp']||$buiten_temp<=$s['alex_temp'])&&($s['kamer_temp']>20||$s['tobi_temp']>20||$s['alex_temp']>20)&&($s['raamkamer']=='Closed'||$s['raamkamer']=='Closed'||$s['raamkamer']=='Closed'))
				if((int)cget('timeramen')<time-43190)
				{
					telegram('Ramen boven open doen, te warm binnen. Buiten = '.$buiten_temp.',kamer = '.$s['kamer_temp'].', Tobi = '.$s['tobi_temp'].', Alex = '.$s['alex_temp'],false,'Kirby');
					cset('timeramen',time);
				}
		}
		$items=array(5=>'keukenzolderg',6=>'wasbakkookplaat',7=>'werkbladtuin',8=>'inkomvoordeur',11=>'badkamer');
		foreach($items as $item => $name)
		if(cget('timerefresh-'.$name)<time-7198)
		{
			RefreshZwave($item,'time',$name,$s['meldingen']);
			cset('timerefresh-'.$name,time);
			break;
		}
		//include('gcal/gcal.php');
		$nodes=json_decode(curl('http://127.0.0.1:8084/json.htm?type=openzwavenodes&idx=3'),true);
		if($nodes['NodesQueried']==1)
		{
			if(cget('timehealnetwork')<time-3600*24*7)
			{
				$result=json_decode(curl('http://127.0.0.1:8084/json.htm?type=command&param=zwavenetworkheal&idx=3'),true);
				if($result['status']=="OK")
				{
					cset('timehealnetwork',time);
					exit;
				}
			}
			/*foreach($nodes['result'] as $node){if(in_array($node['NodeID'],array(2,3,4,5,6,7,8,9,10,11,12,13,14,15,17,18,19,20,22,23,25,26,27,29))){if(cget('timehealnetwork')<time-1800&&cget('timehealnode-'.$node['Name'])<time-3600*12&&cget('timehealnode')<time-300){$healnode=json_decode(curl('http://127.0.0.1:8084/json.htm?type=command&param=zwavenodeheal&idx=3&node='.$node['NodeID']),true);if($healnode['status']=="OK"){lg('             Heal Node '.$node['Name'].' started');cset('timehealnode-'.$node['Name'],time);cset('timehealnode',time);exit;}unset($healnode);}}}*/}else cset('timehealnetwork',0);
}}
function sw($idx,$action="",$info="",$Usleep=600000)
{
	lg("SWITCH ".$action." ".$info);
	if(empty($action))
		curl("http://127.0.0.1:8084/json.htm?type=command&param=switchlight&idx=".$idx."&switchcmd=Toggle");
	else
		curl("http://127.0.0.1:8084/json.htm?type=command&param=switchlight&idx=".$idx."&switchcmd=".$action);
	usleep($Usleep);
}
function sl($idx,$level,$info="",$Usleep=600000)
{
	lg("SETLEVEL ".$level." ".$info);
	curl("http://127.0.0.1:8084/json.htm?type=command&param=switchlight&idx=".$idx."&switchcmd=Set%20Level&level=".$level);
	usleep($Usleep);
}
function ud($idx,$nvalue,$svalue,$info="",$Usleep=600000)
{
	if(!in_array($idx, array(395,532,534)))
		lg("UPDATE ".$nvalue." ".$svalue." ".$info);
		curl('http://127.0.0.1:8084/json.htm?type=command&param=udevice&idx='.$idx.'&nvalue='.$nvalue.'&svalue='.$svalue);
		usleep($Usleep);
}
function setradiator($name,$dif,$koudst=false,$set)
{
	$setpoint=$set-ceil($dif*4);
	if($koudst==true)
		$setpoint=28.0;
	if($setpoint>28)
		$setpoint=28.0;
	elseif($setpoint<4)
		$setpoint=4.0;
	return round($setpoint,0).".0";
}
function double($idx,$action,$comment='',$wait=4000000)
{
	sw($idx,$action,$comment,$wait);
	sw($idx,$action,$comment.' repeat',0);
}
function telegram($msg,$silent=true,$to='Guy')
{
	for($x=1;$x<=100;$x++)
	{
		$result=json_decode(curl('https://api.telegram.org/bot113592115:AAEZ-xCRhO-123456789_3YIr9irxI/sendMessage?chat_id=12345678&text='.$msg.'&disable_notification='.$silent,true));
		if(isset($result->ok))
			if($result->ok===true)
			{
				lg('telegram sent to Guy: '.$msg);
				break;
			}
			else
			{
				lg('telegram sent failed');
			}
		sleep($x*3);
	}
	if($to=='Kirby')
		for($x=1;$x<=100;$x++)
		{
			$result=json_decode(curl('https://api.telegram.org/bot113592115:AAEZ-xCRhO-123456789_3YIr9irxI/sendMessage?chat_id=2345678&text='.$msg.'&disable_notification='.$silent,true));
			if(isset($result->ok))
				if($result->ok===true)
				{
					lg('telegram sent to Kirby: '.$msg);
					break;
				}
				else
				{
					lg('telegram sent failed');
				}
		sleep($x*3);
	}
}
function lg($msg)
{
	curl('http://127.0.0.1:8084/json.htm?type=command&param=addlogmessage&message='.urlencode('=> '.$msg));
}
function ios($msg)
{
	$appledevice='123456789EeBN1nZk0sD/ZHxYptWl12345678905kSRqROHYVNSUzmWV';
	$appleid='your@apple.id';
	$applepass='Y0ur@ppleP@ssw0rd';
	require_once("findmyiphone.php");
	$fmi=new FindMyiPhone($appleid,$applepass);
	$fmi->playSound($appledevice,$msg);
	sms($msg);
}
function sms($msg)
{
	if(1==2)
	{
		$smsuser='clickatelluser';
		$smspassword='clickatellpassword';
		$smsapi=12345678;
		$smstofrom=32412345678;
		curl('http://api.clickatell.com/http/sendmsg?user='.$smsuser.'&password='.$smspassword.'&api_id='.$smsapi.'&to='.$smstofrom.'&text='.urlencode($msg).'&from='.$smstofrom.'');
		usleep(500000);
	}
}
function pingDomain($domain,$port)
{
	$file=fsockopen($domain,$port,$errno,$errstr,10);
	$status=0;
	if(!$file)
		$status=-1;
	else
	{
		fclose($file);
		$status=1;
	}
	return $status;
}
function RefreshZwave($node)
{
	$last=cget('time-RefreshZwave'.$node);
	cset('time-RefreshZwave'.$node,time);
	if($last<time-10)
	{
		$devices=json_decode(file_get_contents('http://127.0.0.1:8084/json.htm?type=openzwavenodes&idx=3'),true);
		foreach($devices['result'] as $devozw)
			if($devozw['NodeID']==$node)
			{
				$device=$devozw['Description'].' '.$devozw['Name'];
				break;
			}
			lg(' > Refreshing node '.$node.' '.$device);
			for($k=1;$k<=5;$k++)
			{
				/*	ControllerBusy(20);*/
				$result=file_get_contents('http://127.0.0.1:8084/ozwcp/refreshpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>'racp','node'=>$node)),),)));
				if($result==='OK')
				{
					cset('timerefresh-'.$device,time);
					break;
				}
				sleep(1);
			}
			if(cget('timedeadnodes')<time-298)
			{
				cset('timedeadnodes',time);
				foreach($devices as $node=>$data)
				{
					if($node=="result")
					{
						foreach($data as $index=>$eltsNode)
						{
							if($eltsNode["State"]=="Dead"&&!in_array($eltsNode['NodeID'],array(57)))
							{
								telegram('Node '.$eltsNode['NodeID'].' '.$eltsNode['Description'].' ('.$eltsNode['Name'].') marked as dead, reviving '.ZwaveCommand($eltsNode['NodeID'],'HasNodeFailed'));
								ControllerBusy(10);
								ZwaveCommand(1,'Cancel');
							}
						}
					}
				}
			}
	}
}
function Zwavecancelaction()
{
	file_get_contents('http://127.0.0.1:8084/ozwcp/admpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>'cancel')),),)));
}
function ZwaveCommand($node,$command)
{
	$cm=array('AssignReturnRoute'=>'assrr','DeleteAllReturnRoutes'=>'delarr','NodeNeighbourUpdate'=>'reqnnu','RefreshNodeInformation'=>'refreshnode','RequestNetworkUpdate'=>'reqnu','HasNodeFailed'=>'hnf','Cancel'=>'cancel');
	$cm=$cm[$command];
	for($k=1;$k<=5;$k++)
	{
		$result=file_get_contents('http://pass2php:pass2php@127.0.0.1:8084/ozwcp/admpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>$cm,'node'=>'node'.$node)),),)));
		if($result=='OK')
			break;
		sleep(1);
	}
	return $result;
}
function ControllerBusy($retries)
{
	for($k=1;$k<=$retries;$k++)
	{
		$result=file_get_contents('http://127.0.0.1:8084/ozwcp/poll.xml');
		$p=xml_parser_create();
		xml_parse_into_struct($p,$result,$vals,$index);
		xml_parser_free($p);
		foreach($vals as $val)
		{
			if($val['tag']=='ADMIN')
			{
				$result=$val['attributes']['ACTIVE'];
				break;
			}
		}
		if($result=='false')
			break;
		if($k==$retries)
		{
			ZwaveCommand(1,'Cancel');
			break;
		}
		sleep(1);
	}
}
function convertToHours($time)
{
	if($time<600)
		return substr(strftime('%M:%S',$time),1);
	elseif($time>=600&&$time<3600)
		return strftime('%M:%S',$time);
	else
		return strftime('%k:%M:%S',$time);
}
function curl($url)
{
	$headers=array('Content-Type: application/json');
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_FRESH_CONNECT,TRUE);
	curl_setopt($ch,CURLOPT_TIMEOUT,5);
	$data=curl_exec($ch);
	curl_close($ch);
	return $data;
}
function cset($key,$value)
{
	if(!$m=xsMemcached::Connect('127.0.0.1',11211))
	{
		die('Memcache failed to connect.');
	}
	$m->Set($key,$value);
}
function cget($key)
{
	if(!$m=xsMemcached::Connect('127.0.0.1',11211))
	{
		die('Memcache failed to connect.');
	}
	return $m->Get($key);
}
class xsMemcached
{
	private $Host;
	private $Port;
	private $Handle;
	public static function Connect($Host,$Port,$Timeout=5)
	{
		$Ret=new self();
		$Ret->Host=$Host;
		$Ret->Port=$Port;
		$ErrNo=$ErrMsg=NULL;
		if(!$Ret->Handle=@fsockopen($Ret->Host,$Ret->Port,$ErrNo,$ErrMsg,$Timeout))
			return false;
			return $Ret;
	}
	public function Set($Key,$Value,$TTL=0)
	{
		return $this->SetOp($Key,$Value,$TTL,'set');
	}
	public function Get($Key)
	{
		$this->WriteLine('get '.$Key);
		$Ret='';
		$Header=$this->ReadLine();
		if($Header=='END')
		{
			$Ret=0;
			$this->SetOp($Key,0,0,'set');
			return $Ret;
		}
		while(($Line=$this->ReadLine())!='END')
			$Ret.=$Line;
		if($Ret=='')
			return false;
		$Header=explode(' ',$Header);
		if($Header[0]!='VALUE'||$Header[1]!=$Key)
			throw new Exception('unexcpected response format');
		$Meta=$Header[2];
		$Len=$Header[3];
		return $Ret;
	}
	public function Quit()
	{
		$this->WriteLine('quit');
	}
	private function SetOp($Key,$Value,$TTL,$Op)
	{
		$this->WriteLine($Op.' '.$Key.' 0 '.$TTL.' '.strlen($Value));
		$this->WriteLine($Value);
		return $this->ReadLine()=='STORED';
	}
	private function WriteLine($Command,$Response=false)
	{
		fwrite($this->Handle,$Command."\r\n");
		if($Response)
			return $this->ReadLine();
		return true;
	}
	private function ReadLine()
	{
		return rtrim(fgets($this->Handle),"\r\n");
	}
	private function __construct()
	{
	}
}
