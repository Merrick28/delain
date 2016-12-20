repertoire=/home/delain/shell
logdir=/home/delain/logs
if test -f $repertoire/encours.txt 
then
#echo "`date` : Déjà en cours" >> $logdir/ia_auto.log
toto=1
else
if [ 7 -le `cat /proc/loadavg | awk '{print $1}' | awk -F "." '{print $1}'` ]
then
echo "`date` : Charge systeme trop elevee au lancement général" >> $logdir/ia_auto.log
else
echo "en_cours" > $repertoire/encours.txt
echo "`date` : Debut du traitement" >> $logdir/ia_auto.log
$repertoire/liste_monstre.sh
$repertoire/ia_boucle.sh >> $logdir/ia_auto.log
if test -f $repertoire/encours.txt 
then
rm $repertoire/encours.txt
fi
fi
fi
