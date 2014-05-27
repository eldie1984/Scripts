
#!/bin/sh
#     -----------------------------------------------------------------------
#                   -=- bo1vmc00.sh -=-
#     -----------------------------------------------------------------------
#
#     Description  du module : Collecte application bo1vm.
#     Numero de Version      : 1.0
#     Modele de collecte     : $Revision: 1.0 $
#
#     -----------------------------------------------------------------------
#     Parametres en entree   : aucun
#     Parametres en sortie   : Code retour :
#                          0 :  Ok
#                          1 :  nok
#     -----------------------------------------------------------------------
#     Auteur        : Raphael BRINON
#     Date creation : 20-02-2007
#     ------------------------------------------------------------------------
#
#     Modifications :
#
#
#     Date   : 25/09/2008
#     Auteur : C.JAGU
#     Objet  : Ajout 2 nouveaux Flux Oracle et 2 Flux DB2
#
#     -----------------------------------------------------------------------
#
JOB="bo1vmc00.sh"
UNXJOB="bo1vmc00"
RETOUR=0

LIST_FIC="ccs cmo cac ccl ccd crc cen cct cga cpt ccx cop cpv cpa cit cve cma cdc cif cda cli cvs ccv cau pcs pmo pac pcl pcd prc pen pct pga ppt pcx pop ppv ppa pit
pve pma pdc pif pda pli pvs pcv pau"
#
###################################
# Message : DEB0001 Lancement de %s
###################################

$TBEEXSCRIPT/start $TBEEXSCRIPT/tbe_0em.sh "$UNXMSROUTER." A $UNXJOB DEB0001 $JOB

####################################################
# Liste des fichiers a collecter dans $UNXDATA
####################################################
cd $UNXDATA
echo "Liste des fichiers CUSCO-DB2 a collecter sous $UNXDATA :"
ls -liart bo1fic*001 2>/dev/null
echo ""
echo "Liste des fichiers CUSCO-ORACLE a collecter sous $UNXDATA :"
ls -liart bo1fip*001 2>/dev/null
echo ""

############################################
#  Collecte des fichiers de FIC
############################################
for SUF_FIC in $LIST_FIC
  do
  FICI=$UNXDATA/bo1fi${SUF_FIC}.dat
  if [ $RETOUR = 0 ]
  then
    $UNXEXSCRIPT/start_tbe $UNXEXSCRIPT/unx_0co.sh $UNXDATA $UNXDATA/tmp bo1 ${SUF_FIC} A
    RETOUR=$?
    if [ $RETOUR != 0 ]
    then
      RETOUR=1
    fi
  fi
  done

########################################################
# Liste des fichiers apres collecte dans $UNXDATA/tmp
########################################################
cd $UNXDATA/tmp
echo ""
echo "Liste des fichiers collecte pour CUSCO-DB2 sous $UNXDATA/tmp :"
ls -liart bo1fwc*.tmp 2>/dev/null
echo ""
echo "Liste des fichiers collecte pour CUSCO-ORACLE sous $UNXDATA/tmp :"
ls -liart bo1fwp*.tmp 2>/dev/null
echo ""


#################################################################
# Test fin de traitement
#################################################################

case $RETOUR in
  0)
    #  INFO  : Traitement ok
     $TBEEXSCRIPT/start $TBEEXSCRIPT/tbe_0em.sh "$UNXMSROUTER." N $UNXJOB TXT0000 $JOB
    ;;
  1)
    #  ERR : pb dans la collecte du fichier
     $TBEEXSCRIPT/start $TBEEXSCRIPT/tbe_0em.sh "$UNXMSROUTER." N $UNXJOB TXT0010 $JOB $FICI
    ;;
  *)
    #  INFO  : Anomalie dans le traitement
     $TBEEXSCRIPT/start $TBEEXSCRIPT/tbe_0em.sh "$UNXMSROUTER." N $UNXJOB TXT9999 $JOB
    ### Code retour inconnu non prevu par l application

    ;;
esac


###################################
# Message : FIN0001 Fin de traitement %s a %s
###################################
$TBEEXSCRIPT/start $TBEEXSCRIPT/tbe_0em.sh "$UNXMSROUTER." A $UNXJOB FIN0001  $JOB

exit $RETOUR
