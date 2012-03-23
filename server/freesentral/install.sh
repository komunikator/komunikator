#! /bin/bash

# install.sh
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

readopt()
{
    read -p "$1: [$2] " tmp
    case "x$tmp" in
	xn|xno|xN|xNO|xNo)
	    ;;
	x)
	    echo "$2"
	    ;;
	*)
	    echo "$tmp"
	    ;;
    esac
}

cmp_dates() {
	year1=`date -d"${1}" +%G`
	year2=`date -d"${2}" +%G`
	if [ ${year1} -gt ${year2} ]; then
		return 1
	else
		if [ ${year1} -lt ${year2} ]; then
			return 0
		fi
	fi
	# if year is the same compare the number of day in the year
	dayyear1=`date -d"${1}" +%j`
	dayyear2=`date -d"${2}" +%j`
	if [ ${dayyear1} -lt ${dayyear2} ]; then
		return 0
	fi
	return 1
}

showhelp()
{
    cat <<EOF
    $version usage:
$0
    [--no_defaults]
    [--config dir] [--scripts dir] [--prompts dir] [--webpage dir] [--upload_dir dir]
    [--psql executable]
    [--dbhost host] [--dbname name]
    [--dbuser user] [--dbpass password]
    [--enable_logging on/off]
    [--generate_certificate yes/no]
    [--timezone localtimezone]
    [--quiet]
        or one of the following unique parameters:
    help version tarball tgz tbz rpm init_system generate_certificate
EOF
}

maketarball()
{
    wd=`pwd|sed 's,^.*/,,'`
    mkdir -p packing/tarballs
    pushd .. > /dev/null
    excl=`find "${wd}" -name '*~' -o -name '.*.swp' | sed 's/^/--exclude /'`
    tar "c${1}f" "${wd}/packing/tarballs/${2}" $tarexclude $excl "${wd}"
    popd > /dev/null
}

confdata()
{
cat << EOF
<?php
/* File created by $version */

/* Settings for connecting to the PostgreSQL database */

/* Host where the database server is running - use "localhost" for local */
\$db_host = "$dbhost";
/* Name of the database (a server may have many independent databases) */
\$db_database = "$dbname";
/* Database username to use when connecting */
\$db_user = "$dbuser";
/* Password for the database access */
\$db_passwd = "$dbpass";

date_default_timezone_set($timezone);

EOF
if [ "x$1" = "xweb" ]; then
cat << EOF
\$target_path = "${prompts}";
\$do_not_load = array();        //modules that are inserted here won't be loaded
\$limit = 20;  //max number to display on page
\$enable_logging = "${enable_logging}"; // possible values: "on"/"off", true/false, "yes"/"no" 
\$upload_path = "${upload_dir}";     // path where file for importing extensions will be uploaded
\$default_ip = "ssl://${ip_yate}";	//	ip address where yate runs
\$default_port = "5039";	// port used to connect to
\$block = array("admin_settings"=>array("cards"));	// don't change this. This option is still being tested
?>
EOF
else
cat << EOF
\$conn  = pg_connect("host='\$db_host' dbname='\$db_database' user='\$db_user' password='\$db_passwd'")
    or die("Could not connect to the postgresql database");

\$vm_base = "$prompts$existing_prompts";
\$no_groups = false;
\$no_pbx = false;
\$uploaded_prompts = "$prompts$existing_prompts";
\$query_on = false;
\$max_resets_conn = 5;
?>
EOF
fi
}

answers_csr() {
	echo --
	echo SomeState
	echo SomeCity
	echo SomeOrganization
	echo SomeOrganizationalUnit
	echo localhost.localdomain
	echo root@localhost.localdomain
	echo ""
	echo ""
}

