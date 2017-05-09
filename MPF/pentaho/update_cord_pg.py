#coding: utf-8
import psycopg2
import pyproj
import logging
from sys import argv,exit,path
from xml.dom import minidom
import urllib
import getopt
from os import environ
# Funcion que iporta las conexiones a la base
path.insert(0, environ['SCRIPTS_HOME'])
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



#################################################################################

if __name__ == "__main__":
        main(argv[1:])
#################################################################################
# Parametros
#################################################################################

logging.debug("Seteo variables")
wgs84=pyproj.Proj("+init=EPSG:4326")
#isn2004=pyproj.Proj("+proj=tmerc +lat_0=-90 +lon_0=-60 +k=1 +x_0=5500000 +y_0=0 +ellps=intl +units=m +no_defs")  # codigo de faja 5
isn2004=pyproj.Proj("+init=EPSG:22195")  # codigo de faja 5
#proj entrada 22195 
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

datosSinCorregir="""select id_mod_ori,lugar_x,lugar_y from ft_modalidad
where lugar_x is not null
and latitud is null 
"""
rows = executeQuery(datosSinCorregir)

for row in rows:
        try:
		longitud,latitud=pyproj.transform(isn2004,wgs84, replace(row[1],',','.'),replace(row[2],',','.'))
	        query=query+"""update ft_modalidad set longitud ='%s',
latitud = '%s'
where id_mod_ori= '%s'; \n""" % (longitud,latitud,row[0])
        	i=i+1
	except:
		logging.error("Se produjo un error con los datos %s %s %s" % (row[0],replace(row[1],',','.'),replace(row[2],',','.')))
        if (i % 10000) == 0:
                commitQuery(query)
                query=""

commitQuery(query)

logging.info("Se finalizo la captura de datos")
# Declaro la instancia del mail y el encabezado
mail_dac=mail(','.join(receivers),'Alarma delitos')
salida_mail = ''
text='REPORTE '+entorno+': Resultados de las queries'

logging.info("--------- Inicio del Envio del Mail  ----------------")
salida_mail = salida_mail+"""<h3 style="color:#0A4273">Correccion de coordenadas de N2 </h3>
<h6><span class="fuente">Salida:</span> Se Corrigieron un total de %s datos</h6>

<hr>""" % i


html="""\

<html>
      <head>
      <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

      <style>

      body{
      padding-left: 50px;
      padding-right: 50px;
      padding-top: 20px;
      padding-bottom: 50px;
      }

      tbody {
      text-align:center;
      font-size: 13px;
      }

      hr {
      padding-top: 10px;
      padding-bottom: 10px;
      }

      tr.primera-fila {
        border-bottom: solid 3px #ccc;
        background-color: #E8E8E8;

      }

      .variacion{
        font-weight: bold;
      }

      .fuente{
        color: #0a85d7;
      }
 h3{
        font-weight: bold;
        margin-bottom: 25px;
      }

      .logo-dac{
      margin-bottom: 30px;
      }

      </style>


      </head>
      <body>

        <div class="logo-dac">
<img src="cid:image1" width="500" alt="Base64 encoded image" />
        </div>

"""+salida_mail+"""
</body>
<table style="border:none;">
        <tr>
        <td style="width: 220px; margin:1px 15px;vertical-align:top;">
        <img width="212" height="61" src="cid:logo" width="500" alt="Base64 encoded image" />


        </td>
        <td style="border-left:1px solid #aaa; color: #575756">
        <p style="margin:1px 20px; padding:0; text-transform:uppercase; font-family:'Arial Black', 'Arial', sans-serif; font-size:10pt;">
Dirección de Análisis Criminal y Planificación de la Persecución Penal (DAC)

        </p>
        <p style="margin:1px 20px; padding:0; font-family:'Arial',sans-serif; font-size:10pt;">

        </p>
        <p style="margin:1px 20px; padding:0; margin-top:15px;font-style:italic;font-family:'Arial',sans-serif; font-size:10pt;">
        Area I+D+i
        </p>

        <p style="margin:1px 20px; padding:0;font-family:'Arial',sans-serif; font-size:10pt;">
        Tte. Gral. Perón 667, Piso 1° Of. 7 - C.P.: C1038AAM
        </p>


        <p style="margin:1px 20px; padding:0;font-family:'Arial',sans-serif; font-size:10pt;">
        CABA - Argentina - Tel.: +54 11 60899093
        </p>


        <p style="margin:1px 20px; padding:0;font-family:'Arial',sans-serif; font-size:10pt;">
        <a href="http://www.mpf.gob.ar">www.mpf.gob.ar</a> | <a href="http://www.fiscales.gob.ar">www.fiscales.gob.ar</a>

        </p>
        </td>
        </tr>
        </table>

</html>"""
mail_dac.send_mail(sender,receivers, text, html,environ['SCRIPTS_HOME']+'/DAC_Logo-01.png')

logging.info("--------- Fin del Script ----------------")

