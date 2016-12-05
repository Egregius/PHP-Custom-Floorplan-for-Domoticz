<?php include "general.php";?>

.fix{position:absolute;}

<?php if($udevice=='other'){ ?>
input[type=submit]{color:#ccc;background-color:#555;display:inline-block;margin-bottom:0;cursor:pointer;border:1px solid transparent;padding:5px;margin:3px 0px -2px 4px;font-size:18px;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;width:80px;height:80px;font-size:28px;margin:4px;}
.menu{top:0px;left:0px;width:90%;height:40px;}
.camera1{top:90px;left:0px;width:50%;height:100%;}
.camera2{top:90px;left:50%;width:50%;height:100%;}
.camerai{width:100%;height:auto;}
<?php }elseif($udevice=='iphone'){ ?>
input[type=submit]{color:#ccc;background-color:#555;display:inline-block;cursor:pointer;border:0px solid transparent;padding:2px;font-size:24px;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;width:80px;height:100px;margin:0px 1px 0px 1px;}
.menu{top:0px;left:0px;width:100%;height:40px;z-index:10;}
.camera1{top:130px;left:0px;width:100%;height:100%;}
.camera2{top:612px;left:0px;width:100%;height:100%;}
.camerai{width:100%;height:auto;}
<?php }elseif($udevice=='ipad'){ ?>
@media only screen and (orientation: portrait) {
  input[type=submit]{color:#ccc;background-color:#555;display:inline-block;margin-bottom:0;cursor:pointer;border:1px solid transparent;padding:5px;margin:3px 0px -2px 4px;font-size:18px;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;width:80px;height:80px;font-size:28px;margin:4px;}
  .menu{top:0px;left:0px;width:90%;height:40px;}
  .camera1{top:0px;left:100px;width:1344px;height:auto;z-index:-10;}
  .camera2{top:50%;left:100px;width:1344px;height:auto;z-index:-10;}
  .camerai{width:100%;height:auto;}
}
@media only screen and (orientation: landscape) {
  input[type=submit]{color:#ccc;background-color:#555;display:inline-block;margin-bottom:0;cursor:pointer;border:1px solid transparent;padding:5px;margin:3px 0px -2px 4px;font-size:18px;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;width:80px;height:80px;font-size:28px;margin:4px;}
  .menu{top:100px;left:1200px;width:90%;height:40px;}
  .camera1{top:0px;left:0px;width:1070px;height:auto;z-index:-10;}
  .camera2{top:695px;right:0px;width:1070px;height:auto;z-index:-11;}
  .camerai{width:100%;height:auto;}
}
<?php } ?>
