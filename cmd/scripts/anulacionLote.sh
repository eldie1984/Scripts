#! /bin/bash
#set -x

#############################################################################
# VARIABLES
#############################################################################
#. set_parametros_comunes.sh
. /export/home/mo/moar/scripts/set_parametros_comunes.sh


export LANG=en_US.ISO-8859-1

SRC_ROOT="/export/home/mo/moar"
SRC_FROM="ClarinGlobal/MO/Output"
SRC_TO="${SRC_ROOT}/repositorio_estatico/mas_oportunidades_procesos_presea/anulacionLote"
SRC_TO_ERROR="${SRC_TO}/error"
SRC_TO_TMP="${SRC_TO}/tmp"
DB_CONTROL="${SRC_ROOT}/scripts/anulacionLote.ctl"
LOG_FILE="${SRC_ROOT}/logs/anulacionLote.log"
LOG_FILE_TMP="${SRC_ROOT}/logs/anulacionLote.tmp"
PREFIJO="mo_lotes_anulados_"
PROCESO="ANULACION_LOTE"
FECHA=`date +%Y%m%d%H%M%S`
TABLA_ORIGEN="ERP_LOTE_ANULADO_TMP"
TABLA_DESTINO="ERP_LOTE_ANULADO"

#############################################################################
# FUNCIONES
#############################################################################

function logexito
{
echo $1 >> $LOG_FILE
echo "INSERT INTO GNR_LOG VALUES (GNR_LOG_ID.NEXTVAL, SYSDATE, GNR_LOG_EJECUCION.NEXTVAL, '$PROCESO', 'INTERFAZ_PRESEA', NULL, 'INFO', '$1');" | sqlplus $USUARIO_BD@$INSTANCIA_BD > /dev/null
}

function logerror
{
cat $LOG_FILE_TMP | 
sed '1i\
Subject:ERROR EN LA INTERFAZ DE PRESEA ANULACION_LOTE' | mail $MAIL_ERROR_TO > /dev/null   
echo "INSERT INTO GNR_LOG VALUES (GNR_LOG_ID.NEXTVAL, SYSDATE, GNR_LOG_EJECUCION.NEXTVAL, '$PROCESO', 'INTERFAZ_PRESEA', NULL, 'ERROR', '$1');" | sqlplus $USUARIO_BD@$INSTANCIA_BD > /dev/null
cat $LOG_FILE_TMP >> $LOG_FILE 
}

#############################################################################
# MAIN
#############################################################################
echo "----------------------------------------------------------------------------------------------------------------" >> $LOG_FILE
echo "INICIO SCRIPT `date +%D-%T` " >> $LOG_FILE

# Copiamos los archivos de Presea a Local
echo ">>> FTP: Copiando archivos de ${FTP_PRESEA_HOST} ${SRC_FROM}/${PREFIJO}*.txt"  >> $LOG_FILE
ftp -n -v $FTP_PRESEA_HOST <<FIN  1> /dev/null 2>&1  
user $FTP_PRESEA_USER $FTP_PRESEA_PASS
#passive
ascii
prompt
cd ${SRC_FROM}
lcd ${SRC_TO_TMP}
mget ${PREFIJO}"*.txt" 
close
quit
FIN

# Vemos si hay archivos para procesar
ARCHIVOS=`find ${SRC_TO_TMP}/${PREFIJO}*.txt 2>/dev/null`
if [ $? -ne 0 ];then  
  echo ">>> No hay archivos a procesar" >> $LOG_FILE
fi

# Recorremos todos los archivos del directorio local
for file in $ARCHIVOS;
do  
  # Obtenemos el nombre del archivo (sin la ruta)
  NOMBRE=`echo $file | sed 's/.*\('"$PREFIJO"'.*\)\.txt/\1/g'`
  
  cat /dev/null > $LOG_FILE_TMP    
  echo "" >> $LOG_FILE_TMP
  echo "**************** PROCESANDO $NOMBRE.txt *******************" >> $LOG_FILE_TMP
  
  dos2unix -ascii -k -n $file anulacion_temp.txt
  chmod 777 anulacion_temp.txt

  # Cargamos los datos del archivo a la BBDD
  echo ">>> SQLLDR: Cargando los datos del archivo $NOMBRE" >> $LOG_FILE_TMP
  sqlldr $USUARIO_BD@$INSTANCIA_BD control=$DB_CONTROL data=anulacion_temp.txt errors=0 log=/dev/null  1>>$LOG_FILE_TMP 2>&1  
  if [ $? -ne 0 ];then        
    mv $file $SRC_TO_ERROR/$NOMBRE"_ERROR_"$FECHA".txt"
    logerror ">>> SQLLDR: Error en la carga del archivo ${NOMBRE}.txt a la tabla ${TABLA_DESTINO}"           
    continue    
  fi
   
  # Si la carga por sqlldr fue exitosa entonces pasamos los datos de la tabla
  # temporal a la definitiva
  echo ">>> SQLPLUS: Pasando los datos de la tabla temporal a la definitiva para $NOMBRE" >> $LOG_FILE_TMP
  sqlplus $USUARIO_BD@$INSTANCIA_BD << FIN 1>>$LOG_FILE_TMP 2>&1 
  whenever sqlerror exit failure;
	DECLARE
	CANT NUMBER;
	BEGIN
	SELECT COUNT(*) INTO CANT FROM CCE_LOTE A WHERE EXISTS (SELECT * FROM ERP_LOTE_ANULADO_TMP B WHERE A.ID = B.LOTE_ID) AND A.ESTADO <> 'CERRADO';
	IF (CANT > 0) THEN
	    RAISE_APPLICATION_ERROR(-20001, 'El estado del lote no es CERRADO');
	ELSE
	    UPDATE CCE_LOTE A SET ESTADO = 'ANULADO', ACTUALIZACION = SYSDATE WHERE EXISTS (SELECT * FROM ERP_LOTE_ANULADO_TMP B WHERE A.ID = B.LOTE_ID);
	    INSERT INTO $TABLA_DESTINO SELECT A.*,'${NOMBRE}' || '.txt' FROM $TABLA_ORIGEN A;
	END IF;
	EXCEPTION WHEN OTHERS THEN
	ROLLBACK;
	RAISE;
	END;
	/
  exit;
FIN
        
  # Si todo salio bien entonces borramos el archivo en el FTP, logeamos
  # en la tabla GNR_LOG y movemos el archivo local a una carpeta de backup        
  if [ $? -eq 0 ];then          
    # Borramos el archivo del ftp de presea  
    echo ">>> FTP: Borrando $NOMBRE.txt de ${FTP_PRESEA_HOST} ${SRC_FROM}" >> $LOG_FILE_TMP
    ftp -n -v $FTP_PRESEA_HOST <<FIN 1>>$LOG_FILE_TMP 2>&1 
    user $FTP_PRESEA_USER $FTP_PRESEA_PASS
    ascii
    prompt
    cd ${SRC_FROM}    
    delete ${NOMBRE}".txt" 
    close
    quit
FIN
    mv $file $SRC_TO 
    logexito "EXITO ;-) Se ha procesado la interfaz $PROCESO para el archivo $NOMBRE.txt"
  else
    mv $file $SRC_TO_ERROR/$NOMBRE"_ERROR_"$FECHA".txt"  
    logerror ">>> SQLPLUS: Error en el traspaso de datos de ${TABLA_ORIGEN} a ${TABLA_DESTINO} para $NOMBRE.txt"                                    
  fi
      
  rm $LOG_FILE_TMP    
done


