#!/bin/bash
name=komunikator
ver=05001 
build_path="./build" 
p=`pwd`
cd $ver
tar --exclude-caches -zcvf $p/$name_$ver.tar.gz .
cd $p
cp $name_$ver.tar.gz $build_path
rm $name_$ver.tar.gz
