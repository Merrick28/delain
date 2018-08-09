CREATE OR REPLACE FUNCTION public.hour(timestamp with time zone)
 RETURNS smallint
 LANGUAGE sql
AS $function$SELECT date_part('hour', $1)::int2$function$

