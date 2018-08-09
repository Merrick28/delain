CREATE OR REPLACE FUNCTION public.unix_timestamp(timestamp with time zone)
 RETURNS bigint
 LANGUAGE sql
AS $function$SELECT (CASE WHEN $1 = NULL THEN 0 ELSE date_part('epoch', $1) END)::int8$function$

