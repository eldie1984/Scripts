#!/usr/bin/python
# coding=utf8
# -*- coding: utf8 -*-
# vim: set fileencoding=utf8 :


# Sample Python client accessing JIRA via SOAP. By default, accesses
# http://jira.atlassian.com with a public account. Methods requiring
# more than basic user-level access are commented out. Change the URL
# and project/issue details for local testing.
# 
# Note: This Python client only works with JIRA 3.3.1 and above (see
# http://jira.atlassian.com/browse/JRA-7321)
#
# Refer to the SOAP Javadoc to see what calls are available:
# http://www.atlassian.com/software/jira/docs/api/rpc-jira-plugin/latest/com/atlassian/jira/rpc/soap/JiraSoapService.html
#
# For a much more comprehensive example, see
# http://svn.atlassian.com/svn/public/contrib/jira/jira-cli/src/cli/jira

import SOAPpy, getpass, datetime

soap = SOAPpy.WSDL.Proxy('http://jira.int.clarin.com/rpc/soap/jirasoapservice-v2?wsdl')
#soap = SOAPpy.WSDL.Proxy('http://localhost:8090/jira/rpc/soap/jirasoapservice-v2?wsdl')

#jirauser = raw_input("Username for jira [fred]: ")
#if jirauser == "":
#    jirauser = "fred"
#
#passwd = getpass.getpass("Password for %s: " % jirauser)
#passwd="fredspassword"

jirauser='dgasch'
passwd='Die1984nue!'
option='S'

# This prints available methods, but the WSDL doesn't include argument
# names so its fairly useless. Refer to the Javadoc URL above instead
#print 'Available methods: ', soap.methods.keys()

def listSOAPmethods():
	for key in soap.methods.keys():
	    print key, ': '
	    for param in soap.methods[key].inparams:
		print '\t', param.name.ljust(10), param.type
	    for param in soap.methods[key].outparams:
	        print '\tOut: ', param.name.ljust(10), param.type


auth = soap.login(jirauser, passwd)

issue = soap.getIssue(auth, 'SOPOP-25384')
print "Retrieved issue:", issue
print

# Note: if anyone can get timestamps to work, please let us know how!
# You can also only set fields that are present in the 'create issue' form, ie. visible in the web interface
f=open('mail.txt','r')
comment=f.read()

#print comment
#while option == 'S':
#	print "Adding comment.."
#	buffer = ''
#	count = 0
#	while True:
#		line=raw_input("Comentario :" )
#		if 'De:' in line: count +=1
#		if count == 2: 
#			buffer_next = line
#			break	
#		buffer += line
#	soap.addComment(auth, issue['key'], {'body': comment})
	#option=raw_input("Otro Comentario[S] :")
	#if option == "":
	#    option = "S"
soap.addComment(auth, issue['key'], {'body':comment})
print "Done!"

# vim set textwidth=1000:
