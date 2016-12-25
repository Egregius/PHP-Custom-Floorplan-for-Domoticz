#!/bin/bash
H=$(date +%H)
if (( 8 <= 10#$H && 10#$H < 23 )); then
	string=$(tail -1 $(/bin/ls -1t /volume1/files/temp/SBFSPOT/Zon-Spot*.csv | /bin/sed q))
	var=$(echo $string | awk -F";" '{print $1,$2,$3,$4,$5,$6,$7,$8,$9}')
	set -- $var
	oost=$8
	west=$9
	var=$(echo $oost | awk -F"," '{print $1}')
	set -- $var
	oostint=$1
	var=$(echo $west | awk -F"," '{print $1}')
	set -- $var
	westint=$1
	sum=$((oostint + westint))
	LAST=`cat /volume1/web/secure/zon.txt`
	if [[ $LAST -ne $sum ]] ; then
		curl -s --connect-timeout 2 --max-time 5 "http://127.0.0.1/secure/zon.php?zon=$sum"
		echo $sum > /volume1/web/secure/zon.txt
	fi
else
	sum=0
fi
DOMOTICZ=`curl -s --connect-timeout 2 --max-time 5 "http://127.0.0.1:8084/json.htm?type=devices&rid=1"`
STATUS=`echo $DOMOTICZ | jq -r '.status'`
if [ "$STATUS" == "OK" ] ; then
	exit
else
	sleep 5
	DOMOTICZ=`curl -s --connect-timeout 2 --max-time 5 "http://127.0.0.1:8084/json.htm?type=devices&rid=1"`
	STATUS2=`echo $DOMOTICZ | jq -r '.status'`
	if [ "$STATUS2" == "OK" ] ; then
		exit
	else
		sleep 5
		DOMOTICZ=`curl -s --connect-timeout 2 --max-time 5 "http://127.0.0.1:8084/json.htm?type=devices&rid=1"`
		STATUS3=`echo $DOMOTICZ | jq -r '.status'`
		if [ "$STATUS3" == "OK" ] ; then
			exit
		else
			curl -s --connect-timeout 2 --max-time 5 --data-urlencode "text=Domoticz Bad - Restarting" --data "silent=false" http://127.0.0.1/secure/telegram.php
			NOW=$(date +"%Y-%m-%d_%H%M%S")
			cp /volume1/appstore/domoticz/var/domoticz.log /volume1/files/temp/domoticz-$NOW.txt
			sudo /var/packages/domoticz/scripts/start-stop-status stop
			sleep 8
			sudo kill $(sudo netstat -anp | awk '/ LISTEN / {if($4 ~ ":8084$") { gsub("/.*","",$7); print $7; exit } }')
			sleep 8
			sudo /var/packages/domoticz/scripts/start-stop-status start
		fi
	fi
fi
