#!/bin/bash
ver=0.5.001 
source="./data" 
target="/usr/share/yate/scripts/config.php"
sudo yes | cp $source$target $target
