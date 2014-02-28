#!/usr/bin/python2.7

# Scrip en python

# Importo librerias

import fileinput
import re
import StringIO


for line in fileinput.input():
    m = re.search('^magnet:.*announce', line )
    if m != None :
	    print m.group(0)
#	    n = re.split(': ', m.group(0))
#	    spam_address = n[1]
	   # print spam_address

#print variable2	    

#buf = StringIO.StringIO(variable2)

#for line in buf:
	#print line
#	m = re.search('^Delivered\-To\:\ reclamoshotmail\@clubcupon.com.*', line )
#	if m != None :
#	    n = re.split(': ', m.group(0))
#	    n[1] = spam_address
#	    print (': ').join(n)
#	else:
#	   print line


