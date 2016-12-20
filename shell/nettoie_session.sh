#!/bin/sh
/usr/bin/psql -A -t  -q -d delain -U delainadm << EOF | grep -v '^$' >> /home/delain/logs/nettoie_session.log 2>&1
select to_char(now(),'YYYY-MM-DD HH24:MI:SS')||' ['||query||']'
 from pg_stat_activity
	where usename = 'webdelain'
	and query_start <= now() - '2 minutes'::interval
	and query like 'select ia_monstre%'
        and query not like '%DEALLOCATE%';

select to_char(now(),'YYYY-MM-DD HH24:MI:SS')||' ['||query||']'
 from pg_stat_activity
	where usename = 'webdelain'
	and query_start <= now() - '2 minutes'::interval
	and query not like 'select ia_monstre%'
	and query not like 'select ''admin_longue_requete''%'
        and query not like '%DEALLOCATE%';

select to_char(now(),'YYYY-MM-DD HH24:MI:SS')||' ['||query||']'
 from pg_stat_activity
	where usename = 'webdelain'
	and query_start <= now() - '5 minutes'::interval
	and query like 'select ''admin_longue_requete''%'
        and query not like '%DEALLOCATE%';
EOF

