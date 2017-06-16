<?php require_once ('/var/www/html/secure/settings.php');if($authenticated){
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width,height=device-height, user-scalable=yes, minimal-ui" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<link href="/styles/index.php" rel="stylesheet" type="text/css"/>
<style>
hr{display:block;height:1px;border:0;border-top:1px solid #777;margin:5px;padding:0;}
</style>
<title>home.egregius.be</title>
<script type="text/javascript">
function fullScreen(theURL) {
params  = \'width=\'+screen.width;
params += \', height=\'+screen.width;
params += \', location=1,status=1,scrollbars=1,menubar=1\';
testwindow= window.open (theURL, \'\', params);
testwindow.moveTo(0,0);
}
</script>
</head>
<body>
<div class="grid">
    <div class="grid-sizer"></div>
    <div class="gutter-sizer"></div>
    <div class="grid-item">
      <a href="https://egregius.be/" target="_blank" class="btn">Just a place holder for bookmarks</a>
    </div>
</div>
</div>
<div class="clear"></div>';
//echo $_SERVER['HTTP_USER_AGENT'].'<br/>'.$udevice;
echo '<br/><br/>
<div class="logout">&nbsp;&nbsp;<form method="POST"><input type="submit" name="logout" value="Logout" class="btn" style="min-width:4em;padding:0px;margin:0px;width:50px;height:39px;"/></form><br/><br/></div>';
} ?>
</body>
<script type="text/javascript" language="javascript" src="scripts/jquery-1.11.1.min.js"></script>
<script type="text/javascript" language="javascript" src="scripts/masonry.pkgd.min.js"></script>
<script language="javascript">
$('.grid').masonry({
  itemSelector: '.grid-item',
  columnWidth: '.grid-sizer',
  gutter: '.gutter-sizer',
  percentPosition: true
});
</script>
</html>
