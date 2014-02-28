#!/bin/bash
########################################################
# Este proceso controla si termino bien el proceso de consulta de pagosDM 
#########################################################
LISTA="soportemo@claringlobal.com.ar,aguidoni@cmd.com.ar"
#Parametros para Oracle
export ORACLE_BASE=/var/opt/oracle
export ORACLE_HOME=$ORACLE_BASE/product/10.2.0/client_1
export ORACLE_BIN=$ORACLE_HOME/bin
#
$ORACLE_BIN/sqlplus bacar/M2rt1n1yR0ss1@balanceo << EOF > /tmp/auxiliar.log
select count(*) from bacar.bac_gnr_log where proceso = 'ConsultaAllPendientesDM' and trunc(fecha_generacion) = trunc(sysdate)-1 and nivel = 'ERROR';
EOF
COUNT=`tail -5 /tmp/auxiliar.log | awk '{ print $1}' | head -n 1`
if [ $COUNT -gt 0 ]; then
   TEXTO="El proceso ConsultaAllPendientesDM de BACAR de anoche termino mal, hay que ejecutarlo a mano cambiandole la frecuencia por el schedule en la BAC_PROCESOS y reiniciando el dominio en NAP11
"
   echo $TEXTO | mailx -r soportemo@claringlobal.com.ar -s "ERROR en ConsultaPagosDM del BAC" $LISTA
fi
#
$ORACLE_BIN/sqlplus bacar/M2rt1n1yR0ss1@balanceo << EOF > /tmp/auxiliar.log
select count(*) from bacar.bac_gnr_log where proceso = 'ConsultaAllPendientesNPS' and trunc(fecha_generacion) = trunc(sysdate)-1 and nivel = 'ERROR';
EOF
COUNT=`tail -5 /tmp/auxiliar.log | awk '{ print $1}' | head -n 1`
if [ $COUNT -gt 0 ]; then
   TEXTO="El proceso ConsultaAllPendientesNPS de BACAR de anoche termino mal, hay que ejecutarlo a mano cambiandole la frecuencia por el schedule en la BAC_PROCESOS y reiniciando el dominio en NAP11" 
  echo $TEXTO | mailx -r soportemo@claringlobal.com.ar -s "ERROR en ConsultaPagosNPS del BAC" $LISTA
fi 
# FIN
