#coding: utf-8
import logging
from elasticsearch import Elasticsearch
from elasticsearch import helpers
from sys import argv,exit,path
#import threading
from os import environ
path.insert(0, environ['SCRIPTS_HOME'][:-8])
from commons import *

# es = Elasticsearch(['http://192.168.57.50:9200'])
# es.indices.create(index='n2', ignore=400)
actions=""
rows = executeQuery("""SELECT id_mod_ori, id_mod_modalidad, id_fecha_hecho, hora_hecho, lugar_hecho, 
       lugarfinhecho, id_actuacion, 
       id_sec_seccional
       descripcion_hecho,observaciones,cod_bahra, cod_bahra_fin
  FROM dw.ft_modalidad
        limit 1000;""")
for row in rows:
	actions=actions+"""{ "create" : { "_index" : "n2", "_type" : "modalidad", "_id" : "%s" } }\n""" % row[0]
	salida="""{ "modalidad" : "%s","fecha_hecho" : "%s","hora_hecho" : "%s","Actuacion" : "%s",""" % (row[1],row[2],row[3],row[6])
	if row[5] is None:
		salida = salida + """lugar_hecho" : null, """
	else:
		salida = salida + """"lugar_hecho" : "%s", """ % row[4]
	if row[5] is None:
		salida = salida + """lugarfinhecho" : null, """
	else:
		salida = salida + """"lugarfinhecho" : "%s", """ % row[5]
	if row[7] is None:
		salida = salida + """Seccional" : null, """
	else:
		salida = salida + """"Seccional" : "%s", """ % row[7]
	if row[8] is None:
		salida = salida + """descripcion_hecho" : null, """
	else:
		salida = salida + """"descripcion_hecho" : "%s", """ % row[8]
	if row[9] is None:
		salida = salida + """observaciones" : null} """
	else:
		salida = salida + """observaciones" : "%s"} \n""" % row[9]
	actions=actions+salida
print actions	