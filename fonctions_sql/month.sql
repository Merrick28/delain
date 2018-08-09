CREATE OR REPLACE FUNCTION public.month(timestamp with time zone)
 RETURNS smallint
 LANGUAGE sql
AS $function$SELECT date_part('month', $1)::int2$function$

