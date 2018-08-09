CREATE OR REPLACE FUNCTION public.dayofmonth(timestamp with time zone)
 RETURNS smallint
 LANGUAGE sql
AS $function$SELECT date_part('day', $1)::int2$function$

