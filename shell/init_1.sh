#!/bin/bash
source `dirname $0`/env
$psql -q -t << EOF
select init_1_avril();
EOF
