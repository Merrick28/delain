#LD_LIBRARY_PATH=/usr/lib:/usr/local/lib:/usr/local/pgsql/lib:/usr/local/ssl/lib;export LD_LIBRARY_PATH
/usr/bin/php /home/delain/public_html/www/signatures.php
#/usr/local/pgsql/bin/psql -t << EOF
#delete from log_objet where llobj_date < now() - '90 days'::interval;
#update positions set pos_magie = pos_magie - 100 where pos_magie > 100;
#update positions set pos_magie = 0 where pos_magie < 100;
#\q
#EOF
