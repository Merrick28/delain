#!/bin/bash
source `dirname $0`/env
$psql -t -d delain -U webdelain << EOF >> $logdir/solde.log 2>&1
update parametres set parm_valeur = 0 where parm_cod in (77,79,81,84);
select verse_solde();
EOF

