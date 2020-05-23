CREATE or replace FUNCTION public.f_est_dans_la_liste(integer, json) RETURNS bool
    LANGUAGE plpgsql
AS
$_$
declare
    key alias for $1;
    list alias for $2;

begin

  return  coalesce(list::jsonb  @>  ('["' || key::text || '"]')::jsonb, false);

end;
$_$;
ALTER FUNCTION public.f_est_dans_la_liste(integer, json) OWNER TO delain;