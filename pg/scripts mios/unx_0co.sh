#!/usr/bin/sh
$Revision: OBJET tbe_0co.sh VERSION 002 DATE 08/10/2003$
#     -----------------------------------------------------------------------
#                   -=- tbe_0co.sh -=-
#     -----------------------------------------------------------------------
#
#     Description  du module : SCRIPT ASSURANT LA COLLECTE D'UN FICHIER
#                              Dans le cas du SELECT=Last ( toutes les versions du fichier present sur disque
#                              au moment de la collecte seront detruits)
#     Numero de Version      : V 1.0
#
#     -----------------------------------------------------------------------
#     Parametres en entree   : $1 -> NOM DU REPERTOIRE SOURCE   (ex : $UNXEXDATA)
#                              $2 -> NOM DU REPERTOIRE CIBLE    (ex : $UNXTMP   )
#                              $3 -> PRD du fichier en minuscul (ex : bmc       )
#                              $4 -> nnn du FICHIER A COLLECTER (ex : 110       )
#                              $5 -> TYPE DE COLLECTE           (ex ; F, L ou A )
#     Parametres en sortie   :
#                        STD>  0  -> cOLLECTE ok
#                              1  -> Nb de parametres passees different de 5
#                              2  -> Pb dans delete fichier scratch (cas d'une reprise)
#                              3  -> Pb dans delete du fichier de collecte (cas d'une reprise)
#                              4  -> Pas de fichiers a collecter ( normal : exit = 0 )
#                              5  -> Type de collecte different de A F ou L
#                              6  -> Pb dans la creation du fichier de collecte
#                              7  -> Pb dans la creation du fichier scratch
#                              *  -> Pb inconnu
#                        GCR>  0  -> Collecte ok
#                             30 -> Nb de parametres passees different de 5
#                             31  -> Pb dans delete fichier scratch (cas d'une reprise)
#                             32  -> Pb dans delete du fichier de collecte (cas d'une reprise)
#                             33  -> Pas de fichiers a collecter ( normal : exit = 0 )
#                             34  -> Type de collecte different de A F ou L
#                             35  -> Pb dans la creation du fichier de collecte
#                             36  -> Pb dans la creation du fichier scratch
#                              *  -> Pb inconnu
#     Fichiers modifies      : creation du fichier de collecte $2/$3fw$4.tmp
#                              creation d'un fichier scratch $2/$3fw$4.dvf
#     COMMANDE a PASSER      : $TBEEXSCRIPT/tbe_0co.sh + 5 parametres
#              Ex : TBEEXSCRIPT/start $TBEEXSCRIPT/tbe_0co.sh $UNXEXDATA $UNXTMP bmc 110 A
#                    collectera tous les fichiers $UNXEXDATA/bmcfi110.dat.* dans $UNXTMP/bmcfw110.tmp
#                    creera un fichier scratch $UNXTMP/bmcfw110.dvf
#     -----------------------------------------------------------------------
#     Auteur        : JM JAUREGUY
#     Date creation : 25/08/1998
#     ------------------------------------------------------------------------
#     Modifications :
#     Date   : 08/10/03
#     Auteur : R.REYNE
#     Objet  : Mise en place du mode de gestion des codes retour
#
#     Date   : 14/10/03
#     Auteur : Y. MACARTY
#     Objet  : Correction du test sur le controle de la suppression
#              des fichiers scratch et cible
#
#     Date   :
#     Auteur :
#     Objet  :
#     ------------------------------------------------------------------------
###########################################################################
# Positionnement des variables de TBE : obligatoire au debut de chaque script de TBE
###########################################################################
PRD_VAR=`echo $UNXPRDAPPLI | tr "[a-z]" "[A-Z]"`
if [ "$PRD_VAR" != "TBE" ]
then
  . $TBEEXSCRIPT/tbe_0en.sh
fi
###########################################################################
# Appel de la librairie de fonctions communes aux scripts
###########################################################################
. $TBEEXSCRIPT/tbe_0lib.sh
###################################################################
# Debut du traitement
###################################################################
RETOUR=0
JOB="tbe_0co.sh"
UNXJOB="tbe_0co"

#######################################################################
# Ecriture de la trace de debut dans la table de surveillance
#######################################################################
sh $TBEEXSCRIPT/tbe_0em.sh "$UNXMSROUTER." "$UNXMSDRV_MENU." $UNXJOB DEB0001 $JOB

