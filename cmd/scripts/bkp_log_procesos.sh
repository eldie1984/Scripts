#!/bin/bash

PATH=/export/home/mo/moar/logs

#
# ZIPEO EL BACKUP DE AYER Y LO COPIO A LA CARPETA LOG_backup
#    borro los backup viejos, guardo los ultimos 10 dias
#

cd $PATH
echo "**************************"
echo "Cominezo busqueda y zipiado --> gzip"
/usr/bin/find . -mtime +0 -name "mo-*" -exec /usr/bin/gzip {} \;
#/usr/bin/find . -mtime +0 -name "*.log" -exec /usr/bin/gzip {} \;
echo "muevo a LOG_backup"
/usr/bin/mv $PATH/*.gz $PATH/LOG_backup/
cd $PATH/LOG_backup
echo "borro los viejos"
/usr/bin/find . -mtime +10 -name "*.gz" -exec /usr/bin/rm {} \;

# FIN