generate_certificate_now()
{
	# generate SSL certificate that will be used when sending requests from web to yate(only if this installs config file for yate)
	echo "Trying to generate SSL certificate"
	cert_dir=$DESTDIR${configs}
	mkdir -p "${cert_dir}"

	crt_dir=${cert_dir}
	key_dir=${cert_dir}
	csr_dir=${cert_dir}
	mkdir -p "${key_dir}"
	mkdir -p "${crt_dir}"
	key="${key_dir}/freesentral.key"
	crt="${crt_dir}/freesentral.crt"
	csr="${csr_dir}/freesentral.csr"

	# check if a new certificate should be generated
	# generate if certificate is already expired or if it will expire today
	replace=1
	if [ -f "${crt}" ]; then
		str=`openssl x509 -in ${crt} -enddate -noout`
		len=${#str}
		expr_date=`date -u -d"${str:9:len}"`
		now=`date -u`
		cmp_dates "${now}" "${expr_date}" && replace=0
	fi
	if [ "${replace}" = 1 ]; then
		# generate key file
		openssl genrsa -des3  -passout pass:freesentral -out "${key}" 1024  2> /dev/null
		echo "Generating SSL key"
		answers_csr | openssl req -new -passin pass:freesentral -key "${key}" -out "${csr}" 2> /dev/null
		echo "Generating SSL csr"
		cp "${key}" "${key}.orig"
		openssl rsa -passin pass:freesentral -in "${key}.orig" -out "${key}" 2> /dev/null
		openssl x509 -req -days 1825 -in "${csr}" -signkey "${key}" -out "${crt}" 2> /dev/null
		rm -f "${key}.orig"
		rm -f "${csr}"
	fi
}

init_system()
{
    if [ X`id -u` != "X0" ]; then
	echo "You need to be root for this action" >&2
	exit 1
    fi
    ## Allow for restarting in case of kernel panic
    f="/etc/sysctl.conf"
    if ! fgrep -q 'kernel.panic' "$f"; then
	echo 'kernel.panic = 5' >> "$f"
	echo '5' > /proc/sys/kernel/panic
    fi

    ## Add the apache helper to allowed sudoers
    f="/etc/sudoers"
    if ! fgrep -q 'freesentral/ctl-root' "$f"; then
	echo -e '## Added for FreeSentral\nDefaults !requiretty\napache env_reset,!syslog,ALL=NOPASSWD: /usr/libexec/freesentral/ctl-root' >> "$f"
    fi

    ## Activate the auto vacuum in PostgreSQL
    f="/var/lib/pgsql/data/postgresql.conf"
    if ! fgrep -q 'FreeSentral' "$f"; then
	cat <<EOF >> "$f"

# generated by FreeSentral - please do not delete this line
#stats_start_collector = on
#stats_row_level = on
track_counts = on

autovacuum = on
autovacuum_naptime = 300
EOF
    fi

    ## Fix the ownership of PostgreSQL log file after live install
    f="/var/log/postgres/postgresql"
    if [ -f "$f" ]; then
	if [ X`stat -c '%U' "$f" 2> /dev/null` != "Xpostgres" ]; then
	    chown -R postgres.postgres "$f"
	    /etc/init.d/postgresql restart
	fi
    fi

    ## Allow Apache to write to the wanpipe config directory
    f="/etc/wanpipe"
    if [ -d "$f" ]; then
	if [ X`stat -c '%U' "$f" 2> /dev/null` != "Xapache" ]; then
	    chown -R apache.root "$f"
	fi
    fi

    ## Allow Apache to write to the network-scripts config directory
    f="/etc/sysconfig/network-scripts"
    if [ -d "$f" ]; then
	if [ X`stat -c '%U' "$f" 2> /dev/null` != "Xapache" ]; then
	    chown -R apache.root "$f"
	fi
    fi
}


pkgname="freesentral"
pkglong="FreeSentral"
shortver="1.2"
version="$pkglong v$shortver"
interactive="yes"
tarexclude="--exclude CVS --exclude .cvsignore --exclude .svn --exclude .xvpics --exclude packing/tarballs --exclude config.php"

scripts=""
configs=""
prompts=""
webpage=""
dbhost="localhost"
dbname="$pkgname"
dbuser="postgres"
dbpass=""
timezone=`sed -n 's/^ZONE= *//p' /etc/sysconfig/clock 2>/dev/null`
test -z "$timezone" && timezone="Europe/London"
upload_dir="/tmp"
enable_logging="on"

case "x$1" in
    x--no_defaults)
	shift
	;;
    *)
	configs="`yate-config --config 2>/dev/null`"
	scripts="`yate-config --scripts 2>/dev/null`"
	prompts="/var/spool/voicemail"
	generate_certificate="yes"
	webpage="/var/www/html"
	if [ -d "$webpage" ]; then
	    webpage="$webpage/$pkgname"
	else
	    webpage="/var/www"
	    if [ -d "$webpage" ]; then
		webpage="$webpage/$pkgname"
	    else
		webpage=""
	    fi
	fi
	;;
