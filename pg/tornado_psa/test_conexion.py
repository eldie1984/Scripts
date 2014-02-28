import paramiko
from datetime import datetime
import logging
from connexion import *




FORMAT = '%(asctime)-15s %(levelname)s %(message)s'
logging.basicConfig(format=FORMAT,level=logging.INFO)


conn=conectarse('xxxxxx','xxxxxxxx','xxxxxx')
conn.make_sudo('xxxxxx','Sam0la\'P','xxxxxx >')
print conn.get_now()

