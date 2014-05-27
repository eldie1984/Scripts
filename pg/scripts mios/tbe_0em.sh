#!/bin/ksh
#$Revision: OBJET tbe_0em.sh VERSION 014 DATE 04/05/2009$
#     -----------------------------------------------------------------------
#                   -=- tbe_0em.sh -=-
#     -----------------------------------------------------------------------
#
#     Description  du module : Envoi d un message formate par l API
#                              de SLBMSDRV version 3.0
#     Numero de Version      : 1.0
#
#     -----------------------------------------------------------------------
#     Parametres en entree   :
#                              $1 = r si renvoi fifo, autre chose sinon
#                              $2 = e si ecriture table, s si non formatage msg,
#                                   autre chose sinon
#                              $3 = module physique
#                              $4 = numero de message
#                              $5 a $x : parametres du texte
#     Parametres en sortie   :
#                              stdout : la ligne message formatee
#                                       ou le message d erreur
#                              return code : 0 : OK, 1 : Non OK
#     Fichiers modifies      : Eventuellement fichier FIFO Routeur
#     -----------------------------------------------------------------------
#     Auteur        : Herve JANOD
#     Date creation : 09/06/94
#     ------------------------------------------------------------------------
#
#     Modifications :
#
#     Date   : 04/04/95
#     Auteur : Herve JANOD
#     Objet  : Utilisation des nouvelles variables UNXMSDRV_* pointant sur les
#              repertoires.
#
#     Date   : 06/11/95
#     Auteur : Herve JANOD...
#     Objet  : Nouvelle version avec utilisation nouvelles options de slblpmss
#
#     Date   : 29/01/2002
#     Auteur : Herve JANOD...
#     Objet  : Ne pas renvoyer les messages type Info a Tivoli
#
#     Date   : 18/10/2002
#     Auteur : Christian COQUET
#     Objet  : Modification du test d envoi dans le FIFO
#
#     Date   : 28/01/2003
#     Auteur : Raphael REYNE
#     Objet  : Fiabilisation du test sur la variable VERSION_MSS
#
#     Date   : 15/11/2004
#     Auteur : Raphael REYNE / Michel WOZNIAK
#     Objet  : Affichage du message d erreur de l API en cas de probleme
#
#     Date   : 01/07/2006
#     Auteur : Isabelle Barbier
#     Objet  : prise en compte du format de message V2 pour OSP
#
#     Date   : 28/12/2006
#     Auteur : Isabelle Barbier
#     Objet  : correctif pour prise en compte des messages de debrayage/embrayge
#               Ils n ont pas été pris en compte avec le format V2 qui ne
#               sait traiter que les message E/A/T/W, hors ce sont des
#               messages de type I. Envoi de ces messages au format V1
#               en attendant decision AUTO/UNIX
#
#     Date   : 10/01/2007
#     Auteur : Isabelle Barbier
#     Objet  : correctif pour prise en compte des messages de debrayage/embrayge
#               au format V2 au meme titre que les TAI
#
#     Date   : 03/04/2007
#     Auteur : Isabelle Barbier
#     Objet  : correctif pour prise en compte de libelles de messages
#               qui contiennent des caracteres "*" ex : *.log
#               modification echo $TEXTE per echo "$TEXTE"
#
#     Date   : 21/11/2007
#     Auteur : Isabelle Barbier
#     Objet  : test du nombre de ligne retourne par le module slblpmss car
#               il en retourne 2 lorsque le message n est pas trouve dans
#               la base message. Si 2 lignes retournees, on ne traite que
#               la premiere car c est une ligne d erreur (la 2eme est en I)
#
#     Date   : xx/xx/xxxx
#     Auteur : Isabelle Barbier
#     Objet  : misea jour numero de version
#
#     Date   : 15/04/2008
#     Auteur : Isabelle Barbier
#     Objet  : suppression du translate en majuscule de la variable UNXMSDRV_SYSID
#
#     -----------------------------------------------------------------------
#####################################################################
# Test presence variable environnement UNXMSDRV_LANGUE
#####################################################################
if [ "x$UNXMSDRV_LANGUE" = "x" ]
  then
    echo "La variable d environnement UNXMSDRV_LANGUE n est pas definie."
    echo "Avez-vous lance ce shell depuis le programme start ?"
    echo "\nArret ...."
    exit 1
