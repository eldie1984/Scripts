#!/bin/bash
#set -x

#####################################################################################################################
#                                          AREA DE PARAMETRIZACION
#####################################################################################################################


variables(){
	APP_NAME=archivar_estados_sin_aviso     # Nombre del Aplicativo. Todos los archivos de log y out llevaran este nombre.(PARAMETRO)
	APP_FUNCION="Depuración de estados sin avisos-Baja Física." # (PARAMETRO)
	DIAS=5                       # Cantidad de días anteriores al actual a eliminar archivos (PARAMETRO)
	DIAS_INTERFACE=5              # Cantidad de días anteriores al actual a eliminar archivos (PARAMETRO)

	#En esta variable van las direcciones, separadas por un espacio, a las que se enviaran los mail de las pruebas en el entorno de desarrollo.
	MAILS_PRUEBAS=soportemo@claringlobal.com.ar


#####################################################################################################################
#                                         FIN DE AREA DE PARAMETRIZACION
#####################################################################################################################
#-------------------------------------------------------------------------------------------------------------
#                                 PARAMETROS COMUNES A TODOS LOS PROCESOS
#-------------------------------------------------------------------------------------------------------------
	#Script que setea las variables de entorno comunes a la mayoria de los procesos.

. /export/home/mo/moar/scripts/set_parametros_comunes.sh

#-------------------------------------------------------------------------------------------------------------

	#Variables de direcciones de mails (Se buscan las direcciones en el archivo definido en MAILS_FILE)
	APP_NAME_UCASE=`echo $APP_NAME | tr '[a-z]' '[A-Z]'`

	MAILS_OK=`cat $MAILS_FILE | grep "${APP_NAME_UCASE}_OK" | awk -F: '{print $2}'`
	MAILS_FALLA=`cat $MAILS_FILE | grep "${APP_NAME_UCASE}_FALLA" | awk -F: '{print $2}'`

	#Si las direcciones del aplicativo no estan en el archivo MAILS_FILE entonces toma las direcciones DEFAULT
	if [[ $MAILS_OK = "" ]]; then
		MAILS_OK=`cat $MAILS_FILE | grep DEFAULT_OK | awk -F: '{print $2}'`
	fi
	if [[ $MAILS_FALLA = "" ]]; then
		MAILS_FALLA=`cat $MAILS_FILE | grep DEFAULT_FALLA | awk -F: '{print $2}'`
	fi



	# Se determina sobre que entorno se esta trabajando...   
	#INSTANCIA=

	if [[ ${ENTORNO} = PROD ]]; then
                echo "" 
		#null
	else
		MAILS_OK=$MAILS_PRUEBAS
		MAILS_FALLA=$MAILS_PRUEBAS
	fi


	#Variables del sistema
	FECHAHOY=`date +%Y%m%d`			# Fecha actual en formato YYYYMMDD 
	ORATAB=/etc/oratab				# Archivo oratab (En este archivo se guardan los path de las base de datos)
}
#====================================================================================================================
#	SETEO DE DIRECTORIOS DE ARCHIVOS 
#====================================================================================================================
directorios(){
	DIR_LOG=${DIR_APP}/logs		          # Directorio de archivos de log ( Log Local ) 
	DIR_CTL=${DIR_APP}/ctl		          # Directorio de archivos de control de SQLLoader
	DIR_OUT=${DIR_APP}/out               # Directorio de archivos de salida 
	DIR_SQL=${DIR_APP}/sql		          # Directorio de archivos SQL 
	DIR_RES=${DIR_APP}/res			       # Directorio de archivos de respuesta del job ( Log Centralizado ) 
	DIR_JAVA=${DIR_APP}/java		       # Directorio de archivos jar y class 

	DIR_DATA=${DIR_APP}/archivos     # Directorio de archivos de datos para el SQLLoader 

	#DIR_BAD=${DIR_APP}/logs/bad	          # Directorio de archivos bad de SQLLoader 
	DIR_BAD=${DIR_DATA}                  # Directorio de archivos bad de SQLLoader (/INSTANCIA/INTERFACE) 
}
#====================================================================================================================
#	       SETEO DE ARCHIVOS
#====================================================================================================================
archivos(){
  TIMESTAMP=`date +%Y%m%d.%H%M%S`

	FILE_OUT=${DIR_OUT}/${APP_NAME}.${TIMESTAMP}.out	      # Archivo de salida del proceso 
	FILE_LOG=${DIR_LOG}/${APP_NAME}.${TIMESTAMP}.log	      # Archivo de log del proceso 
	FILE_OK=${DIR_RES}/${APP_NAME}.${TIMESTAMP}.ok           # Archivo de respuesta de proceso Ok 
	FILE_ERR=${DIR_RES}/${APP_NAME}.${TIMESTAMP}.error       # Archivo de respuesta de proceso con error 
	FILE_AUX=${DIR_LOG}/${APP_NAME}.${TIMESTAMP}.aux.log  	# Archivo Auxiliar

	FILE_MAILBODY=${DIR_OUT}/${APP_NAME}.${TIMESTAMP}.mailbody.txt #Archivo que contiene el cuerpo del mail a enviar.
	FILE_BORRADOS=${DIR_OUT}/${APP_NAME}.${TIMESTAMP}.borrados.out #Archivo que contiene la lista de archivos a borrar
	                                                               #en la depuracion.
}
#====================================================================================================================
#  Loguea por pantalla y escribe en el archivo de log
#====================================================================================================================
loguear(){  
	echo `date '+%d/%m/%Y-%H:%M:%S'`"-${1}" | tee -a ${FILE_LOG} 2>/dev/null
}
#====================================================================================================================
#  Setea ambiente ORACLE
#====================================================================================================================
SetearAmbienteOracle() {

   loguear  "ACCION: Seteando Ambiente Oracle [INSTANCIA: ${INSTANCIA_BD}]"

	if [[ $INSTANCIA_BD = "" ]]; then
		loguear  "RESULTADO: ERROR - Parametro de Instancia Vacio" 
		return 1	
	fi

	ORACLE_SID=$INSTANCIA_BD

	export ORACLE_SID

	loguear "RESULTADO: OK - Ambiente Oracle seteado. "

	return 0
}
#====================================================================================================================
# Realiza una conexion con la base ORACLE
#====================================================================================================================
conectarse() {

sqlplus ${USUARIO_H_BD}@${ORACLE_SID}<<EOF

exit;
EOF
}
#====================================================================================================================
#  Prueba la conexion con la base ORACLE
#====================================================================================================================
TestConexionOracle(){
	
	#----- Conexion mediante SQLPlus -----# 
	loguear  "ACCION: Testeando conexion con base de datos"   

	#rm ${FILE_AUX} 2>/dev/null

	conectarse > ${FILE_AUX} 2>&1

	cat ${FILE_AUX} | grep "ORA-01017" 2>/dev/null
 	if [ $? = 0 ]; then
		loguear "RESULTADO: ERROR ORA-01017 - No se pudo acceder con el usuario especificado."
		return 1	
 	else
     	cat ${FILE_AUX} | grep "ORA-01034" 2>/dev/null 
      if [ $? = 0 ]; then
   		loguear "RESULTADO: ERROR ORA-01034 Oracle not available."
   		return 1	
		else
   		cat ${FILE_AUX} | grep "SQL>" 2>/dev/null 
         if [ $? != 0 ]; then
            loguear "RESULTADO: ERROR - La instancia de Oracle no se encuentra levantada."
      		return 1	
     		fi
      fi
 	fi	

	loguear "RESULTADO: OK - Conexion con la base realizada."
  	#rm ${FILE_AUX} 2>/dev/null
}
#====================================================================================================================
# Envio de mails
#====================================================================================================================
EnviarEmail () {
   #Se define a que direcciones se manda el mail...
   if [ $2 = "OK" ]; then       
      DIRECCIONES=$MAILS_OK
   else
      DIRECCIONES=$MAILS_FALLA
   fi

   #Datos del proceso
   echo "PROCESO: $APP_NAME " >> ${FILE_MAILBODY}
   echo "FUNCION: $APP_FUNCION " >> ${FILE_MAILBODY}
   echo "HOSTNAME: ${HOSTNAME} " >> ${FILE_MAILBODY}
   echo "ENTORNO: ${ENTORNO} " >> ${FILE_MAILBODY}
   echo "SHELL SCRIPT: $SCRIPTNAME " >> ${FILE_MAILBODY}   
   echo "PARAMETROS PASADOS AL SHELL SCRIPT: $PARAMETROS " >> ${FILE_MAILBODY}   
   echo "INSTANCIA DE BASE DE DATOS: ${INSTANCIA_BD} " >> ${FILE_MAILBODY}
   echo "LANZADO POR: $LANZADOR " >> ${FILE_MAILBODY}
   echo " " >> ${FILE_MAILBODY}

	#Se adjunta al mail el archivo de LOG...
	echo " " >> ${FILE_MAILBODY}
	echo "===========================================================================================" >> ${FILE_MAILBODY}
	echo "ARCHIVO DE LOG (${FILE_LOG})" >> ${FILE_MAILBODY}
	echo "===========================================================================================" >> ${FILE_MAILBODY}
	echo " " >> ${FILE_MAILBODY}

	cat ${FILE_LOG} >>  ${FILE_MAILBODY}

  
	#Se adjunta al mail el archivo de OUT...
	echo " " >> ${FILE_MAILBODY}
	echo "===========================================================================================" >> ${FILE_MAILBODY}
	echo "ARCHIVO DE SALIDA (${FILE_OUT})" >> ${FILE_MAILBODY}
	echo "===========================================================================================" >> ${FILE_MAILBODY}
	echo " " >> ${FILE_MAILBODY}

	#ls ${FILE_OUT} 2>/dev/null
	ls ${FILE_OUT} > /dev/null 2>&1
	SAL=$?
	if [ ${SAL} = "0" ]; then       
		cat ${FILE_OUT} >> ${FILE_MAILBODY}
	fi

	echo " " >> ${FILE_MAILBODY}
	echo "===========================================================================================" >> ${FILE_MAILBODY}
	echo "FIN ARCHIVO DE SALIDA" >> ${FILE_MAILBODY}
	echo "===========================================================================================" >> ${FILE_MAILBODY}
	echo " " >> ${FILE_MAILBODY}



	#Envia el mail...
	mail -s "${APP_NAME} - $1" ${DIRECCIONES} < ${FILE_MAILBODY}
	SAL=$?
	rm ${FILE_MAILBODY}
}
#====================================================================================================================
# DEPURACION DE FILESYSTEM DEL APLICATIVO
#====================================================================================================================
DepurarFS_Aplic(){

	#STATUS DE LOS FILESYSTEM ANTES DE DEPURAR
   echo "----------------------------------------------------------------------------------------------------" >>${FILE_OUT}
   echo "Status del FileSystem de la aplicacion antes de la depuracion (los tamaños estan expresados en Kb):" >>${FILE_OUT}
   echo "----------------------------------------------------------------------------------------------------" >>${FILE_OUT}
   df -k ${DIR_APP} >>${FILE_OUT}
   echo "" >> ${FILE_OUT}

   echo "-------------------------------------------------------------------------------------------" >>${FILE_OUT}
   echo "DEPURACION DEL FILESYSTEM DEL APLICATIVO" >>${FILE_OUT}
   echo "-------------------------------------------------------------------------------------------" >>${FILE_OUT}
   echo "" >> ${FILE_OUT}

   #Depuracion de archivos .OUT
   find ${DIR_LOG}/${APP_NAME}.* -type f -mtime +${DIAS} -exec ls {} \; > ${FILE_BORRADOS} 2>/dev/null
   find ${DIR_LOG}/${APP_NAME}.* -type f -mtime +${DIAS} -exec rm {} \; > /dev/null 2>&1 
   SAL=$?
   if [ ${SAL} = "0" ]; then       
      echo "Se borraron los siguientes archivos de ${DIR_LOG} con una antiguedad mayor a ${DIAS} dias:" >>${FILE_OUT}
      cat ${FILE_BORRADOS} >>${FILE_OUT}
      echo "" >> ${FILE_OUT}
   fi

   #Depuracion de archivos .OUT
   find ${DIR_OUT}/${APP_NAME}.*  -type f -mtime +${DIAS} -exec ls {} \; > ${FILE_BORRADOS} 2>/dev/null
   find ${DIR_OUT}/${APP_NAME}.*  -type f -mtime +${DIAS} -exec rm {} \; > /dev/null 2>&1 
   SAL=$?
   if [ ${SAL} = "0" ]; then       
      echo "Se borraron los siguientes archivos de ${DIR_OUT} con una antiguedad mayor a ${DIAS} dias:" >>${FILE_OUT}
      cat ${FILE_BORRADOS} >>${FILE_OUT}
      echo "" >> ${FILE_OUT}
   fi

   #Depuracion de archivos .BAD
   find ${DIR_BAD}/${APP_NAME}.*  -type f -mtime +${DIAS} -exec ls {} \; > ${FILE_BORRADOS} 2>/dev/null
   find ${DIR_BAD}/${APP_NAME}.*  -type f -mtime +${DIAS} -exec rm {} \; > /dev/null 2>&1 
   SAL=$?
   if [ ${SAL} = "0" ]; then       
      echo "Se borrarron los siguientes  archivos de ${DIR_BAD} con una antiguedad mayor a ${DIAS} dias:" >>${FILE_OUT}
      cat ${FILE_BORRADOS} >>${FILE_OUT}
      echo "" >> ${FILE_OUT}
   fi

   #Depuracion de archivos .RES
   find ${DIR_RES}/${APP_NAME}.*  -type f -mtime +${DIAS} -exec ls {} \; > ${FILE_BORRADOS} 2>/dev/null
   find ${DIR_RES}/${APP_NAME}.*  -type f -mtime +${DIAS} -exec rm {} \; > /dev/null 2>&1 
   SAL=$?
   if [ ${SAL} = "0" ]; then       
      echo "Se borraron los siguientes  archivos de ${DIR_RES} con una antiguedad mayor a ${DIAS} dias:" >>${FILE_OUT}
      cat ${FILE_BORRADOS} >>${FILE_OUT}
      echo "" >> ${FILE_OUT}
   fi

	#STATUS DE LOS FILESYSTEM DESPUES DE DEPURAR
   echo "-----------------------------------------------------------------------------------------------------" >>${FILE_OUT}
   echo "Status del FileSystem de la aplicacion despues de la depuracion (los tamaños estan expresados en Kb):" >>${FILE_OUT}
   echo "-----------------------------------------------------------------------------------------------------" >>${FILE_OUT}

   df -k ${DIR_APP} >>${FILE_OUT}
   echo "" >> ${FILE_OUT}

}
#====================================================================================================================
# DEPURACION DE FILESYSTEM DE INTERFACE
#====================================================================================================================
#VER:Sacar esta funcion
DepurarFS_Interface(){

	#STATUS DEL FILESYSTEM ANTES DE DEPURAR
   echo "-----------------------------------------------------------------------------------------------------" >>${FILE_OUT}
   echo "Status del FileSystem ${DIR_DATA} antes de la depuracion (los tamaños estan expresados en Kb):" >>${FILE_OUT}
   echo "-----------------------------------------------------------------------------------------------------" >>${FILE_OUT}
   df -k ${DIR_DATA} >>${FILE_OUT}
   echo "" >> ${FILE_OUT}
   
   #DEPURACION DEL FILESYSTEM /INTANCIA/INTERFACE
   #BORRADO DE LOS ARCHIVOS DE DATOS DE CLEARING Y ATRC EN EL DIRECTORIO \BASE_NAME\INTERFACE (CAMBIAR)
   find ${DIR_DATA}/${APP_NAME}.* -type f -mtime +${DIAS_INTERFACE} -exec ls {} \; > ${FILE_BORRADOS} 2>/dev/null
   find ${DIR_DATA}/${APP_NAME}.* -type f -mtime +${DIAS_INTERFACE} -exec rm {} \; > /dev/null 2>&1
   SAL=$?

   echo "-------------------------------------------------------------------------------------------" >>${FILE_OUT}
   echo "DEPURACION DEL FILESYSTEM ${DIR_DATA}" >>${FILE_OUT}
   echo "-------------------------------------------------------------------------------------------" >>${FILE_OUT}
   echo "" >> ${FILE_OUT}

   if [ ${SAL} = "0" ]; then       
      echo "Se borraron los archivos de liquidez ubicados en ${DIR_DATA} con una antiguedad mayor a ${DIAS_INTERFACE} dias:" >>${FILE_OUT}
      cat ${FILE_BORRADOS} >>${FILE_OUT}
      echo "" >> ${FILE_OUT}
   fi

	#STATUS DEL FILESYSTEM DESPUES DE DEPURAR
   echo "-----------------------------------------------------------------------------------------------------" >>${FILE_OUT}
   echo "Status del FileSystem ${DIR_DATA} despues de la depuracion (los tamaños estan expresados en Kb):" >>${FILE_OUT}
   echo "-----------------------------------------------------------------------------------------------------" >>${FILE_OUT}
   df -k ${DIR_DATA} >>${FILE_OUT}
   echo "" >> ${FILE_OUT}
   
}
#====================================================================================================================
#RESUMEN DEL JOB
#====================================================================================================================
resumen(){

   RET=$1

   if [ $RET = "OK" ]; then

				loguear "TERMINACION: PROCESO $APP_NAME_UCASE FINALIZADO EXITOSAMENTE."
   
				DepurarFS_Aplic
				DepurarFS_Interface
				EnviarEmail "Proceso $APP_NAME Finalizado Exitosamente" OK              
				exit 0 

   else
      #WARNING
      if [ $RET = "WARNING" ]; then
         loguear "TERMINACION: WARNING - Proceso $APP_NAME terminado con errores."
   
         DepurarFS_Aplic
         DepurarFS_Interface
         EnviarEmail "Proceso $APP_NAME terminado con errores." ERROR              
         exit 1 

      else
         loguear "TERMINACION: ERROR - Proceso abortado."
   
         EnviarEmail "Proceso $APP_NAME abortado." ERROR              
         exit 2 
      fi
   fi
}
#====================================================================================================================
# Ejecuta SQL
#====================================================================================================================
# Parametros
# $1->Nombre del script SQL
# $2->Descripcion del script
#====================================================================================================================
EjecutarSQL(){

FILE_SP=${DIR_SQL}/$1
FILE_SP_OUT=${DIR_OUT}/${APP_NAME}.${TIMESTAMP}.SQL.`echo $1 | awk -F. '{printf "%s.out",$1}'`

   loguear  "ACCION: Ejecutando script SQL [$1]."

   if [ !  -f ${FILE_SP} ]; then
		loguear  "RESULTADO: ERROR - Archivo $FILE_SP no fue encontrado." 
		return 1
	fi

loguear "[$FILE_SP_OUT]"

  	sqlplus ${USUARIO_H_BD}@${ORACLE_SID} @${FILE_SP} $2 $3 $4 $5 > ${FILE_SP_OUT}	
   
  	RETSQLPLUS=$?

   #rm ${FILE_SP_OUT} 2>/dev/null

	if [ $RETSQLPLUS != 0 ]; then
		#more ${FILE_SP_OUT}|grep -i codigo >> ${FILE_LOG}
		loguear  "RESULTADO: ERROR - Se produjo un error al ejecutar el script $FILE_SP." 

      #Guarda en el archivo de salida lo generado por el SQLPLUS...
      echo "-------------------------------------------------------------------------------------------" >>${FILE_OUT}
      echo "SPOOL DEL SCRIPT $1" >>${FILE_OUT}
      echo "-------------------------------------------------------------------------------------------" >>${FILE_OUT}
   	cat ${FILE_SP_OUT} >>${FILE_OUT}
      echo "" >> ${FILE_OUT}

		return 1	
	else 
		loguear  "RESULTADO: OK - Script SQL $FILE_SP procesado." 
  	fi

	
}
#====================================================================================================================
# Cargar Archivo mediante Loader
#====================================================================================================================
# Parametros
# $1->
# $2->
#EJEMPLO: Cargar {Archivo} {Tabla} {Archivo de Control}
#Retornos: 0-OK ; 1-Warning ; 3-Error 
#====================================================================================================================
CargarArchivo(){

      #Archivo de datos a cargar...
      FILE_LOAD_DATA=${DIR_DATA}/$1

      #Tabla de la base de Datos a cargar...
      TABLA=$2

      #Archivo de Control de Carga...
      FILE_LOAD_CTL=${DIR_CTL}/$3

      #Archivos Auxiliares...
      FILE_LOAD_BAD=${DIR_BAD}/${APP_NAME}.${TIMESTAMP}.loader.$1.bad
      FILE_LOAD_DIS=${DIR_BAD}/${APP_NAME}.${TIMESTAMP}.loader.$1.dis
      FILE_LOAD_OUT=${DIR_OUT}/${APP_NAME}.${TIMESTAMP}.loader.$1.out

      loguear  "ACCION: Cargando archivo $1 en tabla $2."

      #Se verifica que este el archivo de control...      
      if [ !  -f ${FILE_LOAD_CTL} ]; then
   		loguear  "RESULTADO: ERROR - El Archivo de control $FILE_LOAD_CTL no fue encontrado." 
   		return 2
      fi

      #Se verifica que este el archivo de datos...      
      if [ ! -f ${FILE_LOAD_DATA} ]; then
   		loguear  "RESULTADO: ERROR - El Archivo de datos $FILE_LOAD_DATA no fue encontrado." 
   		return 2
      fi


      sqlldr userid=${USUARIO_H_BD}@${ORACLE_SID}, control=${FILE_LOAD_CTL}, log=${FILE_LOAD_OUT}, bad=${FILE_LOAD_BAD}, data=${FILE_LOAD_DATA}, discard=${FILE_LOAD_DIS}, errors=1000000, silent=all, bindsize=2470000, rows=100

      SAL=$?

      case ${SAL} in
         '0')
      		loguear  "RESULTADO: OK - Archivo $1 cargado exitosamente." 
            echo "RESUMEN DE CARGA:" >>${FILE_LOG}
            echo " " >>${FILE_LOG}
            more ${FILE_LOAD_OUT}|grep "Row"|awk -F, '{$20="             ";print $20,$1}'>>${FILE_LOG}
            echo " " >>${FILE_LOG}
            more ${FILE_LOAD_OUT}|grep "time"|awk -F, '{$20="               ";print $20,$1}'>>${FILE_LOG}
            echo " " >>${FILE_LOG}
            more ${FILE_LOAD_OUT}|grep "Space"|awk -F, '{$20="               ";print $20,$1}'>>${FILE_LOG}
            echo " " >>${FILE_LOG}
            
            RenombrarArchivoDeInterface $1 #Se renombra el archivo de datos
            
      		return 0
         ;;
         '1')
      		loguear  "RESULTADO: ERROR - SQLLDR:Error de Sintaxis/ Error Fatal de Oracle." 

            #Pega al archivo de salida el de salida del loader...
            cat ${FILE_LOAD_OUT} >>${FILE_OUT}
      		return 2
         ;;
         '2')
      		loguear  "RESULTADO: WARNING - Algunos Registros presentan error." 
            echo "RESUMEN DE CARGA:" >>${FILE_LOG}
            echo " " >>${FILE_LOG}
            more ${FILE_LOAD_OUT}|grep "Row"|awk -F, '{$20="             ";print $20,$1}'>>${FILE_LOG}
            echo " " >>${FILE_LOG}
            more ${FILE_LOAD_OUT}|grep "time"|awk -F, '{$20="               ";print $20,$1}'>>${FILE_LOG}
            echo " " >>${FILE_LOG}
            more ${FILE_LOAD_OUT}|grep "Space"|awk -F, '{$20="               ";print $20,$1}'>>${FILE_LOG}
            echo " " >>${FILE_LOG}

            #Pega al archivo de salida al de salida del loader...
            echo "------------------------------------------------------------------------------------" >>${FILE_OUT}
            echo ${FILE_LOAD_OUT} >>${FILE_OUT}
            echo "------------------------------------------------------------------------------------" >>${FILE_OUT}
            cat ${FILE_LOAD_OUT} >>${FILE_OUT}

      		return 1
         ;;
         '3')
      		loguear "RESULTADO: ERROR - SQLLDR: Error de sistema operativo." 

            #Pega al archivo de salida al de salida del loader...
            echo "------------------------------------------------------------------------------------" >>${FILE_OUT}
            echo ${FILE_LOAD_OUT} >>${FILE_OUT}
            echo "------------------------------------------------------------------------------------" >>${FILE_OUT}
            cat ${FILE_LOAD_OUT} >>${FILE_OUT}

      		return 2
         ;;
           *)
      		loguear "RESULTADO: ERROR - SQLLDR: Error desconocido." 

            #Pega al archivo de salida al de salida del loader...
            echo "------------------------------------------------------------------------------------" >>${FILE_OUT}
            echo ${FILE_LOAD_OUT} >>${FILE_OUT}
            echo "------------------------------------------------------------------------------------" >>${FILE_OUT}
            cat ${FILE_LOAD_OUT} >>${FILE_OUT}

      		return 2
         ;;
      esac
}
#====================================================================================================================
# FUNCION PARA RESTAR FECHA
#====================================================================================================================
# Devuelve en la variable HOY_MENOS_X_DIAS,en  formato YYMMDD, la fecha de hoy menos la cantidad de dias especificada
# en la variable X_DIAS o pasada por parametro.
#====================================================================================================================
hoy_menos_x_dias(){
      if [ $1 != "" ]; then 
         X_DIAS=$1
      fi

      ANIO=`date +%Y`
      MES=`date +%m`
      DIA=`date +%d`
      CONT=${X_DIAS}
      
      while [ ${CONT} != "0" ]
      do
         DIA=$(($DIA - 1))
         if [ ${#DIA} = 1 ];then
            DIA=0${DIA}
         fi

         if [ ${DIA} = "00" ];then
#         if [ ${CARGO_TODOS} = "N" ]; then		
         
            MES=$(($MES - 1))
            
            if [ ${MES} = "0" ];then
               MES=12
               ANIO=$(($ANIO - 1))
            fi
             
            if [ ${#MES} = 1 ];then
               MES=0${MES}
            fi
            #Asigna el ultimo dia del mes
            case ${MES} in
               '01')
                  DIA=31
               ;;
               '02')
                  if [ ${ANIO} = "2004" ];then
                     # ANIO BISISESTO...
                     DIA=29
                  else
                     DIA=28
                  fi
               ;;
               '03')
                  DIA=31
               ;;
               '04')
                  DIA=30
               ;;
               '05')
                  DIA=31
               ;;
               '06')
                  DIA=30
               ;;
               '07')
                  DIA=31
               ;;
               '08')
                  DIA=31
               ;;
               '09')
                  DIA=30
               ;;
               '10')
                  DIA=31
               ;;
               '11')
                  DIA=30
               ;;
               '12')
                  DIA=31
               ;;
            esac
         fi
         CONT=$(($CONT - 1))
         #echo ${CONT}
      done
      
      HOY_MENOS_X_DIAS=${ANIO}${MES}${DIA}
      return ${ANIO}${MES}${DIA}
}
#====================================================================================================================
# Cambia el nombre del archivo pasado por parametro por aplicacion.YYMMDD.HHMISS.
#====================================================================================================================
RenombrarArchivoDeInterface(){
   mv ${DIR_DATA}/$1 ${DIR_DATA}/${APP_NAME}.${TIMESTAMP}.input.$1
}
#====================================================================================================================
#SETEA LA VARIABLE FECHAPROCESO
#====================================================================================================================
#Si el primer parametro del script es una fecha con formato YYYYMMDD esta
#se setea en la variable FECHAPROCESO, si es otra cosa, entonces en dicha
#variable se guarda la fecha del día.
SetFechaProceso(){
  FECHAPROCESO=`date +%Y%m%d` 
  
  #Se analiza si hay un primer parametro...
  if [ $1 ] 
  then 
    #Se analiza si se trata de una fecha en formato YYYYMMDD...
    if echo $1 | grep "20[01][0-9][01][0-9][0123][0-9]" >/dev/null 
    then 
      FECHAPROCESO=$1 
    fi 
  fi 
}


