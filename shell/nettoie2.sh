repertoire=/home/sdewitte/public_html/avatars
cd $repertoire
entree=$repertoire/$1
sortie=$repertoire/resultat2.txt
requete=$repertoire/requete.sql
nb_ligne=`wc -l < $entree`
echo $nb_ligne
typeset -i num_ligne=1
while (($num_ligne<=$nb_ligne))
do
ligne=`cat $entree |head -$num_ligne |tail -1`
avatar=`echo $ligne | cut -d";" -f1`
echo $avatar
rm $avatar
num_ligne=$num_ligne+1
done
echo 'fini'