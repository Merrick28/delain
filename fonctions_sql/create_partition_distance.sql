create or replace function create_partition_distance(etage integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$
declare
    etage alias for $1;
    table_exists text;
    texte_etage  text;

begin
    if etage < 0
    then
        texte_etage = 'moins_' || trim(to_char(abs(etage), '999999'));
    else
        texte_etage = trim(to_char(etage, '9999999'));
    end if;
    SELECT into table_exists table_name
    FROM information_schema.tables
    WHERE table_schema = 'public'
      AND table_name = 'distance_' || texte_etage;
    if not found then

        execute 'create table distance_' || texte_etage || ' partition of distance for values in (' ||
                trim(to_char(etage, 999999999)) || ')';
        execute 'create index idx_distance_pos_' || texte_etage || ' on distance_' || texte_etage ||
                ' (distance_pos1,distance_pos2)';
        execute 'create index idx_distance_' || texte_etage || ' on distance_' || texte_etage || ' (distance_distance)';
        execute 'create index idx_distance_etage_' || texte_etage || ' on distance_' || texte_etage ||
                ' (distance_etage)';
        execute 'create index idx_distance_pos1_' || texte_etage || ' on distance_' || texte_etage ||
                ' (distance_pos1,distance_distance)';

        --perform table_exists;
        return 'created';
    else
        return 'found';

    end if;

    return 'ok';
end;


$_$;


ALTER FUNCTION public.create_partition_distance(integer) OWNER TO delain;