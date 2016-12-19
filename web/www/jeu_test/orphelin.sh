#!/bin/bash
REP=`pwd`
for fichier in `ls *php`
do
if !(grep -l $fichier *) > /dev/null
then
echo $fichier
mv $fichier FICHIER_NON_UTILISES/
fi
done

