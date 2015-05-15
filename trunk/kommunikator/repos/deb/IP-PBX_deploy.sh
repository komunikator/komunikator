arch=$(uname -m)

mkdir komunikator.temp
cd komunikator.temp

if [ "$arch" = 'x86_64' ]
then
	wget http://komunikator.ru/repos/deb/1.0.b3/komunikator_64.tar.gz
	sudo mv komunikator_64.tar.gz komunikator.tar.gz
else
	wget http://komunikator.ru/repos/deb/1.0.b3/komunikator.tar.gz
fi

tar -xvzf komunikator.tar.gz
sudo mkdir /root/webrtc2sip_source
sudo mv webrtc2sip_source.tar.gz /root/webrtc2sip_source
rm komunikator.tar.gz

ADDRESS="`ifconfig eth0 2>/dev/null|awk '/inet addr:/ {print $2}'|sed 's/addr://'`"
sed -i "s@ipaddress@$ADDRESS@g" mysql

echo 'deb http://ru.archive.ubuntu.com/ubuntu/ precise main' | sudo tee --append /etc/apt/sources.list
sudo cp -rf apache22 /etc/apt/preferences.d/apache22

#echo "mysql-server mysql-server/root_password password root" | sudo debconf-set-selections
#echo "mysql-server mysql-server/root_password_again password root" | sudo debconf-set-selections

sudo apt-get update
sudo apt-get install -y ntp
sudo apt-get install -y tzdata
sudo dpkg-reconfigure tzdata
sudo dpkg -i *.deb

echo "yate hold" | sudo dpkg --set-selections

if [ "$arch" = 'x86_64' ]
then
	echo "yate-core hold" | sudo dpkg --set-selections
fi

sudo cp -rf mysql /usr/share/dbconfig-common/data/kommunikator/install/mysql

sudo ln -s /var/www/kommunikator /var/www/service

sudo cp -rf ysipchan.yate /usr/lib/yate/ysipchan.yate
sudo chmod +x /usr/lib/yate/ysipchan.yate

sudo apt-get install -f -y

sudo pear install Mail
sudo pear install Mail_Mime
sudo pear install Net_SMTP

YATE_SCRIPTS=/usr/share/yate/scripts
TARGET=/var/www/kommunikator
RECORDS=/var/lib/misc/records

p=`pwd`
cd $YATE_SCRIPTS
sudo npm i mysql
cd $p

sudo mkdir -p $RECORDS
sudo mkdir -p $RECORDS/leg

sudo chown yate:yate $RECORDS -R
sudo ln -sf  $RECORDS $TARGET

if [ "$arch" = 'x86_64' ]
then
	sudo cp -rf yate.default /etc/default/yate
	sudo mkdir /var/run/yate/
	sudo ln -s /var/run/yate.pid /var/run/yate/yate.pid
fi	

sudo apt-get install -y php5-sqlite screen lame

sudo cp -rf sqlite3.php /usr/share/php/DB/

sudo mkdir /etc/webrtc2sip
sudo cp -rf webrtc2sip/* /etc/webrtc2sip
sudo chown -R www-data:www-data /etc/webrtc2sip/c2c_sqlite.db
sudo chmod +x /etc/webrtc2sip/scripts/*

sudo sh webrtc2sip_source.sh

sudo cp -rf moh/* /var/lib/misc/moh
sudo chown -R www-data:www-data /var/lib/misc/moh

sudo cp -rf auto_attendant/* /var/lib/misc/auto_attendant
sudo chown -R www-data:www-data /var/lib/misc/auto_attendant

sudo mkdir /var/www/c2c
sudo cp -rf c2c/* /var/www/c2c
sudo chown -R www-data:www-data /var/www/c2c

sudo mkdir /var/www/callback
sudo cp -rf callback/* /var/www/callback
sudo chown -R www-data:www-data /var/www/callback

cd ../
sudo rm -rf ./komunikator.temp

sudo sed -i "s@;  sip=level 8@  sip=level 9@g" /etc/yate/yate.conf

sudo service yate stop
sudo service yate start

if [ "$arch" = 'x86_64' ]
then
	sudo chown root:root /var/run/yate.pid
fi

sleep 10 && sudo service yate restart

echo 'Установка завершена'
