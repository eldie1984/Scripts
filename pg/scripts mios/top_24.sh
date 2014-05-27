#!/usr/bin/bash

#Definicion de variables
PROCDATE=`date "+%d-%m-%Y ,%H:%M Hs."`
MACHINE=`hostname`
LIST_DISTR=`cat $UNXEXOUTILS/topten24_REPORT_LIST.txt`
SUBJECT="TOP TEN EJECUCIONES ULTIMAS 24 HS ${MACHINE} at ${PROCDATE} "


# Fecha y hora de inicio del batch funcional.
#deb=`uxlst ctl upr=syzsydu09 full|tail -1|awk '{print$5","substr($6,1,4)}'`
# Fecha y hora de fin del batch funcional.
#fin=`uxlst ctl upr=syzsyfu89 full|tail -1|awk '{print$7","substr($8,1,4)}'`
fin=`date +%d/%m/%Y,%H%M`
# Calcula el dia anterior.
function Obsoleto {
local deb=`date +"%d/%m/%Y,%H%M"|awk '{
  FIN=$0;
  split("31,28,31,30,31,30,31,31,30,31,30,31",mon,",");
  dd=substr(FIN,1,2);
  mm=substr(FIN,4,2);
  yy=substr(FIN,7,4);
  hh24mi=substr(FIN,12,4);
  bisiesto=0;
  prv_yy=yy;
  if(dd-1==0) {
    if(mm-1==0) {
      prv_mm=12;
      prv_yy=yy-1;
    }
    else {
      prv_mm=mm-1;
      if(prv_mm==2) {
        if((yy%400==0||yy%4==0&&yy%100!=0)) bisiesto=1;
      }
    }
    prv_dd=mon[prv_mm-1]+bisiesto;
  }
  else {
    prv_dd=dd-1;
    prv_mm=mm;
  }
  printf("%02d/%02d/%04d,%s", prv_dd, prv_mm, prv_yy, hh24mi);
}'`
}

FMT="%d/%m/%Y,%H%M"
deb=`addtime $(date +"${FMT}") "${FMT}" -86400 "${FMT}"`

# Transformacion de la fecha a formato yyyymmdd
syd=`echo ${deb}|awk -F, '{
  dd=substr($1,1,2);
  mm=substr($1,4,2);
  yy=substr($1,7,4);
  print yy mm dd;
}'`

# Transformacion de la fecha a formato yyyymmdd
syf=`echo ${fin}|awk -F, '{
  dd=substr($1,1,2);
  mm=substr($1,4,2);
  yy=substr($1,7,4);
  print yy mm dd;
}'`

# Si el batch aun no finalizo, toma la fecha actual como de fin de consulta.
if [ -z "${syf}" ]; then
  fin=`date +"%d/%m/%Y,%H%M"`
else
  if [ ${syf} -lt ${syd} ]; then
    fin=`date +"%d/%m/%Y,%H%M"`
  fi
fi

# Consulta $U acotada al rango horario del batch funcional.
# Transformacion de fecha inicio y fin a juliano.
# Se pasa todo a segundos para realizar la diferencia (Fin-Ini).
# Ordena por tiempo de ejecucion de mayor a menor y muestra los primeros 10 uprocs.
# Muestra el tiempo consumido en unidad de minutos.

