#/usr/bin/sh

ATTFILE=~/report/ats_report.csv
ATTNAME=ats_report.csv
#MAILTO=alexander.elkin@digt.ru
MAILTO=a.mukhorina@digt.ru
MAILFROM=epr@digt.ru
SUBJECT="Отчет по звонкам"

( cat <<HERE; uuencode "${ATTFILE}" "${ATTNAME}" ) | sendmail -oi -t
From: ${MAILFROM}
To: ${MAILTO}
Subject: $SUBJECT

HERE
