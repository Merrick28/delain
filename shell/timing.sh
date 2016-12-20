/usr/bin/psql -U webdelain -d delain << EOF >> toto.log
\timing
select * from parametres
EOF
