CREATE OR REPLACE FUNCTION public.year(timestamp with time zone)
 RETURNS integer
 LANGUAGE sql
AS $function$SELECT date_part('year', $1)::int4$function$

