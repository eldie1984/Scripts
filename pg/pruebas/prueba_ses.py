import paramiko

ses_dict={}
sesiones=[]
upr=[]

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('yvasa850', username='e449806', password='Ema84nue')

chan = ssh.invoke_shell()

# Ssh and wait for the password prompt.
chan.send('su -  mwpsyz01 \n')
buff = ''
#i=0
while not 'Password:' in buff:
    resp = chan.recv(9999)
    buff += resp
#    i = i + 1
#    print "%s: %s" %(i,resp)
#    if i==20 or resp.endswith('Password:'):
#	ssh.close()
#	exit()

# Send the password and wait for a prompt.
chan.send('Sam0la\'P\n')
buff = ''
i=0
while not 'mwpsyz01 >' in buff:
    resp = chan.recv(9999)
    buff += resp
#    i = i + 1
#    print "%s: %s" %(i,resp)
#    if i==20 or resp.endswith('mwpsyz01 >'):
#        ssh.close()
#        exit()
#
## Execute whatever command and wait for a prompt again.
chan.send('uxlst ses ses=\* | grep -v "Commande" | grep -v "SESSION" | grep -v "\-\-\-" | sed \'/^ *$/d\'|awk \'{print $1"|"$3" "$4" "$5" "$6" "$7" "$8" "$9}\'\n')
buff = ''
while not 'mwpsyz01 >' in buff:
    resp = chan.recv(9999)
    buff += resp

# Now buff has the data I need.
#print 'buff', buff

for sal in buff.split("\n"):
	if sal.find(">") == -1 and sal.find("uxlst") == -1 :
		sal_sp=sal.split('|')
	        ses_dict[sal_sp[0]]=sal_sp[1]
		sesiones.append(sal_sp[0])
#		print sal_sp
#		print "->%s" %sal

#print ses_dict
#ssh.close()


#for key in ses_dict:
#	print "Sesion:%s  -> Nombre:%s " %(key,ses_dict[key])

for ses in sesiones:
	print ' uxshw ses ses=%s | grep upr | awk \'{print $4}\'\n' % ses
	chan.send(' uxshw ses ses=%s | grep upr | awk \'{print $4}\'\n' % ses)
	buff = ''
	while not 'mwpsyz01 >' in buff:
    		resp = chan.recv(9999)
    		buff += resp
	print buff
	for sal in buff.split("\n"):
	        if sal.find(">") == -1 and sal.find("uxlst") == -1 :
        	        upr.append([ses,sal])
for u in upr:
	print u
ssh.close()
