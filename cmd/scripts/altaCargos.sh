#!/bin/bash

#set -x

OLDPID=$(head -1 alta_cargos_java.pid)
HORA_IN_ANT=$(ps -ef |grep $OLDPID | grep -v grep | awk '{print $8}')

T_EJEC=$(expr $(expr $(echo $HORA_IN_ANT|awk -F: '{print $1}') \* 60 2> /dev/null) + $(echo $HORA_IN_ANT|awk -F: '{print $2}') 2> /dev/null)	#Calculamos el tiempo que estuvo ejecutando el proceso java.

if [ $(ps -ef |grep $OLDPID |grep -v grep |wc -l) -eq 1 ]	#verificamos si el proceso esta ejecutando ya
then
	if [ $T_EJEC -ge 10 ]	#si esta ejecutando hace mas de 15 minutos.
	then
		kill -9 $OLDPID
		rm alta_cargos_java.pid
	else
		echo "Ya esta corriendo"
		exit 1
	fi
fi


. /export/home/mo/moar/scripts/set_parametros_comunes.sh

LOG=$DIR_APP/logs/AltaCargos.log

export LANG=en_US.ISO-8859-1

$JAVA_HOME/java -Xms6M -Xmx48M -Duser.timezone=$TZ -Dlog4j.configuration=log4j-altaCargos.xml -Dftp.presea.activo=True -Dftp.presea.produccion=True -classpath $CLASSPATH clarin.cuentasERP.procesos.alta.AltaCargos >> $LOG 2>&1 &

echo $! > alta_cargos_java.pid	#Guarda el pid del proceso java
echo "Termino altaCargo.sh, el proceso java $! lanzado aun puede estar corriendo."
