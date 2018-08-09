CREATE OR REPLACE FUNCTION public.if(boolean, character varying, character varying)
 RETURNS character varying
 LANGUAGE sql
AS $function$SELECT CASE WHEN $1 THEN $2 ELSE $3 END$function$

