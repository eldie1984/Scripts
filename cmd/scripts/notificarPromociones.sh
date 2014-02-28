#!/bin/ksh
#set -x

. /export/home/mo/moar/scripts/set_parametros_comunes.sh

LOG=$DIR_APP/logs/notificacionPromociones.log

LANG=es_AR

$JAVA_HOME/java -Xms120M -Xmx240M -Duser.timezone=$TZ -Dlog4j.configuration=log4j-notificacionPromociones.xml -classpath $CLASSPATH  clarin.procesos.promociones.NotificacionPromocionVendedores >> $LOG 2>&1


