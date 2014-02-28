from curses import initscr,curs_set,newwin,endwin,KEY_RIGHT,KEY_LEFT,KEY_DOWN,KEY_UP
from random import randrange
import time
import MySQLdb
from socket import gethostname
from sys import argv,exit
import getopt
from commons import *
import logging
import psycopg2
#initscr()
#curs_set(0)
#win = newwin(30,160,5,15)
#win.keypad(1)
#win.nodelay(1)
#win.border('|','|','-','-','+','+','+','+')
##win.addch(4,44,'O')
##snake = [ [30,7],[29,8],[28,7],[27,7],[26,7],[25,7] ]
#key = KEY_RIGHT
#while key != 27:
##    win.addstr(0,2,' Score: '+str(len(snake)-6)+' ')
# #   win.timeout(180+ ( (len(snake)-6) % 10- (len(snake)-6) ) * 3 )
#    getkey = win.getch()
#    key = key if getkey==-1 else getkey
#   # snake.insert(0,[snake[0][0]+(key==KEY_RIGHT and 1 or key==KEY_LEFT and -1), snake[0][1]+(key==KEY_DOWN and 1 or key==KEY_UP and -1)])
#   # win.addch(snake[len(snake)-1][1],snake[len(snake)-1][0],' ')
#   # if win.inch(snake[0][1],snake[0][0]) & 255 == 32: snake.pop()
#   # elif win.inch(snake[0][1],snake[0][0]) & 255 == ord('O'):
#   #     c = [n for n in [[randrange(1,58,1),randrange(1,14,1)] for x in range(len(snake))] if n not in snake]
#   #     win.addch(c == [] and 4 or c[0][1],c == [] and 44 or c[0][0],'O')
#   # else: break
#   # win.addch(snake[0][1],snake[0][0],'X')
#endwin()

try:
	db=MySQLdb.connect(host=db_host, user=db_user, passwd=db_pass , db=db_database )
	cursor = db.cursor()
	print "Se realizo la conexion a la base"
except Exception,e:
	print "Nos se pudo realizar la conexion a la base"
	print str(e)
	exit(2)


try:
	db_ly=psycopg2.connect(host=db_host_ly, user=db_user_ly, password=db_pass_ly , database=db_database_ly )
	cursor_ly = db_ly.cursor()
	logging.debug("Se realizo la conexion a la base de LY")
except Exception,e:
	print "Nos se pudo realizar la conexion a la base de LY"
	print str(e)
	exit(2)



#fullscreen = initscr()

#fullscreen.border(0)
#fullscreen.nodelay(1)

#start_x=10
#start_y=4
key = 114
mail_bac=mail('dgasch@cmd.com.ar','Alerta Pagos BAC (py)')
mail_bac.createHtmlHeader("Interfaz","Error","Cantidad")

#f = open('index.html', 'w')
cursor.execute("""set session transaction isolation level READ COMMITTED""")
while key != 27:
	if key == 114:
		try:
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
		except Exception:
			logging.error("error con el cursor")
			break
	

		result=cursor.fetchall()
#	start = start_y
	mail_bac.truncate()
	for miembro in result:
		mail_bac.addDataHtml(miembro[0],miembro[1],miembro[2])
