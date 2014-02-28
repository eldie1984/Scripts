#!/bin/bash
#set -x

####################################################################
# Este script controla que los siguientes procesos esten funcionando
#   * Envio de Mail's
####################################################################

LISTA="adagnino@cmd.com.ar,soportemo@cmd.com.ar,sgoldentair@cmd.com.ar"
#LISTA="adagnino@prima.com.ar"
echo $LISTA

#Parametros para Oracle
export ORACLE_BASE=/var/opt/oracle
export ORACLE_HOME=$ORACLE_BASE/product/10.2.0/client_1
export ORACLE_BIN=$ORACLE_HOME/bin
#PATH=.:$PATH:$ORACLE_HOME/bin:/bin:/usr/local/bin:/usr/bin:/usr/ccs/bin:/opt/bin
echo "Parametros de conexion seteados"

#
#   Envio de mail
#
$ORACLE_BIN/sqlplus sacar/r2cs2c2r24@balanceo << EOF > /tmp/output.log
SELECT COUNT (*) FROM MAIL_REGISTRO WHERE mail_estado_id = 1;
EOF
COUNT=`tail -5 /tmp/output.log | awk '{ print $1}' | head -n 1`
echo "Mails no enviados $COUNT"
if [ $COUNT -gt 80 ]; then
   TEXTO="El proceso de envio de mail tiene $COUNT mail sin enviar, ir a NAP20 y reiniciar el proceso /export/home/mo/moar/scripts/enviarMails.sh"
   echo $TEXTO
   echo $TEXTO | mailx -r soportemo@claringlobal.com.ar -s "MO - ERROR en proceso de envio de mails" $LISTA
fi

#
