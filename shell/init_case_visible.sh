#!/bin/bash
source `dirname $0`/env
$psql -U webdelain  -d delain << EOF
select init_case_visible(etage_numero) from etage where not exists
(select 1 from pos_visible
where pvis_pos1 = pos_cod);
EOF
