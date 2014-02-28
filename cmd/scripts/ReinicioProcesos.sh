#!/bin/bash
#set -x
#LANG=en_US.UTF-8
#export LANG
#LANGUAGE=en
#LC_CTYPE=en_US.UTF-8

####################################################################
# 
# Reinicio cierre de avisos 
#
####################################################################

# Busco el pid del sh cierreDeAvisos.sh
pid=`ps -fea | grep cierreDeAvisos.sh | grep -v grep | awk '{ print $2}'`
echo $pid 

#Busco el pid del java CierreDeAvisos.java
pid2=`ps -fea | grep java | grep $pid | grep -v grep | awk '{ print $2}'`
echo $pid2 

echo voy a matar cierre de avisos
date
#Mato los dos procesos (sh y java)
/usr/bin/kill -9 $pid
/usr/bin/kill -9 $pid2
echo ya mate

#Relanzo el proceso
nohup /export/home/mo/moar/scripts/cierreDeAvisos.sh &
echo se relanzo el proceso cierre de avisos
date

####################################################################
#
# Reinicio el envio de mails 
#
####################################################################

# Busco el pid del sh enviarMails.sh
pid3=`ps -fea | grep enviarMails.sh | grep -v grep | awk '{ print $2}'`
echo $pid3

#Busco el pid del java EnviarMails.java
pid4=`ps -fea | grep java | grep $pid3 | grep -v grep | awk '{ print $2}'`
echo $pid4
echo voy a matar envio de mails
date

#Mato los dos procesos (sh y java)
/usr/bin/kill -9 $pid3
/usr/bin/kill -9 $pid4

echo ya mate

#Relanzo el proceso
nohup /export/home/mo/moar/scripts/enviarMails.sh &

echo relanzo proceso envio de mails
date

####################################################################
#
# Reinicio el Republicacion Masiva PopUp
#
####################################################################

# Busco el pid del sh enviarMails.sh
pid5=`ps -fea | grep republicacionMasivaOffLine.sh | grep -v grep | awk '{ print $2}'`
echo $pid5

#Busco el pid del java RepublicacionMasivaOffLine.java
pid6=`ps -fea | grep java | grep $pid5 | grep -v grep | awk '{ print $2}'`
echo $pid6
echo voy a matar Republicacion Masiva PopUp
date

#Mato los dos procesos (sh y java)
/usr/bin/kill -9 $pid5
/usr/bin/kill -9 $pid6

echo ya mate

#Relanzo el proceso
nohup /export/home/mo/moar/scripts/republicacionMasivaOffLine.sh &

echo relanzo proceso Republicacion Masiva PopUp
date
