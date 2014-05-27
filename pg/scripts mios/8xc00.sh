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
#     Date creation : 31/03/2014
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
JOB="${UNXPRDAPPLI}8xc00.sh"
UNXJOB="${UNXPRDAPPLI}8xc00"
SQL="$UNXLOG/${U_LOCALHOSTNAME}_statics.sql"
R_HOST="yvasa880"
R_USER="mwpsyp01"
R_PASS="Sam0la'P"
SQL_FILE=`basename ${SQL}`

#########################################
# Message : DEB0001 Debut de Traitement
#########################################
$TBEEXSCRIPT/start $TBEEXSCRIPT/tbe_0em.sh "$UNXMSROUTER." A $UNXJOB DEB0001 $JOB

#########################################
# Script Body
#########################################
if [[ ! -f $SQL ]]
	then

echo "set time on" >> $SQL
echo "set echo on" >> $SQL
echo "alter session set nls_date_format='dd/mm/yy';" >> $SQL
fi


########################################################################
#### Recoleccion de archivos de tamanio de filesystems  #####
########################################################################
for file in $(ls $UNXLOG/*8x*.csv)
do
	
	for dato in $(cat $file | sed '/^$/d' | sed 's/ /,/g')
	do

		if [[ $(echo $dato | awk -F, '{ print NF }' ) -eq 9 ]]
			then
			HOSTNAME=`echo $dato | awk -F, '{print $1}' `
			DATELOG=`echo $dato | awk -F, '{print $2}' `
			NOMBREFS=`echo $dato | awk -F, '{print $4}' `
			TAMANIO=`echo $dato | awk -F, '{print $5}' `
			USADO=`echo $dato | awk -F, '{print $6}' `
			PUNTO_MONTAJE=`echo $dato | awk -F, '{print $9}' `	
		else
			HOSTNAME=`echo $dato | awk -F, '{print $1}' `
			DATELOG=`echo $dato | awk -F, '{print $2}' `
			NOMBREFS=`echo $dato | awk -F, '{print $3}' `
			TAMANIO=`echo $dato | awk -F, '{print $4}' `
			USADO=`echo $dato | awk -F, '{print $5}' `
			PUNTO_MONTAJE=`echo $dato | awk -F, '{print $8}' `
		fi


echo "insert into FILESYSTEMS (HOST,FECHA,NOMBREFS,TAMANIO,USADO,PUNTO_MONTAJE)" >> $SQL
echo "values" >> $SQL
echo "('$HOSTNAME','$DATELOG','$NOMBREFS',$TAMANIO,$USADO,'$PUNTO_MONTAJE');" >> $SQL
echo "#commit;" >> $SQL
#!
	done

		if [[ -d $UNXEXDATA/ficheros ]]
			then
			mv $file $UNXEXDATA/ficheros/
		else
			mkdir $UNXEXDATA/ficheros
			mv $file $UNXEXDATA/ficheros/
		fi
done


########################################################################
#### Recoleccion de archivos de tablespaces  #####
########################################################################
for file in $(ls $UNXLOG/dbspace*.lst)
do
	for dato in $(cat $file | sed '/^$/d' | sed 's/ $//g' | sed 's/ /,/g'  |sed 's/,,//g')
	do

			FECHA=`echo $dato | awk -F, '{print $1}' `
			HOSTNAME=`echo $dato | awk -F, '{print $3}'`
			NOMBREDB=`echo $dato | awk -F, '{print $4}' `
			NOMBRETB=`echo $dato | awk -F, '{print $5}' `
			NFRAGS=`echo $dato | awk -F, '{print $6}' `
			MXFRAG=`echo $dato | awk -F, '{print $7}' `	
			TOTSIZ=`echo $dato | awk -F, '{print $8}' `	
			TOT_AVA=`echo $dato | awk -F, '{print $9}' `	
			AVALSIZE=`echo $dato | awk -F, '{print $10}' `	
			USUPCD=`echo $dato | awk -F, '{print $11}' `	

echo "INSERT INTO TABLESPACES   ( FECHA,HOST,NOMBREDB,NOMBRETB,NFRAGS,MXFRAG,TOTSIZ,TOT_AVA,AVALSIZE,USUPCD  )" >> $SQL
echo "values" >> $SQL
echo "('$FECHA','$HOSTNAME','$NOMBREDB','$NOMBRETB',$NFRAGS,$MXFRAG,$TOTSIZ,$TOT_AVA,$AVALSIZE,$USUPCD);" >> $SQL
echo "#commit;" >> $SQL

	done

		if [[ -d $UNXEXDATA/ficheros ]]
			then
			mv $file $UNXEXDATA/ficheros/
		else
			mkdir $UNXEXDATA/ficheros
			mv $file $UNXEXDATA/ficheros/
		fi
done


########################################################################
#### Recoleccion de archivos de tamanio de DB  #####
########################################################################
for file in $(ls $UNXLOG/segments*.lst)
do
	for dato in $(cat $file | sed '/^$/d' | sed 's/ $//g' | sed 's/ /,/g'  |sed 's/,,//g')
	do
			DB_FECHA=`echo $dato | awk -F, '{print $1}' `
			DB_HOST=`echo $dato | awk -F, '{print $3}'`
			DB_SID=`echo $dato | awk -F, '{print $4}' `
			DB_TAMANO=`echo $dato | awk -F, '{print $5}' `


echo "INSERT INTO DB_STATS (DB_FECHA, DB_HOST,  DB_SID, DB_TAMANO )" >> $SQL
echo "values" >> $SQL
echo "('$DB_FECHA','$DB_HOST','$DB_SID',$DB_TAMANO);" >> $SQL
echo "#commit;" >> $SQL
#!
	done

		if [[ -d $UNXEXDATA/ficheros ]]
			then
			mv $file $UNXEXDATA/ficheros/
		else
			mkdir $UNXEXDATA/ficheros
			mv $file $UNXEXDATA/ficheros/
		fi
done

${ORACLE_HOME}/bin/sqlplus -s /nolog <<!
conn E438827/E438827@(DESCRIPTION=(ADDRESS=(PROTOCOL=tcp)(HOST=yvasa880)(PORT=1521))(CONNECT_DATA=(SID=DWH)))
@$SQL
exit;
!

# cd $UNXLOG

# ftp -inv ${R_HOST} <<!
# user ${R_USER} ${R_PASS}
# cd /users2/syz00/log
# hash
# bin
# mput ${SQL_FILE}
# by
# !


RET_code=$?

if [[ $RET_code -eq 0 ]]
	then
		rm $SQL
	fi



# #######################################
# # Message : FIN0001 Fin de traitement
# #######################################

$TBEEXSCRIPT/start $TBEEXSCRIPT/tbe_0em.sh "$UNXMSROUTER." A $UNXJOB TXT0001 $JOB
$TBEEXSCRIPT/start $TBEEXSCRIPT/tbe_0em.sh "$UNXMSROUTER." A $UNXJOB FIN0001 $JOB
