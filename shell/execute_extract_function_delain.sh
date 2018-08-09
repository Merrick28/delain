#!/bin/bash
mon_fichier="'a'"
echo $mon_fichier

ma_fonction=""
$psql -A -F ' ' -t -d delain -U postgres << EOF > tmp_functions.txt
select proname from pg_proc
where proowner <> 10
EOF
cat tmp_functions.txt | while read ma_fct
do
mon_fichier="'/home/delain/delain/sql/${ma_fct}.sql'"
mon_fichier_court="/home/delain/delain/sql/${ma_fct}.sql"
echo "monfichier : ($mon_fichier)"
ma_fonction="'${ma_fct}'"
/usr/bin/psql -t -d delain -U postgres << EOF
COPY (SELECT pg_catalog.pg_get_functiondef(oid) FROM pg_proc WHERE pg_proc.proname = $ma_fonction) TO $mon_fichier
EOF
sed -i 's/\\n/\n/g' $mon_fichier_court
sed -i 's/\\t/\t/g' $mon_fichier_court
sed -i 's/\\r/\r/g' $mon_fichier_court
done

