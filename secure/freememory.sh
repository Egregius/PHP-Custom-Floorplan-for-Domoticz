#!/bin/bash
sync && echo 3 | sudo tee /proc/sys/vm/drop_caches
echo memory cleared >> /var/log/floorplan.log