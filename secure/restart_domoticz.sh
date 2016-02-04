#!/bin/sh

sudo service domoticz.sh stop
sleep 10
sudo killall domoticz
sleep 2
sudo service domoticz.sh start
