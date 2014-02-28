###################################################################
#Script que controla, a traves del PID, que un determinado proceso
#este ejecutandose. Caso contrario lo relanza.
##################################################################
#!/bin/sh

FIN_SUB_DIR=/export/home/mo/moar/scripts

. $FIN_SUB_DIR/set_parametros_comunes.sh

export LANG=es_AR

FILTRO_FIN_SUB="cierreDeAvisos.sh"
FILTRO_COMANDO_FIN_SUB="java"
COMANDO_FIN_SUB=/cierreDeAvisos.sh 
DST_MAIL="soportemo@claringlobal.com"

exit_error()
{
echo "Error al relanzar $DIR_APP$COMANDO_FIN_SUB" | mail -s "ERROR al Relanzar ""$COMANDO_FIN_SUB" -r "$REM_MAIL" $DST_MAIL
exit 1
}

PID=`ps -ef | grep $FILTRO_FIN_SUB | grep -v grep`
if [ "$PID" = "" ]; then
	cd $DIR_APP || exit_error
	cd $FIN_SUB_DIR

nohup $FIN_SUB_DIR$COMANDO_FIN_SUB & 
	echo "Comando $DIR_APP$COMANDO_FIN_SUB RELANZADO" | mail -s "Se relanzo el comando $COMANDO_FIN_SUB" $DST_MAIL

fi

#echo "Se chequeo el comando " | mail -s "Comando chequeado!" -r "$REM_MAIL"  "$DST_MAIL"
