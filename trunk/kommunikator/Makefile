
DESTDIR :=
vardir = /var/www/kommunikator
usrdir = /usr/share/kommunikator

all:

install:
	mkdir -p ${DESTDIR}${vardir}/ext/resources/themes
	mkdir -p ${DESTDIR}${vardir}/ext/resources/css
	cd src; \
	cp ./* ${DESTDIR}${vardir}; \
	cp -R js ${DESTDIR}${vardir}; \
	cp -R php ${DESTDIR}${vardir}; \
	cp ext/ext-all.js ${DESTDIR}${vardir}/ext; \
	cp ext/resources/css/ext-all*.css ${DESTDIR}${vardir}/ext/resources/css; \
	cp -R ext/resources/themes/images ${DESTDIR}${vardir}/ext/resources/themes
	mkdir -p ${DESTDIR}/usr/share/dbconfig-common/data/kommunikator/install
	cd SQL; \
	cat shema_mysql.sql > ${DESTDIR}/usr/share/dbconfig-common/data/kommunikator/install/mysql
	mkdir -p ${DESTDIR}/etc/kommunikator; \
	cp etc/apache.conf ${DESTDIR}/etc/kommunikator; \
	cp -R etc/yate ${DESTDIR}/etc/kommunikator
	mkdir -p ${DESTDIR}${usrdir}; \
	cp -R scripts ${DESTDIR}${usrdir}; \
	chmod -R 755 ${DESTDIR}${usrdir}/scripts/*; \
	ln -sf ${usrdir}/scripts/config.php ${DESTDIR}${vardir}/config.php
	ln -sf ${usrdir}/scripts/lib_queries.php ${DESTDIR}${vardir}/lib_queries.php
	ln -sf ${usrdir}/scripts/libyate.php ${DESTDIR}${vardir}/libyate.php
        chmod 766 /var/lib/php5
        mkdir -p /var/lib/misc/auto_attendant
        mkdir -p /var/lib/misc/moh 
        chmod -R 766 /var/lib/misc
        ln -sf  /var/lib/misc/auto_attendant ${DESTDIR}${vardir}/auto_attendant
        ln -sf  /var/lib/misc/moh ${DESTDIR}${vardir}/moh



