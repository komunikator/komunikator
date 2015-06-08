kitemp="/tmp/kitemp"
megastep="/tmp/megastep"

mkdir -p "$kitemp"

cp deb/INSTALL deb/deploy.sh "$kitemp"/
cp -rf deb/deps/* "$kitemp"/
cp /var/www/kommunikator_1.0.0-2_all.deb "$kitemp"/
cp ../SQL/shema_mysql.sql "$kitemp"/

chmod +x "$kitemp"/INSTALL

p=`pwd`
cd "$kitemp"/data
tar -xvzf data.tar.gz
rm data.tar.gz
cd $p

cp -rf ../callback ../c2c "$kitemp"/data/all/var/www

mkdir -p "$megastep"
cp megastep.tar.gz "$megastep"

cd "$megastep"
tar -xvzf megastep.tar.gz
rm megastep.tar.gz
./makeself.sh "$kitemp" IP-PBX_deploy.sh "Komunikator Installer" ./INSTALL
cd $p

mv "$megastep"/IP-PBX_deploy.sh /var/www/
rm -rf "$kitemp" "$megastep"