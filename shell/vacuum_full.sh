#!/bin/sh
echo "Nettoyage approfondi de la base en cours" >> /home/delain/public_html/stop_jeu
/usr/bin/psql -U delainadm -q -t -d delain << EOF >> /home/delain/logs/result_vacuum.log 2>&1
vacuum full;
EOF
rm -f /home/delain/public_html/stop_jeu
