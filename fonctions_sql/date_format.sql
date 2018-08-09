CREATE OR REPLACE FUNCTION public.date_format(timestamp with time zone, text)
 RETURNS text
 LANGUAGE sql
AS $function$SELECT CASE WHEN $1 = NULL THEN '' ELSE to_char($1, $2) END$function$

