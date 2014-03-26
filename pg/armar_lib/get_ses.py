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

## Execute whatever command and wait for a prompt again.
chan.send('uxlst ses ses=\* | grep -v "Commande" | grep -v "SESSION" | grep -v "\-\-\-" | sed \'/^ *$/d\'|awk \'{print $1"|"$3" "$4" "$5" "$6" "$7" "$8" "$9}\'\n')
buff = ''
while not 'mwpsyz01 >' in buff:
    resp = chan.recv(9999)
    buff += resp


for sal in buff.split("\n"):
	if sal.find(">") == -1 and sal.find("uxlst") == -1 :
		sal_sp=sal.split('|')
	        ses_dict[sal_sp[0]]=sal_sp[1]
		sesiones.append(sal_sp[0])

w = csv.writer(open("sesiones.csv", "w"))
for key, val in ses_dict.items():
    w.writerow([key,val.translate(string.maketrans("\n\t\r", "   "))])

ssh.close()
