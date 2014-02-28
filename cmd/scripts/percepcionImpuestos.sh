#! /bin/bash
#set -x

#############################################################################
# VARIABLES
#############################################################################
#. set_parametros_comunes.sh
. /export/home/mo/moar/scripts/set_parametros_comunes.sh

export LANG=en_US.ISO-8859-1

#############################################################################
# VARIABLES
#############################################################################

SRC_ROOT="/export/home/mo/moar"
SRC_FROM="ClarinGlobal/mo/Output"
SRC_TO="${SRC_ROOT}/repositorio_estatico/mas_oportunidades_procesos_presea/percepciones"
SRC_TO_ERROR="${SRC_TO}/error"
SRC_TO_TMP="${SRC_TO}/tmp"
AWK_FILE="${SRC_ROOT}/scripts/percepcionImpuestos.awk"
DB_CONTROL="${SRC_ROOT}/scripts/percepcionImpuestos.ctl"
LOG_FILE="${SRC_ROOT}/logs/percepcionImpuestos.log"
LOG_FILE_TMP="${SRC_ROOT}/logs/percepcionImpuestos.tmp"
PREFIJO="mo_percepcion_impuestos_"
PROCESO="PercepcionImpuestos"
FECHA=`date +%Y%m%d%H%M%S`
TABLA_ORIGEN="ERP_PERCEPCION_TMP"
TABLA_DESTINO="ERP_PERCEPCION"

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
Subject:ERROR EN LA INTERFAZ DE PRESEA PERCEPCION_IMPUESTOS' | mail $MAIL_ERROR_TO > /dev/null   
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

  # Validamos que el formato del archivo sea correcto y en caso de error
  # enviamos un mail y logeamos en GNR_LOG
  dos2unix -ascii -k -n $file percepcion_temp.txt
  chmod 777 percepcion_temp.txt 

  echo ">>> AWK: Validando los datos del archivo $NOMBRE" >> $LOG_FILE_TMP
  awk -f $AWK_FILE percepcion_temp.txt 1>>$LOG_FILE_TMP 2>&1 
  if [ $? -ne 0 ];then
    mv $file $SRC_TO_ERROR/$NOMBRE"_ERROR_"$FECHA".txt"    
    logerror ">>> AWK: Error en el formato del archivo ${NOMBRE}.txt"        
    continue
  fi
  
  # Cargamos los datos del archivo a la BBDD
  echo ">>> SQLLDR: Cargando los datos del archivo $NOMBRE" >> $LOG_FILE_TMP
  sqlldr $USUARIO_BD@$INSTANCIA_BD control=$DB_CONTROL data=percepcion_temp.txt errors=0 log=/dev/null  1>>$LOG_FILE_TMP 2>&1  
  if [ $? -ne 0 ];then        
    mv $file $SRC_TO_ERROR/$NOMBRE"_ERROR_"$FECHA".txt"
    logerror ">>> SQLLDR: Error en la carga del archivo ${NOMBRE}.txt a la tabla ${TABLA_DESTINO}"           
    continue    
  fi
   
  # Si la carga por sqlldr fue exitosa entonces pasamos los datos de la tabla
  # temporal ERP_PERCEPCION_TMP a ERP_PERCEPCION
  echo ">>> SQLPLUS: Pasando los datos de la tabla temporal a la definitiva para $NOMBRE" >> $LOG_FILE_TMP
  sqlplus $USUARIO_BD@$INSTANCIA_BD << FIN 1>>$LOG_FILE_TMP 2>&1 
  whenever sqlerror exit failure;
  insert into $TABLA_DESTINO select a.*,'$NOMBRE.txt' from $TABLA_ORIGEN a;
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
    logexito "EXITO ;-) Se ha procesado la interfaz PERCEPCION_IMPUESTOS para el archivo $NOMBRE.txt"
  else
    mv $file $SRC_TO_ERROR/$NOMBRE"_ERROR_"$FECHA".txt"  
    logerror ">>> SQLPLUS: Error en el traspaso de datos de ${TABLA_ORIGEN} a ${TABLA_DESTINO} para $NOMBRE.txt"                                    
  fi
      
  rm $LOG_FILE_TMP    
done


