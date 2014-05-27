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


def correccion(query_bac,query_ly_1,query_ly_2,interfaz,error):
	logging.info("Encontre alertas de %s para %s" % (error,interfaz))
        cursor.execute("""%s""" % query_bac)
	lista=[]
	str_lista=''
      	for registro in cursor.fetchall():
		lista.append(str(registro[0]))
	str_lista = '\''+('\',\'').join(lista)+'\''
        logging.debug("la lista de ID's es: \n %s" % str_lista)


        cursor_ly.execute  (query_ly_1+'('+str_lista+')')
        logging.info("1 paso => Voy a ejecutar las siguientes querys en BAC: ")
        for result in cursor_ly.fetchall():
        	logging.info(result[0])
		cursor_pr.execute("%s" % result[0])
		db_pr.commit()
	if len(query_ly_2) > 5:
		cursor_ly.execute  (query_ly_2+'('+str_lista+')')        
		logging.info("2 paso => Voy a ejecutar las siguientes querys en BAC: ")
	        for result in cursor_ly.fetchall():
        		logging.info(result[0])
			cursor_pr.execute("%s" % result[0])
			db_pr.commit()




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
tablas={'CLIENTE':'erp_cliente_ly','PEDIDO':'erp_pedido_ly','FACTURA':'erp_factura_ly','PAGOS':'erp_pago_ly','PRODUCTO':'erp_producto_ly','USUARIO':'erp_usuario_ly'}

##################################################
logging.debug("Seteo variables")


logging.info("---------Inicio Script ----------------")
	
# Declaro la instancia del mail y el encabezado
mail_bac=mail(','.join(receivers),'Alerta Pagos BAC (py)')
mail_bac.createHtmlHeader("Interfaz","Error","Cantidad")
# COMIENZO CON LAS VERIFICACIONES:
try:
	db=MySQLdb.connect(host=db_host, user=db_user, passwd=db_pass , db=db_database )
	cursor = db.cursor()
	logging.debug("Se realizo la conexion a la base")
except Exception:
	logging.error("Nos se pudo realizar la conexion a la base")
	exit(2)

try:
	db_ly=psycopg2.connect(host=db_host_ly, user=db_user_ly, password=db_pass_ly , database=db_database_ly )
	cursor_ly = db_ly.cursor()
	logging.debug("Se realizo la conexion a la base de LY")
except Exception:
	logging.error("Nos se pudo realizar la conexion a la base de LY")
	exit(2)

try:
	db_pr=MySQLdb.connect(host=db_host_pr, user=db_user_pr, passwd=db_pass_pr , db=db_database )
	cursor_pr = db_pr.cursor()
	logging.debug("Se realizo la conexion a la base")
except Exception:
	logging.error("Nos se pudo realizar la conexion a la base")
	exit(2)

try:
	db_ly_pr=psycopg2.connect(host=db_host_ly_pr, user=db_user_ly_pr, password=db_pass_ly_pr , database=db_database_ly )
	cursor_ly_pr = db_ly_pr.cursor()
	logging.debug("Se realizo la conexion a la base de LY")
except Exception:
	logging.error("Nos se pudo realizar la conexion a la base de LY")
	exit(2)


cursor.execute("""select 'CLIENTE', substring(errormsg from 42 for 150),count(*) from erp_cliente_ly where IsError='Y' group by substring(errormsg from 42 for 150)
union
select 'USUARIO', if (substring(errormsg from 42 for 150) like '%Ya existe un contacto%','Ya existe un contacto para la Entidad Comercial',substring(errormsg from 42 for 150)) ,count(*) from erp_usuario_ly where IsError='Y'  group by if (substring(errormsg from 42 for 150) like '%Ya existe un contacto%','Ya existe un contacto para la Entidad Comercial',substring(errormsg from 42 for 150))
union
select 'PEDIDO', substring(errormsg from 42 for 150),count(*) from erp_pedido_ly where IsError='Y' group by substring(errormsg from 42 for 150)
union
select 'FACTURA', substring(errormsg from 42 for 150),count(*) from erp_factura_ly where IsError='Y' group by substring(errormsg from 42 for 150)
union
select 'PAGOS', substring(errormsg from 42 for 150),count(*) from erp_pago_ly where IsError='Y' group by substring(errormsg from 42 for 150)
union
select 'PRODUCTO', substring(errormsg from 42 for 150),count(*) from erp_producto_ly where IsError='Y' group by substring(errormsg from 42 for 150);
                """)


# Procesamos los datos obtenidos para incluirlos luego en un mail.

result=cursor.fetchall()
lista=[]

