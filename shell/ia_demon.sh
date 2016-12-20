#!/bin/sh
repertoire=/home/delain/shell
logdir=/home/delain/logs
while true 
do
if [ 7 -le `cat /proc/loadavg | awk '{print $1}' | awk -F "." '{print $1}'` ]
then
echo "`date` : Charge systeme trop elevee au lancement général" >> $logdir/ia_auto.log
else
echo "`date` : Debut du traitement" >> $logdir/ia_auto.log
$repertoire/liste_monstre.sh  >> $logdir/ia_auto.log
$repertoire/ia_boucle.sh >> $logdir/ia_auto.log
fi
sleep 10
done