#====================================================================================================================
#EJECUTA PROCESOS JAVA
#====================================================================================================================
EjecutarJava(){

	JAVA_CP=$1
	JAVA_MAIN=$2

	FILE_JAVA_LOG=${DIR_LOG}/${APP_NAME}.${JAVA_MAIN}.${TIMESTAMP}.log

	loguear  "ACCION: Ejecutando java [$2] para [$3]."

	${JAVA_HOME}/java -classpath ${CLASSPATH}:${DIR_JAVA}:${JAVA_CP} ${JAVA_MAIN} $3 $4 $5  > ${FILE_JAVA_LOG} 2>&1

	SAL=$?

	if [ ${SAL} != 0 ]; then
		loguear  "RESULTADO: ERROR - Se produjo un error al ejecutar el script ${JAVA_MAIN}." 

		#Guarda en el archivo de salida lo generado por JAVA...
		echo "-------------------------------------------------------------------------------------------" >>${FILE_OUT}
		echo "SPOOL DEL SCRIPT ${JAVA_MAIN}" >>${FILE_OUT}
		echo "-------------------------------------------------------------------------------------------" >>${FILE_OUT}
		cat ${FILE_JAVA_LOG} >>${FILE_OUT}
		echo "" >> ${FILE_OUT}

		return 1	
	else 
		loguear  "RESULTADO: OK - Proceso Java ${JAVA_MAIN} procesado exitosamente." 
	fi

}

