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

# Declaro la instancia del mail y el encabezado
mail_bac=mail(','.join(receivers),'Alerta Pagos BAC (py)')
mail_bac.createHtmlHeader("id_lote,id_pago,id_cargo")
# COMIENZO CON LAS VERIFICACIONES:
try:
	db=MySQLdb.connect(host=db_host, user=db_user, passwd=db_pass , db=db_database )
	cursor = db.cursor()
	logging.debug("Se realizo la conexion a la base")
except Exception:
	logging.error("Nos se pudo realizar la conexion a la base")
	exit(2)

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

try:
        db_ly=psycopg2.connect(host=db_host_ly_pr, user=db_user_ly_pr, password=db_pass_ly_pr , database=db_database_ly )
	cursor_ly = db_ly.cursor()
	logging.debug("Se realizo la conexion a la base de LY")
except Exception:
	logging.error("Nos se pudo realizar la conexion a la base de LY")
        exit(2)

cursor_cc.execute("""select de.id from deals d,
		deal_externals de
		where d.is_end_user=1
		and end_date<'2013-11-01'
		and de.created > '2013-08-01'
		and de.deal_id=d.id
                """)


# Procesamos los datos obtenidos para incluirlos luego en un mail.

lista=[]
lista_bac=[]
str_lista=''
str_lista_bac=''

for registro in cursor_cc.fetchall():
	lista.append(str(registro[0]))
str_lista = (',').join(lista)
#print str_lista
#logging.debug("la lista de ID's es: \n %s" % str_lista)
#print "la lista de ID's es: \n %s" % str_lista


cursor.execute("""select efl.C_Invoice_ID,efl.id_lote,epl.C_AllocationHdr_ID,epl.id_pago 
		  from cce_cargo cc 
		  left join cce_pago cp on cp.id_pago=cc.id_pago
                  left join erp_factura_ly efl on cc.id_lote=efl.id_lote 
		  left join erp_pago_ly epl on epl.id_pago=cc.id_pago
		  where cp.id_portal=18 and cc.id_lote is not null
                  and cc.id_producto=18001
		  and efl.C_Invoice_ID is not null
		  and epl.C_AllocationHdr_ID is not null
                  and cp.id_pago_portal in (%s)
                  and cc.generacion < '2013-11-14'
		  and cc.id_lote not in (1179510)

		""" % str_lista)


#
result=cursor.fetchall()
#for i in result:
#	print i[0]
print result
for registro in result:
	lista_bac.append(str(registro[0]))
str_lista_bac = (',').join(lista_bac)


for miembro in result:
	mail_bac.addDataHtml(miembro[0],miembro[1],miembro[2])
	logging.debug("| %s | %s | %s | %s" % (miembro[0],miembro[1],miembro[2],miembro[3]))
	print "/* id_lote=%s |id_pago= %s */" % (miembro[1],miembro[3])
	print """update erp_factura_ly
	set issendrequired = 'Y',
	tipocomprobante='FC',
	c_doctypetarget_id=null,
	punto_venta=101,
	fecha_envio=null,
	dateacct='2013-11-30'
	where id_lote in (%s);""" % miembro[1]

	print """ update erp_cargo_ly
	set m_product_id=1017122
	where id_lote in (%s);""" % miembro[1]
	
	print """ update cce_cargo
	set id_producto=18000
	where id_lote in (%s) ;""" % miembro[1]

	print """ update erp_pago_ly
	set fecha_envio=null
        where id_pago in (%s) ;""" % miembro[3]


	cursor_pr.execute( """update erp_factura_ly
        set issendrequired = 'Y',
        tipocomprobante='FC',
        c_doctypetarget_id=null,
        punto_venta=101,
        fecha_envio=null
        where id_lote in (%s);""" % miembro[1])

	cursor_pr.execute( """ update erp_cargo_ly
        set m_product_id=1017122
	where id_lote in (%s);""" % miembro[1])

        
	cursor_pr.execute(""" update cce_cargo
        set id_producto=18000
        where id_lote in (%s) ;""" % miembro[1])

	cursor_pr.execute( """update erp_pago_ly
		        set fecha_envio=null
		        where id_pago in (%s) ;
		        """ % miembro[3])

	db_pr.commit()
