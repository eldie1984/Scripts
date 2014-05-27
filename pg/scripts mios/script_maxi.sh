#!/bin/bash
# -------------------------------------------------------------------------------
# <<expdu.sh>>
# -------------------------------------------------------------------------------
# Description du module : Export Sesion,Tarea,Uprocs
# -------------------------------------------------------------------------------
#
# Codes de retour       :       0 : OK
#                                       1 : Erreur
#
# -------------------------------------------------------------------------------
# Auteur                        : Maximiliano Bley
# Date création                 : 12/11/2013
# Modification:
# -------------------------------------------------------------------------------
# Version: 1.0
# -------------------------------------------------------------------------------
# -------------------------------------------------------------------------------
# Programme principal
# -------------------------------------------------------------------------------
function Usage {
    echo "Erreur de parametres"
    echo "Usage $0 e <SESION> pour l'exportation"
    echo "Usage $0 i <SESION> pour l'importation"

    exit 1
}

# --------------------------------------------------------------------------
# Fonction de controle des parametres d'appel
# --------------------------------------------------------------------------


function Export {
		clear
		#Export Uprocs
		#$UXEXE/uxext UPR EXP UPR=${SESION}* output=EXP_UPR_${SESION}.lst
		echo "UPROC exporté dedans $PWD\EXP_UPR_${SESION}.lst"

        #Export Task
		#$UXEXE/uxext TSK EXP UPR=${SESION}* output=EXP_TSK_${SESION}.lst
		echo "TASK exporté dedans $PWD\EXP_TSK_${SESION}.lst"

        #Export Sesion
        #$UXEXE/uxext SES EXP SES=${SESION} VSES=000 output=EXP_SES_${SESION}.lst
		echo "SESSION exporté dedans $PWD\EXP_SES_${SESION}.lst"

}


function Inport_t {
		clear
		#$UXEXE/uxins TSK EXP CIBLE=* UPR=${SESION}* input=EXP_TSK_${SESION}.lst
		echo "TASK dossier importé de $PWD\EXP_TSK_${SESION}.lst"
		
}

function Inport_u {
		clear
		#$UXEXE/uxins UPR EXP UPR=${SESION}* input=EXP_UPR_${SESION}.lst
		echo "UPROC dossier importé de $PWD\EXP_UPR_${SESION}.lst"
		
}


function Inport_s {
		clear
		#$UXEXE/uxins SES EXP SES=${SESION} input=EXP_SES_${SESION}.lst
		echo "SESSION dossier importé de $PWD\EXP_SES_${SESION}.lst"
		
}

function ControlerParametres {
    if [ $# -ne 2 ]
    then
        Usage
    else
    echo "Parametres : $*"
        if [ $1 = 'e' ]
        then
                SESION=$2
                Export
        else
                if [ $1 = 'i' ]
                then
                        SESION=$2
						Menu
                        
                else
                        Usage
                fi
        fi
   fi
}

#ControlerParametres $*

function Menu {
	while true
	do
	clear
	echo "#####################################################"
	echo
	echo " 1) -> Importer une session "
	echo
	echo " 2) -> Importer une  uproc "
	echo
	echo " 3) -> Importer une tâche "
	echo
	echo " 4) -> Tous "
	echo
	echo " 0) -> Sortie "
	echo
	echo
	echo -n "choisir une des options: "
	read OPCION
	case $OPCION in
		"1") Inport_s
			echo
			echo "Appuyer sur une touche pour continuent"
			read;;
		"2") Inport_u
			echo
			echo "Appuyer sur une touche pour continuent"
			read;;
		"3") Inport_t
			echo
			echo "Appuyer sur une touche pour continuent"
			read;;
		"4") Inport_u
			Inport_s
			Inport_t
			echo
			echo "Appuyer sur une touche pour continuent"
			read
			return 0;;
		"0") return 0;;
		*) ;;
	esac
	done
}


ControlerParametres $*