#! /bin/bash
#set -x

. /export/home/mo/moar/scripts/set_parametros_comunes.sh

LOG=$DIR_APP/logs/cierreDeLotes.log

export LANG=en_US.ISO-8859-1

$JAVA_HOME/java -Xms64M -Xmx256M -Duser.timezone=$TZ -Dlog4j.configuration=log4j-cierreDeLotes.xml -classpath $CLASSPATH clarin.cuentas.manager.LoteServiceRunner >> $LOG 2>&1
