#coding: utf-8
import logging
import time
from sys import argv,exit,path
import threading
from os import environ
path.insert(0, environ['SCRIPTS_HOME'])
path.insert(1, environ['SCRIPTS_HOME'][:-8])
from commons import *

logging.basicConfig(format=FORMAT,level=logging.INFO)

from daemon import runner


class App():
    def __init__(self):
        self.stdin_path = '/dev/null'
        self.stdout_path = '/dev/tty'
        self.stderr_path = '/dev/tty'
        self.pidfile_path = '/tmp/mydaemon.pid'
        self.pidfile_timeout = 5

    def run(self):
        logging.debug("Seteo variables")
        threads = list()
        logging.info("--------- Inicio del Script ----------------")
        commitQuery("""update ft_modalidad set cod_bahra=null where cod_bahra=0;""")

        datosSinCorregirModalidad="""select id_mod_ori,longitud,latitud from ft_modalidad
        where cod_bahra is null
        and longitud is not null
        order by id_mod_ori asc
        limit 500
        """
        while True:
            threads = list()
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
            if cantidad < 500 :
                time.sleep(10000)

def Modalidad(rows):
    logging.info("Thread %s iniciado" %threading.currentThread().getName())
    query=""
    for row in rows:
        try:
            barrio=getBarrio(row[2],row[1])
        except Exception,e:
            logging.error(str(e))
            barrio=0
        if barrio == 0:
            try:
                barrio=getBarrio(row[2],row[1],8)
            except Exception , e:
                logging.error(str(e))

        query=query+"""update ft_modalidad set 
                    cod_bahra=%s
                    where id_mod_ori= '%s'; \n""" % (barrio,row[0])
    commitQuery(query)
    logging.debug("Se finalizo la carga en la base de modalidad")
    logging.info("Thread %s finalizado" %threading.currentThread().getName())




#####################################

#    Consultas a la base de datos       #

#####################################

app = App()
daemon_runner = runner.DaemonRunner(app)
daemon_runner.do_action()