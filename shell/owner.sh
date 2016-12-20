#!/bin/bash
source `dirname $0`/env
$psql -A -F ' ' -t -d delain -U webdelain << EOF > tmp_owner.txt
select schemaname,tablename from pg_tables
where schemaname not in  ('pg_catalog','information_schema','ftp')
and tableowner != 'delain'
EOF
cat tmp_owner.txt | while read schema table
do
echo "$schema  -- $table"
/usr/bin/psql -t -d delain -U webdelain << EOF
alter table ${schema}.${table} owner to delain;
EOF
done
