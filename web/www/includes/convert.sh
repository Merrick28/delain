#!/bin/sh
for fichier in `ls *php`
do
echo $fichier
iconv -f iso-8859-1 -t utf-8 $fichier > temp.php
rm $fichier
mv temp.php $fichier
done

