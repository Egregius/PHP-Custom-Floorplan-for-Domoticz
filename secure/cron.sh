#!/bin/bash
H=$(date +%H)
if (( 9 <= 10#$H && 10#$H < 23 )); then 
	string=$(tail -1 $(/bin/ls -1t /volume1/homes/guy/BackupWWW/RPi-PiHole/var/log/SBFSPOT/Zon-Spot*.csv | /bin/sed q))
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
else
    sum=0
fi
DOMOTICZ=`curl -s --connect-timeout 2 --max-time 5 "http://127.0.0.1:8084/json.htm?type=command&param=udevice&idx=412&nvalue=0&svalue=$sum"`
STATUS=`echo $DOMOTICZ | jq -r '.status'`
if [ "$STATUS" == "OK" ] ; then
	STAMP=$(date +%s)
	LASTSNAP=$(</volume1/homes/guy/BackupWWW/RPi-PiHole/var/log/lastsync.cache)
	if [ $LASTSNAP -lt $(($STAMP-300)) ]
	then
		sudo rsync -PrlptDvsmh --stats --exclude '*Spot*' -e "ssh -i /root/.ssh/id_rsa -p 1598" /volume1/homes/guy/BackupWWW/RPi-PiHole/var/log/SBFSPOT/ root@mail.egregius.be:/var/www/egregius.be/zon/GUY >> /volume1/homes/guy/BackupWWW/RPi-PiHole/var/log/sbfspot.log
		echo $STAMP > /volume1/homes/guy/BackupWWW/RPi-PiHole/var/log/lastsync.cache
	fi
	exit
else
	sleep 5
	DOMOTICZ=`curl -s --connect-timeout 2 --max-time 5 "http://127.0.0.1:8084/json.htm?type=command&param=udevice&idx=412&nvalue=0&svalue=$sum"`
	STATUS2=`echo $DOMOTICZ | jq -r '.status'`
	if [ "$STATUS2" == "OK" ] ; then
		exit
	else
		sleep 5
		DOMOTICZ=`curl -s --connect-timeout 2 --max-time 5 "http://127.0.0.1:8084/json.htm?type=command&param=udevice&idx=412&nvalue=0&svalue=$sum"`
		STATUS3=`echo $DOMOTICZ | jq -r '.status'`
		if [ "$STATUS3" == "OK" ] ; then
			exit
		else
			curl -s --connect-timeout 2 --max-time 5 --data-urlencode "text=Domoticz Bad - Restarting" --data "silent=false" http://127.0.0.1/secure/telegram.php
			NOW=$(date +"%Y-%m-%d_%H%M%S")
			cp /volume1/appstore/domoticz/var/domoticz.log /volume1/files/temp/domoticz-$NOW.txt
			sudo /var/packages/domoticz/scripts/start-stop-status stop
			sleep 5
			sudo killall domoticz
			sudo killall domoticz
			sudo killall pass2php.php
			sudo killall pass2php.php
			sudo killall /usr/local/domoticz/bin/domoticz
			sudo killall /usr/local/domoticz/bin/domoticz
			sleep 12
			sudo /var/packages/domoticz/scripts/start-stop-status start
		fi
	fi
fi
