#! /bin/bash
#set -x

. /export/home/mo/moar/scripts/set_parametros_comunes.sh

LOG=$DIR_APP/logs/ValidarPagosNPS.log

LANG=es_AR

echo "> procesando ValidarPagosNPS - `date +%d/%m/%Y--%T`"
echo ":::: Inicio proceso ValidarPagosNPS `date +%d/%m/%Y--%T`"  >> $LOG
$JAVA_HOME/java -Xms6M -Xmx48M -classpath $CLASSPATH -Dclarin.log4j.LOCAL=True clarin.procesos.nps.ValidarPagosNPS >> $LOG 2>&1
echo ":::: Fin proceso ValidarPagosNPS `date +%d/%m/%Y--%T`"  >> $LOG

# FIN
