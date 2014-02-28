#!/bin/ksh
#set -x

OLDPID=$(head -1 calcular_percepcion_java.pid)
HORA_IN_ANT=$(ps -ef |grep $OLDPID | grep -v grep | awk '{print $8}')

T_EJEC=$(expr $(expr $(echo $HORA_IN_ANT|awk -F: '{print $1}') \* 60 2> /dev/null) + $(echo $HORA_IN_ANT|awk -F: '{print $2}') 2> /dev/null)	#Calculamos el tiempo que estuvo ejecutando el proceso java.

if [ $(ps -ef |grep $OLDPID |grep -v grep |wc -l) -eq 1 ]	#verificamos si el proceso esta ejecutando ya
then
	if [ $T_EJEC -ge 70 ]	#si esta ejecutando hace mas de 70 minutos.
	then
		kill -9 $OLDPID
		rm calcular_percepcion_java.pid
	else
		echo "Ya esta corriendo"
		exit 1
	fi
fi


. /export/home/mo/moar/scripts/set_parametros_comunes.sh

LOG=$DIR_APP/logs/calcularPercepciones.log

LANG=es_AR

$JAVA_HOME/java -Xms512M -Xmx1024M -Xdebug -Xrunjdwp:transport=dt_socket,address=4444,server=y,suspend=n -Duser.timezone=$TZ -Dlog4j.configuration=log4j-calcularPercepciones.xml -classpath $CLASSPATH  clarin.datosFacturacion.service.ProcesoCalculoPercepcionRunner >> $LOG 2>&1
#$JAVA_HOME/java -Xms512M -Xmx1024M -Duser.timezone=$TZ -Dlog4j.configuration=log4j-calcularPercepciones.xml -classpath $CLASSPATH  clarin.datosFacturacion.service.ProcesoCalculoPercepcionRunner >> $LOG 2>&1 &

echo $! > calcular_percepcion_java.pid	#Guarda el pid del proceso java
echo "Termino calcular_percepcion_java.sh, el proceso java $! lanzado aun puede estar corriendo."
