#!/bin/bash
# TODO : commenter chaque ligne pour expliquer le but
source `dirname $0`/env
date >> $logdir/eboulements.log
$psql -t -d delain -U webdelain << EOF >> $logdir/eboulements.log 2>&1
select entretien_mines();
EOF

