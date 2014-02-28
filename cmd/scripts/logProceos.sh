#!/bin/bash
#
# Si alguno de los 2 log es muy grande, lo borro y aviso por mail
#
LISTA="soportemo@claringlobal.com.ar"
FECHA=`date +%Y-%m-%d`
HORA=`date +%H:%M`
cd /export/home/mo/moar/logs
TAMANO=`du MailsSender.log | awk '{ print $1}'`
if [ $TAMANO -gt 50000 ]; then
        /usr/bin/cp MailsSender.log MailsSender$FECHA-$HORA.log
        /usr/bin/gzip MailsSender$FECHA-$HORA.log
	/usr/bin/rm MailsSender.log
	/usr/bin/touch MailsSender.log
	TEXTO="El log MailsSender.log en NAP20 esta creciendo mucho, se procedio a copiarlo y zipiarlo. Controlar el proceso envioMails"
	echo $TEXTO | mailx -r soportemo@claringlobal.com.ar -s "LOG de Procesos muy GRANDE" $LISTA
fi
#
TAMANO=`du cierreDeAvisos.log | awk '{ print $1}'`
if [ $TAMANO -gt 50000 ]; then
        /usr/bin/cp cierreDeAvisos.log cierreDeAvisos$FECHA-$HORA.log
        /usr/bin/gzip cierreDeAvisos$FECHA-$HORA.log 
	/usr/bin/rm cierreDeAvisos.log
	/usr/bin/touch cierreDeAvisos.log
	TEXTO="El log cierreDeAvisos.log en NAP20 esta creciendo mucho, se procedio a copiarlo y zipiarlo. Controlar el proceso cierreDeAvisos"
	echo $TEXTO | mailx -r soportemo@claringlobal.com.ar -s "LOG de Procesos muy GRANDE" $LISTA
fi
# FIN
