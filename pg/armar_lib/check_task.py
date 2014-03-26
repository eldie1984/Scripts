import os
import paramiko
from datetime import datetime

format = '%d/%m/%Y %H%M%S'
tareas=[]

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('yvasa850',os.environ['User'],os.environ['Pass'])

chan = ssh.invoke_shell()

# Ssh and wait for the password prompt.
chan.send('su -  mwpsyz01 \n')
buff = ''

#Busco la sentencia Password en el buffer

while not 'Password:' in buff:
    resp = chan.recv(9999)
    buff += resp


#Mando la pass

chan.send('Sam0la\'P\n')
buff = ''
i=0
while not 'mwpsyz01 >' in buff:
    resp = chan.recv(9999)
    buff += resp

chan.send('uxlst ctl status=w,o,e since=`date \'+%d/%m/%Y\'`,0000 full upr=* ses=* mu=* before=31/12/9999,2359 execinfo=\* execsev=* |grep -v SESSION | grep -v Commande| grep -v "\-\-\-" | sed \'/^ *$/d\' | awk \'{print $1"|"$2"|"$4"|"$5"|"$6}\'\n')
buff = ''
while not 'mwpsyz01 >' in buff:
    resp = chan.recv(9999)
    buff += resp

# Now buff has the data I need.
#print 'buff', buff

for sal in buff.split("\n"):
	if sal.find(">") == -1 and sal.find("uxlst") == -1 :
		sal_sp=sal.split('|')
		fecha=sal_sp[3]+' '+''.join(e for e in sal_sp[4] if e.isalnum())
		print fecha
		tareas.append([sal_sp[0],sal_sp[1],sal_sp[2],datetime.strptime(fecha,format).strftime('%d/%m/%Y %H:%M:%S')])

#print tareas
##print ses_dict
##ssh.close()
#
#
##for key in ses_dict:
##	print "Sesion:%s  -> Nombre:%s " %(key,ses_dict[key])
#
for task in tareas:
#	print ' uxshw ses ses=%s | grep upr | awk \'{print $4}\'\n' % ses
	chan.send('uxshw tsk ses=%s upr=* mu=\* | grep mon | awk \'{print $4}\' | sed \'s/(//g;s/)//g;s/,/|/g\'\n' % task[0])
	hora = ''
	while not 'mwpsyz01 >' in hora:
    		resp = chan.recv(9999)
    		hora += resp
        chan.send('uxshw tsk ses=%s upr=* mu=\* | grep mon | awk \'{print $4}\' | sed \'s/(//g;s/)//g;s/,/|/g\'\n' % task[0])
        prog = ''
        while not 'mwpsyz01 >' in prog:
                resp = chan.recv(9999)
                prog += resp

#	print buff
	for sal in hora.split("\n"):
	        if sal.find(">") == -1 and sal.find("uxshw") == -1 :
        	        if sal !='0000|000|00':
				sal_sp=sal.split('|')
				print "%s\t%s\t%s\t%s\t%s\t%s\t%s" % (task[0],task[1],task[2],task[3],sal_sp[0],sal_sp[1],sal_sp[2])
			else :        		
				for salp in prog.split("\n"):
			                if sal.find(">") == -1 and sal.find("uxshw") == -1 :
                                		sal_sp=sal.split('|')
						if len(sal_sp[0]) !=4:
							plani=sal
						else:
							tiempo_h =sal_sp[0]
							tiempo_m = sal_sp[1]
	                        print "%s\t%s\t%s\t%s\t%s\t%s\t%s" % (task[0],task[1],task[2],task[3],plani,timepo_h,tiempo_m)
#for u in upr:
#	print u
ssh.close()
