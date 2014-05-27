#!/bin/ksh
#set -x
#     -----------------------------------------------------------------------
#                   -=-  ${UNXPRDAPPLI}8x030.sh -=-
#     -----------------------------------------------------------------------
#
#     Description  du module : Check FS occupation
#     Numero de Version      : V1.0
#
#     -----------------------------------------------------------------------
#     Parametres en entree   :
#     Parametres en sortie   :
#     Fichiers modifies      :
#     -----------------------------------------------------------------------
#     Auteur        : Diego Gasch
#     Date creation : 20/02/2014
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
JOB=${UNXPRDAPPLI}8x030.sh
UNXJOB=${UNXPRDAPPLI}8x030
HOSTNAME=`hostname`
DATE=`date '+%d/%m/%y'`
DATELOG=`date '+%d%m%y'`


#########################################
# Message : DEB0001 Debut de Traitement
#########################################
$TBEEXSCRIPT/start $TBEEXSCRIPT/tbe_0em.sh "$UNXMSROUTER." A $UNXJOB DEB0001 $JOB

#########################################
# Script Body
#########################################


if [ ! -f $UNXLOG/$UNXJOB"_"$DATELOG.csv ]
then
        if [[ $(uname) == 'AIX' ]]
        then
        	for i in $(df -k  | grep -v Free | grep -v "/proc"|  awk '{print $1","$2","$3","$4",,"$7}'| sed 's/%//g')
        	do
                NOMBREFS=`echo $i | awk -F, '{print $1}' `
				TAMANIO=`echo $i | awk -F, '{print $2}' `
				res=`echo $i | awk -F, '{print $4}'| sed 's/%//g'`
				USADO=$(( res * TAMANIO / 100))
				PUNTO_MONTAJE=`echo $i | awk -F, '{print $6}' `

                echo $HOSTNAME,$DATE,$NOMBREFS,$TAMANIO,$USADO,,,$PUNTO_MONTAJE >> $UNXLOG/$UNXJOB"_"$DATELOG.csv
        	done

        else
        	for i in $(df -k  | grep -v kbytes |  awk '{print $1","$2","$3","$4","$5","$6}')
        	do
                echo $HOSTNAME,$DATE,$i >> $UNXLOG/$UNXJOB"_"$DATELOG.csv
        	done
        fi
fi

#######################################
# Message : FIN0001 Fin de traitement
#######################################

$TBEEXSCRIPT/start $TBEEXSCRIPT/tbe_0em.sh "$UNXMSROUTER." A $UNXJOB TXT0001 $JOB
$TBEEXSCRIPT/start $TBEEXSCRIPT/tbe_0em.sh "$UNXMSROUTER." A $UNXJOB FIN0001 $JOB