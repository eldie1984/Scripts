import paramiko
from datetime import datetime
import logging
from connexion import *




FORMAT = '%(asctime)-15s %(levelname)s %(message)s'
logging.basicConfig(format=FORMAT,level=logging.INFO)


conn=conectarse('yvasa850','e449806','Ema84nue')
conn.make_sudo('mwpsyz01','Sam0la\'P','mwpsyz01 >')
print conn.get_now()

