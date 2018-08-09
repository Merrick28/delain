CREATE OR REPLACE FUNCTION public.week(timestamp with time zone)
 RETURNS smallint
 LANGUAGE sql
AS $function$SELECT date_part('week', $1)::int2$function$

