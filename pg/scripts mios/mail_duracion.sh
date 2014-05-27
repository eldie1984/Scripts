#!/bin/ksh
#set -x
#     -----------------------------------------------------------------------
#                   -=-  ${UNXPRDAPPLI}8xc00.sh -=-
#     -----------------------------------------------------------------------
#
#     Description  du module : Colecta de archivos
#     Numero de Version      : V1.0
#
#     -----------------------------------------------------------------------
#     Parametres en entree   :
#     Parametres en sortie   :
#     Fichiers modifies      :
#     -----------------------------------------------------------------------
#     Auteur        : Diego Gasch
#     Date creation : 05/05/2014
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
RETOUR=0
JOB="${UNXPRDAPPLI}8c010.sh"
UNXJOB="${UNXPRDAPPLI}8c010"
DATE=`date '+%d/%m/%Y,%H00'`
HOSTNAME=`hostname`
SQL="$UNXLOG/${U_LOCALHOSTNAME}_check.sql"
HOST_ID=`${ORACLE_HOME}/bin/sqlplus -s /nolog <<!
conn E438827/E438827@(DESCRIPTION=(ADDRESS=(PROTOCOL=tcp)(HOST=yvasa880)(PORT=1521))(CONNECT_DATA=(SID=DWH)))
set head off
  set feedback off
  set pagesize 5000
  set linesize 30000
select id from userbk where host='$HOSTNAME';
exit;
!
`
HOST_ID=$(echo $HOST_ID |sed '/^$/d' )

if [[ "k$HOST_ID" == "k" ]]
then
echo "No esta declarado el host en la tabla USR BKP"
exit 1
fi

List="diego.gasch@ext.mpsa.com"
#List="horacioluis.demarco@mpsa.com,andres.toth@mpsa.com,ldp_indus_grc@mpsa.com"
#########################################
# Message : DEB0001 Debut de Traitement
#########################################
$TBEEXSCRIPT/start $TBEEXSCRIPT/tbe_0em.sh "$UNXMSROUTER." A $UNXJOB DEB0001 $JOB

#########################################
# Script Body
#########################################

init () {
if [[ ! -f $SQL ]]
        then

echo "set time on" >> $SQL
echo "set echo on" >> $SQL
echo "alter session set nls_date_format='dd/mm/yyyy HH24:MI:SS';" >> $SQL
echo "delete from EJECUCIONES where host = $HOST_ID;" >> $SQL
fi
}

Migrar_datos() {
for dato in $(cat $UNXEXOUTILS/INDUS/log/batch_ctl.log | sed 's/ /,/g')
do
#HOST=`echo $dato | awk -F, '{print $9}' `
SESION=`echo $dato | awk -F, '{print $1}' `
UPROC=`echo $dato | awk -F, '{print $2}' `
MU=`echo $dato | awk -F, '{print $3}' `
INICIO=`echo $dato | awk -F, '{print $5}' `
INICIO_HR=`echo $dato | awk -F, '{print $6}' `
FIN=`echo $dato | awk -F, '{print $7}' `
FIN_HR=`echo $dato | awk -F, '{print $8}' `
ESTADO=`echo $dato | awk -F, '{print $4}' `


echo "INSERT INTO EJECUCIONES  ( HOST, SESION, UPROC,  MU,  INICIO,  FIN,  ESTADO  )" >> $SQL
#echo "values" >> $SQL
echo "select '$HOST_ID','$SESION','$UPROC','$MU','$INICIO $INICIO_HR','$FIN $FIN_HR',ID from ESTADOS where ESTADO='$ESTADO';" >> $SQL
#echo " commit;" >> $SQL

done
cat $SQL
}

copiar_a_db() {
${ORACLE_HOME}/bin/sqlplus -s /nolog <<!
conn E438827/E438827@(DESCRIPTION=(ADDRESS=(PROTOCOL=tcp)(HOST=yvasa880)(PORT=1521))(CONNECT_DATA=(SID=DWH)))
@$SQL
commit;
exit;
!
}

