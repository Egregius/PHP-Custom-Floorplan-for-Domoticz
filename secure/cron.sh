#!/bin/bash
OPT="$1"
/usr/bin/php /volume1/web/secure/cron.php $OPT > /dev/null 2>&1 &
echo OPT = $OPT
sleep 2
if [ "$OPT" == "all" ] ; then
	DOMOTICZ=`curl -s --connect-timeout 2 --max-time 5 "http://127.0.0.1:8084/json.htm?type=devices&rid=1"`
	echo $DOMOTICZ
	STATUS=`echo $DOMOTICZ | jq -r '.status'`
	if [ "$STATUS" == "OK" ] ; then
		echo status OK
		exit
	else
		curl -s --connect-timeout 2 --max-time 5 --data-urlencode "text=Domoticz Bad" https://home.egregius.be/secure/telegram.php
		sleep 15
		DOMOTICZ=`curl -s --connect-timeout 2 --max-time 5 "http://127.0.0.1:8084/json.htm?type=devices&rid=1"`
		STATUS2=`echo $DOMOTICZ | jq -r '.status'`
		if [ "$STATUS2" == "OK" ] ; then
			echo status OK
			curl -s --connect-timeout 2 --max-time 5 --data-urlencode "text=Domoticz Status2 OK" https://home.egregius.be/secure/telegram.php
			exit
		else
			curl -s --connect-timeout 2 --max-time 5 --data-urlencode "text=Domoticz Very Bad" https://home.egregius.be/secure/telegram.php
			sleep 15
			DOMOTICZ=`curl -s --connect-timeout 2 --max-time 5 "http://127.0.0.1:8084/json.htm?type=devices&rid=1"`
			STATUS3=`echo $DOMOTICZ | jq -r '.status'`
			if [ "$STATUS3" == "OK" ] ; then
				echo status OK
				curl -s --connect-timeout 2 --max-time 5 --data-urlencode "text=Domoticz Status3 OK" https://home.egregius.be/secure/telegram.php
				exit
			else
				curl -s --connect-timeout 2 --max-time 5 --data-urlencode "text=Domoticz Extreme Bad - Rebooting NAS" https://home.egregius.be/secure/telegram.php
				sudo /usr/sbin/reboot
			fi
		fi
	fi
fi
