#! /bin/bash
#set -x

. /export/home/mo/moar/scripts/set_parametros_comunes.sh
 
LIBS_PATH=/export/home/mo/moar/java_libs
LOG=/export/home/mo/moar/logs/transaccionesXCampanias.log

LANG=es_AR

CLASSPATH=${CLASSPATH}:${LIBS_PATH}/gdata-java-client-1.40.0.atlassian-2.jar
CLASSPATH=${CLASSPATH}:${LIBS_PATH}/google-collections-1.0-rc2.jar

$JAVA_HOME/java -Xms120M -Xmx120M -classpath $CLASSPATH -Duser.timezone=$TZ -Dlog4j.configuration=log4j-transaccionesXCampanias.xml clarin.procesos.analytics.TransaccionesXCampanias > $LOG 2>&1