enviar_mail() {

exec > $UNXLOG/prueba.html 2>&1


echo "Subject: Ejecucion batch PEC $(hostname)"

echo "Content-type: text/html"
echo
echo "<HTML>"
echo "Buenos d&#237as </br>
Les paso los tiempos de ejecuci&#243;n del batch de PEC </br>
"
echo "Ejecución del backup de la bbdd  </br> </br>"
echo "<CENTER>"
${ORACLE_HOME}/bin/sqlplus -s /nolog <<!
conn E438827/E438827@(DESCRIPTION=(ADDRESS=(PROTOCOL=tcp)(HOST=yvasa880)(PORT=1521))(CONNECT_DATA=(SID=DWH)))
set pages 1000 trim on head on feed off;
set mark html on head "bgcolor=#CCCCCC" table "bgcolor=#8EB4E6 border=1";
alter session set nls_date_format = 'dd/mm/yyyy HH24:MI:SS';
select sesion,e.estado,min(inicio) as inicio,max(fin) as fin, to_char(to_date('00:00:00','HH24:MI:SS') +  (max(fin) - min(inicio)), 'HH24:MI:SS') as duracion
from ejecuciones ej , estados e
where sesion not in (select sesion from ejecuciones where estado <> 3)
and host= 19
and ej.estado=e.id
group by sesion, e.estado
union
select sesion,e.estado,min(inicio) as inicio,min(inicio) as fin,to_char(to_date('00:00:00','HH24:MI:SS') +  (min(inicio) - min(inicio)), 'HH24:MI:SS') as duracion from
ejecuciones ej , estados e
where ej.estado <> 3
and host= 19
and ej.estado=e.id
group by sesion, e.estado
order by 3
;
quit
!

echo "</CENTER><BR>"

echo "Ejecución del batch  </br> </br>"

${ORACLE_HOME}/bin/sqlplus -s /nolog <<!
conn E438827/E438827@(DESCRIPTION=(ADDRESS=(PROTOCOL=tcp)(HOST=yvasa880)(PORT=1521))(CONNECT_DATA=(SID=DWH)))
set pages 1000 trim on head on feed off;
set mark html on head "bgcolor=#CCCCCC" table "bgcolor=#8EB4E6 border=1";
alter session set nls_date_format = 'dd/mm/yyyy HH24:MI:SS';
select sesion,e.estado,min(inicio) as inicio,max(fin) as fin, to_char(to_date('00:00:00','HH24:MI:SS') +  (max(fin) - min(inicio)), 'HH24:MI:SS') as duracion
from ejecuciones ej , estados e
where sesion not in (select sesion from ejecuciones where estado <> 3 )
and host= $HOST_ID
and ej.estado=e.id
group by sesion, e.estado
union
select sesion,e.estado,min(inicio) as inicio,min(inicio) as fin,to_char(to_date('00:00:00','HH24:MI:SS') +  (min(inicio) - min(inicio)), 'HH24:MI:SS') as duracion from
ejecuciones ej , estados e
where ej.estado <> 3
and ej.estado=e.id
and host= $HOST_ID
group by sesion, e.estado
order by 3
;
quit
!

echo "</HTML>"


cat $UNXLOG/prueba.html |sendmail -F 'GRCIndus - BFGRCIn' -f 'grcindus@mpsa.com' $List
rm $UNXLOG/prueba.html
}

historizar() {
QUERY=" alter session set nls_date_format='dd/mm/yyyy HH24:MI:SS';
insert into Dueracion_batch
  (select sesion,estado,min(inicio) as inicio,max(fin) as fin, to_char(to_date('00:00:00','HH24:MI:SS') +  (max(fin) - min(inicio)), 'HH24:MI:SS') as duracion
from ejecuciones
where sesion not in (select sesion from ejecuciones where estado <> 3)
and host= $HOST_ID
group by sesion, estado);
insert into Dueracion_batch
(select sesion,estado,min(inicio) as inicio,min(inicio) as fin,to_char(to_date('00:00:00','HH24:MI:SS') +  (min(inicio) - min(inicio)), 'HH24:MI:SS') as duracion from
ejecuciones
where estado <> 3
and host= $HOST_ID
group by sesion, estado);"

${ORACLE_HOME}/bin/sqlplus -s /nolog <<!
conn E438827/E438827@(DESCRIPTION=(ADDRESS=(PROTOCOL=tcp)(HOST=yvasa880)(PORT=1521))(CONNECT_DATA=(SID=DWH)))
@$QUERY
exit;
!

}


if [ "$1" != "" ]
then
DATE=$1
fi

if [ "$2" != "test" ]
then
for i in `perl -e 'print join(" ", 1..144)'`
do
date
echo $UNXEXOUTILS/INDUS/script/batch_ctl.sh ALL \"$DATE\"
$UNXEXOUTILS/INDUS/script/batch_ctl.sh ALL \"$DATE\"
init
Migrar_datos
copiar_a_db

RET_code=$?

if [[ $RET_code -eq 0 ]]
        then
                rm $SQL
        fi
sleep 300
done

enviar_mail

historizar

else
echo $UNXEXOUTILS/INDUS/script/batch_ctl.sh ALL \"$DATE\"
$UNXEXOUTILS/INDUS/script/batch_ctl.sh ALL \"$DATE\"
init
Migrar_datos
copiar_a_db
RET_code=$?

if [[ $RET_code -eq 0 ]]
        then
                rm $SQL
        fi

#enviar_mail

historizar

fi

########################################
## Message : FIN0001 Fin de traitement
########################################

$TBEEXSCRIPT/start $TBEEXSCRIPT/tbe_0em.sh "$UNXMSROUTER." A $UNXJOB TXT0001 $JOB
$TBEEXSCRIPT/start $TBEEXSCRIPT/tbe_0em.sh "$UNXMSROUTER." A $UNXJOB FIN0001 $JOB
