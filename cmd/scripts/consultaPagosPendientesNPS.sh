#!/bin/ksh
#set -x

. /export/home/mo/moar/scripts/set_parametros_comunes.sh

LOG=$DIR_APP/logs/consultaPagosPendientesNPS.log

LANG=es_AR

$JAVA_HOME/java -Xms256M -Xmx512M -Duser.timezone=$TZ -Dlog4j.configuration=log4j-consultaPagosPendientesNPS.xml -classpath $CLASSPATH  clarin.cuentas.domain.service.ProcesoConsultaNPSRunner >> $LOG 2>&1

