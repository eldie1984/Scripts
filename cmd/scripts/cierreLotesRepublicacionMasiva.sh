#! /bin/bash
#set -x

. /export/home/mo/moar/scripts/set_parametros_comunes.sh

LOG=$DIR_APP/logs/cierreLotesRepublicacionMasiva.log

export LANG=en_US.ISO-8859-1

$JAVA_HOME/java -Xms64M -Xmx256M -Dlog4j.configuration=log4j-cierreLotesRepublicacionMasiva.xml -classpath $CLASSPATH clarin.procesos.republicacionMasiva.CierreLotesRepublicacionExpirados >> $LOG 2>&1
