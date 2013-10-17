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
# (указывает какой программе выполнить данный файл)

apt-get update
# обновляет информацию о пакетах, содержащихся в репозиториях

rm -f ./yate.deb
# удаляет файл yate.deb (без запроса подтверждения на удаление, и игнорирования ошибок)

wget http://4yate.ru/repos/deb/yate.deb
# скачивает файл yate.deb по ip-адресу 4yate.ru/repos/deb

dpkg -i ./yate.deb
# осуществляет прямую (из локального файла) установку пакета из deb-файла yate.deb

rm -f ./komunikator.deb
# удаляет файл komunikator.deb (без запроса подтверждения на удаление, и игнорирования ошибок)

wget http://4yate.ru/repos/deb/komunikator.deb
# скачивает файл komunikator.deb по ip-адресу 4yate.ru/repos/deb

dpkg -i ./komunikator.deb
# осуществляет прямую (из локального файла) установку пакета из deb-файла komunikator.deb

yes | apt-get install -f
# разрешение зависимостей - установка требуемых пакетов из репозиториев