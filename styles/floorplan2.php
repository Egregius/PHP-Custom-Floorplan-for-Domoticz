<?php include "general.php";?>

h2{font-size:36px;}

.fix{position:absolute;}
.center{text-align:center;}
.z1{z-index:100;}
.i48{width:55px;height:auto;}
.box{left:0px;width:80px;background:#222;padding-top:10px; border:thin #666}

<?php if($udevice=='other'||$udevice=='iphone'){ ?>
.clock{top:0px;left:200px;width:142px;text-align:center;font-size:40px;font-weight:500;color:#CCC;}
.kodi{top:7px;left:87px;}
.kodicontrol{top:10px;left:170px;}
.films{top:10px;left:250px;}
.filmstobi{top:10px;left:330px;}
.series{top:10px;left:410px;}
.picam1{top:7px;left:25px;}
.picam2{top:7px;left:150px;}
.tv{top:7px;left:17px;}
.kodi{top:7px;left:87px;}
.meldingen{top:7px;left:390px;}
.sony{top:7px;left:300px;}
.sirene{top:30px;left:200px;}
.sd{top:30px;left:200px;}
.regenpomp{top:10px;left:10px;}
.zwembadfilter{top:10px;left:138px;}
.zwembadwarmte{top:10px;left:266px;}
.water{top:10px;left:395px;}
.logout{bottom:0px;left:10px;}
<?php }elseif($udevice=='ipad'){ ?>
@media only screen and (orientation: portrait) {
  .clock{top:0px;left:200px;width:142px;text-align:center;font-size:40px;font-weight:500;color:#CCC;}
  .kodi2{top:7px;left:87px;}
  .kodicontrol{top:10px;left:170px;}
  .films{top:10px;left:250px;}
  .filmstobi{top:10px;left:330px;}
  .series{top:10px;left:410px;}
  .picam1{top:7px;left:87px;}
  .picam1{top:7px;left:87px;}
  .tv{top:7px;left:17px;}
  .kodi{top:7px;left:87px;}
  .meldingen{top:7px;left:395px;}
  .sony{top:7px;left:310px;}
  .sirene{top:30px;left:200px;}
  .sd{top:30px;left:200px;}
  .regenpomp{top:10px;left:10px;}
  .zwembadfilter{top:10px;left:138px;}
  .zwembadwarmte{top:10px;left:266px;}
  .water{top:10px;left:395px;}
  .logout{bottom:0px;left:10px;}
}
@media only screen and (orientation: landscape) {
  .clock{top:0px;left:200px;width:142px;text-align:center;font-size:40px;font-weight:500;color:#CCC;}
  .kodi{top:7px;left:87px;}
  .kodicontrol{top:10px;left:170px;}
  .films{top:10px;left:250px;}
  .filmstobi{top:10px;left:330px;}
  .series{top:10px;left:410px;}
  .picam1{top:7px;left:87px;}
  .picam1{top:7px;left:87px;}
  .tv{top:7px;left:17px;}
  .kodi{top:7px;left:87px;}
  .meldingen{top:7px;left:395px;}
  .sony{top:7px;left:310px;}
  .sirene{top:30px;left:200px;}
  .sd{top:30px;left:200px;}
  .regenpomp{top:10px;left:10px;}
  .zwembadfilter{top:10px;left:138px;}
  .zwembadwarmte{top:10px;left:266px;}
  .water{top:10px;left:395px;}
  .logout{bottom:0px;left:10px;}
}
<?php } ?>