fi
#####################################################################
# Test presence variable environnement UNXMSDRV_APPLICATION
#####################################################################
if [ "x$UNXMSDRV_APPLICATION" = "x" ]
  then
    echo "La variable d environnement UNXMSDRV_APPLICATION n est pas definie."
    echo "Avez-vous lance ce shell depuis le programme start ?"
    echo "\nArret ...."
    exit 1
fi
#####################################################################
# Test presence variable environnement UNXMSDRV_MODULE
#####################################################################
if [ "x$UNXMSDRV_MODULE" = "x" ]
  then
    echo "La variable d environnement UNXMSDRV_MODULE n est pas definie."
    echo "Avez-vous lance ce shell depuis le programme start ?"
    echo "\nArret ...."
    exit 1
fi
#####################################################################
# Test presence au moins 4 parametres
#####################################################################
if [ $# -lt 4 ]
  then
    echo "Vous n avez pas passe au moins 4 parametres."
    echo "parametre passes : $@"
    echo "\nArret ...."
    exit 1
fi
#####################################################################
# Test presence variable environnement UNXMSDRV_EXDATA
#####################################################################
if [ "x$UNXMSDRV_EXDATA" = "x" ]
  then
    echo "La variable d environnement UNXMSDRV_EXDATA n est pas definie."
    echo "Avez-vous lance ce shell depuis le programme start ?"
    echo "\nArret ...."
    exit 1
fi
#####################################################################
# Test presence variable environnement UNXMSDRV_EXBIN
#####################################################################
if [ "x$UNXMSDRV_EXBIN" = "x" ]
  then
    echo "La variable d environnement UNXMSDRV_EXBIN n est pas definie."
    echo "Avez-vous lance ce shell depuis le programme start ?"
    echo "\nArret ...."
    exit 1
fi
#####################################################################
# Test presence variable environnement UNXMSDRV_EXSCRIPT
#####################################################################
if [ "x$UNXMSDRV_EXSCRIPT" = "x" ]
  then
    echo "La variable d environnement UNXMSDRV_EXSCRIPT n est pas definie."
    echo "Avez-vous lance ce shell depuis le programme start ?"
    echo "\nArret ...."
    exit 1
fi
#####################################################################
# Test presence variable environnement UNXMSDRV_TMP
#####################################################################
if [ "x$UNXMSDRV_TMP" = "x" ]
  then
    echo "La variable d environnement UNXMSDRV_TMP n est pas definie."
    echo "Avez-vous lance ce shell depuis le programme start ?"
    echo "\nArret ...."
    exit 1
fi
#####################################################################
# Test presence variable environnement UNXMSDRV_SURV
#####################################################################
if [ "x$UNXMSDRV_SURV" = "x02" ]
  then
# message de type surveillance V2 donc verification des variables neccessaires
    if [ "x$UNXMSDRV_TYPE_MES" = "x" ]
        then
                UNXMSDRV_TYPE_MES="APP"
        else
                UNXMSDRV_TYPE_MES=`echo $UNXMSDRV_TYPE_MES | cut -c1-3 | tr '[a-z]' '[A-Z]'`
    fi
    if [ "x$UNXMSDRV_TYPE_MES" = "xAPP" ]
       then
           if [ "x$UNXMSDRV_PRD_GENE" = "x" ]
                then
                        echo "La variable d environnement UNXMSDRV_PRD_GEN n est pas definie."
                        echo "elle est obligatoire pour un message APP de type V2"
                        echo "\nArret ...."
                        exit 1
                else
                        UNXMSDRV_PRD_GENE=`echo $UNXMSDRV_PRD_GENE | cut -c1-5 | tr '[a-z]' '[A-Z]'`
           fi
           if [ "x$UNXMSDRV_PRD_OCC" = "x" ]
                then
                        UNXMSDRV_PRD_OCC="$UNXMSDRV_PRD_GENE"
                else
                        UNXMSDRV_PRD_OCC=`echo $UNXMSDRV_PRD_OCC | cut -c1-5 | tr '[a-z]' '[A-Z]'`
           fi
        else
           if [ "x$UNXMSDRV_PRD_OCC" = "x" ]
                then
                        echo "La variable d environnement UNXMSDRV_PRD_OCC n est pas definie."
                        echo "elle est obligatoire pour un message LIB de type V2"
                        echo "\nArret ...."
                        exit 1
                else
                        UNXMSDRV_PRD_OCC=`echo $UNXMSDRV_PRD_OCC | cut -c1-5 | tr '[a-z]' '[A-Z]'`
           fi
        fi
    if [ "x$UNXMSDRV_APPLI_REF" = "x" ]
        then
                echo "La variable d environnement UNXMSDRV_APPLI_REF n est pas definie."
                echo "elle est obligatoire pour un message de type V2"
                echo "\nArret ...."
                exit 1
        else
                UNXMSDRV_APPLI_REF=`echo $UNXMSDRV_APPLI_REF | cut -c1-15 | tr '[a-z]' '[A-Z]'`
    fi
    if [ "x$UNXMSDRV_SYSID" = "x" ]
        then
# recherche dy nom du moeud si on est en cluster
# sinon recuperation du hostname
                UNXMSDRV_SYSID=`hostname`
                UNXMSDRV_SYSID=`echo $UNXMSDRV_SYSID | cut -c1-8 `
        else
                UNXMSDRV_SYSID=`echo $UNXMSDRV_SYSID | cut -c1-8 `
    fi
fi
RENVOI=`echo $1 | awk '{print tolower(substr($1,1,1))}'`
TABLE=`echo $2 | awk '{print tolower(substr($1,1,1))}'`
if [ "$TABLE" = "n" ]
  then
    TABLE="e"
fi
OBJETU=$3
NUMMSG=$4
#####################################################################
# Recherche des parametres du texte si necessaire
#####################################################################
shift 4
#####################################################################
# Creation de la ligne formatee recuperee dans un fichier
#####################################################################
if [ $# -eq 0 ]
  then
    $UNXMSDRV_EXBIN/slblpmss -f 1 -a $UNXMSDRV_APPLICATION -l $UNXMSDRV_MODULE -b $UNXMSDRV_EXDATA/${UNXPRDAPPLI}fpms$UNXMSDRV_LANGUE.dat -p 8,8,1,0,7,2,17,3,0,25 -o$TABLE 300 SEND $OBJETU I $NUMMSG "Texte non trouve" 1>$UNXMSDRV_TMP/tbe_0em1$$.tmp 2>$UNXMSDRV_TMP/tbe_0em2$$.tmp
  else
    $UNXMSDRV_EXBIN/slblpmss -f 1 -a $UNXMSDRV_APPLICATION -l $UNXMSDRV_MODULE -b $UNXMSDRV_EXDATA/${UNXPRDAPPLI}fpms$UNXMSDRV_LANGUE.dat -p 8,8,1,0,7,2,17,3,0,25 -o$TABLE 300 SEND $OBJETU I $NUMMSG "Texte non trouve" "$@" 1>$UNXMSDRV_TMP/tbe_0em1$$.tmp 2>$UNXMSDRV_TMP/tbe_0em2$$.tmp
fi
RETOUR=$?
####################################################################
# recherche type de machine
#####################################################################
TYPE_MACHINE=`uname -a | cut -d" " -f1`
#####################################################################
# Toutes les actions ci-apres se font uniquement si un message est disponible
#####################################################################
if [ $RETOUR -eq 0 ]
  then
#####################################################################
# Envoi dans le fifo si necessaire
#####################################################################
    if [ "$RENVOI" = "r" -o "$RENVOI" = "R" ]
      then
        TEXTE=`cat $UNXMSDRV_TMP/tbe_0em2$$.tmp`
        NBLIGNETEXTE=`echo "$TEXTE" | wc -l`
        if [ "$NBLIGNETEXTE" -gt 1 ]
           then
                TEXTE=`echo "$TEXTE" | head -1`
        fi
## recup de la version du message emis (pour compatibilite future)
        VERSION_MSS=`echo "$TEXTE" | awk '{ print substr($0,1,3)}'`
        if [ "X$VERSION_MSS" = 'X}03' ]
          then
## si version msg 3, recup de la gravite
            GRAVITE=`echo "$TEXTE" | awk '{ print substr($0,79,1)}'`
          else
## sinon, mise a E par defaut pour renvoi (compatibilite ascendante)
            GRAVITE=E
        fi
## debut modif C.Coquet du 18/10/2002
        if [ $GRAVITE != "I" -o \( $GRAVITE = "I" -a \( $OBJETU = "tbe_0ds" -o $OBJETU = "tbe_0fs" \) \) ]
          then
                if [ "$UNXMSDRV_SURV" = "02" ]
                   then
# message surveillance format V2 ( }02 )
# formatage du message en fonction de la gravite
                        DEB_TEXTEV2="}02.""$UNXMSDRV_SYSID"."$UNXMSDRV_PRD_OCC"."$UNXMSDRV_APPLI_REF"."$GRAVITE".
                        LONG=`echo "$TEXTE" | sed 's/\^/ /g' | cut -c89- `
                        MOBJETU=`echo $OBJETU | tr '[a-z]' '[A-Z]'`
                        if [ "$UNXMSDRV_TYPE_MES" = "LIB" ]
                           then
# en attendant une deuxieme base message pour les messages courts : il manque la short description
                                SHORT=`echo $LONG | cut -d"#" -f1 `
                                LONGUEUR=`echo $SHORT | wc -c `
                                while [ $LONGUEUR  -le 42 ]
                                do
                                        SHORT=$SHORT" "
                                        LONGUEUR=`expr $LONGUEUR + 1`
                                done
                                LONG=`echo $LONG | cut -d"#" -f2`
                                TEXTEV2="$DEB_TEXTEV2""$SHORT""$LONG"
                           else
                                CLONG="|"`echo "$TEXTE" | sed 's/\^/ /g' | cut -c89-106 `
                                case $GRAVITE in
                                E|A)
                                        SHORT="$UNXMSDRV_PRD_GENE"."$MOBJETU"."$NUMMSG""                     "
                                        TEXTEV2="$DEB_TEXTEV2""$SHORT""|""$LONG"
                                ;;
                                T|I)
                                        SHORT="$UNXMSDRV_PRD_GENE"."$MOBJETU"."$NUMMSG"
                                        TEXTEV2="$DEB_TEXTEV2""$SHORT"" ""$LONG"
                                ;;
                                W)
                                        TEXTEV2="$DEB_TEXTEV2""$MOBJETU"."$NUMMSG".`date '+%Y/%m/%d %H:%M:%S'`".      ""$LONG"
                                ;;
                                esac
                        fi
                        $UNXMSDRV_EXBIN/slblpmsd ECHO $UNXMSDRV_FIFOIN 5 "$TEXTEV2"
                   else
