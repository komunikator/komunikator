#! /bin/sh

# make_all_prompts.sh
# This file is part of the FreeSentral Project http://freesentral.com
#
# FreeSentral - is a Web Graphical User Interface for easy configuration of the Yate PBX software
# Copyright (C) 2008-2009 Null Team
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301, USA.

export IFS='	'
while read name text; do
    f="$name.au"
    if [ -f "$f" ]; then
	echo "$f already exists, skipping..."
    else
	echo "$f: $text"
	echo "$text" | text2wave -scale 4 -F 8000 -otype ulaw | sox -t raw -r 8000 -c 1 -U - "$f"
    fi
done << EOF
deleted	This message was deleted
greeting	Voicemail
menu	To listen to the first message press 0, to jump to the previous message press 7, to replay the current message press 8, to jump to the next message press 9, to record your greeting press 1, to listen to your greeting press 2, to exit press 3, to listen to this menu again press *
nogreeting	Voicemail	
novmail	This number is not recognized in the system
password	Password
usernumber	Usernumber
EOF