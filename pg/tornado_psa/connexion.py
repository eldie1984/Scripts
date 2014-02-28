import paramiko
from datetime import datetime
import logging



class conectarse(object):

        format = '%d/%m/%Y %H%M%S'
        tareas=[]

        def __init__(self,host,User,Pass):
                self.ssh = paramiko.SSHClient()
                self.ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
                self.ssh.connect(host, username=User, password=Pass)

                self.chan = self.ssh.invoke_shell()
        def make_sudo(self,remote_user,remote_pass,end_sentence):
                # Ssh and wait for the password prompt.
                logging.debug("Estoy haciendome SU")
                self.chan.send('su -  %s \n' % remote_user)
                buff = ''

                #Busco la sentencia Password en el buffer
                logging.debug("Busco la sentencia de password")
                while not 'Password:' in buff:
                    resp = self.chan.recv(9999)
                    buff += resp


                #Mando la pass
                logging.debug("Mando la password")
                self.chan.send('%s\n' %remote_pass)
                buff = ''
                while not end_sentence in buff:
                    resp = self.chan.recv(9999)
                    buff += resp
        def get_upr_task(self,upr):
                logging.debug('uxlst ctl upr=%s full | awk \'{print $5"|"$6"|"$7"|"$8}\' | sed 1d | sed 1d | sed 1d | sed 1d | tail -1' % upr)
                self.chan.send('uxlst ctl upr=%s full | awk \'{print $5"|"$6"|"$7"|"$8}\' | sed 1d | sed 1d | sed 1d | sed 1d | tail -1 \n' % upr)
                buff = ''
                while not 'mwpsyz01 >' in buff:
                    resp = self.chan.recv(9999)
                    buff += resp

                for sal in buff.split("\n"):
                        if sal.find(">") == -1 and sal.find("uxlst") == -1 :
                                sal_sp=sal.split('|')
                                fecha_st=sal_sp[0]+' '+''.join(e for e in sal_sp[1] if e.isalnum())
                                fecha_end=sal_sp[2]+' '+''.join(e for e in sal_sp[3] if e.isalnum())
                self.fecha_st = datetime.strptime(fecha_st,self.format).strftime('%d/%m/%Y %H:%M:%S')
                self.fecha_end = datetime.strptime(fecha_end,self.format).strftime('%d/%m/%Y %H:%M:%S')

	def get_now(self):
		salida=[]
		logging.debug('uxlst ctl status=w,o,e since=`date \'+%d/%m/%Y\'`,0000 full upr=* ses=* mu=* before=31/12/9999,2359 execinfo=\* execsev=* |sed 1d | sed 1d | sed 1d | sed 1 | awk \'{print $1"|"$2"|"$4"|"$5"|"$6}\'\n')
		self.chan.send('uxlst ctl status=w,o,e since=`date \'+%d/%m/%Y\'`,0000 full upr=* ses=* |sed 1d | sed 1d | sed 1d | sed 1d | awk \'{print $1"|"$2"|"$4"|"$5"|"$6}\'\n')
		buff = ''
		while not 'mwpsyz01 >' in buff:
		    resp = self.chan.recv(9999)
		    buff += resp

                for sal in buff.split("\n"):
                        if sal.find(">") == -1 and sal.find("uxlst") == -1 :
                                sal_sp=sal.split('|')

				logging.info(sal_sp)
                                fecha=sal_sp[3]+' '+''.join(e for e in sal_sp[4] if e.isalnum())
				salida.append([sal_sp[0],sal_sp[1],sal_sp[2],datetime.strptime(fecha,self.format).strftime('%d/%m/%Y %H:%M:%S')])
		return salida

        def close(self):
                self.ssh.close()


FORMAT = '%(asctime)-15s %(levelname)s %(message)s'
logging.basicConfig(format=FORMAT,level=logging.INFO)