#	is_mail=1
#if is_mail==1 or test==1:
#	text='REPORTE: ERRORES de los envios de LIBERTYA'
#	html="""\
#	           <html>
#			<head>
#			<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
#			</head>
#			<body>
#			<h1 style="color:OrangeRed">Este es un reporte solo INFORMATIVO, NO ESCALAR </h1>
#			"""+mail_bac.html+"""</tbody>
#			</table>
#			<h3> Reenviar el mail a los responsables del portal</h3>
#			                        
#			<p>Alama corriendo en el equipo """ + hostname + """ en """ + pathDir+ """ </p> 
#			</body>
#		</html>"""
#	mail_bac.send_mail(sender,receivers, text, html)



#        logging.info("c_invoice_id: %s, id_lote: %s " % (miembro[0], miembro[1]))
#        logging.info("Ejecuto en bac la siguiente sentencia: "+ """
#insert into erp_anulacion_ly 
#(id_portal,C_AllocationHdr_ID,AllocationAction,AD_Client_ID,AD_Org_ID,fecha_envio,pago_enviado,is_error_pago,error_msg_pago,generacion,actualizacion,baja,id_pago)
#values
#(18,%s,null,1010057,1010093,null,'N','N','','2013-11-19 14:23:59',null,null,%s)
#""" % (miembro[2], miembro[3]))
#        cursor_pr.execute("""insert into erp_anulacion_ly 
#       (id_portal,C_AllocationHdr_ID,AllocationAction,AD_Client_ID,AD_Org_ID,fecha_envio,pago_enviado,is_error_pago,error_msg_pago,generacion,actualizacion,baja)
#        values
#        (18,%s,null,1010057,1010093,null,'N','N','','2013-11-19 14:23:59',null,%s)""" % (miembro[2], miembro[3]))
#        db_pr.commit()
#        logging.info("Ejecuto en bac la siguiente sentencia: "+"""
#insert into erp_anulacion_detalle_ly 
#(id_anulacion_ly,C_Invoice_ID,C_Order_ID,CreditNote_DocumentNo,factura_enviado,is_error_factura,error_msg_factura,pedido_enviado,is_error_pedido,error_msg_pedido,generacion,actualizacion,baja)
#values
#(%s,%s,null,null,'N','N','','N','N',null,'2013-11-19 14:23:59',null,null) 
#""" % (cursor_pr.lastrowid,miembro[0]))
#        cursor_pr.execute("""insert into erp_anulacion_detalle_ly 
#        (id_anulacion_ly,C_Invoice_ID,C_Order_ID,CreditNote_DocumentNo,factura_enviado,is_error_factura,error_msg_factura,pedido_enviado,is_error_pedido,error_msg_pedido,generacion,actualizacion,baja)
#        values
#        (%s,%s,null,null,'N','N','','N','N',null,'2013-11-19 14:23:59',null,null) 
#        """ % (cursor_pr.lastrowid,miembro[0]))
#        db_pr.commit()
#
#        logging.info ("Ejecuto en LY la siguiente sentencia: "  + """ 
#update libertya.c_invoice
#set externaldocument_id =externaldocument_id||'_'||c_invoice_id
#where c_invoice_id=%s
#""" % miembro[0])
#        cursor_ly.execute("""update libertya.c_invoice
#                        set externaldocument_id =externaldocument_id||'_'||c_invoice_id
#                        where c_invoice_id=%s
#        """ % miembro[0])
#        db_ly.commit()
#
#        logging.info ("Ejecuto en LY la siguiente sentencia: "  + """ 
#                update libertya.c_allocationhdr
#                set externaldocument_id =externaldocument_id||'_'||c_allocationhdr_id
#                where c_allocationhdr_id=%s
#                """ % miembro[0])
#        cursor_ly.execute("""update libertya.c_allocationhdr
#                set externaldocument_id =externaldocument_id||'_'||c_allocationhdr_id
#                where c_allocationhdr_id=%s
#                """ % miembro[0])
#        db_ly.commit()
#

cursor.close()
db.close()
cursor_cc.close()
db_cc.close()
cursor_pr.close()
db_pr.close()

cursor_ly.close()
db_ly.close()

