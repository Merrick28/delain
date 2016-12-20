#!/bin/bash
source `dirname $0`/env
$psql -q -t -d delain -U webdelain << EOF
select coalesce(liv_fichier,'F') from livrisons where liv_fichier = '$1';
EOF