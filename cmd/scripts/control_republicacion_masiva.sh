#!/bin/bash
#set -x

#===================================================================================
#-------------------CONTROL SOBRE EL PROCESO DE REPUBLICACION MASIVA ---------------
#===================================================================================
#@since: 11/08/2011 
#@author: Pablo Tamburro
#===================================================================================
#===================================================================================
# -------------------------	D E S C R I P C I O N ----------------------------------
#===================================================================================
# Este script en forma de alarma envia mails a los destinatarios de la lista, cuando 
# alguna de las siguientes validaciones falla:
# 1. El proceso republicacionMasivaOffLine.sh no esta corriendo
#===================================================================================
#===================================================================================

date

#Parametros generales
LISTA="ptamburro@prima.com.ar,soporte@cmd.com.ar"
ALARMA="APAGADA"
TEXTO_ALARMA="" 
TMP=/tmp

echo "Destinatarios de alarmas: $LISTA"


#Parametros para la conexion a la base
USER_DB=sacar
PASS_DB=r2cs2c2r24
DB=balanceo
ORACLE_HOME=/var/opt/oracle/product/10.2.0/client_1
export ORACLE_HOME
PATH=$PATH:/var/opt/oracle/product/10.2.0/client_1/bin/
export PATH


#PATH=.:$PATH:$ORACLE_HOME/bin:/bin:/usr/local/bin:/usr/bin:/usr/ccs/bin:/opt/bin

echo "Parametros de conexion seteados"


#***********************
# 1.Control proceso activo
#***********************

CTRL1=`ps -ef |grep republicacionMasivaOffLine.sh | wc -l`


# -le (<=)
if [ $CTRL1 -le 1 ]; then
   ALARMA="PRENDIDA"	
   TEXTO1="|||PROCESO INACTIVO|||\\n> No esta corriendo el proceso republicacionMasivaOffLine.sh\\n||||||||||||||||||||||\\n"
   echo -e $TEXTO1 >>$TMP/outputCRM.log
fi


#************************************
# 2.Control Avisos con error antiguos 
#************************************

sqlplus -S $USER_DB/$PASS_DB@$DB << ENDOFSQL >$TMP/outputCRM1.log
select count(distinct(id_lote))
from sac_republicacion_intermedia
where id_estado = 3
and trunc(fecha_actualizacion) < trunc(sysdate - 5);
exit;
ENDOFSQL

CTRL2=`tail -2 /tmp/outputCRM1.log | awk '{ print $1}' | head -n 1`

if [ $CTRL2 -ge 1 ]; then
   ALARMA="PRENDIDA"	
   TEXTO2="|||ERROR LOTE|||\\n> Existe al menos un lote con avisos erroneos desde hace más de 5 días \\n||||||||||||||||\\n"
   echo -e $TEXTO2 >>$TMP/outputCRM.log
fi



#*************************************
# 3.Control cantidad avisos sin procesar
#*************************************

sqlplus -S $USER_DB/$PASS_DB@$DB << ENDOFSQL >$TMP/outputCRM2.log
select count(*) as cant
from sac_republicacion_intermedia
where id_estado = 1
and trunc(fecha_actualizacion) < trunc(sysdate);
exit;
ENDOFSQL

CTRL3=`tail -2 /tmp/outputCRM2.log | awk '{ print $1}' | head -n 1`

if [ $CTRL3 -ge 1 ]; then
   ALARMA="PRENDIDA"	
   TEXTO2="|||AVISOS SIN PROCESAR|||\\n> Existen $CTRL3 avisos sin procesar \\n||||||||||||||||\\n"
   echo -e $TEXTO2 >>$TMP/outputCRM.log
fi




#*************************************
# Envio mail si existe error
#*************************************

if [ $ALARMA == "PRENDIDA" ]; then
	echo "ALARMA ACTIVADA: envio mail a $LISTA"
	cat /tmp/outputCRM.log | mailx -s "MO - ERROR en proceso de Republicacion Masiva" $LISTA
else
	echo "No existen errores, por lo tanto no se dispara ninguna alarma" 
fi


#********************************
# Eliminamos archivos temporales 
#********************************
rm $TMP/outputCRM1.log
rm $TMP/outputCRM2.log
rm $TMP/outputCRM.log


echo "Fin controles"

#
