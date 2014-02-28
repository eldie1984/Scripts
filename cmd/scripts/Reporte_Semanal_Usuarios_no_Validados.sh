#!/bin/bash
#set -x
LISTA="gmarty@cmd.com.ar"
#LISTA="soportemo@claringlobal.com.ar"
RUTA=$(pwd)
cd /export/home/mo/moar/scripts
#Parametros para Oracle
export ORACLE_BASE=/var/opt/oracle
export ORACLE_HOME=$ORACLE_BASE/product/10.2.0/client_1
export ORACLE_BIN=$ORACLE_HOME/bin
$ORACLE_BIN/sqlplus sacar/r2cs2c2r24@balanceo @Reporte_Semanal_Usuarios_no_Validados.sql Reporte_Semanal_Usuarios_no_Validados.html
# Lo mando por mail
TEXTO="Reporte Reporte_Semanal_Usuarios_no_Validados\n\nScript ubicado en $RUTA/Reporte_Semanal_Usuarios_no_Validados.sh"
echo $TEXTO > body.out
(cat body.out; uuencode Reporte_Semanal_Usuarios_no_Validados.html Reporte_Semanal_Usuarios_no_Validados.html) | mailx -r soportemo@claringlobal.com.ar -s "Reporte Usuarios NO Validados " $LISTA
#uuencode cumpleanosdeldia.csv cumpleanosdeldia.csv |  mailx -r soportemo@claringlobal.com.ar -s "Reporte de Cumpleaños del dia" $LISTA
#cat "$PATH"/cumpleanosdeldia.log|unix2dos|uuencode Cumple.csv |mail -s "Reporte de Cumpleaños del dia" $LISTA
#uuencode cumple.csv cumple.csv | mailx -s "Reporte de Cumpleaños del dia" $LISTA
# FIN
