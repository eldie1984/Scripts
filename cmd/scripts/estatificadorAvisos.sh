
#set -x



. /export/home/mo/moar/scripts/set_parametros_comunes.sh


#Parametros para Oracle
ORACLE_BASE=/var/opt/oracle
export ORACLE_BASE

ORACLE_HOME=$ORACLE_BASE/product/10.2.0/client_1
export ORACLE_HOME

LD_LIBRARY_PATH=$ORACLE_HOME/lib
export LD_LIBRARY_PATH

PATH=$ORACLE_HOME/bin:$PATH
export PATH

TNS_ADMIN=$ORACLE_HOME/network/admin
NLS_LANG=AMERICAN_AMERICA.WE8ISO8859P1
ORA_NLS33=$ORACLE_HOME/ocommon/nls/admin/data


LOG=$DIR_APP/logs/EstatificadorAvisos.log

LANG=es_AR

echo `date +%Y.%m.%d-%H:%M` "INICIO DE PROCESO"

$JAVA_HOME/java -Xms12M -Xmx24M -Duser.timezone=$TZ -Dlog4j.configuration=log4j-estatificadorAvisos.xml -classpath $CLASSPATH clarin.procesos.archivador.EstatificadorAvisos $DIR_APP/java_libs/archivador.properties >> $LOG 2>&1