#		fullscreen.addstr(start,start_x, "%s" % miembro[0])
#		fullscreen.addstr(start,start_x+8, "|" )
#		fullscreen.addstr(start,start_x+10,"%s" % miembro[1])
#		fullscreen.addstr(start,start_x+162, "|" )
#		fullscreen.addstr(start,start_x+164,"%s" %miembro[2])
#		start=start + 2
#
#	fullscreen.refresh()
#	if key == 114:
#		try:
#			cursor.execute("""select "FACTURA", count(*) from erp_factura_ly where fecha_envio is null and id_portal not in  (12) and ad_client_id not in (1973) and c_bpartner_id is not null and id_lote not in (1308482,1328812,1421626,867947,1217041,1228883,1253709,1365738)
#			union 
#			select "PEDIDO", count(*) from erp_pedido_ly where fecha_envio is null
#			union
#			select "PAGO", count(*) from erp_pago_ly where fecha_envio is null
#			union
#			select "Cliente", count(*) from erp_cliente_ly where fecha_envio is null and id_portal not in (12) and ad_client_id not in (1973)
#			union
#			select "usuario", count(*) from erp_usuario_ly where fecha_envio is null and id_portal not in (12) and ad_client_id not in (1973)
#			union
#			select "Anulacion", count(*) from erp_anulacion_ly eal where eal.pago_enviado='N';
#			;""")
#		except Exception:
#	                        logging.error("error con el sursor")
#	                        break
#		result=cursor.fetchall()
#
#		try:
#			cursor_ly.execute("""SELECT 'Facturas no procesadas', Posted as "estado",count(*)
#					FROM libertya_prod.libertya.C_Invoice
#					WHERE (posted not in ('Y','N')) AND (AD_Client_ID IN (1010016,1010056,1010057)) AND (docstatus IN ('CO','CL', 'VO', 'RE')) 
#					group by posted
#					union
#					SELECT 'Recibos no procesadas', Posted as "estado",count(*)
#					FROM libertya_prod.libertya.C_AllocationHdr
#					WHERE (posted not in ('Y','N')) AND (AD_Client_ID IN (1010016,1010056,1010057)) AND (docstatus IN ('CO','CL', 'VO', 'RE'))
#					group by posted
#					union
#					SELECT 'Pagos no procesadas', Posted as "estado",count(*)
#					FROM libertya_prod.libertya.C_Payment
#					WHERE (posted not in ('Y','N')) AND (AD_Client_ID IN (1010016,1010056,1010057)) AND (docstatus IN ('CO','CL', 'VO', 'RE'))
#					group by posted
#					union
#					SELECT 'Libros de Caja no procesadas', Posted as "estado",count(*)
#					FROM libertya_prod.libertya.C_Cash
#					WHERE (posted not in ('Y','N')) AND (AD_Client_ID IN (1010016,1010056,1010057)) AND (docstatus IN ('CO','CL', 'VO', 'RE')) 
#					group by posted;""")
#		except Exception,e:
#			logging.error("Error con el cursor de LY")
#			print str(e)
#		result_ly=cursor_ly.fetchall()
#	start=35
#
#	fullscreen.addstr(start,start_x+30, "Interfaz")
#        fullscreen.addstr(start,start_x+42, "|" )
#        fullscreen.addstr(start,start_x+44,"Cantidad comprobantes")
#	start=start + 2
#
#	fullscreen.addstr(start-1,start_x+30, "----------------------------------")
#	for miembro in result:
#			fullscreen.addstr(start,start_x+30, "%s" % miembro[0])
#                        fullscreen.addstr(start,start_x+42, "|" )
#	                fullscreen.addstr(start,start_x+44,"%s" % miembro[1])
#	                start=start + 2
#			
#        start=35
#
#	fullscreen.addstr(start,start_x+88, "Interfaz")
#	fullscreen.addstr(start,start_x+112, "|" )
#	fullscreen.addstr(start,start_x+114,"Estado")
#	fullscreen.addstr(start,start_x+120, "|" )
#	fullscreen.addstr(start,start_x+124,"Cantidad")
#	start=start + 2
#
#	fullscreen.addstr(start-1,start_x+88, "------------------------------------------------------")
#	for miembro in result_ly:
#                        fullscreen.addstr(start,start_x+88, "%s" % miembro[0])
#                        fullscreen.addstr(start,start_x+112, "|" )
#                        fullscreen.addstr(start,start_x+116,"%s" % miembro[1])
#                        fullscreen.addstr(start,start_x+120, "|" )
#                        fullscreen.addstr(start,start_x+124,"%s" % miembro[2])
#                        start=start + 2
#	key = fullscreen.getch()
#	fullscreen.clear()
	print "Creo el archivo"
	f = open('index.html', 'w')
	f.truncate()
	f.write("""<html>
                  <head>
                  <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
                  </head>
                        <body>
                        """+mail_bac.html+"""</tbody>
                        </table>
                        
                        </body>
              </html>""")
	f.close()
	time.sleep(25)
	key=114

#	fullscreen.addstr(12, 25, "Hola Mundo desde Python curses!")
	#fullscreen.refresh()

#fullscreen.getch()

#f.close()
#endwin()
