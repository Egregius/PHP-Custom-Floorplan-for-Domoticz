# PHP-Custom-Floorplan-for-Domoticz
PHP Custom Floorplan and scripting for Domoticz

<img src="http://egregius.be/wp-content/2015/11/Floorplan.png"/>

## Requirements
### As a cronjob
https://github.com/Egregius/LUA-Pass2PHP-for-Domoticz
php-cli
Optionnally memcached, php-memcached

### As Floorplan
php enabled webserver (Apache, Nginx,...)
Optionnally memcached, opcache,...

## Installation
Copy files to the webserver path, edit secure/settings.php
Place a new images/Home.png image with your floorplan. Easiest will be that it has exactly the same dimensions.
Edit floorplan.php with your names of switches, position them on the floorplan in styles/floorplan.php

See also http://egregius.be/tag/domoticz/ for more screenshots and other Domoticz stuff
