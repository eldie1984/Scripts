#coding: utf-8
import psycopg2
import pyproj
import logging
from sys import argv,exit
from xml.dom import minidom
import urllib
import getopt
# Funcion que iporta las conexiones a la base
from commons import *
from string import *

#Defino los parametros que va a aceptar el script
receivers=['dgasch@mpf.gov.ar','deula@mpf.gov.ar']
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


def commitQuery (query):
        try:
                cnxn = psycopg2.connect("dbname=DW_DAC user=pentaho_user password=Dac123 host=192.168.57.76")
                logging.debug("Se realizo correctamente la conexion a la base")
                cur = cnxn.cursor()
                logging.debug("Se genero correctamente el cursor")
        except Exception,e:
                logging.error("No se pudo realizar la conexion a la base")
                logging.error(str(e))
        logging.info("Se comienza con la correccion de datos")
        try:
                logging.debug("Se ejecuta el script :\n %s" % query)
                cur.execute(query)
                cnxn.commit()                
        except Exception,e:
                logging.error("No se ejecuta el script :\n ")
                logging.error(str(e))
        logging.info("Se finalizo la correccion de datos")
        cur.close()
        cnxn.close
        return 

def executeQuery (query):
        try:
                #con_string = 'DSN=%s;UID=%s;PWD=%s;DATABASE=%s;' % (dsn, user, password, database)
                #cnxn = pyodbc.connect(con_string)
                cnxn = psycopg2.connect("dbname=DW_DAC user=pentaho_user password=Dac123 host=192.168.57.76")
                logging.debug("Se realizo correctamente la conexion a la base")
                cur = cnxn.cursor()
                logging.info("Se genero correctamente el cursor")
        except Exception,e:
                logging.error("No se pudo realizar la conexion a la base")
                logging.error(str(e))
        try:
            logging.debug("Se ejecuta el script :\n %s" % query)
            cur.execute(query)
                #count=cur.rowcount
                #logging.info("Se modificaron %s filas" % count)
            rows=cur.fetchall()
        except Exception,e:
                logging.error("No se ejecuta el script :\n %s" % query)
                logging.error(str(e))
        logging.info("Se ejecuto la captura de datos")
        cur.close()
        cnxn.close  
        return rows

def getDir(id_del,localidad,direccion):
	query=''
	url_str ='http://192.168.57.50/search.php?format=xml&q=%s+%s' %(direccion.replace(" ","+"),localidad.replace(" ","+"))
	xml_str = urllib.urlopen(url_str).read()
	xmldoc = minidom.parseString(xml_str)
	obs_values = xmldoc.getElementsByTagName('place')
	logging.debug(url_str)	
	query = query + """update ft_modalidad set 
	lugar_lat=%s, 
	lugar_long=%s, 
    nominatim=true
	where id_modalidad = %s
	""" %(obs_values[len(obs_values)-1].attributes['lat'].value,obs_values[len(obs_values)-1].attributes['lon'].value,obs_values[len(obs_values)-1].attributes['type'].value,id_del)
	#lon=obs_values[len(obs_values)-1].attributes['lon'].value
	logging.debug(query)

	
	# prints the first base:OBS_VALUE it finds
	#obs_values=obs_values.translate(transtab)


	return query
#################################################################################

if __name__ == "__main__":
        main(argv[1:])
#################################################################################
# Parametros
#################################################################################

logging.debug("Seteo variables")

i=0
i_last=0
##################################################

logging.info("--------- Inicio del Script ----------------")

#########################################################
#
#   Se realiza un chequeo de la conexion a la base de   #
#   datos y en caso que de error se aborta el script    #
#   y se emite un mensaje de error
#
#########################################################

query=""
#####################################

#   Consultas a la base de datos    #

#####################################
logging.debug("Declaro las consultas a ejecutarse")

dir_sin_corregir_mod="""select id_modalidad,lower(lugar_hecho) from ft_modalidad where cod_bahra is null  
and lower(lugar_hecho) not like '%% y %' 
and lower(lugar_hecho) not  like 'esquina' 
and lower(lugar_hecho) not like '%%penitenciario%'  
and lower(lugar_hecho) not like 'capital%'  
and lower(lugar_hecho) not like 'caba'  
and lower(lugar_hecho) not like 'c.a.b.a.'  
and lower(lugar_hecho) not like 'no %'  
and lower(lugar_hecho) not like 'capital%'  
and lower(lugar_hecho) not like '%%comisaria%'
and lower(lugar_hecho) not like '%%aeropuerto%'
and lower(lugar_hecho) not like '%%estacion%'
and lower(lugar_hecho) not like '%%ffcc%'
and lower(lugar_hecho) not like '%%interior%'
and lower(lugar_hecho) not like '%% determinar%'
and lower(lugar_hecho) not like '%% ignora%'
and lower(lugar_hecho) not like '%%subte%'
and lower(lugar_hecho) not like '%%omnibus%'
and lower(lugar_hecho) not like '%%hospital%'
and lower(lugar_hecho) not like '%%via publica%'
and lower(lugar_hecho) not like '%%manzana%'
and lower(lugar_hecho) not like '%%villa %'
and lower(lugar_hecho) not like 'ciudad autonoma de buenos aires'
and lower(lugar_hecho) not like '%%mza%'
and lower(lugar_hecho) not like '%% y %'
and lower(lugar_hecho) not like '%%esquina %'
"""
rows = executeQuery(dir_sin_corregir_mod )
barrio=""
for row in rows:
	try:
		query_dir=getDir(row[0],"capital federal",row[1])
	except IndexError:
		logging.warn("No existe la dir")
		query_dir=''
	except Exception,e:
		logging.error("error con la direccion")
		logging.error(str(e))
		query_dir=''

	query=query+"""%s; \n""" % (query_dir)
#	i=i+1
#	if (i % 1000) == 0:
#		commitQuery(query)
#		query=""

#commitQuery(query)
query=""

logging.info("Se finalizo la captura de datos")
#commitQuery(query)


logging.info("--------- Fin del Script ----------------")
