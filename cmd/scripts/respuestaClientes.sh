#! /bin/bash
#set -x

. /export/home/mo/moar/scripts/set_parametros_comunes.sh

LOG=$DIR_APP/logs/respuestaClientes.log

$JAVA_HOME/java -Xms120M -Xmx240M -Duser.timezone=$TZ -Dlog4j.configuration=log4j-respuestaClientes.xml -Dftp.presea.activo=True -Dftp.presea.produccion=True -classpath $CLASSPATH clarin.cuentasERP.procesos.respuesta.RespuestaClientes >> $LOG 2>&1
