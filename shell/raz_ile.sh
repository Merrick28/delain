#!/bin/bash
source `dirname $0`/env
$psql -q -t << EOF
select raz_ile();
EOF
