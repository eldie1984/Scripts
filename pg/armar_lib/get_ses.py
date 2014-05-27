import os
import paramiko
import csv
import string
import logging
import cx_Oracle

ses_dict={}
sesiones=[]
upr=[]

try:
       con = cx_Oracle.connect('E438827/E438827@(DESCRIPTION=(ADDRESS=(PROTOCOL=tcp)(HOST=yvasa880)(PORT=1521))(CONNECT_DATA=(SID=DWH)))')
       cur = con.cursor()

except :
       logging.error("se produjo un error al conectarse a la base")
       exit(1)


ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('yvasa850.inetpsa.com',username='E449806',password='Ema84nue')

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
chan.send('uxlst ses ses=SYZ\* | grep -v "Commande" | grep -v "SESSION" | grep -v "\-\-\-" | sed \'/^ *$/d\'|awk \'{print $1"|"$3" "$4" "$5" "$6" "$7" "$8" "$9}\'\n')
buff = ''
while not 'mwpsyz01 >' in buff:
    resp = chan.recv(9999)
    buff += resp


for sal in buff.split("\n"):
	if sal.find(">") == -1 and sal.find("uxlst") == -1 :
		sal_sp=sal.split('|')
	        ses_dict[sal_sp[0]]=sal_sp[1].translate(string.maketrans("\n\t\r", "   "))
		sesiones.append(sal_sp[0])

w = csv.writer(open("sesiones.csv", "w"))
for key, val in ses_dict.items():
    w.writerow([key,val.translate(string.maketrans("\n\t\r", "   "))])
    print key
    print val
#    print """insert into SESIONES (SES_NAME, SES_DESCIPCION)
#                values
#                ('%s','%s')""" % (key,val)
    if val.find("'") == -1:
        cur.execute("""insert into SESIONES (SES_NAME, SES_DESCIPCION)
                values
		('%s','%s')""" % (key,val))
        cur.execute(""" commit""")
    else:
	 print """insert into SESIONES (SES_NAME, SES_DESCIPCION)
                values
                ('%s','%s')""" % (key,val)

ssh.close()
cur.close()
con.close()
