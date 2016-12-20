PATH=$PATH:/usr/sbin:/usr/java/bin:/usr/bin
PATH=$PATH:/usr/local/pgsql/bin:.
#PATH=$PATH:/databases/pgsql-v7.4/bin:.
export PATH

repertoire=/home/delain/shell
BASE=delain;export $BASE
echo '********************'  >> /home/delain/logs/ia_auto.log
echo 'Début traitement :' >> /home/delain/logs/ia_auto.log
date  >> /home/delain/logs/ia_auto.log
$repertoire/stats.sh > /dev/null
$repertoire/boucle_total.sh > /dev/null

echo 'Fin traitement :' >> /home/delain/logs/ia_auto.log
date >> /home/delain/logs/ia_auto.log
