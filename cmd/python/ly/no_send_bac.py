#!/usr/bin/env python
#-*- coding:utf-8 -*-
#
#
# Autor: Diego Gasch
#
# Empresa: CMD s.a
#
# Fecha de generación:  2013-07-03
#
# Fecha de ultima modificación: 2013-07-03
#
# Ultima modificación: ([quien] + [que]) dgasch creacion del script
#
#
# Objetivo: Enviar un reporte con los errores de envio desde BAC a Libertya incluyendo la cantidad de los mismos
#
# Forma de uso: En el crontab y en la documentacion de la wiki tenemos la linea con la que podemos ejecutar el script, el mismo no debe usarse con parametros.
#
# Proximas mejoras a incorporar: Securizar el script testeando que el mismo rechace cualquier parametro que se le incluya.
#
# Version : 1.1

######################################
# Codigo:                                                                                     #
#####################################

# IMPORTO LIBRERIAS, DEFINO VARIABLES, CREO CLASES Y FUNCIONES:

# Importo las librerias necesarias para la ejecucion del script
import MySQLdb
import psycopg2
from socket import gethostname
from sys import argv,exit
import getopt
import time
import logging

# Funcion que iporta las conexiones a la base

from commons import *

receivers=['ndemarchi@cmd.com.ar','dgasch@cmd.com.ar','mcortez@cmd.com.ar','sostapowicz@cmd.com.ar','mmolina@cmd.com.ar']
test=0

def main(argv):
	level_opt=logging.INFO
	global receivers
	global test
	try:
		opts, args = getopt.getopt(argv,"hdt:",["debug","testing"])
	except getopt.GetoptError:
		print """-d --debug   ---> Pone en debug el log 
		-t --testing  ---> Pone a soporte como receptor del mail
		"""
		exit(2)

	for opt, arg in opts:
		if opt == '-h':
			print """-d --debug   ---> Pone en debug el log 
			-t --testing  ---> Pone a soporte como receptor del mail
			"""
			exit()
		elif opt in ("-d", "--debug"):
			level_opt=logging.DEBUG
		elif opt in ("-t", "--testing"):
			print "Testing"
			test=1
			receivers = []
			receivers.append(arg)
			logging.basicConfig(format=FORMAT,level=level_opt)

# Genero dos funciones fuera de la clase 

##################################################
###############################

if __name__ == "__main__":
	main(argv[1:])

################################
# Parametros
################################
hostname=gethostname()
pathDir=argv[0]
is_mail=0

##################################################
logging.debug("Seteo variables")


logging.info("---------Inicio Script ----------------")
# COMIENZO CON LAS VERIFICACIONES:
#try:
#	db=MySQLdb.connect(host=db_host, user=db_user, passwd=db_pass , db=db_database )
#	cursor = db.cursor()
#	logging.debug("Se realizo la conexion a la base")
#except Exception:
#	logging.error("Nos se pudo realizar la conexion a la base")
#	exit(2)
#
try:
	db_cc=MySQLdb.connect(host=db_host_cc, user=db_user_cc, passwd=db_pass_cc , db=db_database_cc )
	cursor_cc = db_cc.cursor()
	logging.debug("Se realizo la conexion a la base de CC")
except Exception:
	logging.error("Nos se pudo realizar la conexion a la base de CC")
	exit(2)

try:
	db_pr=MySQLdb.connect(host=db_host_pr, user=db_user_pr, passwd=db_pass_pr , db=db_database )
	cursor_pr = db_pr.cursor()
	logging.debug("Se realizo la conexion a la base")
except Exception:
	logging.error("Nos se pudo realizar la conexion a la base")
	exit(2)

while 1:
#	cursor_cc.execute("""select de.id from deals d,
#	                deal_externals de
#	                where d.is_end_user=1
#	                and end_date>'2013-11-01' 
#	                and de.deal_id=d.id
#	                """)
#	lista=[]
#	str_lista=''
#
#	for registro in cursor_cc.fetchall():
#	        lista.append(str(registro[0]))
#		str_lista = (',').join(lista)


	cursor_pr.execute("""update erp_factura_ly
	set fecha_envio ='2020-01-01'
	where id_portal=18
	and fecha_envio is null
	and generacion > '2013-11-30'
	and dateacct is null
	#and id_lote not in (1460529,1460539,1432926)
	#and punto_venta <> 101""")
	print cursor_pr.rowcount
	db_pr.commit()
	time.sleep(5)

#cursor.close()
#db.close()
#cursor_ly.close()
#db_ly.close()
cursor_pr.close()
db_pr.close()
#cursor_ly_pr.close()
#db_ly_pr.close()

