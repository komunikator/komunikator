#!/bin/bash

rm -f ./kommunikator.deb 
apt-get update
wget http://172.17.2.147/kommunikator.deb
dpkg -i ./kommunikator.deb
yes | apt-get -f install
