import os
import paramiko
import csv

ses_dict={}
sesiones=[]
upr=[]

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('yvasa850',os.environ['User'],os.environ['Pass'])

chan = ssh.invoke_shell()

# Ssh and wait for the password prompt.
chan.send('su -  mwpsyz01 \n')
buff = ''
#i=0
while not 'Password:' in buff:
    resp = chan.recv(9999)
    buff += resp

# Send the password and wait for a prompt.
chan.send('Sam0la\'P\n')
buff = ''
i=0
while not 'mwpsyz01 >' in buff:
    resp = chan.recv(9999)
    buff += resp


for key, val in csv.reader(open("ses_upr.csv")):
    sesiones.append(val)

w = csv.writer(open("upr.csv", "w"))

for ses in sesiones:
	print '  uxlst upr upr=%s | sed 1d | sed 1d | sed 1d| sed 1d | awk \'{print $3,$4,$5,$6,$7}\'\n' % ses
	chan.send('  uxlst upr upr=%s | sed 1d | sed 1d | sed 1d| sed 1d | awk \'{print $3,$4,$5,$6,$7}\'\n' % ses)
	buff = ''
	while not 'mwpsyz01 >' in buff:
    		resp = chan.recv(9999)
    		buff += resp
	
	for sal in buff.split("\n"):
	        if sal.find(">") == -1 and sal.find("uxshw") == -1 :
#        	        upr.append([ses,sal])
			w.writerow([ses,sal])
#for u in upr:
#	print u
ssh.close()
