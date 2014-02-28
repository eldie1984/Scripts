#!/bin/bash
#set -x
LISTA="adagnino@prima.com.ar,soportemo@claringlobal.com.ar,fraffetto@cmd.com.ar,sgoldentair@cmd.com.ar"
#LISTA="soportemo@claringlobal.com.ar"
#PATH=/export/home/mo/moar/scripts
cd /export/home/mo/moar/scripts
#Parametros para Oracle
export ORACLE_BASE=/var/opt/oracle
export ORACLE_HOME=$ORACLE_BASE/product/10.2.0/client_1
export ORACLE_BIN=$ORACLE_HOME/bin
$ORACLE_BIN/sqlplus sacar/r2cs2c2r24@balanceo @controles.sql controles.html
# Lo mando por mail
TEXTO="Reporte de controles"
echo $TEXTO > body.out
(cat body.out; uuencode controles.html controles.html) | mailx -r soportemo@claringlobal.com.ar -s "Reporte de Control" $LISTA
#uuencode cumpleanosdeldia.csv cumpleanosdeldia.csv |  mailx -r soportemo@claringlobal.com.ar -s "Reporte de Cumpleaños del dia" $LISTA
#cat "$PATH"/cumpleanosdeldia.log|unix2dos|uuencode Cumple.csv |mail -s "Reporte de Cumpleaños del dia" $LISTA
#uuencode cumple.csv cumple.csv | mailx -s "Reporte de Cumpleaños del dia" $LISTA
# FIN
