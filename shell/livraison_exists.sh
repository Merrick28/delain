#!/bin/bash
source `dirname $0`/env
$psql -A -q -t -d delain -U ${USERNAME} << EOF
select count(*) from livraisons where liv_fichier = '$1';
EOF