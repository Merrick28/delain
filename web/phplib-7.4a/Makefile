# 
# PHPLIB MAKEFILE
#
# $Id: Makefile,v 1.3 2002/01/05 16:15:12 nhruby Exp $
# 
# Release early, release often - `make dist` will create for you
# four different archives for the current revision with only one command
#
# Copyright (c) 1998-2000 Sascha Schumann <sascha@schumann.cx>
#
# some other goodies (written up, because I tend to forget them...)
# 
# cvs export -D now -d dist php-lib
# (will export (without CVS directories) the module php-lib at its
# current state into directory dist)
#
# cvs rtag -D now PHPLIB-X.XX php-lib
# (assign the current revisions a special tag)
#

REVISION=$(shell cat VERSION)

DISTDIR=phplib-${REVISION}

BSF=phplib-${REVISION}

all:
	@echo ""
	@echo "You have not read the documentation."
	@echo "Please read the documentation in the doc/ directory."
	@echo ""
	@echo "Thank you for using PHPLIB"
	@echo ""

dist:
	@if [ -d ${DISTDIR} ] ; then echo "DISTDIR exists already"; exit 1; fi
	@if [ -e ".filelist" ] ; then echo "stalled .filelist?"; exit 1; fi
	ls > .filelist
	mkdir ${DISTDIR}
	chmod 755 ${DISTDIR}
	cp -pRf `cat .filelist` ${DISTDIR}
	sed -e 's/= "kk";/= "example_user";/' -e 's/= "test";/= "example_database";/' < php/local.inc > ${DISTDIR}/php/local.inc
	echo "RELEASE ${REVISION}" > ${DISTDIR}/VERSION
	rm .filelist
	find ${DISTDIR} -name CVS -type d | xargs rm -rf 
	find ${DISTDIR} -name \*~ -type f | xargs rm -rf 
	#${MAKE} -C ${DISTDIR}/doc doc
	tar cvf ${BSF}.tar ${DISTDIR} > /dev/null
	gzip -c9 ${BSF}.tar > ${BSF}.tar.gz
	#bzip2 -c9 ${BSF}.tar > ${BSF}.tar.bz2
	#zip -r9 ${BSF}.zip ${DISTDIR} &> /dev/null
	#shar -z ${DISTDIR} > ${BSF}.shar 2>/dev/null
	rm ${BSF}.tar
	rm -rf ${DISTDIR}
