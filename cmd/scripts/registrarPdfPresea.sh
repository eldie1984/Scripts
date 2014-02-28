#!/bin/ksh
#set -x
. /export/home/mo/moar/scripts/set_parametros_comunes.sh

#LANG=en_US.ISO-8859-1
#export $LANG
PATHTXT="/export/home/mo/moar/txt/registrarPdfPresea"
LOG="/export/home/mo/moar/logs/RegistrarPdfPresea.log"
FECHA=`date +%d_%m_%Y`

echo "INICIO DE PROCESO" >> $LOG
echo `date +%d/%m/%Y--%H:%M:%S` "COPIANDO ARCHIVO TXT VIA RSYNC" >> $LOG
/usr/bin/rsync -avz rsync://filermo01.int.clarin.com/copiarfcmo/pdfs_procesados.txt $PATHTXT >> $LOG 2>&1

if [ $? -ne 0 ]; then
        echo "ERROR AL COPIAR EL ARCHIVO TXT" >> $LOG
        exit 1
else
        echo "ARCHIVO TXT COPIADO CORRECTAMENTE" >> $LOG
fi


echo `date +%d/%m/%Y--%H:%M:%S` "INICIO DE PROCESO JAVA" >> $LOG
$JAVA_HOME/java -Xms64M -Xmx128M -Duser.timezone=$TZ -Dlog4j.configuration=log4j-registrarPdfPresea.xml -classpath $CLASSPATH clarin.cuentasERP.domain.service.PdfRunner $PATHTXT/pdfs_procesados.txt >> $LOG 2>&1 
echo `date +%d/%m/%Y--%H:%M:%S` "FIN DE PROCESO JAVA" >> $LOG
echo `date +%d/%m/%Y--%H:%M:%S` "BACKUP DEL ARCHIVO TXT" >> $LOG
mv $PATHTXT/pdfs_procesados.txt $PATHTXT/BKP/pdfs_procesados.txt_$FECHA
echo `date +%d/%m/%Y--%H:%M:%S` "FIN DE PROCESO" >> $LOG
exit 0