#printf "UPR\t\tDATE\t\tJULIAN\t\tBEGIN\tJULIAN\t\tEND\tELAPSE\n" > top_ten24.log
echo ${deb} ${fin}
uxlst ctl ses=syz* since=\(${deb}\) before=\(${fin}\) full|grep -v SYZ6 | grep -v SYZRI | grep -v SYZCCR | awk '
/^ / {
  split("31,59,90,120,151,181,212,243,273,304,334,365",mon,",");

  ddd_0=0;
  dd=substr($5,1,2);
  mm=substr($5,4,2);
  yy_0=substr($5,7,4);
  i=mm-1;

  plus=0;
  if((yy_0%400==0||yy_0%4==0&&yy_0%100!=0)&&mm>2) plus=1;

  if(mm>1) ddd_0=mon[i];

  ddd_0+=int(dd)+plus;

  ddd_1=0;
  dd=substr($7,1,2);
  mm=substr($7,4,2);
  yy_1=substr($7,7,4);
  i=mm-1;

  plus=0;
  if((yy_1%400==0||yy_1%4==0&&yy_1%100!=0)&&mm>2) plus=1;

  if(mm>1) ddd_1=mon[i];

  ddd_1+=dd+plus;

  hh24=substr($6,1,2);
  mi=substr($6,3,2);
  ss_0=substr($6,5,2);
  mi+=hh24*60;
  ss_0+=mi*60;

  hh24=substr($8,1,2);
  mi=substr($8,3,2);
  ss_1=substr($8,5,2);
  mi+=hh24*60;
  ss_1+=mi*60;

  printf("%s\t%s\t%03d/%04d\t%s\t%03d/%04d\t%s\t%6d\n",$2,$5,ddd_0,yy_0,$6,ddd_1,yy_1,$8,((ddd_1*24*3600+ss_1) - (ddd_0*24*3600+ss_0))/60);

}' | sort -nrk7 | head -10 > top_ten24.log


printf "UPR\t\tDATE\tBEGIN\tEND\tELAPSE\tDESCRIPCION\n" > top_ten24_mail.log

while read LINEA
do
  MYUPR=`echo $LINEA | awk -F" " '{print $1}'`
  MYDATE=`echo $LINEA | awk -F" " '{print $2}'`
  MYBEGIN=`echo $LINEA | awk -F" " '{print $4}'`
  MYEND=`echo $LINEA | awk -F" " '{print $6}'`
  MYELAPSED=`echo $LINEA | awk -F" " '{print $7}'`
  MYDESCRIP=`uxlst upr upr=$MYUPR | awk 'NR>4 {print $3" "$4" "$5" "$6" "$7" "$8" "$9" "$10" "$11" "$12" "$13}'`


  printf "$MYUPR\t$MYDATE\t$MYBEGIN\t$MYEND\t$MYELAPSED\t$MYDESCRIP\n" >>  top_ten24_mail.log


done < top_ten24.log



###### Envio  Mail  #########################################

#    mailx -s "${SUBJECT}"  ${LIST_DISTR} < top_ten24_mail.log

mailx -s "${SUBJECT}" eduardo.vazquezacosta1@mpsa.com < top_ten24_mail.log
mailx -s "${SUBJECT}" agustin.mouso@mpsa.com < top_ten24_mail.log
mailx -s "${SUBJECT}" robertohoracio.rodriguez2@mpsa.com < top_ten24_mail.log
mailx -s "${SUBJECT}" sebastian.vallone1@mpsa.com < top_ten24_mail.log
mailx -s "${SUBJECT}" mario.castroruiz@ext.mpsa.com < top_ten24_mail.log
mailx -s "${SUBJECT}" valeria.arias@mpsa.com < top_ten24_mail.log
mailx -s "${SUBJECT}" hernan.oshiro@ext.mpsa.com < top_ten24_mail.log
mailx -s "${SUBJECT}" alberto.bley@ext.mpsa.com < top_ten24_mail.log
mailx -s "${SUBJECT}" indus.bamac@mpsa.com < top_ten24_mail.log
mailx -s "${SUBJECT}" andres.moreno@ext.mpsa.com < top_ten24_mail.log
mailx -s "${SUBJECT}" wilsonariel.pastor@ext.mpsa.com < top_ten24_mail.log
mailx -s "${SUBJECT}" dario.veron@ext.mpsa.com < top_ten24_mail.log
mailx -s "${SUBJECT}" mariacarolina.pozo@ext.mpsa.com < top_ten24_mail.log