esac
webuser="apache"
ip_yate="127.0.0.1"

case "x`yate-config --version 2>/dev/null`" in
    x2.*|x3.*|x4.*|x5.*|x6.*|x7.*|x8.*|x9.*)
	;;
    *)
	scripts=""
	configs=""
	prompts=""
	;;
esac

psqlcmd="`which psql`"

echo "--------------------------------------------------------"
echo "Note!!! FreeSentral requires at least PostgreSQL 8.2.0"
echo "--------------------------------------------------------"

if [ "$#" = "1" ]; then
    case "x$1" in
	xtarball|xtargz|xtgz)
	    maketarball z "$pkgname-$shortver.tar.gz"
	    exit
	    ;;
	xtarbz2|xtarbz|xtbz)
	    maketarball j "$pkgname-$shortver.tar.bz2"
	    exit
	    ;;
	x*.tar.gz|x*.tgz)
	    maketarball z "$1"
	    exit
	    ;;
	x*.tar.bz2|x*.tbz)
	    maketarball j "$1"
	    exit
	    ;;
	x*.tar)
	    maketarball "" "$1"
	    exit
	    ;;
	xrpm)
	    maketarball z "$pkgname-$shortver.tar.gz" || exit $?
	    rpmbuild -tb "packing/tarballs/$pkgname-$shortver.tar.gz"
	    exit
	    ;;
	xhelp)
	    showhelp
	    exit
	    ;;
	xversion)
	    echo "$pkglong $shortver"
	    exit
	    ;;
	xgenerate_certificate)
	    generate_certificate_now
	    exit
	    ;;
	xinit_system)
	    init_system
	    exit
	    ;;
    esac
fi

while [ "$#" != "0" ]; do
    cmd="$1"
    shift
    case "x$cmd" in
	x--help|x-h)
	    showhelp
	    exit
	    ;;
	x--version|x-V)
	    echo "$pkglong $shortver"
	    exit
	    ;;
	x--quiet|x-q)
	    interactive="no"
	    ;;
	x--config|x-c)
	    configs="$1"
	    shift
	    ;;
	x--scripts|x-s)
	    scripts="$1"
	    shift
	    ;;
	x--prompts|x-p)
	    prompts="$1"
	    shift
	    ;;
	x--webpage|x-w)
	    webpage="$1"
	    shift
	    ;;
	x--psql)
	    psqlcmd="$1"
	    shift
	    ;;
	x--dbhost)
	    dbhost="$1"
	    shift
	    ;;
	x--dbname)
	    dbname="$1"
	    shift
	    ;;
	x--dbuser)
	    dbuser="$1"
	    shift
	    ;;
	x--dbpass)
	    dbpass="$1"
	    shift
	    ;;
	x--upload_dir)
		upload_dir="$1"
		shift
		;;
	x--timezone)
		timezone="$1"
		shift
		;;
	x--enable_logging)
		enable_logging="$1"
		shift
		;;
	x--generate_certificate)
		generate_certificate="$1"
		shift
		;;
	*)
	    echo "Unexpected parameter: $cmd" >&2
	    showhelp >&2
	    exit
	    ;;
    esac
