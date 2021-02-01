CREATE or replace FUNCTION public.release(text, integer) RETURNS void
    LANGUAGE plpgsql
AS
$_$-- Releases given mutex, if it exists.

declare
    key alias for $1;
    perso_cod alias for $2;
begin
    delete from guards where guard_key = key and guard_perso_cod = perso_cod;
end;
$_$;


ALTER FUNCTION public.release(text) OWNER TO delain;