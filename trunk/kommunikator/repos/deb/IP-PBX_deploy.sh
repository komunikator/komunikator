#  | RUS | - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

#    «Komunikator» – Web-интерфейс для настройки и управления программной IP-АТС «YATE»
#    Copyright (C) 2012-2013, ООО «Телефонные системы»

#    ЭТОТ ФАЙЛ является частью проекта «Komunikator»

#    Сайт проекта «Komunikator»: http://4yate.ru/
#    Служба технической поддержки проекта «Komunikator»: E-mail: support@4yate.ru

#    В проекте «Komunikator» используются:
#      исходные коды проекта «YATE», http://yate.null.ro/pmwiki/
#      исходные коды проекта «FREESENTRAL», http://www.freesentral.com/
#      библиотеки проекта «Sencha Ext JS», http://www.sencha.com/products/extjs

#    Web-приложение «Komunikator» является свободным и открытым программным обеспечением. Тем самым
#  давая пользователю право на распространение и (или) модификацию данного Web-приложения (а также
#  и иные права) согласно условиям GNU General Public License, опубликованной
#  Free Software Foundation, версии 3.

#    В случае отсутствия файла «License» (идущего вместе с исходными кодами программного обеспечения)
#  описывающего условия GNU General Public License версии 3, можно посетить официальный сайт
#  http://www.gnu.org/licenses/ , где опубликованы условия GNU General Public License
#  различных версий (в том числе и версии 3).

#  | ENG | - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

#    "Komunikator" is a web interface for IP-PBX "YATE" configuration and management
#    Copyright (C) 2012-2013, "Telephonnyie sistemy" Ltd.

#    THIS FILE is an integral part of the project "Komunikator"

#    "Komunikator" project site: http://4yate.ru/
#    "Komunikator" technical support e-mail: support@4yate.ru

#    The project "Komunikator" are used:
#      the source code of "YATE" project, http://yate.null.ro/pmwiki/
#      the source code of "FREESENTRAL" project, http://www.freesentral.com/
#      "Sencha Ext JS" project libraries, http://www.sencha.com/products/extjs

#    "Komunikator" web application is a free/libre and open-source software. Therefore it grants user rights
#  for distribution and (or) modification (including other rights) of this programming solution according
#  to GNU General Public License terms and conditions published by Free Software Foundation in version 3.

#    In case the file "License" that describes GNU General Public License terms and conditions,
#  version 3, is missing (initially goes with software source code), you can visit the official site
#  http://www.gnu.org/licenses/ and find terms specified in appropriate GNU General Public License
#  version (version 3 as well).

#  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

#!/bin/bash
arch=$(uname -m)

mkdir komunikator.temp
cd komunikator.temp

if [ "$arch" = 'x86_64' ]
then
	wget http://komunikator.ru/repos/deb/1.0.b2/komunikator_64.tar.gz
	sudo mv komunikator_64.tar.gz komunikator.tar.gz
else
	wget http://komunikator.ru/repos/deb/1.0.b2/komunikator.tar.gz
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

cd ../
sudo rm -rf ./komunikator.temp

sudo sed -i "s@;  sip=level 8@  sip=level 9@g" /etc/yate/yate.conf

sudo service yate stop
sudo service yate start

if [ "$arch" = 'x86_64' ]
then
	sudo chown root:root /var/run/yate.pid
fi

echo 'Установка завершена'
