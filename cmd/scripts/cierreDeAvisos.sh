#! /bin/bash
#set -x

. /export/home/mo/moar/scripts/set_parametros_comunes.sh
 
LOG=/export/home/mo/moar/logs/cierreDeAvisos.log

LANG=es_AR

$JAVA_HOME/java -Xms120M -Xmx120M -classpath $CLASSPATH -Duser.timezone=$TZ -Dlog4j.configuration=log4j-cierreDeAvisos.xml clarin.procesos.avisos.CierreDeAvisos > $LOG 2>&1

