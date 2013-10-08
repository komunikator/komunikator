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