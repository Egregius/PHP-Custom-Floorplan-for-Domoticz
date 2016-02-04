#!/bin/bash
devid="$1"
name="$2"
chk=`sudo ps x | grep "refreshzwave.php $devid" | grep -cv grep`
if  [ "$chk" = "0" ] ; then
   php /var/www/secure/refreshzwave.php $devid $name >> /var/log/floorplan.log 2>&1 &
fi
