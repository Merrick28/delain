CREATE or replace FUNCTION public.guard(text, integer) RETURNS void
    LANGUAGE plpgsql
AS
$_$-- Atomifies the execution of functions.
-- It checks for the existence of $1 in the table of guards.
-- If there are none, it creates it.
-- Else, it waits for the existing to be deleted, before adding it and returning.

declare
    key alias for $1;
    perso_cod alias for $2;
    mutex integer;
begin
    loop
        select into mutex count(1) from guards where guard_key = key and guard_perso_cod = perso_cod;
        if mutex = 0 then
            exit;
        end if;
    end loop;
    insert into guards (guard_key, guard_perso_cod) values (key, perso_cod);
end;
$_$;


ALTER FUNCTION public.guard(text) OWNER TO delain;