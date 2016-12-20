
DATEJOUR=`date +'%y-%m-%d'`;export DATEJOUR
LOGDIR='/home/sdewitte/logs';export LOGDIR
mv $LOGDIR/ia_auto.log $LOGDIR/ia_auto.$DATEJOUR.log
mv /home/sdewitte/public_html/debug/php.log /home/sdewitte/public_html/debug/php.$DATEJOUR.log
mv /home/sdewitte/public_html/debug/sql.log /home/sdewitte/public_html/debug/sql.$DATEJOUR.log
/bin/mailx -s 'Résultats logs' merrick@jdr-delain.net < $LOGDIR/ia_auto.$DATEJOUR.log
/usr/bin/gzip $LOGDIR/ia_auto.$DATEJOUR.log

