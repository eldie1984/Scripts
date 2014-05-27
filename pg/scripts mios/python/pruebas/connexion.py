import paramiko
from datetime import datetime
import logging



class conectarse(object):

    """Clase para realizar las conexiones a los equipos con  la funcionalidad de sudo"""
    sudo='N'
    def __init__(self,host,User,Pass):
        self.ssh = paramiko.SSHClient()
        self.ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        self.ssh.connect(host, username=User, password=Pass)
        logging.debug("Ejecute el ssh connect")
        self.chan = self.ssh.invoke_shell()
        self.chan.send('echo PS1=$PS1 \n')
        while True:
            resp = self.chan.recv(9999)
            if resp.find('PS1') != -1 and resp.find('echo') == -1:
                self.PS1=resp.split('=')[1].strip()
                logging.debug("Seteo la variable PS1 a %s",self.PS1)
                break

    def make_sudo(self,remote_user,remote_pass):
        logging.debug("Estoy haciendome SU")
        self.chan.send('su -  %s \n' % remote_user)
        buff = ''

        #Busco la sentencia Password en el buffer
        logging.debug("Busco la sentencia de password")
        while not 'Password:' in buff:
            resp = self.chan.recv(9999)
            buff += resp

        self.sudo='S'
        #Mando la pass
        logging.debug("Mando la password")
        self.chan.send('%s\n' %remote_pass)
        self.chan.send('echo PS1=$PS1 \n')
        while True:
            resp = self.chan.recv(9999)
            if resp.find('PS1') != -1 and resp.find('echo') == -1:
                self.PS1=resp.split('=')[1].strip()
                logging.debug("Seteo la variable PS1 a %s",self.PS1)
                break


    def close(self):
        self.ssh.close()



class operaciones_stats(object):
    """Operaciones para traer datos desde los equipos"""
    def __init__(self,ssh_conn):
        self.chan = ssh_conn
    def get_df(self,patron,sudo,tipo='full'):
        if tipo=='full':
            comando='cat $UNXLOG/*.csv'
            logging.debug('Seteo de comando full %s' % comando)
        else:
            hoy=datetime.now().strftime('%d%m%y')
            comando='cat $UNXLOG/%s*.csv' % hoy
            logging.debug('Seteo de comando hoy %s' %  comando)
            
        self.chan.send('%s \n' % comando)
        logging.debug("self.chan.send('%s \n' % comando)")
        buff = ''
        while not patron in buff:
            resp = self.chan.recv(9999)
            buff += resp
        buff = ''
        self.chan.send('%s \n' % comando)
        while not patron in buff:
            resp = self.chan.recv(9999)
            logging.debug(resp)
            buff += resp

        return buff



if __name__ == "__main__":
    import os
    FORMAT = '%(asctime)-15s %(levelname)s %(message)s'
    logging.basicConfig(format=FORMAT,level=logging.DEBUG)
    conn=conectarse('yvasa850',os.environ['User'],os.environ['Pass'])
    conn.make_sudo('mwpsyz01','Sam0la\'P')
    oper=operaciones_stats(conn.chan)
    oper.get_df(conn.PS1,conn.sudo)