#! /bin/bash
#set -x

. /export/home/mo/moar/scripts/set_parametros_comunes.sh

LOG=$DIR_APP/logs/AltaEstadoCuenta.log

export LANG=en_US.ISO-8859-1

$JAVA_HOME/java -Xms6M -Xmx48M  -Duser.timezone=$TZ -Dlog4j.configuration=log4j-altaEstadoCuenta.xml -Dftp.presea.activo=True -Dftp.presea.produccion=True -classpath $CLASSPATH  clarin.cuentasERP.procesos.alta.AltaEstadoCuenta >> $LOG 2>&1