done

echo "Installer for $version"

if [ "x$interactive" != "xno" ]; then
    echo "At the following prompts you can enter the word 'no' to disable defaults"
    configs=`readopt "Install Yate config file in" "$configs"`
    scripts=`readopt "Install Yate scripts in" "$scripts"`
    prompts=`readopt "Install IVR prompts in" "$prompts"`
    webpage=`readopt "Install Web pages in" "$webpage"`
    if [ "x$prompts" = "x" ]; then
	existing_prompts=`readopt "Path where IVR prompts are found(necesarry for config,scripts and web pages)" "$prompts"`
    fi
    ip_yate=`readopt "Ip address for yate server" "$ip_yate"`
    webuser=`readopt "Web user " "$webuser"`
    dbhost=`readopt "Database host" "$dbhost"`
    if [ -n "$dbhost" ]; then
	dbname=`readopt "Database name" "$dbname"`
	dbuser=`readopt "Database user" "$dbuser"`
	dbpass=`readopt "Database password" "$dbpass"`
	psqlcmd=`readopt "PostgreSQL command" "$psqlcmd"`
    else
	dbname=""
	dbuser=""
	dbpass=""
	psqlcmd=""
    fi
fi

if [ "x$webpage$dbhost" = "x" ]; then
    echo "Nothing to do!"
    exit
fi

if [ -n "$psqlcmd" ]; then
    if "$psqlcmd" --version 2> /dev/null | grep -q ' [89\.]'; then
	/bin/true
    else
	echo "Invalid or too old PostgreSQL client: $psqlcmd" >&2
	psqlcmd=""
    fi
fi

case x"$timezone"x in
    x\"*\"x)
	;;
    *)
	timezone="\"$timezone\""
	;;
esac

echo "Install options"
cat <<EOF
    Config file in '$configs'
    Scripts dir in '$scripts'
    IVR prompts in '$prompts'
    Web pages in   '$webpage'
	Web user       '$webuser'
    Database:
        Host     '$dbhost'
        Name     '$dbname'
        User     '$dbuser'
        Password '$dbpass'
    PosgreSQL tool '$psqlcmd'
EOF

case "x$DESTDIR" in
    x)
	;;
    x/)
	DESTDIR=""
	;;
    x*/)
	;;
    *)
	DESTDIR="$DESTDIR/"
	;;
esac
if [ "x$DESTDIR" != "x" ]; then
    echo "Destination directory: '$DESTDIR'"
fi

if [ "x$interactive" != "xno" ]; then
    if [ -z `readopt "Proceed with installation?" "yes"` ]; then
	echo "Aborting..."
	exit
    fi
fi


if [ -n "$configs" ]; then
    echo "Installing Yate configuration files"

	# yate.conf
    fe="$DESTDIR$configs/yate.conf";
    e="
[localsym]
; pwlib does not clean up properly on Linux so we must disable global symbols
;  unfortunately preventing all pwlib plugins from loading
h323chan.yate=yes
"

    if [ -e "$fe" ]; then
	if [ -z `readopt "Overwrite existing yate.conf ?" "yes"` ]; then
	    echo "Please edit file $fe like follows:"
	    echo "$e"
	    fe=""
	fi
    fi
    if [ -n "$fe" ]; then
	echo "Creating yate configuration file"
	mkdir -p "$DESTDIR$configs"
	echo "; File created by $version
$e" > "$fe"
    fi

	# regexroute.conf
    fe="$DESTDIR$configs/regexroute.conf";
    e="
[priorities]
; Route here before register.php which is at priority 100
route=95

[default]
\${address}^127\\.0\\.0\\.=goto localhost
\${username}.=goto localhost

