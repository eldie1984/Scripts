#! /bin/bash
#set -x

. /export/home/mo/moar/scripts/set_parametros_comunes.sh

LOG=$DIR_APP/logs/ActualizarTelefonosInternetToPhone.log

LANG=es_AR

# 22-09-2010 - Fernando: Cambio -Xmx120M   por  -Xmx300M
$JAVA_HOME/java -Xms120M -Xmx512M -Duser.timezone=$TZ -Dlog4j.configuration=log4j-actualizarTelefonosInternetToPhone.xml -classpath $CLASSPATH clarin.net2phone.process.ActualizarTelefonosInternetToPhone >> $LOG 2>&1

