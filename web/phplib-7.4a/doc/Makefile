#
# $Id: Makefile,v 1.1.1.1 2000/04/17 16:39:57 kk Exp $

# The main documentation is made of different chapters

docs = sgml/documentation.sgml sgml/blob.sgml
html_docs = $(docs:%.sgml=%.html)
txt_docs = $(docs:%.sgml=%.txt)

%.html: %.sgml
	sgml2html -s 1 $<

%.txt: %.sgml
	sgml2txt -c latin $<

doc: $(docs) $(html_docs) $(txt_docs)

clean:
	rm -f $(MASTER) *.html *.txt 
