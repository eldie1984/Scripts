#! /bin/bash
#set -x

. /export/home/mo/moar/scripts/set_parametros_comunes.sh

LOG=$DIR_APP/logs/generacionIndices.log

export LANG=en_US.ISO-8859-1

parametros='INDICE_NOMBRE FACTURACION_GLOBAL,FACTURACION_MO,FACTURACION_PS'

$JAVA_HOME/java -Xms64M -Xmx256M -Duser.timezone=$TZ -Dlog4j.configuration=log4j-generacionIndices.xml -classpath $CLASSPATH clarin.procesos.grandesVendedores.GeneracionIndices $parametros >> $LOG 2>&1
