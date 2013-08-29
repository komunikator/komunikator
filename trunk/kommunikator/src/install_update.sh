#!/bin/bash

   if [ $# -ne 1 ]
   then
   echo "Usage $0 url"
   exit 2
   fi

name=komunikator
temp_path="/tmp/$name"
rm $temp_path -r -f
mkdir $temp_path
p=`pwd`
cd $temp_path
wget $1
tar -xvzf ./update.tar.gz
sudo ./update.sh
cd $p
rm $temp_path -r -f
