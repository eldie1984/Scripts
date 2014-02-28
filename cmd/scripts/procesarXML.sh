#! /bin/bash
#set -x

. /export/home/mo/moar/scripts/set_parametros_comunes.sh
 
LOG=/export/home/mo/moar/logs/ProcesarXML.log

LANG=es_AR

if [ $LOGNAME = "sunadm" ]; then
$JAVA_HOME/java -Xms12M -Xmx24M -Duser.timezone=$TZ -classpath $CLASSPATH -Xms512m -Xmx1024m -Dlog4j.configuration=log4j-procesarXML.xml clarin.procesos.grandesVendedores.ProcesarXML >> $LOG 2>&1
   else
       echo "*****************************"
       echo "No estas logueado como sunadm"
       echo "*****************************"
fi
# FIN sh
