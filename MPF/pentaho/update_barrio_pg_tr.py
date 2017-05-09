#coding: utf-8
import psycopg2
import logging
from sys import argv,exit,path
from xml.dom import minidom
import urllib
import getopt
import threading
from os import environ
# Funcion que iporta las conexiones a la base
path.insert(0, environ['SCRIPTS_HOME'])
path.insert(1, environ['SCRIPTS_HOME'][:-8])
from commons import *

receivers=['dgasch@mpf.gov.ar','deula@mpf.gov.ar']
def main(argv):
	level_opt=logging.INFO
	global receivers
	global test
	global offset
	global limit
	global is_cargo
	global is_modalidad
	offset=0
	limit=500000
	is_modalidad=1
	try:
		opts, args = getopt.getopt(argv,"hdt:l:o:",["debug","testing","offset","limit","modalidad"])
	except getopt.GetoptError:
		print """-d --debug	 ---> Pone en debug el log
-t --testing	---> Pone a la persona indicada como receptor del mail
-o --offset	 ---> Setea el offest al numero ingresado, defecto 0
-l --limit		---> Setea el limite al numero ingresado, defecto 50000
"""
		exit()
	for opt, arg in opts:
  		if opt == '-h':
			print """-d --debug	 ---> Pone en debug el log
-t --testing	---> Pone a la persona indicada como receptor del mail
-o --offset	 ---> Setea el offest al numero ingresado, defecto 0
-l --limit		---> Setea el limite al numero ingresado, defecto 50000
"""
			exit()
		elif opt in ("-d", "--debug"):
			level_opt=logging.DEBUG
		elif opt in ("-o", "--offset"):
			offset=arg
		elif opt in ("-l", "--limit"):
			limit=arg
		elif opt in ("-t", "--testing"):
			print "Testing"
			test=1
			receivers = []
			receivers.append(arg)
		logging.basicConfig(format=FORMAT,level=level_opt)


def Modalidad(rows):
	logging.info("Thread %s iniciado" %threading.currentThread().getName())
	query=""
	for row in rows:
		try:
			barrio=getBarrio(row[2],row[1])
		except Exception,e:
#			logging.error("error con el barrio")
			logging.error(str(e))
			barrio=0
		if barrio == 0:
			try:
				barrio=getBarrio(row[2],row[1],8)
			except Exception , e:
#				logging.error("error con el barrio")
				logging.error(str(e))

		query=query+"""update ft_modalidad set 
	cod_bahra=%s
	where id_mod_ori= '%s'; \n""" % (barrio,row[0])
	commitQuery(query)
	logging.debug("Se finalizo la carga en la base de modalidad")
	logging.info("Thread %s finalizado" %threading.currentThread().getName())

#################################################################################

if __name__ == "__main__":
				main(argv[1:])
#################################################################################
# Parametros
#################################################################################

logging.debug("Seteo variables")
i=0
i_last=0
threads = list()


datosSinCorregirModalidad="""select id_mod_ori,longitud,latitud from ft_modalidad
where cod_bahra is null
and longitud is not null
order by id_mod_ori asc
limit %s offset %s
""" % (limit,offset)
##################################################

logging.info("--------- Inicio del Script ----------------")

#####################################

#	 Consultas a la base de datos		#

#####################################
commitQuery("""update ft_modalidad set cod_bahra=null where cod_bahra=0;""")

if is_modalidad==1 :
	rows = executeQuery(datosSinCorregirModalidad)
	barrio=""
	print len(rows)
	cantidad=len(rows)/10
	for rows_sub in [rows[x:x+cantidad] for x in xrange(0, len(rows), cantidad)]:
		print len(rows_sub)
		t = threading.Thread(target=Modalidad, args=(rows_sub,))
		threads.append(t)
		t.start()

for t in threads:
		t.join()
logging.info("Se finalizo la captura de datos")

logging.info("--------- Fin del Script ----------------")
