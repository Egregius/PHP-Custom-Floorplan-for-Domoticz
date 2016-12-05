<?php include "general.php";?>

h2{font-size:36px;}

.fix{position:absolute;}
.center{text-align:center;}
.z1{z-index:100;}
.i48{width:55px;height:auto;}
.box{left:0px;width:80px;background:#222;padding-top:10px; border:thin #666}

<?php if($udevice=='other'){ ?>

<?php }elseif($udevice=='ipad'){ ?>
@media only screen and (orientation: portrait) {

}
@media only screen and (orientation: landscape) {

}
<?php } ?>
