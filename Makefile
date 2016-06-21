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

DESTDIR :=
vardir = /var/www/kommunikator
usrdir = /usr/share/kommunikator
misc = /var/lib/misc
all:

install:
	mkdir -p ${DESTDIR}${vardir}/ext/locale
	mkdir -p ${DESTDIR}${vardir}/ext/resources/themes
	mkdir -p ${DESTDIR}${vardir}/ext/resources/css
	cd src; \
	cp ./* ${DESTDIR}${vardir}; \
	cp -R js ${DESTDIR}${vardir}; \
	cp -R php ${DESTDIR}${vardir}; \
	cp ext/ext-all.js ${DESTDIR}${vardir}/ext; \
	cp ext/locale/*.* ${DESTDIR}${vardir}/ext/locale; \
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
	mkdir -p ${DESTDIR}${misc}; \
	mkdir -p ${DESTDIR}${misc}/auto_attendant; \
	mkdir -p ${DESTDIR}${misc}/moh; \
	cp misc/*.* ${DESTDIR}${misc}
	ln -sf ${misc}/auto_attendant ${DESTDIR}${vardir}/auto_attendant
	ln -sf ${misc}/moh ${DESTDIR}${vardir}/moh