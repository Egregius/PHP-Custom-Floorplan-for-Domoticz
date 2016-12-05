<?php include "general.php";?>

h2{font-size:36px;}

.fix{position:absolute;}
.i48{width:48px;height:auto;}

.setpoints{width:99%;border-spacing:0;padding:0;}
.setpoints tr{text-align:center;line-height:2;font-size:26px;}
.brander{width:99%;border-spacing:0;padding:0;margin-bottom:-10px;}
.brander tr, .brander td{text-align:right;line-height:1.2;font-size:16px;margin-bottom:-30px;}

<?php if($udevice=='other'){ ?>
  .header{top:0px;left:0px;width:98%;height:70px;}
.btn{height:50px;font-size:1.4em;}

  input[type=submit]{cursor:pointer;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;border-radius:0;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;}
  .dimmer{top:0px;left:0px;height:735px;width:390px;padding:50px;background:#111;z-index:1000;}
  .dimmerlevel{top:20px;left:25px;width:19px;color:#333;font-size:90%;}
  .dimlevel{background-color:#333;color:#eee;font-size:300%;padding:0px;text-align:center;width:19.4%;height:91px;}
  .dimlevela{background-color:#ffba00;color:#000;}
<?php }elseif($udevice=='iphone'){ ?>
  .header{top:0px;left:0px;width:99%;height:70px;}
  .btn{min-width:4em;height:50px;color:#ccc;background-color:#333;text-align:center;display:inline-block;margin-bottom:0;cursor:pointer;border:1px solid transparent;padding:5px;margin:3px 0px 2px 4px;font-size:1em;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;}
  .btn:hover{color:#fff;}
  input[type=submit]{cursor:pointer;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;border-radius:0;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;}
  .dimmer{top:0px;left:0px;height:735px;width:390px;padding:50px;background:#111;z-index:1000;}
  .dimmerlevel{top:20px;left:25px;width:19px;color:#333;font-size:90%;}
  .dimlevel{background-color:#333;color:#eee;font-size:300%;padding:0px;text-align:center;width:19.4%;height:91px;}
  .dimlevela{background-color:#ffba00;color:#000;}

<?php }elseif($udevice=='ipad'){ ?>
@media only screen and (orientation: portrait) {
  .header{top:0px;left:0px;width:98%;height:70px;}
  .btn{color:#ccc;background-color:#333;min-width:1.3em;text-align:center;display:inline-block;margin-bottom:0;cursor:pointer;border:1px solid transparent;padding:5px;margin:3px 0px 2px 4px;font-size:1em;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;}
  .btn:hover{color:#fff;}
  input[type=submit]{cursor:pointer;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;border-radius:0;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;}
  .dimmer{top:0px;left:0px;height:735px;width:390px;padding:50px;background:#111;z-index:1000;}
  .dimmerlevel{top:20px;left:25px;width:19px;color:#333;font-size:90%;}
  .dimlevel{background-color:#333;color:#eee;font-size:300%;padding:0px;text-align:center;width:19.4%;height:91px;}
  .dimlevela{background-color:#ffba00;color:#000;}

}
@media only screen and (orientation: landscape) {
  .header{top:0px;left:0px;width:98%;height:70px;}
  .btn{color:#ccc;background-color:#333;min-width:1.3em;text-align:center;display:inline-block;margin-bottom:0;cursor:pointer;border:1px solid transparent;padding:5px;margin:3px 0px 2px 4px;font-size:1em;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;}
  .btn:hover{color:#fff;}
  input[type=submit]{cursor:pointer;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;border-radius:0;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;}
  .dimmer{top:0px;left:0px;height:735px;width:390px;padding:50px;background:#111;z-index:1000;}
  .dimmerlevel{top:20px;left:25px;width:19px;color:#333;font-size:90%;}
  .dimlevel{background-color:#333;color:#eee;font-size:300%;padding:0px;text-align:center;width:19.4%;height:91px;}
  .dimlevela{background-color:#ffba00;color:#000;}

}
<?php } ?>
