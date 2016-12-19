for fichier in `ls *php`
do
sed -e "s/apc_fetch('g_url')/\$type_flux.apc_fetch('g_url')/" $fichier > temp1234.txt
rm $fichier
mv temp1234.txt $fichier 
echo $fichier
done

