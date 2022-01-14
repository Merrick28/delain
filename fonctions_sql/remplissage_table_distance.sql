create or replace function remplissage_table_distance() RETURNS text
    LANGUAGE plpgsql
AS
$_$
declare
    ligne_etage     record;

begin
    for ligne_etage in select distinct(pos_etage) as pos_etage from positions
        loop
            perform remplissage_table_distance_etage(ligne_etage.pos_etage);
        end loop;
    return 'termine';
end;


$_$;


ALTER FUNCTION public.remplissage_table_distance() OWNER TO delain;
