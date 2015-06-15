##===========================================================================##
##=============[ Installer's configuration constant variables ]==============##
##===========================================================================##

##Supported Linux distros' codenames
udistro="trusty"

##Message to be displayed on unsopported systems
wrongdistromessage="Установка Komunikator 1.0.b3 может производиться только на ОС Ubuntu 14.04"

##===========================================================================##
##=============================[ Installation ]==============================##
##===========================================================================##

release=$(lsb_release -cs)
if [ "$release" = "$udistro" ]
then
	arch=$(uname -m)
	
	echo "Installer: Doing some APT configuration for compatibility..."
		echo 'deb http://ru.archive.ubuntu.com/ubuntu/ precise main' | tee --append /etc/apt/sources.list
		cp -rf apache22 /etc/apt/preferences.d/apache22
				
		apt-get -qq update

	echo "Installer: Generating and setting the DB user passwords..."
		apt-get install -qq -y pwgen
		dbuserpw=$(pwgen -cAns -1)
		printf "ПОЖАЛУЙСТА, КАК СЛЕДУЕТ ЗАПОМНИТЕ ПАРОЛЬ И ПОТОМ УДАЛИТЕ ЭТОТ ФАЙЛ\nПароль пользователя root для доступа к базе данных MySQL\n$dbuserpw" > ~/DB_root_password.txt
		echo "mysql-server mysql-server/root_password password $dbuserpw" | debconf-set-selections
		echo "mysql-server mysql-server/root_password_again password $dbuserpw" | debconf-set-selections

	echo "Installer: Installing some tools and dependencies..."
		apt-get install -qq -y ntp tzdata php5-sqlite screen lame libsrtp0 libsrtp0-dev libssl1.0.0 libssl-dev libspeex1 libspeex-dev libspeexdsp1 libspeexdsp-dev libxml2 libxml2-dev
	
	echo "Installer: Installing the distro packages..."
		if [ "$arch" = 'x86_64' ]
		then		
			dpkg -i *_all.deb *_amd64.deb
		else
			dpkg -i *_all.deb *_i386.deb
		fi
	
	echo "Installer: Preventing the old Yate version from being auto-updated for compatibility..."
		echo "yate hold" | dpkg --set-selections
		if [ "$arch" = 'x86_64' ]
		then				
			echo "yate-core hold" | dpkg --set-selections
		fi
	
	echo "Installer: Installing the package dependencies..."
		apt-get install -qq -f -y
	
	echo "Installer: Configuring the database..."
		hostaddress="`ifconfig eth0 2>/dev/null|awk '/inet addr:/ {print $2}'|sed 's/addr://'`"		
		sed -i "s@ipaddress@$hostaddress@g" shema_mysql.sql		
		mysql -uroot -p$dbuserpw -e "CREATE USER 'kommunikator'@'localhost' IDENTIFIED BY 'kommunikator';"
		mysql -uroot -p$dbuserpw -e "create database kommunikator"
		mysql -u root -p$dbuserpw kommunikator < shema_mysql.sql	
		mysql -uroot -p$dbuserpw -e "GRANT ALL PRIVILEGES ON * . * TO 'kommunikator'@'localhost';"		
	
	echo "Installer: Copying some components and doing some post-installation configuration..."
                chmod 664 -R data
                
		if [ "$arch" = 'x86_64' ]
		then		
			cp -aprf data/all/* /
			cp -aprf data/amd64/* /
		else
			cp -aprf data/all/* /
			cp -aprf data/x86/* /
                        echo "#" > /etc/default/yate
		fi

                rm -rf data
		
                chmod 665 /etc/webrtc2sip
                chmod 755 -R /usr/lib/yate /etc/webrtc2sip/scripts /usr/local/bin/webrtc2sip /usr/local/lib/libtiny*
		chown -R www-data:www-data /var/lib/misc/moh /var/lib/misc/auto_attendant /var/www/c2c /var/www/callback /etc/webrtc2sip/c2c_sqlite.db		
		chown -R yate:yate /var/lib/misc/records/
		
		ldconfig
		
		pear install Mail Mail_Mime Net_SMTP		
	
		p=`pwd`
		cd /usr/share/yate/scripts
			npm i mysql
		cd $p		
			
	echo "Installer: Restarting the services..."
		service mysql restart
		service apache2 restart
		service yate stop
		service yate start
		
	echo "Установка завершена"

else
	echo "$wrongdistromessage"
fi
	
