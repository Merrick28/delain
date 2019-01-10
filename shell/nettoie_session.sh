#!/bin/bash
source `dirname $0`/env
$psql -A -t  -q -d delain -U webdelain << EOF | grep -v '^$' >> $logdir/nettoie_session.log 2>&1
select to_char(now(),'YYYY-MM-DD HH24:MI:SS')||' ['||query||'],'||pg_cancel_backend(pid)
 from pg_stat_activity
	where usename = 'webdelain'
	and query_start <= now() - '2 minutes'::interval
	and query like 'select ia_monstre%'
	and query not like '%vacuum%'
        and query not like '%DEALLOCATE%';

select to_char(now(),'YYYY-MM-DD HH24:MI:SS')||' ['||query||'],'||pg_cancel_backend(pid)
 from pg_stat_activity
	where usename = 'webdelain'
	and query_start <= now() - '3 minutes'::interval
	and query not like 'select ia_monstre%'
	and query not like 'select ''admin_longue_requete''%'
	and query not like 'select purge_%'
	and query not like '%vacuum%'
        and query not like '%DEALLOCATE%';


select to_char(now(),'YYYY-MM-DD HH24:MI:SS')||' ['||query||'],'||pg_cancel_backend(pid)
 from pg_stat_activity
	where usename = 'webdelain'
	and query_start <= now() - '5 minutes'::interval
	and query like 'select ''admin_longue_requete''%'
	and query not like 'select purge_%'
	and query not like '%vacuum%'
        and query not like '%DEALLOCATE%';
EOF

