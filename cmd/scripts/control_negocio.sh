#!/bin/bash
#set -x

###################################################################################
# Este script verifica que las comisiones, destaques, publicaciones y registros 
#  en MO este dentro de un rango "normal".
# Desde el crontab del usuario mo se invoca a este script cada dos horas para
#  verificar dichos parametros. Si los indices del sitio estan por 
#  debajo de lo esperado, envia mail notificando a la LISTA definida abajo.  
# Este script recibe como parametro las 4 condiciones minimas de comisiones, destaques,
#  publicaciones y registros (en ese orden). Si no se encuentra ese minimo
#  se alerta.
###################################################################################


date






LISTA="adagnino@cmd.com.ar,ldefrancesco@claringlobal.com.ar,mcapuano@cmd.com.ar,jlinares@claringlobal.com.ar"


echo "Destinatarios de alarmas: $LISTA"

COMISIONES_MIN=$1 
DESTAQUES_MIN=$2 
PUBLICACIONES_MIN=$3 
REGISTROS_MIN=$4

#Parametros para Oracle
export ORACLE_BASE=/var/opt/oracle
export ORACLE_HOME=$ORACLE_BASE/product/10.2.0/client_1
export ORACLE_BIN=$ORACLE_HOME/bin

#PATH=.:$PATH:$ORACLE_HOME/bin:/bin:/usr/local/bin:/usr/bin:/usr/ccs/bin:/opt/bin
echo "Parametros de conexion seteados"


#
# Comisiones (compras)
#
$ORACLE_BIN/sqlplus sacar/r2cs2c2r24@balanceo << EOF > /tmp/output.log
select count(1) from sacar.cce_cargo where tipo_cargo_id = 1 and trunc(sysdate) = trunc(generacion);
EOF
COUNT=`tail -5 /tmp/output.log | awk '{ print $1}' | head -n 1`
echo "Cantidad de comisiones generadas $COUNT (esperado:$COMISIONES_MIN)"
if [ $COUNT -lt $COMISIONES_MIN ]; then
   TEXTO="Hay menos comisiones generadas ($COUNT) que las tabuladas para esta hora ($COMISIONES_MIN). Por favor, revise.\n Esta alarma es generada en: NAP20:/export/home/mo/moar/scripts/control_negocio.sh"
   echo -e $TEXTO
   echo -e $TEXTO | mailx -r soportemo@claringlobal.com.ar -s "MO - Alarma comisiones" $LISTA
fi


#
# Destaques contratados
#
$ORACLE_BIN/sqlplus sacar/r2cs2c2r24@balanceo << EOF > /tmp/output.log
select count(1) from sacar.cce_cargo where tipo_cargo_id = 2 and trunc(sysdate) = trunc(generacion);
EOF
COUNT=`tail -5 /tmp/output.log | awk '{ print $1}' | head -n 1`
echo "Cantidad de destaques contratados $COUNT (esperado:$DESTAQUES_MIN)" 
if [ $COUNT -lt $DESTAQUES_MIN ]; then
   TEXTO="Hay menos destaques generados ($COUNT) que las tabuladas para esta hora ($DESTAQUES_MIN). Por favor, revise.\n Esta alarma es generada en: NAP20:/export/home/mo/moar/scripts/control_negocio.sh"
   echo -e $TEXTO
   echo -e $TEXTO | mailx -r soportemo@claringlobal.com.ar -s "MO - Alarma destaques" $LISTA
fi


#
# Publicaciones nuevas
#
$ORACLE_BIN/sqlplus sacar/r2cs2c2r24@balanceo << EOF > /tmp/output.log
select count(*) from sacar.sac_aviso where av_estado = 3 and trunc(sysdate) = trunc(av_fecha_creacion);
EOF
COUNT=`tail -5 /tmp/output.log | awk '{ print $1}' | head -n 1`
echo "Cantidad de publicaciones nuevas $COUNT (esperado:$PUBLICACIONES_MIN)"
if [ $COUNT -lt $PUBLICACIONES_MIN ]; then
   TEXTO="Hay menos publicaciones nuevas ($COUNT) que las tabuladas para esta hora ($PUBLICACIONES_MIN). Por favor, revise.\n Esta alarma es generada en: NAP20:/export/home/mo/moar/scripts/control_negocio.sh"
   echo -e $TEXTO
   echo -e $TEXTO | mailx -r soportemo@claringlobal.com.ar -s "MO - Alarma publicaciones" $LISTA
fi


#
# Registros nuevos
#
$ORACLE_BIN/sqlplus sacar/r2cs2c2r24@balanceo << EOF > /tmp/output.log
select count(*) from sacar.sac_usuario where trunc(sysdate) = trunc(usu_fecha_ingreso);
EOF
COUNT=`tail -5 /tmp/output.log | awk '{ print $1}' | head -n 1`
echo "Cantidad de usuarios registrados $COUNT (esperado:$REGISTROS_MIN)"
if [ $COUNT -lt $REGISTROS_MIN ]; then
   TEXTO="Hay menos usuarios registrados ($COUNT) que los tabulados para esta hora ($REGISTROS_MIN). Por favor, revise.\n Esta alarma es generada en: NAP20:/export/home/mo/moar/scripts/control_negocio.sh"
   echo -e $TEXTO
   echo -e $TEXTO | mailx -r soportemo@claringlobal.com.ar -s "MO - Alarma registros" $LISTA
fi


echo "Fin controles"

#
