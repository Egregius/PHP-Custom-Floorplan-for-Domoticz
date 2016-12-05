<?php include "general.php";?>
.isotope:after{content:'';display:block;clear:both;}
.grid{position:relative;clear:both;width:calc(100%-20px);}
.grid:after {content: '';display: block;clear: both;}

.box{text-align:center;left:0px;background:#222;padding:5px;margin:10px;}
.right{text-align:right;}

<?php if($udevice=='other'){ ?>
h1{font-size:2em;}
.menu{width:20.5%;max-width:300px;}
.volume{width:14.9%;max-width:96px;}
.level{width:11.3%;max-width:105px;}
.surround{width:45%;max-width:300px;}
<?php }elseif($udevice=='iphone'){ ?>
h1{font-size:2em;}
.menu{width:139px;}
.volume{width:112px;}
.level{width:64px;}
.surround{width:120px;}
<?php }elseif($udevice=='ipad'){ ?>
@media only screen and (orientation: portrait) {
h1{font-size:4em;}
.menu{width:362px;}
.volume{width:240px;}
.level{width:98px;}
.surround{width:480px;}
.btn{height:120px;}  
}
@media only screen and (orientation: landscape) {
h1{font-size:5em;}
.menu{width:490px;}
.volume{width:192px;}
.level{width:135px;}
.surround{width:494px;}
.btn{height:120px;}  
}
<?php } ?>
