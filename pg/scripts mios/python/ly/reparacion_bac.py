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

import logging

# Funcion que iporta las conexiones a la base

from commons import *

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
	
# Declaro la instancia del mail y el encabezado
# COMIENZO CON LAS VERIFICACIONES:
try:
    db=MySQLdb.connect(host=db_host, user=db_user, passwd=db_pass , db=db_database )
    cursor = db.cursor()
    logging.debug("Se realizo la conexion a la base")
except Exception:
    logging.error("Nos se pudo realizar la conexion a la base")
    exit(2)

try:
    db_pr=MySQLdb.connect(host=db_host_pr, user=db_user_pr, passwd=db_pass_pr , db=db_database )
    cursor_pr = db_pr.cursor()
    logging.debug("Se realizo la conexion a la base")
except Exception:
    logging.error("Nos se pudo realizar la conexion a la base")
    exit(2)

try:
	db_ly=psycopg2.connect(host=db_host_ly_pr, user=db_user_ly_pr, password=db_pass_ly_pr , database=db_database_ly )
	cursor_ly = db_ly.cursor()
	logging.debug("Se realizo la conexion a la base de LY")
except Exception:
	logging.error("Nos se pudo realizar la conexion a la base de LY")
	exit(2)

#cursor.execute("""select c_invoice_id,id_lote from erp_factura_ly where id_portal=16 
#	and GrandTotal=4 
#	and generacion='2013-10-25 14:55:59'
#
#        """)

cursor.execute("""select efl.C_Invoice_ID,efl.C_Order_ID,efl.id_lote,null,null from erp_pedido_ly efl
		                left join cce_cargo cc on cc.id_lote=efl.id_lote
				                where efl.id_portal=15
						                and efl.id_lote in (
		1121597,
		1121600,
		1121601,
		1122348,
		1122349,
		1122352,
		1122354,
		1122361,
		1122371,
		1133555,
		1133556,
		1133557,
		1133558,
		1133559,
		1133560,
		1133561,
		1133564,
		1133565,
		1133566,
		1135502,
		1135505,
		1135506,
		1135507,
		1135509,
		1135523,
		1187638,
		1187645,
		1209250,
		1223260,
		1223262,
		1227071,
		1227072,
		1227073,
		1227075,
		1230628,
		1230630,
		1230631,
		1230633,
		1230636,
		1230637,
		1230645,
		1230648,
		1233259,
		1235268,
		1235269,
		1236177,
		1241914,
1241960,
1242610,
1242625,
1242626,
1242640,
1251831,
1254750,
1254751,
1254752,
1256521,
1256528,
1256563,
1256567,
1256939,
1258595,
1258601,
1258617,
1267716,
1268278,
1275468,
1275478,
1279635,
1283974,
1283979,
1283982,
1283988,
1283992,
1289698,
1290056,
1290057,
1291314,
1292909,
1298818,
1307440,
1307743,
1307744,
1307745,
1307746,
1307747,
1307750,
1307753,
1307754,
1314592,
1327933,
1327934,
1327938,
1329480,
1329483,
1329485,
1336661,
1336662,
1336663,
1336664,
1336665,
1336666,
1336667
								)""")

# Procesamos los datos obtenidos para incluirlos luego en un mail.

result=cursor.fetchall()

for miembro in result:
	logging.info("c_invoice_id: %s, id_lote: %s " % (miembro[0], miembro[2]))
	logging.info("Ejecuto en bac la siguiente sentencia: "+	"""
insert into erp_anulacion_ly 
(id_portal,C_AllocationHdr_ID,AllocationAction,AD_Client_ID,AD_Org_ID,fecha_envio,pago_enviado,is_error_pago,error_msg_pago,generacion,actualizacion,baja,id_pago)
values
(15,null,null,1010016,1010099,null,'Y','N','','2014-01-02 14:23:59',null,null,null)
""" )
	cursor_pr.execute("""insert into erp_anulacion_ly 
	(id_portal,C_AllocationHdr_ID,AllocationAction,AD_Client_ID,AD_Org_ID,fecha_envio,pago_enviado,is_error_pago,error_msg_pago,generacion,actualizacion,baja)
	values
	(15,null,null,1010016,1010099,null,'Y','N','','2014-01-02 14:23:59',null,null)"""  )
	db_pr.commit()
 	logging.info("Ejecuto en bac la siguiente sentencia: "+"""
insert into erp_anulacion_detalle_ly 
(id_anulacion_ly,C_Invoice_ID,C_Order_ID,CreditNote_DocumentNo,factura_enviado,is_error_factura,error_msg_factura,pedido_enviado,is_error_pedido,error_msg_pedido,generacion,actualizacion,baja)
values
(%s,%s,%s,null,'N','N','','N','N',null,'2014-01-02 14:23:59',null,null) 
""" % (cursor_pr.lastrowid,miembro[0],miembro[1]))
	cursor_pr.execute("""insert into erp_anulacion_detalle_ly 
	(id_anulacion_ly,C_Invoice_ID,C_Order_ID,CreditNote_DocumentNo,factura_enviado,is_error_factura,error_msg_factura,pedido_enviado,is_error_pedido,error_msg_pedido,generacion,actualizacion,baja)
	values
	(%s,%s,%s,null,'N','N','','N','N',null,'2014-01-02 14:23:59',null,null) 
	""" % (cursor_pr.lastrowid,miembro[0],miembro[1]))
	db_pr.commit()

#	logging.info ("Ejecuto en LY la siguiente sentencia: "	+ """ 
#update libertya.c_invoice
#set externaldocument_id =externaldocument_id||'_'||c_invoice_id
#where c_invoice_id=%s
#""" % miembro[0])
#	cursor_ly.execute("""update libertya.c_invoice
#			set externaldocument_id =externaldocument_id||'_'||c_invoice_id
#			where c_invoice_id=%s
#	""" % miembro[0])
#	db_ly.commit()
#
#	logging.info ("Ejecuto en LY la siguiente sentencia: "  + """ 
#		update libertya.c_allocationhdr
#		set externaldocument_id =externaldocument_id||'_'||c_allocationhdr_id
#		where c_allocationhdr_id=%s
#		""" % miembro[0])
#        cursor_ly.execute("""update libertya.c_allocationhdr
#                set externaldocument_id =externaldocument_id||'_'||c_allocationhdr_id
#                where c_allocationhdr_id=%s
#	        """ % miembro[0])
#        db_ly.commit()



	
#cursor_pr.execute("""insert into erp_anulacion_ly (id_portal,C_AllocationHdr_ID,AllocationAction,AD_Client_ID,AD_Org_ID,fecha_envio,pago_enviado,is_error_pago,error_msg_pago,generacion,actualizacion,baja)
#		values
#		(16,null,null,1010056,1010092,null,'Y','N','','2013-10-25 14:55:59',null,null)""")
# Cierro conexion a DB

cursor.close()
db.close()
cursor_pr.close()
db_pr.close()

logging.info("------- Fin del Script --------------")
