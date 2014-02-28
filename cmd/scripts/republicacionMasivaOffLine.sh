#! /bin/bash
#set -x

. /export/home/mo/moar/scripts/set_parametros_comunes.sh

LOG=$DIR_APP/logs/republicacionMasivaOffLine.log

export LANG=en_US.ISO-8859-1

$JAVA_HOME/java -Xms64M -Xmx256M -Dlog4j.configuration=log4j-republicacionMasivaOffLine.xml -classpath $CLASSPATH clarin.procesos.republicacionMasiva.RepublicacionMasivaOffLine >> $LOG 2>&1
