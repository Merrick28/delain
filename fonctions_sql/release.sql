CREATE OR REPLACE FUNCTION public.release(text)
 RETURNS void
 LANGUAGE plpgsql
AS $function$-- Releases given mutex, if it exists.

declare
  key alias for $1;
begin
  delete from guards where guard_key = $1;
end;$function$

