#!/bin/bash
ulimit -n 4096
sudo sh -c 'echo 1048576 > /proc/sys/fs/inotify/max_user_watches'
sudo chmod 777 /volume1/@appstore/domoticz
sudo chmod 777 /volume1/@appstore/domoticz/var
sudo killall fileindexd
sudo killall synoindexd
sudo killall synoindexplugind
sudo killall synoindexscand
sudo killall synoindexworkerd
sudo killall /usr/syno/sbin/fileindexd
sudo killall /usr/syno/sbin/synoindexd
sudo killall /usr/syno/sbin/synoindexplugind
sudo killall /usr/syno/sbin/synoindexscand
sudo killall /usr/syno/sbin/synoindexworkerd
sudo chmod -x /usr/syno/sbin/fileindexd
sudo chmod -x /usr/syno/sbin/synoindexd
sudo chmod -x /usr/syno/sbin/synoindexplugind
sudo chmod -x /usr/syno/sbin/synoindexscand
sudo chmod -x /usr/syno/sbin/synoindexworkerd
sudo mkdir /var/run/fail2ban
sudo /usr/bin/fail2ban-client -c /etc/fail2ban start

curl -s --connect-timeout 2 --max-time 5 --data-urlencode "text=Diskstation started" https://home.egregius.be/secure/telegram.php
