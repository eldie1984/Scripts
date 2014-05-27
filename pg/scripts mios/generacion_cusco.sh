#/usr/bin/sh

DIR_CENTRAL="."
DIR_EXPORT="."

function create_file {
for file in $(cat $1)
do
	echo  $2/$file
	if [ ! -f $2/$file.001 ]
	then
		touch $2/$file.001
	fi
done
}

function execute_collection {
for batch in $(cat $1_batch)
do
	echo ejecuto
	echo $UNXEXSCRIPT/$batch
	if [ $? -ne 0 ]
	then 
		error
	fi
done
}


function batch {
if [ $(ls -ltr $1| grep .001 | wc -l) -eq 48 ]
then
	for job in $(cat batch | sed '/#/d;/^ *$/d')
	do
		echo $UNXEXSCRIPT/$job
		if [ $? -ne 0 ]
		then 
			error
		fi
	done
else
	echo No estan los 48 archivos
fi
}

function error {
	echo ocurrio un error en $1
	echo $1 | mailx -s "Probema en la ejecucion de $1" $LISTA_MAIL
}


function menu {
	while true
	do
	clear
	echo "#####################################################"
	echo
	echo " 1) -> Crear archivos Central "
	echo
	echo " 2) -> Crear archivos Export "
	echo
	echo " 3) -> Ejecucion manual del batch de reporting "
	echo
	echo " 0) -> Salir "
	echo
	echo
	echo  "Elija una de las opciones \c"
	read OPCION
	case $OPCION in
		"1") create_file central $DIR_CENTRAL
			echo "Se crearon los archivos de Central"
			while true
			do
				echo " Desea ejecutar la collection? [S/N]:"
				read coll
				case $coll in 
					"s")execute_collection central
						echo "se ejecuto la collection"
						echo "Presione una tecla para continuar"
						read
						break;;
					"S")execute_collection central
						echo "se ejecuto la collection"
						echo "Presione una tecla para continuar"
						read
						break;;
					"n") break;;
					"N") break;;
				esac
			done
		;;
		"2") create_file export $DIR_EXPORT
			echo "Se crearon los archivos de Central"
			while true
			do
				echo " Desea ejecutar la collection? [S/N]:"
				read coll
				case $coll in 
					"s")execute_collection export
						echo "se ejecuto la collection"
						echo "Presione una tecla para continuar"
						read
						break;;
					"S")execute_collection export
						echo "se ejecuto la collection"
						echo "Presione una tecla para continuar"
						read
						break;;
					"n") break ;;
					"N") break;;
				esac
			done
		;;
		"3") batch
			echo "Presione una tecla para continuar"
			read;;
		"0") return 0;;
		*) ;;
	esac
	done
}

if [ $# -ne 0 ]
then
	case $1 in 
	
	"1")create_file central $DIR_CENTRAL
		exit 0;;
	"CC")	
		execute_collection central
		exit 0;;
	"2")create_file export $DIR_EXPORT
		exit 0;;
	"CE")execute_collection export
		exit 0;;
	"3")batch
	;;
	esac
else
menu
fi