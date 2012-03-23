#!/bin/bash

if [ "x$1" = "x--background" ]; then
    shift
    "$0" "$@" &
    exit 0
fi

if [ "x$1" = "x--verbose" ]; then
    shift
else
    exec < /dev/null &> /dev/null
fi

file="$1"
shift

if [ "x$file" = "x" -o "x$1" = "x" ]; then
    exit 1
fi

sox -t raw -r 8000 -s -b 16 "$@" -t wav - | lame - "$file"
/usr/share/yate/scripts/send_voicem.php "$file"
#chown apache "$file"
#chown apache "$@"
