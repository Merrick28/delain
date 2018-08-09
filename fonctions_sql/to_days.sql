CREATE OR REPLACE FUNCTION public.to_days(timestamp with time zone)
 RETURNS bigint
 LANGUAGE sql
AS $function$SELECT CASE WHEN $1 = NULL THEN NULL ELSE floor(unix_timestamp($1)/86400)::int8 END$function$

