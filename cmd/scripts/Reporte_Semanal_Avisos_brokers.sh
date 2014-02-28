#!/bin/bash
#set -x
LISTA="soportemo@claringlobal.com.ar,mdeangelis@claringlobal.com.ar,acastro@claringlobal.com.ar,fpasqualini@claringlobal.com.ar,jfernandez@claringlobal.com.ar,jlinares@claringlobal.com.ar"
#LISTA="soportemo@claringlobal.com.ar"
#PATH=/export/home/mo/moar/scripts
cd /export/home/mo/moar/scripts
#Parametros para Oracle
export ORACLE_BASE=/var/opt/oracle
export ORACLE_HOME=$ORACLE_BASE/product/10.2.0/client_1
export ORACLE_BIN=$ORACLE_HOME/bin
$ORACLE_BIN/sqlplus sacar/r2cs2c2r24@balanceo @Reporte_Semanal_Avisos_brokers.sql Reporte_Semanal_Avisos_brokers.txt
# Lo mando por mail
TEXTO="Reporte Reporte_Semanal_Avisos_brokers"
echo $TEXTO > body.out
(cat body.out; uuencode Reporte_Semanal_Avisos_brokers.txt Reporte_Semanal_Avisos_brokers.txt) | mailx -r soportemo@claringlobal.com.ar -s "Reporte Semanal Avisos Broker " $LISTA
#uuencode cumpleanosdeldia.csv cumpleanosdeldia.csv |  mailx -r soportemo@claringlobal.com.ar -s "Reporte de Cumpleaños del dia" $LISTA
#cat "$PATH"/cumpleanosdeldia.log|unix2dos|uuencode Cumple.csv |mail -s "Reporte de Cumpleaños del dia" $LISTA
#uuencode cumple.csv cumple.csv | mailx -s "Reporte de Cumpleaños del dia" $LISTA
# FIN
