#!/usr/bin/python

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


# Note: if anyone can get timestamps to work, please let us know how!
# You can also only set fields that are present in the 'create issue' form, ie. visible in the web interface

baseurl = soap.getServerInfo(auth)['baseUrl']
titulo=raw_input("Titulo:")
Descripcion=raw_input("Descripcion: ")
Proyecto=raw_input("Proyecto: ")

newissue = soap.createIssue(auth, {
		'project': 'SOPOP',
		'type': '3',
		'assignee':jirauser,
		'reporter':jirauser,
		'priority':'3',
#		'components': [{'id': '10242','name':'Component 2'}, {'id': '10370', 'name':'Roy '}],
#
		'customFieldValues': [{
		  'values': [Proyecto],
		  'customfieldId': 'customfield_10000',
		  'key': None
		}],
		'summary': Descripcion,
		'description':titulo})

print "Created %s/browse/%s" % (baseurl, newissue['key'])

#print "Adding comment.."
#soap.addComment(auth, newissue['key'], {'body': 'Comment added with SOAP'})

#print 'Updating issue..'
#soap.updateIssue(auth, newissue['key'], [
#		{"id": "summary", "values": ['[Updated] Issue created with Python'] },
#
		# Change issue type to 'New feature'
#		{"id":"issuetype", "values":'2'},

		# Setting a custom field. The id (10010) is discoverable from
		# the database or URLs in the admin section

#		{"id": "customfield_10010", "values": ["Random text set in updateIssue method"] },

#		{"id":"fixVersions", "values":['10331']},
		# Demonstrate setting a cascading selectlist:
#		{"id": "customfield_10061", "values": ["10098"]},
#		{"id": "customfield_10061_1", "values": ["10105"]},
#		{"id": "duedate", "values": datetime.date.today().strftime("%d/%b/%y")}

#		])
#soap.updateIssue(auth, newissue['key'], [
#		{"id": "customfield_10500", "values": "Hooray, a read-only value"}
#		])

print 'Resolving issue..'


print "Done!"

# vim set textwidth=1000:
