<?php error_reporting(E_ALL);ini_set("display_errors","on");
header("Content-type:text/css;charset:UTF-8");
header("Cache-Control:must-revalidate");
header("Expires:".gmdate("D, d M Y H:i:s",time()+86400)." GMT");
if(strpos($_SERVER['HTTP_USER_AGENT'],'iPad')!==false)$udevice='ipad';
elseif(strpos($_SERVER['HTTP_USER_AGENT'],'iPhone')!==false)$udevice='iphone';
else $udevice='other';
?>
.agent{<?php echo $_SERVER['HTTP_USER_AGENT']; ?>}
.udevice{<?php echo $udevice; ?>}

html{padding:0;margin:0;color:#ccc;font-family:sans-serif;height:100%;}
body{padding:0;margin:0;background:#000;width:100%;height:100%;}
.navbar{position:fixed;top:0px;left:0px;width:100%;padding:2px 0px 2px 0px;z-index:100;background-color:#111;}

a:link{text-decoration:none;color:#ccc}
a:visited{text-decoration:none;color:#ccc}
a:hover{text-decoration:none;color:#ccc;}
a:active{text-decoration:none;color:#ccc}
form{display:inline;margin:0px;padding:0px;}
input[type=text]  {cursor:pointer;-webkit-appearance:none;border-radius:0;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;border:0px solid transparent;}
input[type=number]{cursor:pointer;-webkit-appearance:none;border-radius:0;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;border:0px solid transparent;}
input[type=submit]{cursor:pointer;-webkit-appearance:none;border-radius:0;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;border:0px solid transparent;}
input[type=select]{cursor:pointer;-webkit-appearance:none;border-radius:0;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;border:0px solid transparent;}
input[type=date]{cursor:pointer;-webkit-appearance:none;border-radius:0;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;border:0px solid transparent;}

.btn{background-color:#333;color:#ccc;text-align:center;display:inline-block;border:0px solid transparent;padding:2px;margin:1px 0px 1px 1px;-webkit-appearance:none;}
.btn:hover{color:#000;background-color:#ffba00;overflow:visible;}
.btna{color:#000;background-color:#f5b324;}
.clear{clear:both;}
.box{text-align:center;left:0px;background:#222;padding:10px;margin:10px;}
.right{text-align:right;}



<?php if($udevice=='other'){ ?>
.content{height:100%;min-height:80%;margin:0 auto;padding-top:55px;}
.btn{height:40px;font-size:1.4em;}
.b1{width:500px;max-width:99%}
.b2{width:500px;max-width:48.55%}
.b3{width:500px;max-width:31%}
.b4{width:500px;max-width:23.9%}
.b5{width:500px;max-width:19%}
.b6{width:500px;max-width:15.7%}
.b7{width:500px;max-width:13.2%}
.b8{width:500px;max-width:12%}
.b9{width:500px;max-width:10%}
.b10{width:500px;max-width:10%}
<?php }elseif($udevice=='iphone'){ ?>
.content{height:100%;min-height:80%;margin:0 auto;padding-top:60px;}
.btn{height:56px;font-size:2.5em;}
.b1{width:500px;max-width:99%}
.b2{width:500px;max-width:48.55%}
.b3{width:500px;max-width:31%}
.b4{width:500px;max-width:24.3%}
.b5{width:500px;max-width:19%}
.b6{width:500px;max-width:15.7%}
.b7{width:500px;max-width:13.2%}
.b8{width:500px;max-width:12%}
.b9{width:500px;max-width:10.3%}
.b10{width:500px;max-width:10%}
<?php }elseif($udevice=='ipad'){ ?>
@media only screen and (orientation: portrait) {
.content{height:100%;min-height:80%;margin:0 auto;padding-top:120px;}
.btn{height:75px;font-size:2em;}
.b1{width:500px;max-width:99%}
.b2{width:500px;max-width:48.55%}
.b3{width:500px;max-width:31%}
.b4{width:500px;max-width:24.62%}
.b5{width:500px;max-width:19%}
.b6{width:500px;max-width:15.7%}
.b7{width:500px;max-width:13.2%}
.b8{width:500px;max-width:12%}
.b9{width:500px;max-width:10.75%}
.b10{width:500px;max-width:10%}
}

@media only screen and (orientation: landscape) {
.content{height:100%;min-height:80%;margin:0 auto;padding-top:120px;}
.btn{height:75px;font-size:2em;}
.b1{width:500px;max-width:99%}
.b2{width:500px;max-width:48.55%}
.b3{width:500px;max-width:31%}
.b4{width:500px;max-width:24.74%}
.b5{width:500px;max-width:19%}
.b6{width:500px;max-width:15.7%}
.b7{width:500px;max-width:13.2%}
.b8{width:500px;max-width:12%}
.b9{width:500px;max-width:10.85%}
.b10{width:500px;max-width:10%}
}
<?php } ?>