for miembro in result:
	#mail_bac.addDataHtml(miembro[0],miembro[1],miembro[2])
	#is_mail=1
	if miembro[0] == 'PAGOS' and miembro[1].find("CreditDebitAmountsMatchError") != -1:
		query_bac="""select id_pago from erp_pago_ly where errormsg like '%CreditDebitAmountsMatchError%';"""
		query_ly_1="""SELECT ('UPDATE `erp_pago_ly` 
        SET iserror=\\'N\\', ErrorMsg=\\'\\', C_AllocationHdr_ID='||c_allocationhdr_id||', AllocationHdr_DocumentNo=\\''||documentno||'\\', actualizacion= now() WHERE id_pago=\\''||externaldocument_id||'\\';') AS U
        FROM libertya.c_allocationhdr
        WHERE externaldocument_id in """
		query_ly_2="""select ('UPDATE `bac`.`cce_pago` SET  `comprobante_numero`='||documentno||', `comprobante_tipo`=\\''||allocationtype||'\\', `id_pago_ly`='||c_allocationhdr_id||', actualizacion= now() WHERE `id_pago`=\\''||externaldocument_id||'\\';') AS U
               FROM libertya.c_allocationhdr
               WHERE externaldocument_id in """
	
		correccion(query_bac,query_ly_1,query_ly_2,'PAGOS','CreditDebitAmountsMatchError')


        if miembro[0] == 'USUARIO' and miembro[1].find("Ya existe un contacto para la Entidad Comercial") != -1:
		query_bac="""select id_usuario from erp_usuario_ly where ad_user_id is null and errormsg like '%Ya existe un contacto%';"""
                
		query_ly_1="""
SELECT ('UPDATE erp_usuario_ly SET iserror=\\'N\\', errormsg=\\'\\', fecha_envio=null, ad_user_id='||ad_user_id||
' WHERE id_usuario='||userexternalid||' and  AD_Org_ID='||ad_org_id||';') AS U
FROM libertya.ad_user 
WHERE userexternalid IN """ 

                query_ly_2="""
SELECT ('UPDATE cce_usuario_cliente SET id_usuario_ly='||ad_user_id||
' WHERE id_usuario='||userexternalid||' ;') AS U2
FROM libertya.ad_user 
WHERE userexternalid IN """
		correccion(query_bac,query_ly_1,query_ly_2,'USUARIO','Ya existe un contacto para la Entidad Comercial')


        elif miembro[0] == 'FACTURA' and miembro[1].find("Ya existe un documento con el ID de Documento Externo") != -1:
		query_bac="""select id_lote  from erp_factura_ly where errormsg like '%Ya existe un documento con el ID de Documento Externo ingresado%'; """

		query_ly_1="""select ('UPDATE `erp_factura_ly`
SET `iserror`=\\'N\\', `ErrorMsg`=\\'\\'
, `C_Invoice_ID`=\\''||ci.c_invoice_id||'\\'
, `Invoice_DocumentNo`=\\''||ci.documentno||'\\'
, `GrandTotal`=\\''||ci.grandtotal||'\\'
, actualizacion= now() 
WHERE `id_lote`=\\''||externaldocument_id||'\\';') from libertya.c_invoice ci where externaldocument_id in  """

		query_ly_2="""select ('UPDATE `bac`.`cce_lote` 
SET `estado`=\\'FACTURADO\\'
, `comprobante_numero`=\\''||substring(documentno from 5)||'\\'
, `comprobante_tipo`=\\'FC'||substring(documentno from 1 for 1)||'\\'
, `id_factura_ly`=\\''||c_invoice_id||'\\'
, actualizacion= now() 
WHERE `id_lote`='||externaldocument_id||';') as U 
from libertya.c_invoice where externaldocument_id in """

		correccion(query_bac,query_ly_1,query_ly_2,'FACTURA','Ya existe un documento con el ID de Documento Externo')
	elif miembro[0] == 'PEDIDO' and miembro[1].find("Ya existe un documento con el ID de Documento Externo") != -1:
                query_bac="""select id_lote  from erp_pedido_ly where errormsg like '%Ya existe un documento con el ID de Documento Externo ingresado%'; """

                query_ly_1="""select ('UPDATE `erp_pedido_ly`
SET `iserror`=\\'N\\', `ErrorMsg`=\\'\\'
, `C_Invoice_ID`=\\''||ci.c_invoice_id||'\\'
, `Invoice_DocumentNo`=\\''||ci.documentno||'\\'
, actualizacion= now() 
WHERE `id_lote`=\\''||externaldocument_id||'\\';') from libertya.c_invoice ci where externaldocument_id in  """

		query_ly_2="""select ('UPDATE `bac`.`cce_lote` 
SET `estado`=\\'FACTURADO\\'
, `comprobante_numero`=\\''||substring(documentno from 5)||'\\'
, `comprobante_tipo`=\\'FC'||substring(documentno from 1 for 1)||'\\'
, `id_factura_ly`=\\''||c_invoice_id||'\\'
, actualizacion= now() 
WHERE `id_lote`='||externaldocument_id||';') as U 
from libertya.c_invoice where externaldocument_id in """

		correccion(query_bac,query_ly_1,query_ly_2,miembro[0],'Ya existe un documento con el ID de Documento Externo')

	elif miembro[0] == 'PEDIDO' and miembro[1].find("Error al completar el pedido:No es posible completar la operación, el período está cerrado") != -1:
		logging.info("update %s set errormsg='',iserror='N', fecha_envio=null where errormsg like '%s'" % (tablas[miembro[0]], '%'+miembro[1]+'%'))
                cursor_pr.execute("update %s set errormsg='',iserror='N', fecha_envio=null where errormsg like '%s' and iserror='Y'" % (tablas[miembro[0]], '%'+miembro[1]+'%'))
		db_pr.commit()

	elif miembro[0] == 'FACTURA' and miembro[1].find("Error al completar la factura:No es posible completar la operación, el período está cerrado") != -1:
		logging.info("update %s set errormsg='',iserror='N', fecha_envio=null where errormsg like '%s'" % (tablas[miembro[0]], '%'+miembro[1]+'%'))
                cursor_pr.execute("update %s set errormsg='',iserror='N', fecha_envio=null where errormsg like '%s' and iserror='Y'" % (tablas[miembro[0]], '%'+miembro[1]+'%'))
                db_pr.commit()
	elif miembro[0] == 'PEDIDO' and miembro[1].find("Error al completar la factura:Entidad Comercial con el cr") != -1:
		logging.info("Encontre alertas de %s para %s" % (miembro[1],miembro[0]))
		cursor.execute("""select distinct(c_bpartner_id) from %s where errormsg like '%s'""" % (tablas[miembro[0]],'%'+miembro[1]+'%'))
		lista=[]
		str_lista=''
		for registro in cursor.fetchall():
			lista.append(str(registro[0]))
		        str_lista = '\''+('\',\'').join(lista)+'\''
		logging.debug("la lista de ID's es: \n %s" % str_lista)
		cursor_ly_pr.execute  ("""update libertya.c_bpartner
		set creditminimumamt=10000000
		where c_bpartner_id in """+'('+str_lista+')')
		db_ly_pr.commit()
		logging.info("""update libertya.c_bpartner
		                set creditminimumamt=10000000
		                where c_bpartner_id in """+'('+str_lista+')')
		logging.info("update %s set errormsg='',iserror='N', fecha_envio=null where errormsg like '%s'" % (tablas[miembro[0]], '%'+miembro[1]+'%'))
		cursor_pr.execute("update %s set errormsg='',iserror='N', fecha_envio=null where errormsg like '%s' and iserror='Y'" % (tablas[miembro[0]], '%'+"Error al completar la factura:Entidad Comercial con el cr"+'%'))
                db_pr.commit()

	elif miembro[0] == 'PRODUCTO' and miembro[1].find("Error al actualizar el articulo:Error: : Stock =") != -1:
		logging.info("update %s set errormsg='',iserror='N', fecha_envio=null where errormsg like '%s'" % (tablas[miembro[0]], '%'+miembro[1]+'%'))
                cursor_pr.execute("update %s set errormsg='',iserror='N', fecha_envio=null ,producttype = 'I' where errormsg like '%s' and iserror='Y'" % (tablas[miembro[0]], '%'+miembro[1]+'%'))
                db_pr.commit()
	else:
		while True:
			option=raw_input("Desear resetear los registros %s  --> %s [S/N]: " % (miembro[0], miembro[1]))
			if option == 'S' or option == 's':
				logging.info("update %s set errormsg='',iserror='N', fecha_envio=null where errormsg like '%s'" % (tablas[miembro[0]], '%'+miembro[1]+'%'))
		                cursor_pr.execute("update %s set errormsg='',iserror='N', fecha_envio=null where errormsg like '%s' and iserror='Y'" % (tablas[miembro[0]], '%'+miembro[1]+'%'))
       	        		db_pr.commit()
				break
			elif option == 'N' or option == 'n':
				break
			else:
				print "Seleccione una opcion Valida"

		

# Cierro conexion a DB

cursor.close()
db.close()
cursor_ly.close()
db_ly.close()
cursor_pr.close()
db_pr.close()
cursor_ly_pr.close()
db_ly_pr.close()


logging.info("------- Fin del Script --------------")
