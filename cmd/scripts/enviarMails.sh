#!/bin/ksh
#set -x

. /export/home/mo/moar/scripts/set_parametros_comunes.sh

LOG=$DIR_APP/logs/MailsSender.log

LANG=es_AR

export LOG LANG

$JAVA_HOME/java -Xms120M -Xmx240M -Duser.timezone=$TZ -Dlog4j.configuration=log4j-enviarMails.xml -classpath $CLASSPATH  clarin.util.mailer.ProcessSender >> $LOG 2>&1


