#!/bin/bash
source $(dirname $0)/env
CURRENT_SCRIPT=$(readlink -f "$0")
export SCRIPTPATH=$(dirname "$CURRENT_SCRIPT")
cd ${CURRENT_SCRIPT}/../
git diff --name-only origin/master | grep ^fonctions_sql/ | sort >.diff_to_apply
echo "Modifs à appliquer " >>${shellroot}/livraison.log
cat .diff_to_apply >>${shellroot}/livraison.log
