#!/bin/bash
# test diff
source $(dirname $0)/env
CURRENT_SCRIPT=$(readlink -f "$0")
export SCRIPTPATH=$(dirname "$CURRENT_SCRIPT")
cd ${SCRIPTPATH}/../
git diff --name-only origin/master | grep ^fonctions_sql/ | sort >.diff_to_apply
git diff --name-only origin/master  | sort >> ${shellroot}/livraison.log
echo "Modifs à appliquer " >>${shellroot}/livraison.log
cat .diff_to_apply >>${shellroot}/livraison.log
