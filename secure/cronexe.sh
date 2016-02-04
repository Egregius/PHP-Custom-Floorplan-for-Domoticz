#!/bin/bash
name="$1"
/usr/bin/php /var/www/secure/test.php $name > /dev/null 2>&1 &