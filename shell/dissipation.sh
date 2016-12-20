#!/bin/bash
# TODO : commenter chaque ligne pour expliquer le but
source `dirname $0`/env
$psql -t -d delain -U webdelain << EOF >> $logdir/dissipation.log 2>&1
select dissipation_magique();
select cron_maj_idee();
select consolide_classement_concours();
EOF

