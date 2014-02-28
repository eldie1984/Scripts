#!/bin/ksh
#set -x

/export/home/mo/moar/scripts/set_parametros_comunes.sh

LOG=/export/home/mo/moar/logs/consultarPagosPendienteDM.log

LANG=es_AR

$JAVA_HOME/java -Xms512M -Xmx1024M -Duser.timezone=$TZ -Dlog4j.configuration=log4j-consultarPagosPendienteDM.xml -classpath $CLASSPATH  clarin.cuentas.domain.service.ProcesoConsultaDMRunner >> $LOG 2>&1


