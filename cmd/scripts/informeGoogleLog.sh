#!/bin/bash

#===================================================================================
#===================================================================================
#                       REPORTE ANALISIS DEL LOG - GOOGLE BOT
#@since: 22/07/2010
#@author: ptamburro
#===================================================================================
#===================================================================================

ORACLE_HOME=/var/opt/oracle/product/10.2.0/client_1
export ORACLE_HOME
PATH=$PATH:/var/opt/oracle/product/10.2.0/client_1/bin/
export PATH
NRO_DIA_AYER=`TZ=GMT+24 date +%d`
AYER=$(/usr/bin/date +%Y-%m-$NRO_DIA_AYER)
HOY=$(/usr/bin/date +%Y-%m-%d)
USER_DB=SACAR
PASS_DB=r2cs2c2r24
DB=BALANCEO
TMP=/tmp
SERVIDOR="'NAP16'"
#LISTA_MAIL=ptamburro@prima.com.ar,adagnino@prima.com.ar
LISTA_MAIL=ptamburro@prima.com.ar,adagnino@prima.com.ar,fraffetto@cmd.com.ar,jacri@cmd.com.ar
echo  =========================================================
echo  INICIO GENERACION REPORTE GOOGLE LOG
echo  =========================================================

#deleteamos la tabla analisis_google_log
#sqlplus $USER_DB/$PASS_DB@$DB << ENDOFSQL
#DELETE ANALISIS_GOOGLE_LOG;
#COMMIT;
#exit;
#ENDOFSQL


echo  =========================================================
echo  Se ejecuta la query que arma el reporte en la base:
echo  $USER_DB@$DB	 
echo  =========================================================

#ejecutamos el script que genera el informe y lo deja en /tmp/reporteFormateado.txt
sqlplus -S $USER_DB/$PASS_DB@$DB @/export/home/mo/moar/scripts/reporteGoogleLogFormateado.sql << ENDOFSQL
exit;
ENDOFSQL

#enviamos el mail con el reporte
cat /tmp/reporteFormateado.txt | mailx -s "MO - Avisos crawleados por Google" $LISTA_MAIL

echo  =========================================================
echo  Se obtuvo el informe y se envio a:
echo  $LISTA_MAIL
echo  FIN.
echo  =========================================================


#sqlplus -s scott/tiger @ scott.sql
#if test $? -ne 0 ; then
#   echo 'sqlplus error'
 #  exit 1
#else
 #  echo 'sqlplus OK'
  # exit 0
#fi

