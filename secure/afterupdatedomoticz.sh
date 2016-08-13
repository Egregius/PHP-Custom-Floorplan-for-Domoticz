#!/bin/bash
find /volume1/@appstore/domoticz/www/ -type f -name "*.html" -o "*.js" -exec sudo sed -ir 's/\"iDisplayLength\"\s*:\s*[0-9]+,/\"iDisplayLength\" : -1, \"paging\": false,/g' {} +
