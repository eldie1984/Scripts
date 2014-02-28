#!/bin/ksh
#set -x

. /export/home/mo/moar/scripts/set_parametros_comunes.sh

LOG=$DIR_APP/logs/passwordsAMonitorear.log

LANG=es_AR

$JAVA_HOME/java -Xms64M -Xmx128M -Duser.timezone=$TZ -Dlog4j.configuration=log4j-passwordsAMonitorear.xml -classpath $CLASSPATH  clarin.util.monitoreo.service.MonitoreoRunner /export/home/mo/moar/txt/passwords.txt >> $LOG 2>&1

