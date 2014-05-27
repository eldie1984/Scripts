#/bin/bash
# -------------------------------------------------------------------------------
# <<bmk6l010.sh>>
# -------------------------------------------------------------------------------
# Description du module : Check amount of sessions
# -------------------------------------------------------------------------------
#
# Codes de retour       :       0 : OK
#                               1 : Erreur
#
# -------------------------------------------------------------------------------
# Auteur                        : Diego Gasch
# Date création                 : 30/01/2014
# Modification:
#     -----------------------------------------------------------------------
#     Modifications :
#
#     Date   : --------
#     Auteur : ------------------------
#     Objet  : --------------------------------------------------------------
#              --------------------------------------------------------------
#     -----------------------------------------------------------------------
#
# Version: 1.0
# -------------------------------------------------------------------------------
# Programme principal
# -------------------------------------------------------------------------------

JOB="bmk6l010.sh"
UNXJOB="bmk6l010"
RETOUR=0
OUTLOG=$UNXLOG/salida_comparacion.log
DESTMAIL="ldp_indus_grc@mpsa.com"

################################################################################
# Message : DEB0001 Lancement de %s
################################################################################

${TBEEXSCRIPT}/start ${TBEEXSCRIPT}/tbe_0em.sh "${UNXMSROUTER}." A ${UNXJOB} DEB0001 ${JOB}


################################################################################
# Execution de bmk6l010
################################################################################

echo "Se produjeron las siguientes modificaciones en las sesiones"  > $OUTLOG
echo >> $OUTLOG
echo >> $OUTLOG
for i in $(ls -ltr /users/uvs00/exploit/a_vide/*.dat | awk '{print $9}')
do
#diff /users/uvs00/exploit/a_vide/SYZ_UPR.dat /users/uvs00/exploit/a_vide/SYZ_UPR.dat.chk >> $OUTLOG
if [ -f $i.chk ]
then
	if [ $(diff $i $i.chk | wc -l) -ne 0 ]
	then 
		echo $i >> $OUTLOG
		diff $i $i.chk >> $OUTLOG
	fi
else
	cp $i $i.chk
fi
done

if [ $(cat $OUTLOG | wc -l) -ne 3 ]
	then
	cat $OUTLOG | mailx -s "Modificacion de las Sesiones habilitadas" $DESTMAIL
	for i in $(ls -ltr /users/uvs00/exploit/a_vide/*.dat | awk '{print $9}')
	do
	cp $i $i.chk
	done
else 
	echo "Todo OK"
fi


################################################################################
# Test fin de traitement
################################################################################


${TBEEXSCRIPT}/start ${TBEEXSCRIPT}/tbe_0em.sh "${UNXMSROUTER}." A ${UNXJOB} TXT0000 ${JOB}


################################################################################
# Message : FIN0001 Fin du traitement %s
################################################################################

${TBEEXSCRIPT}/start ${TBEEXSCRIPT}/tbe_0em.sh "${UNXMSROUTER}." A ${UNXJOB} FIN0001 ${JOB}
