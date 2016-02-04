#!/bin/sh
#sudo chmod 777 /var/log
#sudo truncate -s 0 /var/log/apache-access.log
#sudo truncate -s 0 /var/log/apache-error.log
#sudo truncate -s 0 /var/log/domoticz.log
#sudo touch /var/log/floorplan.log
#sudo truncate -s 0 /var/log/floorplan.log
#sudo chown pi:pi /var/log/domoticz.log
#sudo chown www-data:www-data /var/log/floorplan.log
#sudo chown www-data:www-data /var/log/apache-access.log
#sudo chown www-data:www-data /var/log/apache-error.log
sudo ln -s /var/log/ozwcp.poll.XXXXXX.xml /home/pi/domoticz/ozwcp.poll.XXXXXX.xml
sudo ln -s /var/log/ozwcp.topo.XXXXXXl /home/pi/domoticz/ozwcp.topo.XXXXXX
sudo ln -s /var/log/OZW_Log.txt /home/pi/domoticz/Config/OZW_Log.txt
#sudo service domoticz.sh restart
#sudo service apache2 reload
ozwcp.topo.XXXXXX