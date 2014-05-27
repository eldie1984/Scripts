#!/bin/ksh
#set -x
#     -----------------------------------------------------------------------
#                   -=-   -=-
#     -----------------------------------------------------------------------
#
#     Description  du module : Colecta de estadisticas
#     Numero de Version      : V1.0
#
#     -----------------------------------------------------------------------
#     Parametres en entree   :
#     Parametres en sortie   :
#     Fichiers modifies      :
#     -----------------------------------------------------------------------
#     Auteur        : Diego Gasch
#     Date creation : 16/04/2014
#     ------------------------------------------------------------------------
#
#     Modifications :
#
#     Date   :
#     Auteur :
#     Objet  :
#
#     ------------------------------------------------------------------------
##########################################
# Declaration des variables du traitement
##########################################
DATE=`date '+%d/%m/%y %H:%M'`
DATE_LOG=`date '+%d%m%y'`
HOSTNAME=`hostname`
FICLOG=$UNXLOG/host_stats_$DATE_LOG.lst
SQL=$UNXLOG/$HOSTNAME_host.sql
UNXLOG='/users2/syz00/log'
ORACLE_HOME='/soft/ora1020'
export ORACLE_HOME

if [[ ! -f $FICLOG ]]
then
	touch $FICLOG
fi

if [[ ! -f $SQL ]]
then
	echo "set time on" >> $SQL
	echo "set echo on" >> $SQL
	echo "alter session set nls_date_format='dd/mm/yy HH24:MI';" >> $SQL
fi



if [[ $(uname) == 'SunOS' ]]
then
	mem=`prstat -Z -n1,2 1 1 | tail -2 | head -1  | awk '{print $5}' | sed 's/%//g'`
	salida=`vmstat 1 2 | tail -1 | awk '{print $20,$21}'`
	usr=`echo $salida | awk '{print $1}'`
	sys=`echo $salida | awk '{print $1}'`
fi

echo $HOSTNAME,$DATE,$mem,$usr,$sys >> $FICLOG

echo "INSERT INTO HOST_STATS (FECHA, HOST, CPU_USR, MEMORIA, CPU_SYS  )" >> $SQL
echo "values" >> $SQL
echo "('$DATE','$HOSTNAME',$usr,$mem,$sys);" >> $SQL



${ORACLE_HOME}/bin/sqlplus -s /nolog <<!
conn E438827/E438827@(DESCRIPTION=(ADDRESS=(PROTOCOL=tcp)(HOST=yvasa880)(PORT=1521))(CONNECT_DATA=(SID=DWH)))
@$SQL
exit;
!

RET_code=$?

if [[ $RET_code -eq 0 ]]
then
	rm $SQL
	mv $FICLOG $UNXEXDATA/ficheros
fi