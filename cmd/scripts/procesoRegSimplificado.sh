#!/bin/ksh
#set -x

. /export/home/mo/moar/scripts/set_parametros_comunes.sh

LOG=$DIR_APP/logs/procesoRegSimplificado.log

LANG=es_AR
echo `date +%Y.%m.%d-%H:%M` "INICIO DE PROCESO"
$JAVA_HOME/java -Xms128M -Xmx1024M -Dlog4j.configuration=log4j-procesoRegSimplificado.xml -classpath $CLASSPATH  clarin.datosFacturacion.service.ProcesoPadronRegimenSimplificadoRunner >> $LOG 2>&1
echo `date +%Y.%m.%d-%H:%M` "FIN DE PROCESO"

