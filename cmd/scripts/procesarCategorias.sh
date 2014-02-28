#! /bin/bash
#set -x

. /export/home/mo/moar/scripts/set_parametros_comunes.sh
 
LOG=/export/home/mo/moar/logs/procesarCategorias.log

LANG=es_AR
LC_COLLATE=en_US.ISO8859-1
export LC_COLLATE
LC_CTYPE=en_US.ISO8859-1
export LC_CTYPE

$JAVA_HOME/java -Xms120M -Xmx120M -classpath $CLASSPATH -Duser.timezone=$TZ -Dlog4j.configuration=log4j-procesarCategorias.xml clarin.categoria.util.ProcesarCategorias > $LOG 2>&1

