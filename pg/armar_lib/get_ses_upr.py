import os
import paramiko
import csv
import string

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


for key, val in csv.reader(open("sesiones.csv")):
    sesiones.append(key)

w = csv.writer(open("ses_upr.csv", "w"))

for ses in sesiones:
	print ' uxshw ses ses=%s | grep upr | awk \'{print $4}\'\n' % ses
	chan.send(' uxshw ses ses=%s | grep upr | awk \'{print $4}\'\n' % ses)
	buff = ''
	while not 'mwpsyz01 >' in buff:
    		resp = chan.recv(9999)
    		buff += resp
	
	for sal in buff.split("\n"):
	        if sal.find(">") == -1 and sal.find("uxshw") == -1 :
	       	        upr.append([ses,sal.translate(string.maketrans("\n\t\r", "   "))])
#			w.writerow([ses,sal.translate(string.maketrans("\n\t\r", "   "))])
for u in upr:
	print u
	print ' uxlst upr upr=%s | sed 1d | sed 1d | sed 1d| sed 1d | awk \'{print $3,$4,$5,$6,$7,$8,$9,$10}\'\n' % u[1]
	chan.send(' uxlst upr upr=%s | sed 1d | sed 1d | sed 1d| sed 1d | awk \'{print $3,$4,$5,$6,$7,$8,$9,$10}\'\n' % u[1])
        buff = ''
        while not 'mwpsyz01 >' in buff:
                resp = chan.recv(9999)
                buff += resp

        for sal in buff.split("\n"):
                if sal.find(">") == -1 and sal.find("uxlst") == -1 :
                       w.writerow([u[0],u[1],sal.translate(string.maketrans("\n\t\r", "   "))])

#for u in upr:
#	print u
ssh.close()
