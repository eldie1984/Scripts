#! /bin/bash
#set -x

. /export/home/mo/moar/scripts/set_parametros_comunes.sh

LOG=$DIR_APP/logs/RespuestaCargo.log

export LANG=en_US.ISO-8859-1

$JAVA_HOME/java -Xms120M -Xmx240M -Duser.timezone=$TZ -Dlog4j.configuration=log4j-respuestaCargo.xml -Dftp.presea.activo=True -Dftp.presea.produccion=True -classpath $CLASSPATH clarin.cuentasERP.procesos.respuesta.RespuestaCargo >> $LOG 2>&1
