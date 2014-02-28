#! /bin/bash
#set -x

. /export/home/mo/moar/scripts/set_parametros_comunes.sh

LOG=$DIR_APP/logs/ReactivarUsuarios.log

echo ":::: Inicio ReactivarUsuarios `date +%d/%m/%Y--%T` -- "  >> $LOG

$JAVA_HOME/java -Xms16M -Xmx512M -Duser.timezone=$TZ -Dlog4j.configuration=log4j-reactivarUsuarios.xml -classpath $CLASSPATH clarin.procesos.suspensiones.ReactivarUsuarios >> $LOG 2>&1

echo ":::: Fin ReactivarUsuarios    `date +%d/%m/%Y--%T` --  "  >> $LOG
echo "====================================================="    >> $LOG
