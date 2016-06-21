#/usr/bin/sh
tmp=/tmp
p=/root/report
sql=$p/ats_report$1.sql

if [ ! -f $sql ]
then
   echo "File $sql does not exist."
   exit 1;
fi
#exit 0;

rm -f $tmp/ats_report.tmp
mysql -uroot -proot < $sql
iconv -t WINDOWS-1251 -f UTF-8 $tmp/ats_report.tmp > $p/ats_report.csv
$p/send_mail.sh

