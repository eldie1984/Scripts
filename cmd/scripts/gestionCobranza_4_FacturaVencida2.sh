#! /bin/bash
#set -x

. /export/home/mo/moar/scripts/set_parametros_comunes.sh

LOG=$DIR_APP/logs/gestionCobranza.log

export LANG=en_US.ISO-8859-1

template='MAIL_TEMPLATE_ID CCE_FACTURA_VENCIDA_2_BK_H,CCE_FACTURA_VENCIDA_2_BK_I,CCE_FACTURA_VENCIDA_2_WEB_H,CCE_FACTURA_VENCIDA_2_WEB_I'

$JAVA_HOME/java -Xms64M -Xmx256M -Duser.timezone=$TZ -Dlog4j.configuration=log4j-gestionCobranza.xml -classpath $CLASSPATH clarin.procesos.cuentas.gestionCobranza.GestionCobranza $template >> $LOG 2>&1