#====================================================================================================================
#                                    JUNTA LAS LINEAS DE BODY Y CODEBAR
#====================================================================================================================
JuntarBodyConCodigoBarras(){

IN_PAGO_FACIL=$1
OUT_PAGO_FACIL=$2

loguear  "ACCION: Juantando Body con CODIGO BARRAS."

# Define las constantes que corresponden a los distintos registros dentro del archivo enviado por NPS
const_header_file=1
const_header_batch=5
const_body_batch=6
const_body_barcode=7
const_trailer_batch=8
const_trailer_file=9

# Inicializa un contador para recorrer las lineas
cont=0

# Obtiene la cantidad de lineas del archivo
cantidadlineas=`cat ${IN_PAGO_FACIL} | wc -l`
cantidadlineas=`echo ${cantidadlineas} +1 | bc`

# Itera a traves del archivo de PagoFacil
while [ "${cantidadlineas}" -gt "$cont" ] ; do
	# Incrementa el contador
	cont=`echo ${cont} +1 | bc`

	# Obtiene el primer caracter de la linea leŒda
	primercaracter=`cat ${IN_PAGO_FACIL} | head -${cont} | tail +${cont} | cut -b1`


	# La linea corresponde al body_batch #########################################
	if [ "${primercaracter}" -eq "${const_body_batch}" ]
	then
		# Procesa los datos de la transaccion
		#echo "$cont.    Procesando linea del batch..."
		#echo "$cont.      Procesando linea de la transaccion..."
		linea1=`cat ${IN_PAGO_FACIL} | head -${cont} | tail +${cont} |tr -d '\r' | tr -d '\n'`

 		# Incrementa el contador
		cont=`echo ${cont} +1 | bc`

		# Procesa el codigo de barras de la transaccion
		#echo "$cont.      Procesando linea del codigo de barras..."
		linea2=`cat ${IN_PAGO_FACIL} | head -${cont} | tail +${cont}`

		# Escribe a un archivo temporal la linea
		echo "${linea1}${linea2}" >> ${OUT_PAGO_FACIL}
 	fi
done

loguear  "RESULTADO: OK - Juantando Body con CODIGO BARRAS procesado exitosamente." 
}

