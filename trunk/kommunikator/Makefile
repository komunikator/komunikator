
DESTDIR :=
vardir = /var/www/kommunikator

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
	cat shema_mysql.sql ntn_settings.sql unique.sql > ${DESTDIR}/usr/share/dbconfig-common/data/kommunikator/install/mysql
	mkdir -p ${DESTDIR}/etc/kommunikator
	cp etc/apache.conf ${DESTDIR}/etc/kommunikator