[localhost]
; The following are for testing purposes
^99991001\$=tone/dial
^99991002\$=tone/busy
^99991003\$=tone/ring
^99991004\$=tone/specdial
^99991005\$=tone/congestion
^99991006\$=tone/outoforder
^99991007\$=tone/milliwatt
^99991008\$=tone/info
"

    if [ -e "$fe" ]; then
	if [ -z `readopt "Overwrite existing regexroute.conf ?" "yes"` ]; then
	    echo "Please edit file $fe like follows:"
	    echo "$e"
	    fe=""
	fi
    fi
    if [ -n "$fe" ]; then
	echo "Creating regexroute configuration file"
	mkdir -p "$DESTDIR$configs"
	echo "; File created by $version
$e" > "$fe"
    fi

	# extmodule.conf
    fe="$DESTDIR$configs/extmodule.conf";
    e="
[scripts]
register.php=param
ctc-global.php=
banbrutes.php=
"

    if [ -e "$fe" ]; then
	if [ -z `readopt "Overwrite existing extmodule.conf ?" "yes"` ]; then
	    echo "Please edit file $fe like follows:"
	    echo "$e"
	    fe=""
	fi
    fi
    if [ -n "$fe" ]; then
	echo "Creating extmodule configuration file"
	mkdir -p "$DESTDIR$configs"
	echo "; File created by $version
$e" > "$fe"
    fi

	# moh.conf
    fe="$DESTDIR$configs/moh.conf";
    e="
