/*
Recherche d'un entier dans une liste d'entier
 */
CREATE or replace FUNCTION public.f_est_dans_la_liste(integer, json) RETURNS bool
    LANGUAGE plpgsql
AS
$_$
declare
    needle alias for $1;
    list alias for $2;

begin

  return  coalesce(list::jsonb  @>  ('["' || needle::text || '"]')::jsonb, false);

end;
$_$;
ALTER FUNCTION public.f_est_dans_la_liste(integer, json) OWNER TO delain;

/*
Recherche d'un entier dans une liste json sur une clé de recherche spécifique
 */

CREATE or replace FUNCTION public.f_est_dans_la_liste(integer, text, json) RETURNS bool
    LANGUAGE plpgsql
AS
$_$
declare
    needle alias for $1;
    key alias for $2;
    list alias for $3;

	  row record;                -- Les données de la fonction

begin

	for row in (  select  json_array_elements( list )->>key  as value  )
	loop
	  if needle = row.value then
	      return  true ;
    end if;
	end loop;

  return  false ;

end;
$_$;
ALTER FUNCTION public.f_est_dans_la_liste(integer, text, json) OWNER TO delain;