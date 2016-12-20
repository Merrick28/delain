repertoire=/home/delain/shell
sortie=$repertoire/liste_monstre.txt
rm $sortie
/usr/bin/psql -U webdelain -d delain -f $repertoire/liste_monstre.sql -q -t -o $sortie
sed '/^$/d' $sortie > tt 
mv tt $sortie
