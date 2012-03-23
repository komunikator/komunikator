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

cat "$@" | lame -r -x -s 8000 -m mono --resample 11.025 - "$file"

chown apache "$file"
chown apache "$@"
