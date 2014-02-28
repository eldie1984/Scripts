#!/bin/bash
#set -x
LISTA="soportemo@claringlobal.com.ar,mcapuano@claringlobal.com.ar,jlinares@claringlobal.com.ar,acastro@claringlobal.com.ar,jfernandez@claringlobal.com.ar"
#LISTA="soportemo@claringlobal.com.ar"
#PATH=/export/home/mo/moar/scripts
cd /export/home/mo/moar/scripts
#Parametros para Oracle
export ORACLE_BASE=/var/opt/oracle
export ORACLE_HOME=$ORACLE_BASE/product/10.2.0/client_1
export ORACLE_BIN=$ORACLE_HOME/bin
$ORACLE_BIN/sqlplus sacar/r2cs2c2r24@balanceo @Usu_Registro_Validado_Semana.sql Usu_Registro_Validado_Semana.csv
# Lo mando por mail
TEXTO="Reporte de Usuarios Registrados en al semana"
echo $TEXTO > body.out
(cat body.out; uuencode Usu_Registro_Validado_Semana.csv Usu_Registro_Validado_Semana.csv) | mailx -r soportemo@claringlobal.com.ar -s "Reporte de Usuarios Registrados en la semana" $LISTA
#uuencode cumpleanosdeldia.csv cumpleanosdeldia.csv |  mailx -r soportemo@claringlobal.com.ar -s "Reporte de Cumpleaños del dia" $LISTA
#cat "$PATH"/cumpleanosdeldia.log|unix2dos|uuencode Cumple.csv |mail -s "Reporte de Cumpleaños del dia" $LISTA
#uuencode cumple.csv cumple.csv | mailx -s "Reporte de Cumpleaños del dia" $LISTA
# FIN
