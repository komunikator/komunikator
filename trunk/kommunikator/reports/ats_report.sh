#/usr/bin/sh
tmp=/tmp

rm -f $tmp/ats_report.tmp
mysql -uroot -proot < ./ats_report.sql
iconv -t WINDOWS-1251 -f UTF-8 $tmp/ats_report.tmp > ./ats_report.csv
./send_mail.sh