# message surveillance format V1 ( }03 )
                        $UNXMSDRV_EXBIN/slblpmsd ECHO $UNXMSDRV_FIFOIN 5 "$TEXTE"
                fi
        fi
## fin modif C.Coquet du 18/10/2002
  fi
#####################################################################
# Envoi de la ligne sur STDOUT si deuxieme parametre different de S,
# envoi de la ligne NON FORMATEE sinon
#####################################################################
    if [ "$TABLE" = "s" -o "$TABLE" = "S" -o "$TABLE" = "e" -o "$TABLE" = "E" ]
      then
        cat $UNXMSDRV_TMP/tbe_0em1$$.tmp
      else
        if [ "$TABLE" != "M" -a "$TABLE" != "m" ]
          then
            cat $UNXMSDRV_TMP/tbe_0em2$$.tmp
        fi
    fi
else
  # Modification du 15/11/2004
  echo "Code retour de l API : $RETOUR"
  cat $UNXMSDRV_TMP/tbe_0em1$$.tmp
  cat $UNXMSDRV_TMP/tbe_0em2$$.tmp
fi
#####################################################################
# Effacement des fichiers temporaires
#####################################################################
rm $UNXMSDRV_TMP/tbe_0em1$$.tmp
rm $UNXMSDRV_TMP/tbe_0em2$$.tmp
#####################################################################
# Exit avec le return code du programme slblpmss
#####################################################################
exit $RETOUR