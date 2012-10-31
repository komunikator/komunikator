
DESTDIR :=
vardir = /var/www/kommadmin

all:

install:
	mkdir -p ${DESTDIR}${vardir}
	cd src; \
	cp -R ./* ${DESTDIR}${vardir};
	mkdir -p ${DESTDIR}/etc/kommadmin
	cp etc/apache.conf ${DESTDIR}/etc/kommadmin
