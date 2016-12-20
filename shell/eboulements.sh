#!/bin/sh
date >> /home/delain/logs/eboulements.log
/usr/bin/psql -t -d delain -U delainadm << EOF >> /home/delain/logs/eboulements.log 2>&1
select entretien_mines();
EOF

