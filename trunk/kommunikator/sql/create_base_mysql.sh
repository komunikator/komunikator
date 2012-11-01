#!/bin/bash
# ----- setup -----
DB="PBX"
ROOTUSER="root"
ROOTPASS="root"
USERUSER="user"
USERPASS="user"
FILE="./PBX_mysql.sql"
# -----------------
mysqladmin -u $ROOTUSER -p$ROOTPASS create $DB
CREATEUSER="CREATE USER '$USERUSER'@'localhost' IDENTIFIED BY '$USERPASS';\nGRANT SELECT, INSERT, UPDATE, DELETE ON \`$DB\`.* TO '$USERUSER'@'localhost';\n"
echo -e $CREATEUSER |mysql -u $ROOTUSER -p$ROOTPASS
cat $FILE |mysql -D $DB -u $ROOTUSER -p$ROOTPASS
