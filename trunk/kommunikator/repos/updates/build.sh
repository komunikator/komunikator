#!/bin/bash
name=komunikator
ver=05001 
build_path="./build" 
p=`pwd`
cd $ver
tar --exclude-caches -zcvf $p/$name.$ver.tar.gz .
cd $p
cp $name.$ver.tar.gz $build_path
rm $name.$ver.tar.gz
