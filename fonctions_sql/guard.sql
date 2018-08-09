CREATE OR REPLACE FUNCTION public.guard(text)
 RETURNS void
 LANGUAGE plpgsql
AS $function$-- Atomifies the execution of functions.
-- It checks for the existence of $1 in the table of guards.
-- If there are none, it creates it.
-- Else, it waits for the existing to be deleted, before adding it and returning.

declare
  key alias for $1;
  mutex integer;
begin
  loop
    select into mutex count(1) from guards where guard_key = $1;
    if mutex = 0 then
      exit;
    end if;
  end loop;
  insert into guards (guard_key) values ($1);
end;$function$

