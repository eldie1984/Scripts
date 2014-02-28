#!/bin/bash

PATH_LOG=/export/home/mo/moar/logs/backup

# ZIPEO EL BACKUP DE AYER Y LO COPIO A LA CARPETA backup
#    borro los backup viejos

cd /export/home/mo/moar/logs
find /export/home/mo/moar/logs -mtime +0 -name "*.log*" -exec mv {} backup/ \;
find /export/home/mo/moar/logs/backup/ -name "*.log*" -exec gzip {} \;
# FIN
