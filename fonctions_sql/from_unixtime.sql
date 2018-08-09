CREATE OR REPLACE FUNCTION public.from_unixtime(integer)
 RETURNS timestamp without time zone
 LANGUAGE sql
AS $function$SELECT '1970-01-01 00:00:00'::timestamp + ($1 || ' seconds')::interval$function$