###################################################################
# Verification des parametres passes en argument
###################################################################
if [ $RETOUR = 0 ]
   then
     if [ $# != 5 ]
        then
         ### Le nombre de parametres fournis par l application  est different de 5
         RETOUR=1
         TBECR=30
        fi
fi
########################################################################
# Recuperation  des parametres passes en argument + init fichier srcatch
########################################################################
if [ $RETOUR = 0 ]
   then
      # RECUPERATION DES PARAMETRES
      FICSRC=$1/$3fi$4.dat.001
      FICCIBLE=$2/$3fw$4.tmp
      TYPEC=$5

      #initialisation d'un fichier scratch generant les ordres de delete (qui seront fait en fin de chaine)
      FICSCRATCH=$2/$3fw$4.dvf
fi
#################################################################
# RECHERCHE SI FICHIER SRCATCH EXISTE ( cas du reprise de traitement)
#################################################################
if [ $RETOUR = 0 ]
  then
     ls ${FICSCRATCH} > /dev/null 2>&1
     if [ $? = 0 ]
        then
          ### Fichier scratch  present dans le repertoire cible (on est dans une reprise-->delete du fichier)
          rm $FICSCRATCH
          if [ -f $FICSCRATCH ]
          then
              RETOUR=2
              TBECR=31
          fi
     fi
fi
#################################################################
# RECHERCHE SI FICHIER CIBLE EXISTE ( cas du reprise de traitement)
#################################################################
if [ $RETOUR = 0 ]
  then
     ls ${FICCIBLE} > /dev/null 2>&1
     if [ $? = 0 ]
        then
          ### Fichier  present dans le repertoire cible (on est dans une reprise-->delete du fichier)
          rm $FICCIBLE
          if [ -f $FICCIBLE ]
          then
              RETOUR=3
              TBECR=32
          fi
     fi
fi

echo "TLA : FICSRC = ${FICSRC}"

#################################################################
# RECHERCHE SI FICHIER  A COLLECTER EXISTE
#################################################################
if [ $RETOUR = 0 ]
  then
     ls ${FICSRC} > /dev/null 2>&1
        if [ $? != 0 ]
           then
             ### Pas de fichier  present dans le repertoire $UNXEXDATA
             echo " ERR1 Pas de fichier a collecter !!!  generation d'un fichier vide "
             #touch $FICCIBLE
             touch $FICSCRATCH
             RETOUR=4
             TBECR=33
        else
             # Recherche si un seul fichier sur disque ()
             NBFIC=`ls ${FICSRC} | wc -l`
             echo "${NBFIC} "
             if [ $NBFIC = 2 ]
             then
                 echo " ERR2 Pas de fichier a collecter !!!  generation d'un fichier vide "
                 #touch $FICCIBLE
                 touch $FICSCRATCH
                 RETOUR=4
                 TBECR=33
             fi
        fi
fi
##################################################################
# TEST SUR LE TROISIEME PARAMETRE (TYPE DE COLLECTE ) : F  A  L
##################################################################
if [ $RETOUR = 0 ]
  then
     if [ $TYPEC != "A" -a  $TYPEC != "F" -a  $TYPEC != "L" ]
         then
         ### Le parametre n³3 passe en argument est different de A ou F ou L
         echo " le type de collecte est incorrect ( A L F )"
         RETOUR=5
         TBECR=34
     fi
fi
########################
# TRAITEMENT DES DIFFERENTS CAS DE COLLECTE POSSIBLES
########################
if [ $RETOUR = 0 ]
  then
     ###############
     # CAS DU ALL
     ###############
    if [ $TYPEC = "A" ]
      then
         # Concatenation de tous les fichiers (ALL)
         LISTFIC=`ls ${FICSRC}`
         if [ $? = 0 ]
         then
           NBFIC=`echo "$LISTFIC" | wc -l`
           NUM=0
           NBFIC=2
           NBUTEE=`expr $NBFIC-1`
           while [ $RETOUR = 0 -a $NUM -lt $NBUTEE ]
           do
              POS=`expr $NBFIC - $NUM`
              NFIC=`echo "$LISTFIC" | tail -$POS | head -1`
                 cat $NFIC >> ${FICCIBLE}
                 if [ $? != 0 ]
                    then
                  ### Impossible de copier le fichier $NFIC dans le fichier ${FICCIBLE}
                  echo "ERR3 Impossible de copier le fichier $NFIC dans le fichier ${FICCIBLE}"
                  RETOUR=6
                  TBECR=35
              fi
              echo "${NFIC}" >> $FICSCRATCH
                 if [ $? != 0 ]
                    then
                  ### Impossible de copier le fichier $NFIC dans le fichier ${FICSCRATCH}
                   echo "ERR4 Impossible de copier le fichier $NFIC dans le fichier ${FICSCRATCH}"
                  RETOUR=7
                  TBECR=36
              fi
              NUM=`expr $NUM + 1 `
           done
         fi
    fi
     ########################
     # CAS DU F : FIRST (récupération du permier fichier uniquement, les suivants seront repris par la suite)
     ########################
    if [ $TYPEC = "F" ]
       then
        # FIRST : Recupération du PREMIER FICHIER
           NFIC=`ls -1 ${FICSRC}.[0-9][0-9][0-9] | head -1`
        cp $NFIC $FICCIBLE
        if [ $? != 0 ]
        then
            ### Impossible de copier le fichier $NFIC dans le fichier ${FICCIBLE}
            echo "ERR5 Impossible de copier le fichier $NFIC dans le fichier ${FICCIBLE}"
            RETOUR=6
            TBECR=35
        else
            echo "${NFIC}" >> $FICSCRATCH
               if [ $? != 0 ]
                  then
                ### Impossible de copier le fichier $NFIC dans le fichier ${FICCIBLE}
                echo "ERR6 Impossible de copier le fichier $NFIC dans le fichier ${FICCIBLE}"
                RETOUR=7
                TBECR=36
            fi
        fi
    fi
     ########################
     # CAS DU L : LAST (recuperation du dernier fichier + generation des delete des autres fcihiers )
     ########################
    if [ $TYPEC = "L" ]
       then
        # LAST : Recuperation du DERNIER FICHIER (en fait version (-1) )
        NFIC=`ls ${FICSRC}.[0-9][0-9][0-9] | tail -2 | head -1`
        cp $NFIC $FICCIBLE
        if [ $? != 0 ]
        then
            ### Impossible de copier le fichier $NFIC dans le fichier ${FICCIBLE}
            RETOUR=6
            TBECR=35
        fi

        # Creation d'un fichier scratch pour deleter toutes les versions du fichier
        if [ $RETOUR = 0 ]
        then
          LISTFIC=`ls ${FICSRC}.[0-9][0-9][0-9]`
          if [ $? = 0 ]
          then
            NBFIC=`echo "$LISTFIC" | wc -l`
            NUM=0
            NBUTEE=`expr $NBFIC - 1 `
            while [ $RETOUR = 0 -a $NUM -lt $NBUTEE ]
            do
              POS=`expr $NBFIC - $NUM`
              NFIC=`echo "$LISTFIC" | tail -$POS | head -1`
              echo "${NFIC}" >> $FICSCRATCH
                 if [ $? != 0 ]
                    then
                  ### Impossible de copier le fichier $NFIC dans le fichier ${FICSCRATCH}
                  RETOUR=7
                  TBECR=36
              fi
              NUM=`expr $NUM + 1 `
            done
          fi
        fi
    fi
fi

#################################################################
# Test fin de traitement
#################################################################
case $RETOUR in
  0)
    ret_msg 'sh $TBEEXSCRIPT/tbe_0em.sh "$UNXMSROUTER." N $UNXJOB TXT0000 $JOB $FICSRC'
    ;;
  1)
    ret_msg 'sh $TBEEXSCRIPT/tbe_0em.sh "$UNXMSROUTER." N $UNXJOB TXT0010 $JOB'
    ;;
  2)
    ret_msg 'sh $TBEEXSCRIPT/tbe_0em.sh "$UNXMSROUTER." N $UNXJOB TXT0020 $JOB $FICSCRATCH'
    ;;
  3)
    ret_msg 'sh $TBEEXSCRIPT/tbe_0em.sh "$UNXMSROUTER." N $UNXJOB TXT0030 $JOB $FICCIBLE'
    ;;
  4)
    RETOUR=0
    ret_msg 'sh $TBEEXSCRIPT/tbe_0em.sh "$UNXMSROUTER." N $UNXJOB TXT0040 $JOB $FICSRC'
    ;;
  5)
    ret_msg 'sh $TBEEXSCRIPT/tbe_0em.sh "$UNXMSROUTER." N $UNXJOB TXT0050 $JOB'
    ;;
  6)
    ret_msg 'sh $TBEEXSCRIPT/tbe_0em.sh "$UNXMSROUTER." N $UNXJOB TXT0060 $JOB $FICCIBLE'
    ;;
  7)
    ret_msg 'sh $TBEEXSCRIPT/tbe_0em.sh "$UNXMSROUTER." N $UNXJOB TXT0070 $JOB $FICSCRATCH'
    ;;
  *)
    ret_msg 'sh $TBEEXSCRIPT/tbe_0em.sh "$UNXMSROUTER." N $UNXJOB TXT0100 $JOB'
    ### Code retour inconnu non prevu par l application
    ;;
esac

#######################################################################
# Ecriture de la trace de fin dans la table de surveillance
#######################################################################
sh $TBEEXSCRIPT/tbe_0em.sh "$UNXMSROUTER." "$UNXMSDRV_MENU." $UNXJOB FIN0001 $JOB

ret_exit $RETOUR