[mohs]
default=while true; do madplay -q --no-tty-control -m -R 8000 -o raw:- -z $prompts$existing_prompts/moh/*.mp3; done
madplay=while true; do madplay -q --no-tty-control -m -R 8000 -o raw:- -z \${mohlist}; done
"

    if [ -e "$fe" ]; then
	if [ -z `readopt "Overwrite existing moh.conf ?" "yes"` ]; then
	    echo "Please edit file $fe like follows:"
	    echo "$e"
	    fe=""
	fi
    fi
    if [ -n "$fe" ]; then
	echo "Creating moh configuration file"
	mkdir -p "$DESTDIR$configs"
	echo "; File created by $version
$e" > "$fe"
    fi

	# pgsqldb.conf
    fe="$DESTDIR$configs/pgsqldb.conf";
    e="
[freesentral]
host=$dbhost
database=$dbname
user=$dbuser
password=$dbpass
"

    if [ -e "$fe" ]; then
	if [ -z `readopt "Overwrite existing pgsqldb.conf ?" "yes"` ]; then
	    echo "Please edit file $fe like follows:"
	    echo "$e"
	    fe=""
	fi
    fi
    if [ -n "$fe" ]; then
	echo "Creating pgsqldb configuration file"
	mkdir -p "$DESTDIR$configs"
	echo "; File created by $version
$e" > "$fe"
    fi	

	# queues.conf
    fe="$DESTDIR$configs/queues.conf";
    e="
[general]
; General settings of the queues module

; account: string: Name of the database account used in queries
account=freesentral
; priority: int: Priority of message handlers
priority=20
; rescan: int: Period of polling for available operators, in seconds
;rescan=5
; mintime: int: Minimum time between queries, in milliseconds
;mintime=500

[queries]
; SQL queries that get data about the queue and operators

; queue: string: Query to pick queue parameters, returns zero or one row
; Relevant substitutions:
;  \${queue}: string: Name of the queue as obtained from routing
; Relevant returned params:
;  mintime: int: Minimum time between queries, in milliseconds
;  length: int: Maximum queue length, will declare congestion if grows larger
;  maxout: int: Maximum number of simultaneous outgoing calls to operators
;  greeting: string: Resource to be played initially as greeting
;  onhold: string: Resource to be played while waiting in queue
;  maxcall: int: How much to call the operator, in milliseconds
;  prompt: string: Resource to play to the operator when it answers
;  notify: string: Target ID for notification messages about queue activity
;  detail: bool: Notify when details change, including call position in queue
;  single: bool: Make just a single delivery attempt for each queued call
queue=SELECT mintime, length, maxout, greeting, 'moh/madplay' as onhold, maxcall, prompt, detail FROM groups WHERE groups.group_id='\${queue}'

; avail: string: Query to fetch operators to which calls can be distributed
; Relevant substitutions:
;  \${queue}: string: Name of this queue
;  \${required}: int: Number of operators required to handle incoming calls
;  \${current}: int: Number of calls to operators currently running
;  \${waiting}: int: Total number of calls waiting in this queue (assigned or not)
; Mandatory returned params:
;  location: string: Resource where the operator is located
;  username: string: User name of the operator
; Relevant returned params:
;  maxcall: int: How much to call the operator, in milliseconds
;  prompt: string: Resource to play to the operator when it answers
avail=SELECT extensions.location, extensions.extension as username FROM extensions, group_members WHERE extensions.extension_id=group_members.extension_id AND group_members.group_id='\${queue}' AND extensions.location IS NOT NULL AND coalesce(extensions.inuse_count,0)=0 ORDER BY extensions.inuse_last LIMIT \${required} 

[channels]
; Resources that will be used to handle incoming and outgoing calls
; incoming: string: Target that will handle incoming calls while queued
incoming=external/nodata/queue_in.php
; outgoing: string: Target that will be called to make calls to operators
outgoing=external/nodata/queue_out.php
"

    if [ -e "$fe" ]; then
	if [ -z `readopt "Overwrite existing queues.conf ?" "yes"` ]; then
	    echo "Please edit file $fe like follows:"
	    echo "$e"
	    fe=""
	fi
    fi
    if [ -n "$fe" ]; then
	echo "Creating queues configuration file"
	mkdir -p "$DESTDIR$configs"
	echo "; File created by $version
$e" > "$fe"
    fi

	# pbxassist.conf
    fe="$DESTDIR$configs/pbxassist.conf";
    e="
[general]
; Common settings

; enabled: bool: Enable the module and install message handlers
enabled=yes

; priority: int: Priority to install message handlers into engine
;priority=15

; default: bool: Assist channels by default, module must be enabled first
;default=yes

; incoming: bool: Assist incoming calls, needs that default is enabled
;incoming=yes

; filter: regexp: Expression matching assisted channel IDs, default all
;filter=

; dtmfpass: bool: Enter DTMF pass-through mode by default
;dtmfpass=no

; minlen: int: Minimum length of command sequences
;minlen=2

; maxlen: int: Maximum length of command sequences
;maxlen=20

; timeout: int: Inter-digit timeout in milliseconds
;timeout=30000

; retake: string: Exact sequence to exit DTMF pass-through mode
retake=###

[transfer]
; blind transfer: make call on behalf of peer, hangup this
; key: *1nnnnn*
trigger=\*1\([0-9]\+\)\*$
target=\1

[fortransfer]
; put the peer on hold and dial another number
; key: *2nnnnn*
trigger=\*2\([0-9]\+\)\*$
target=\1
onhangup=yes

[dotransfer]
; transfer held to active (2nd) call
; key: *4
trigger=\*4$

[onhold]
; toggle call on/off hold
; key: *0
trigger=\*0$

[returnhold]
; always return to the held peer
; key: *7
trigger=\*7$

[conference]
; put us and peer in an ad-hoc conference or return to conference
; key: *3
trigger=\*3$

[returnconf]
; always return to conference
; key: *6
trigger=\*6$

[returntone]
; always return to a dialtone, hang up peer
; key: *9
trigger=\*9$
;operation=dialtone

[secondcall]
; hangup the peer and dial another number
; key: *8nnnnn*
trigger=\*8\([0-9]\+\)\*$
target=\1

[seconddial]
; execute a dial while at dialtone
; key: nnnn*
trigger=^\([0-9]\+\)\*$
pbxstates=dial
target=\1
operation=secondcall

[flush]
; no operation, flush the buffer
; key: #
trigger=#$
operation=
pbxgreedy=yes
pbxprompt=tone/info

[flush-dial]
; flush the buffer, return to dialtone
; key: #
trigger=#$
pbxstates=dial
pbxgreedy=yes
pbxprompt=tone/info
message=call.execute
callto=tone/dial

[passthrough]
; enter DTMF pass-through mode
; key: **
trigger=\*\*
pbxprompt=tone/probe/1

; Example: enter conference named conf/dyn-N with key sequence #N# where N=0..9
;[conference]
;trigger=#\([0-9]\)#$
;message=call.conference
;room=conf/dyn-\1

[silence]
; silence the dialtone, keep collecting tones
; key: n
trigger=^[0-9]$
pbxstates=dial
pastekeys=\0
pbxgreedy=yes
message=call.execute
callto=tone/noise

[collect]
; keep collecting tones
; key: nnnn
trigger=^[0-9]\+$
pbxstates=dial
pastekeys=\0
pbxgreedy=yes
operation=

[transparent]
; send a tone as-is to the remote
; key: n
trigger=^[0-9]$

;[blessing]
; allow the remote user to use the PBX functionality - dangerous!
; key: *9
;trigger=^\*9$
;operation=setstate
;pbxguest=no
;id=${peerid}
"

    if [ -e "$fe" ]; then
	if [ -z `readopt "Overwrite existing pbxassist.conf ?" "yes"` ]; then
	    echo "Please edit file $fe like follows:"
	    echo "$e"
	    fe=""
	fi
    fi
    if [ -n "$fe" ]; then
	echo "Creating pbxassist configuration file"
	mkdir -p "$DESTDIR$configs"
	echo "; File created by $version
$e" > "$fe"
    fi

	# openssl.conf
    fe="$DESTDIR$configs/openssl.conf";
    e="
; This file keeps the configuration of the openssl module
; Each section, except for 'general' configures a server context

[general]

;[server_context]
; This section configures a SSL server context

; enable: boolean: Enable or disable the context
; Defaults to yes
;enable=yes

; domains: string: Comma separated list of domains the context will be used for
; A subdomain wildcard can be specified for a given domain, e.g.
;  *.null.ro will match any null.ro subdomains (including the 'null.ro' domain)
;domains=

; certificate: string: The name of the file containing the certificate for the context
; This parameter is required
;certificate=

; key: string: Optional certificate key file name
;key=

[freesentral_context]
enable=yes
certificate=freesentral.crt
key=freesentral.key
"

    if [ -e "$fe" ]; then
	if [ -z `readopt "Overwrite existing openssl.conf ?" "yes"` ]; then
	    echo "Please edit file $fe like follows:"
	    echo "$e"
	    fe=""
	fi
    fi
    if [ -n "$fe" ]; then
	echo "Creating openssl configuration file"
	mkdir -p "$DESTDIR$configs"
	echo "; File created by $version
$e" > "$fe"
    fi

	# rmanager.conf
    fe="$DESTDIR$configs/rmanager.conf";
    e="
[general]
; Each section creates a connection listener in the Remote Manager.
; An empty (all defaults) general section is assumed only in server mode if the
;  configuration file is missing.

; port: int: TCP Port to listen on, 0 to disable the listener
;port=5038

; addr: ipaddress: IP address to bind to
;addr=127.0.0.1

; header: string: Header string to display on connect
;header=YATE ${version}-${release} (http://YATE.null.ro) ready.

; password: string: Password required to authenticate as admin, default empty!
;password=

; userpass: string: Password to authenticate as observer user, default empty!
;userpass=

; timeout: int: Timeout until authentication succeeds in msec
;  Defaults to waiting 30s until closing an unauthenticated connection
;  Set to zero to disable else enforced minimum value is 5000 ms (5s)
;timeout=30000

; telnet: bool: Initiate TELNET negotiation on connect
;telnet=yes

; output: bool: Enable output as soon as connecting
;  This setting is ignored if an userpass is set
;output=no

; interactive: bool: Disable the TCP coalescing to improve interactivity
;  This is almost never required and needs Yate to run as superuser
;interactive=no

; context: string: SSL context to use to secure the connection
;  Setting a context enables SSL on the listener and overrides any domain
;context=

; domain: string: Domain used to identify the SSL context to use
;  Setting a domain enables SSL on the listener
;domain=

; verify: keyword: SSL handshake client certificate verification type
;  For acceptable values see the documentation of the openssl module
;  By default no client certificate is required
;    * none - Don't ask for a certificate, don't stop if verification fails (default)
;    * peer - Certificate is verified only if provided (a server always provides one)
;    * only - Server only - verify client certificate only if provided and only once
;    * must - Server only - client must provide a certificate at every (re)negotiation
;    * once - Server only - client must provide a certificate only at first negotiation 
;verify=

[freesentral_socket]
port=5039
addr=${ip_yate}
header=Freesentral connection 
context=freesentral_context
verify=none
"

    if [ -e "$fe" ]; then
	if [ -z `readopt "Overwrite existing rmanager.conf ?" "yes"` ]; then
	    echo "Please edit file $fe like follows:"
	    echo "$e"
	    fe=""
	fi
    fi
    if [ -n "$fe" ]; then
	echo "Creating rmanager configuration file"
	mkdir -p "$DESTDIR$configs"
	echo "; File created by $version
$e" > "$fe"
    fi
    test "x${generate_certificate}" = "xyes" && generate_certificate_now
fi

if [ -n "$scripts" -a -d scripts ]; then
    echo "Installing Yate scripts"
    mkdir -p "$DESTDIR$scripts"
    # this is a convenient way to filter what we copy
    (cd scripts; tar cf - $tarexclude *) | tar xf - -C "$DESTDIR$scripts/"
    confdata > "$DESTDIR$scripts/config.php"
fi

if [ -n "$prompts" -a -d prompts ]; then
    echo "Installing IVR prompts"
    mkdir -p "$DESTDIR$prompts"
    (cd prompts; tar cf - $tarexclude *) | tar xf - -C "$DESTDIR$prompts/"
    test X`id -u` = "X0" && chown -R $webuser "$DESTDIR$prompts"
fi

if [ -n "$webpage" -a -d web ]; then
    echo "Installing Web application"
    mkdir -p "$DESTDIR$webpage"
    (cd web; tar cf - $tarexclude *) | tar xf - -C "$DESTDIR$webpage/"
    if [ -n "$dbhost" ]; then
	echo "Creating configuration file"
	confdata web > "$DESTDIR$webpage/config.php"
    fi
fi

if [ -n "$psqlcmd" -a -n "$dbhost" ]; then
    echo "Initializing the database"
    if [ -n "$dbpass" ]; then
	tty -s && echo "At the password prompt please enter: $dbpass"
	export PGPASSWORD="$dbpass"
    fi
	echo "If you are updating then ignore: 'ERROR:  database \"${dbname}\" already exists'. If you are installing please use another name for the database."
    "$psqlcmd" -h "$dbhost" -U "$dbuser" -d template1 -c "CREATE DATABASE $dbname"
    unset PGPASSWORD
fi

if [ -n "$webpage" ]; then
	pushd "$DESTDIR$webpage" > /dev/null
	if [ ! -d upload ]; then
		mkdir upload
	fi
	test X`id -u` = "X0" && chown $webuser upload/
	if [ -z "$DESTDIR" ]; then
	    echo "Trying to update database"
	    chmod +x force_update.php
	    ./force_update.php
	fi
	popd > /dev/null
fi
