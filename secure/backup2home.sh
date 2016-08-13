#!/bin/bash
curl -s 'http://127.0.0.1/secure/cleandomoticzdb.php'
NOW=$(date +"%Y-%m-%d")
NOWTIME=$(date +"%Y-%m-%d_%H%M%S")
LOGFILE=/volume1/homes/guy/BackupWWW/_logs/backup2home-$NOW.txt
EXCL='/volume1/homes/guy/BackupWWW/excludedfiles.txt'
echo ------------------------------------ Syncing domoticz -- $(date +"%Y-%m-%d %H:%M:%S") UTC+2 >> $LOGFILE
rsync -PrlptDvsmh --stats --delete  --exclude-from $EXCL /volume1/@appstore/domoticz/ /volume1/homes/guy/BackupWWW/domoticz >> $LOGFILE
USER="kodi"
PASSWORD="kodi"
#rm "$OUTPUTDIR/*gz" > /dev/null 2>&1
databases=`mysql -u $USER -p$PASSWORD -e "SHOW DATABASES;" | tr -d "| " | grep -v Database`
for db in $databases; do
    if [[ "$db" != "information_schema" ]] && [[ "$db" != "performance_schema" ]] && [[ "$db" != "mysql" ]] && [[ "$db" != _* ]] ; then
        echo "Dumping database: $db"
        FOLDER="/volume1/homes/guy/BackupWWW/nas-sql/$db/`date +%Y`/`date +%m`/`date +%d`/`date +%H%M`"
		mkdir -p $FOLDER
		/usr/bin/mysqldump -q -u $USER -p$PASSWORD --databases $db > $FOLDER/$db.sql
    fi
done
find /volume1/homes/guy/BackupWWW/nas-sql/ -type f -name '*.sql' -exec gzip "{}" -fq9  \; 
find /volume1/homes/guy/BackupWWW/nas-sql/ -type f -mtime +7 -exec rm {} \;   
find /volume1/homes/guy/BackupWWW/nas-sql/ -empty -type d -delete