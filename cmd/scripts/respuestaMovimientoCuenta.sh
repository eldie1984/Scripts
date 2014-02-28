#! /bin/bash
#set -x

. /export/home/mo/moar/scripts/set_parametros_comunes.sh

LOG=$DIR_APP/logs/respuestaMovimientoCuenta.log

export LANG=en_US.ISO-8859-1

$JAVA_HOME/java -Xms6M -Xmx48M -Djava.awt.headless=true -Duser.timezone=$TZ -Dlog4j.configuration=log4j-respuestaMovimientoCuenta.xml -Dftp.presea.activo=True -Dftp.presea.produccion=True -classpath $CLASSPATH clarin.cuentasERP.procesos.respuesta.RespuestaMovimientoCuenta >> $LOG 2>&1


