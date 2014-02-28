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
mail_bac.createHtmlHeader("Interfaz","Error","Cantidad")
# COMIENZO CON LAS VERIFICACIONES:
try:
    db=MySQLdb.connect(host=db_host, user=db_user, passwd=db_pass , db=db_database )
    cursor = db.cursor()
    logging.debug("Se realizo la conexion a la base")
except Exception:
    logging.error("Nos se pudo realizar la conexion a la base")

try:
    db_ly=psycopg2.connect(host=db_host_ly, user=db_user_ly, password=db_pass_ly , database=db_database_ly )
    cursor_ly = db_ly.cursor()
    logging.debug("Se realizo la conexion a la base de LY")
except Exception:
    logging.error("Nos se pudo realizar la conexion a la base de LY")

try:
    db_pr=MySQLdb.connect(host=db_host_pr, user=db_user_pr, passwd=db_pass_pr , db=db_database )
    cursor_pr = db_pr.cursor()
    logging.debug("Se realizo la conexion a la base")
except Exception:
    logging.error("Nos se pudo realizar la conexion a la base")


cursor.execute("""select 'CLIENTE', substring(errormsg from 42 for 150),count(*) from erp_cliente_ly where IsError='Y' group by substring(errormsg from 42 for 150)
union
select 'USUARIO', substring(errormsg from 42 for 150),count(*) from erp_usuario_ly where IsError='Y' group by substring(errormsg from 42 for 150)
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
		cursor.execute("""select id_pago from erp_pago_ly where errormsg like '%CreditDebitAmountsMatchError%';""")
		for registro in cursor.fetchall():
			lista.append(str(registro[0]))
		str_lista = '\''+('\',\'').join(lista)+'\''
		print str_lista
		
		
		cursor_ly.execute  ("""SELECT ('UPDATE `erp_pago_ly` 
	SET iserror=\\'N\\', ErrorMsg=\\'\\', C_AllocationHdr_ID='||c_allocationhdr_id||', AllocationHdr_DocumentNo='||documentno||', actualizacion= now() WHERE id_pago=\\''||externaldocument_id||'\\';') AS U
	FROM libertya.c_allocationhdr
	WHERE externaldocument_id in (%s)""" % str_lista)
		
		for result in cursor_ly.fetchall():
			print result[0]
			cursor_pr.execute("%s" % result[0])
			db_pr.commit()
		cursor_ly.execute  ("""select ('UPDATE `bac`.`cce_pago` SET  `comprobante_numero`='||documentno||', `comprobante_tipo`=\\''||allocationtype||'\\', `id_pago_ly`='||c_allocationhdr_id||', actualizacion= now()WHERE `id_pago`=\\''||externaldocument_id||'\\';') AS U
		FROM libertya.c_allocationhdr
		WHERE externaldocument_id in  (%s);"""  % str_lista)
		
		for result in cursor_ly.fetchall():
                        print result[0]
			cursor_pr.execute("%s" % result[0])
                        db_pr.commit()



if is_mail==1 or test==1:

	text='REPORTE: ERRORES de los envios de LIBERTYA'
	html="""\
            <html>
                  <head>
                  <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
                  </head>
                        <body>
                        <h1 style="color:OrangeRed">Este es un reporte solo INFORMATIVO, NO ESCALAR </h1>
                        """+mail_bac.html+"""</tbody>
                        </table>
                        <h3> Reenviar el mail a los responsables del portal</h3>
                        
                      <p>Alama corriendo en el equipo """ + hostname + """ en """ + pathDir+ """ </p> 
                        </body>
              </html>
        """
	mail_bac.send_mail(sender,receivers, text, html)
		




# Cierro conexion a DB

cursor.close()
db.close()
cursor_ly.close()
db_ly.close()
cursor_pr.close()
db_pr.close()

logging.info("------- Fin del Script --------------")