#####################################################################################################################
#                                                  MAIN
#####################################################################################################################
SCRIPTNAME=$0   #Se guarda el path y el nombre del script.
PARAMETROS="'${1} ${2} ${3} ${4} ${5}'" #Parametros pasados al shell script.

SetFechaProceso $1
variables
directorios
archivos

loguear "Comienzo del proceso [${APP_NAME}]"
#====================================================================================================================
#                                         SETEO DEL AMBIENTE ORACLE
#====================================================================================================================
SetearAmbienteOracle
RET=$?
if [ ${RET} != "0" ]; then
   echo
   resumen ABORT
fi
#====================================================================================================================
#                                    TEST DE CONEXION CON LA BASE DE DATOS       
#====================================================================================================================
TestConexionOracle
RET=$?
if [ ${RET} != "0" ]; then
   echo
   resumen ABORT
fi
#####################################################################################################################
#                                            AREA CUSTOMIZABLE
#####################################################################################################################
#NOTA: En la variable FECHAPROCESO se guarda la fecha de hoy en formato YYYYMMDD,o la fecha que se pase como primer
#parametro al script (esta ultima tambien debe tener el formato YYYYMMDD).

#si vino el parametro como fecha de se usa la fecha enviada.
#si el parametro NO es una fecha se usa la del dia.
#si no viene nada se usa la del dia menos uno.


EjecutarSQL archivar_estados_sin_aviso.sql

#####################################################################################################################
#FIN AREA CUSTOMIZABLE
#####################################################################################################################
  
#FIN DEL PROCESO
resumen OK
