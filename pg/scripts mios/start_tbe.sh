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
