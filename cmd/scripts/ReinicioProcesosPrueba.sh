#!/bin/bash
#set -x
#LANG=en_US.UTF-8
#export LANG
#LANGUAGE=en
#LC_CTYPE=en_US.UTF-8


####################################################################
#
# Reinicio el Republicacion Masiva PopUp
#
####################################################################

# Busco el pid del sh enviarMails.sh
echo antes de primer grep
pid5=`ps -fea | grep republicacionMasivaOffLine.sh | grep -v grep | awk '{ print $2}'`
echo $pid5

#Busco el pid del java RepublicacionMasivaOffLine.java
echo antes de segundo grep
pid6=`ps -fea | grep java | grep $pid5 | grep -v grep | awk '{ print $2}'`
echo $pid6
echo voy a matar Republicacion Masiva PopUp
date

#Mato los dos procesos (sh y java)
#/usr/bin/kill -9 $pid5
#/usr/bin/kill -9 $pid6

echo ya mate

#Relanzo el proceso
#nohup /export/home/mo/moar/scripts/republicacionMasivaOffLine.sh &

echo relanzo proceso Republicacion Masiva PopUp
date
