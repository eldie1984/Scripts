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
	print "User: %s" % i[1]
	print "Host: %s" % i[0]
	print "Pass: %s" % i[2]
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

	buff=conn.get_df('>',i[3])
	filesistems=[]
	for sal in buff.split("\n"):
                if sal.find(",") != -1 :
			lista=sal.split(",")
			filesistems.append([lista[0],lista[2],lista[3],lista[4],lista[7].translate(string.maketrans("\n\t\r", "   "))])
			try:
				fecha=datetime.strptime(lista[1],'%d/%m/%y %H:%M:%S').strftime('%d/%m/%y')
			except:
				fecha=lista[1]
			print """insert into FILESYSTEMS (HOST,FECHA,NOMBREFS,TAMANIO,USADO,PUNTO_MONTAJE)
                        values
                        ('%s','%s','%s','%s','%s','%s');""" % (lista[0],fecha,lista[2],lista[3],lista[4],lista[7].translate(string.maketrans("\n\t\r", "   ")).strip())

			cur.execute("""alter session set nls_date_format='dd/mm/yy'""")

			cur.execute("""insert into FILESYSTEMS (HOST,FECHA,NOMBREFS,TAMANIO,USADO,PUNTO_MONTAJE)
			values
			('%s','%s','%s','%s','%s','%s')""" % (lista[0],fecha,lista[2],lista[3],lista[4],lista[7].translate(string.maketrans("\n\t\r", "   ")).strip()))
			con.commit()
#	cur.bindarraysize=len(filesistems)
#	cur.setinputsizes(20,50,int,int,int,100)
#	cur.setinputsizes("insert into FILESYSTEMS (HOST,FECHA,NOMBREFS,TAMANIO,USADO,PUNTO_MONTAJE) values ( :1, sysdate, :2, :3, :4, :5)",filesistems)
#	cur2 = con.cursor()
#	cur2.execute('select * from FILESYSTEMS')
#	res = cur2.fetchall()
#	print res
cur.close()
#	cur2.close()
con.close()
