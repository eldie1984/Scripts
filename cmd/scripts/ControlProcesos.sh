#!/bin/bash
#set -x

####################################################################
# Este script controla que los siguientes procesos esten funcionando
#   * Envio de Mail's
#   * Cierre de Avisos de MO
####################################################################

LISTA="adagnino@cmd.com.ar,soporte@cmd.com.ar"
#LISTA="dgasch@cmd.com.ar"
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
$ORACLE_BIN/sqlplus sacar/r2cs2c2r24@balanceo << EOF > /tmp/output_env.log
select count(*) from sacar.mail_registro where fecha_modif = (sysdate - (10/(24*60))) and mail_estado_id = 2;
EOF
COUNT_ENV=`tail -5 /tmp/output_env.log | awk '{ print $1}' | head -n 1`
if [ $COUNT -gt 100 && $COUNT_ENV -eq 0 ]; then
   TEXTO="El proceso de envio de mail tiene:\n $COUNT mail sin enviar\n $COUNT_ENV mails enviados\n  ir a NAP20, chequear que el proceso este corriendo.\n Si esta corriendo, reiniciar el proceso /export/home/mo/moar/scripts/enviarMails.sh, matando la jvm antes de volver a arrancar \n\n Si no esta corriendo, buscar el proceso java, matarlo y ejecutar /export/home/mo/moar/scripts/enviarMails.sh \n\n\n Este mail es enviado desde NAP20 /export/home/mo/moar/scripts/ControlProcesos.sh\n\n"
   echo -e $TEXTO
   echo -e $TEXTO | mailx -r soportemo@claringlobal.com.ar -s "MO - ERROR en proceso de envio de mails" $LISTA
fi

#
#   Cierre de avisos MO
#
/var/opt/oracle/product/10.2.0/client_1/bin/sqlplus sacar/r2cs2c2r24@balanceo << EOF > /tmp/output.log
SELECT COUNT (*) FROM SAC_AVISO WHERE AV_ESTADO = 3 AND AV_FECHA_EXPIRACION BETWEEN SYSDATE -40 AND SYSDATE;
EOF
COUNT=`tail -5 /tmp/output.log | awk '{ print $1}' | head -n 1`
echo "Avisos no cerrados $COUNT"
if [ $COUNT -gt 5000 ]; then
    #pid=`ps -fea | grep cierreDeAvisos.sh | grep -v grep | awk '{ print $2}'`
    #echo $pid 
    #pid2=`ps -fea | grep java | grep $pid | grep -v grep | awk '{ print $2}'`
    #echo $pid2 
    #/usr/bin/kill -9 $pid2
    #nohup /export/home/mo/moar/scripts/cierreDeAvisos.sh &
    TEXTO="El proceso de cierre de avisos tiene $COUNT registros sin procesar. Por favor, ingrese a NAP20 y reinicie el proceso /export/home/mo/moar/scripts/cierreDeAvisos.sh"
    echo $TEXTO
    echo $TEXTO | mailx -r soportemo@claringlobal.com.ar -s "MO - ERROR en proceso de cierre de avisos" $LISTA
fi

# FIN

