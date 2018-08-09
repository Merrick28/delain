CREATE OR REPLACE FUNCTION public.trans_nom(text)
 RETURNS record
 LANGUAGE sql
AS $function$select ltrim(rtrim(lower($1)));$function$

