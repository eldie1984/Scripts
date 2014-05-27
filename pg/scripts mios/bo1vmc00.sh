bo1vmc00.sh
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


yvai0150:BO10PRE:/users/bo100/exploit/script>more UNXEXSCRIPT/start_tbe
UNXEXSCRIPT/start_tbe: A file or directory in the path name does not exist.
yvai0150:BO10PRE:/users/bo100/exploit/script>more $UNXEXSCRIPT/start_tbe
#!/bin/sh
$Revision: OBJET start VERSION 001 DATE 01/07/2003$
# -------------------------------------------
# Shell de lancement de commande  pour indus
# -------------------------------------------
UTLTR=/users                                    ;export UTLTR
APPLI=$UTLTR/tbe00                              ;export APPLI
TBERECEPT=$APPLI/recept                         ;export TBERECEPT
TBEEXPLOIT=$APPLI/exploit                       ;export TBEEXPLOIT
TBEEXSQL=$APPLI/exploit/sql                     ;export TBEEXSQL
TBEEXSQLINI=$APPLI/exploit/sql/ini              ;export TBEEXSQLINI
TBEEXSCRIPT=$APPLI/exploit/script               ;export TBEEXSCRIPT
TBEEXDATA=$APPLI/exploit/data                   ;export TBEEXDATA
TBEEXBIN=$APPLI/exploit/bin                     ;export TBEEXBIN
TBEEXDOC=$APPLI/exploit/doc                     ;export TBEEXDOC
TBEEXCRON=$APPLI/exploit/cron                   ;export TBEEXCRON
TBEEXINST=$APPLI/exploit/install                ;export TBEEXINST
TBEEXSQL=$APPLI/exploit/sql                     ;export TBEEXSQL
TBEDOC=$APPLI/doc                               ;export TBEDOC
TBEBIN=$APPLI/bin                               ;export TBEBIN
TBELOG=$APPLI/log                               ;export TBELOG
TBESCRIPT=$APPLI/script                         ;export TBESCRIPT
TBETMP=$APPLI/tmp                               ;export TBETMP
UNXMSDRV_EXSCRIPT=$APPLI/exploit/script         ;export UNXMSDRV_EXSCRIPT
UNXMSDRV_EXBIN=$APPLI/exploit/bin               ;export UNXMSDRV_EXBIN
# Les deux variables suivantes ne doivent pas etre presentes dans le start !!!!
#UNXMSDRV_EXDATA=$APPLI/exploit/data             ;export UNXMSDRV_EXDATA
#UNXPRDAPPLI=tbe                                 ;export UNXPRDAPPLI
# PATH=/bin:/usr/bin:/usr/local/bin             ;export PATH
#
# -------------------------------------------
#  Lancement de la ligne de commande
# -------------------------------------------
if [ $# -eq 0 ]
then
      env
else
      exec "$@"
fi
