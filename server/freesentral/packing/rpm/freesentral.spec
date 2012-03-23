Summary:	FreeSentral
Name:     	freesentral
Version: 	1.2
Release:	1
License:	GPL
Packager:	Paul Chitescu <paulc@voip.null.ro>
Group:		Applications/Communications
URL:		http://www.freesentral.com/
Source:		http://yate.null.ro/tarballs/freesentral/%{name}-%{version}.tar.gz
BuildArch:	noarch
BuildRoot:	%{_tmppath}/%{name}-%{version}-%{release}-root
Requires:	yate >= 3.0.0
Requires:	yate-scripts
Requires:	yate-ssl
Requires:	postgresql
Requires:	postgresql-server
Requires:	apache
Requires:	apache-mod_php
Requires:	apache-mod_ssl
Requires:	php-pgsql
Requires:	php-cli
Requires:	php-sockets
Requires:	php-openssl
Requires:	php-ming
Requires:	sudo
Requires:	madplay
Requires:	/sbin/chkconfig
Requires:	/sbin/ifconfig

%define prefix  /usr
%define __find_requires /bin/true
%define __perl_requires /bin/true


%description
FreeSentral is a full IP PBX with a a Web Graphical User Interface for easy
configuration. It is based on the Yate telephony server.

%files
%defattr(-,root,root)
/var/www/html/*
%attr(750,apache,root) /var/www/html/upload
%attr(750,apache,root) %dir /var/spool/voicemail
%attr(750,apache,root) /var/spool/voicemail/*
/usr/share/yate/scripts/*
%dir /usr/libexec/%{name}
%attr(755,root,root) /usr/libexec/%{name}/install.sh
%attr(500,root,root) /usr/libexec/%{name}/ctl-root
%attr(550,root,apache) /usr/libexec/%{name}/ctl-apache
/etc/rc.d/init.d/freesentral*
%dir %{_sysconfdir}/%{name}
%config(noreplace) %{_sysconfdir}/%{name}/*

%post
/sbin/chkconfig httpd on
/sbin/chkconfig postgresql on
/sbin/chkconfig freesentral on
/sbin/chkconfig freesentral-init on
/sbin/chkconfig --del yate


%prep
%setup -q -n %{name}

%build

%install
DESTDIR=%{buildroot} ./install.sh --quiet --config %{_sysconfdir}/%{name} \
 --scripts /usr/share/yate/scripts --prompts /var/spool/voicemail --webpage /var/www/html \
 --psql no --generate_certificate no
mkdir -p %{buildroot}/etc/rc.d/init.d
cp -p packing/rpm/%{name} %{buildroot}/etc/rc.d/init.d/
cp -p packing/rpm/%{name}-init %{buildroot}/etc/rc.d/init.d/
mkdir -p %{buildroot}/usr/libexec/%{name}
cp -p install.sh libexec/* %{buildroot}/usr/libexec/%{name}/
mkdir -p %{buildroot}/var/www/html/upload

%clean
rm -rf %{buildroot}

%changelog
* Wed Jan 03 2010 Paul Chitescu <paulc@voip.null.ro>
- Created specfile
