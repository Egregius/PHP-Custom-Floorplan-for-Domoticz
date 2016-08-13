<?php error_reporting(E_ALL);ini_set("display_errors", "on");
echo '<b><big><a href="">Cache view</a>&nbsp;&nbsp;<a href="memcache.php">MemCache</a>&nbsp;&nbsp;<a href="opcache.php">OpCache</a>&nbsp;&nbsp;<a href="opcache1.php">OpCache</a></big></b><br/><br/>';
if(isset($_POST)) {
	if(isset($_POST['reset'])&&isset($_POST['value'])) {
		cset($_POST['reset'],$_POST['value']);
	}
}
$keys =getMemcachedKeys();
sort($keys);
echo '<table cellpadding="0px" cellspacing="0px">';
foreach($keys as $key) {
	$content=cget($key);
	echo '<tr><td valign="middle" align="right"><form method="POST"><input type="submit" name="reset" value="'.$key.'" style="height:23px;padding:0;margin:0 5px -15px 0"/></td><td valign="top"><input type="text" size="15" style="text-align:right;" name="value" value="'.htmlspecialchars($content).'"/></form></td>';
	if(startsWith($key,'time')!==false) {if($content!=0) echo '<td valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.strftime("%a %e %b %k:%M:%S", $content).'</td>';}
	elseif(startsWith($key,'dimmer')!==false){
		echo '<td valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		switch ($content) {
			case 0: echo 'Normal';break;
			case 1: echo 'Sleep';break;
			case 2: echo 'Wake';break;
		}
		echo '</td>';
	}
	elseif(startsWith($key, 'setpoint')!==false){
		echo '<td valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		switch ($content) {
			case 0: echo 'Normal';break;
			case 1: echo 'Voorwarmen';break;
			case 2: echo 'Manual';break;
			case 3: echo 'Door licht';break;
		}
		echo '</td>';
	}
	elseif(startsWith($key, 'weer')!==false){
		echo '<td valign="top">&nbsp;';
		print_r(unserialize($content));
		echo '</td>';
	}
	echo '</tr>';
}
echo '</table>';
function startsWith($haystack, $needle) {return $needle === ""||strrpos($haystack,$needle,-strlen($haystack))!==false;}
function getMemcachedKeys($host='127.0.0.1',$port=11211){
    $mem=@fsockopen($host,$port);
    if($mem===FALSE) return -1;
    $r=@fwrite($mem,'stats items'.chr(10));
    if($r===FALSE) return -2;
    $slab=array();
    while(($l=@fgets($mem,1024))!==FALSE){
        $l=trim($l);
        if($l=='END') break;
        $m=array();
        $r=preg_match('/^STAT\sitems\:(\d+)\:/',$l,$m);
        if($r!=1) return -3;
        $a_slab=$m[1];
        if(!array_key_exists($a_slab,$slab)) $slab[$a_slab]=array();
    }
    reset($slab);
    foreach ($slab AS $a_slab_key => &$a_slab) {
        $r = @fwrite($mem, 'stats cachedump ' . $a_slab_key . ' 100' . chr(10));
        if ($r === FALSE) return -4;
        while (($l = @fgets($mem, 1024)) !== FALSE) {
            $l = trim($l);
            if ($l == 'END') break;
            $m = array();
            $r = preg_match('/^ITEM\s([^\s]+)\s/', $l, $m);
            if ($r != 1) return -5;
            $a_key = $m[1];
            $a_slab[] = $a_key;
        }
    }
    @fclose($mem);
    unset($mem);
    $keys = array();
    reset($slab);
    foreach ($slab AS &$a_slab) {
        reset($a_slab);
        foreach ($a_slab AS &$a_key) $keys[] = $a_key;
    }
    unset($slab);

    return $keys;
}
function cset($key,$value){if(!$m=xsMemcached::Connect('127.0.0.1', 11211)){die('Memcache failed to connect.');}$m->Set($key,$value);}
function cget($key){if(!$m=xsMemcached::Connect('127.0.0.1', 11211)){die('Memcache failed to connect.');}return $m->Get($key);}
class xsMemcached{
	private $Host;private $Port;private $Handle;
	public static function Connect($Host,$Port,$Timeout=5){$Ret=new self();$Ret->Host=$Host;$Ret->Port=$Port;$ErrNo=$ErrMsg=NULL;if(!$Ret->Handle=@fsockopen($Ret->Host,$Ret->Port,$ErrNo,$ErrMsg,$Timeout))return false;return $Ret;}
	public function Set($Key,$Value,$TTL=0){return $this->SetOp($Key,$Value,$TTL,'set');}
	public function Get($Key){$this->WriteLine('get '.$Key);$Ret='';$Header=$this->ReadLine();if($Header=='END'){$Ret=0;$this->SetOp($Key,0,0,'set');return $Ret;}while(($Line=$this->ReadLine())!='END')$Ret.=$Line;if($Ret=='')return false;$Header=explode(' ',$Header);if($Header[0]!='VALUE'||$Header[1]!=$Key) throw new Exception('unexcpected response format');$Meta=$Header[2];$Len=$Header[3];return $Ret;}
	public function Quit(){$this->WriteLine('quit');}
	private function SetOp($Key,$Value,$TTL,$Op){$this->WriteLine($Op.' '.$Key.' 0 '.$TTL.' '.strlen($Value));$this->WriteLine($Value);return $this->ReadLine()=='STORED';}
	private function WriteLine($Command,$Response=false){fwrite($this->Handle,$Command."\r\n");if($Response)return $this->ReadLine();return true;}
	private function ReadLine(){return rtrim(fgets($this->Handle),"\r\n");}
	private function __construct(){}
}