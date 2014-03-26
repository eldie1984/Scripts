import csv
import os
import string
import cx_Oracle
from connexion import *
from datetime import datetime

usuarios=[]

for host,user,passw,sudo in csv.reader(open("users.csv")):
    usuarios.append([host,user,passw,sudo])

print usuarios

try:
       con = cx_Oracle.connect('E438827/E438827@(DESCRIPTION=(ADDRESS=(PROTOCOL=tcp)(HOST=yvasa880)(PORT=1521))(CONNECT_DATA=(SID=DWH)))')
       cur = con.cursor()

except :
       logging.error("se produjo un error al conectarse a la base")
       exit(1)

for i in usuarios:
	logging.info("User: %s" % i[1])
	logging.info("Host: %s" % i[0])
	logging.info("Pass: %s" % i[2])
	if i[3] != 'S':
		try:
			conn=conectarse(i[0],i[1],i[2])
			logging.info("me conecte sin sudo")
		except:	
			break
	else:		
		print "entro al else"
		conn=conectarse(i[0],os.environ['User'],os.environ['Pass'])
		conn.make_sudo(i[1],i[2],i[1]+' >')
		logging.info("me conecte con sudo")

	buff=conn.get_db('>',i[3])
	filesistems=[]
	for sal in buff.split("\n"):
                if sal.find(",") != -1 :
			lista=sal.split(",")
			fecha=datetime.strptime(lista[0],'%d/%m/%Y %H:%M:%S').strftime('%d/%m/%y')
			try:
				fecha=datetime.strptime(lista[0],'%d/%m/%Y %H:%M:%S').strftime('%d/%m/%y')
			except:
				fecha=lista[0]
			print """INSERT INTO DB_STATS (DB_FECHA, DB_HOST,  DB_SID, DB_TAMANO )
                        values
                        ('%s','%s','%s',%s);""" % (fecha,lista[1],lista[2],lista[3].translate(string.maketrans("\n\t\r", "   ")).strip())

			cur.execute("""alter session set nls_date_format='dd/mm/yy'""")

			cur.execute("""INSERT INTO DB_STATS (DB_FECHA, DB_HOST,  DB_SID, DB_TAMANO )
			values
			('%s','%s','%s',%d)""" % (fecha,lista[1],lista[2],int(lista[3].translate(string.maketrans("\n\t\r", "   ")).strip())))
			con.commit()
cur.close()
con.close()
