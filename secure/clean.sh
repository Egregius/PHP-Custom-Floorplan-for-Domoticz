#!/bin/bash
cd /picam1
(ls 2* -t|head -n 500;ls 2*)|sort|uniq -u|xargs rm

find /picam1/2*.jpg -type f -mmin +1440 -exec rm {} \;

DIR=/picam1/large/
limit="20"

root=$(df -P / | awk '{ gsub("%",""); capacity = $5 }; END { print capacity }')
if [ $root -gt 40 ]
then
	while IFS= read -r -d $'\0' line ; do
		file="${line#* }"
		echo rm "$file" 
		rm "$file"
		let limit-=1
		[[ $limit -le 0 ]] && break
		done < <(find "$DIR" -maxdepth 1 -printf '%T@ %p\0' \
			2>/dev/null | sort -z -n)
fi

if [ $root -gt 70 ]
then
	wakeonlan 00:11:32:2c:b7:21
fi


if [ $(df -P /var/log | awk '{ gsub("%",""); capacity = $5 }; END { print capacity }') -gt 90 ]
then
	curl 'http://127.0.0.1:443/secure/telegram.php?text=/var/log%20full,%20rebooting%20Domoticz'
	sleep 3
	sudo reboot
fi
