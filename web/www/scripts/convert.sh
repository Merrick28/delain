#!/bin/sh
for fichier in `ls *js`
do
echo $fichier
iconv -f us-ascii -t utf-8 $fichier > temp.php
rm $fichier
mv temp.php $fichier
done

