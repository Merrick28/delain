#!/bin/bash
source `dirname $0`/env
$psql -U webdelain -d delain  -q -t << EOF
select count(*) from sessions where sess_date < now() - '10 minutes'::interval
EOF
