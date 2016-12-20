#!/bin/bash
source `dirname $0`/env
for f in `find $livroot -type f -maxdepth 1| grep -v "initial_import.sql"`; do
  livexist=`$shellroot/livraison_exists.sh $f
  if [ "$livexist" -eq "0" ];then
    echo "$f déjà traité"
  else
    echo "$f a traiter"
  fi
done