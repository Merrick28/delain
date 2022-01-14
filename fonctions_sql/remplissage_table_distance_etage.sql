create or replace function remplissage_table_distance_etage(integer)
    RETURNS text
    LANGUAGE plpgsql
AS
$_$
declare
    code_retour     text;
    ligne_position  record;
    ligne_position2 record;
    temp            integer;
    etage alias for $1;

begin

    perform create_partition_distance(etage);
    commit;
    for ligne_position in select distinct(pos_cod) as pos_cod
                          from positions
                          where pos_etage = etage
        loop
            for ligne_position2 in
                select distinct(pos_cod) as pos_cod
                from positions
                where pos_etage = etage
                loop
                    select into temp distance_distance
                    from distance
                    where distance_pos1 = ligne_position.pos_cod
                      and distance_pos2 = ligne_position2.pos_cod;
                    if not found
                    then

                        insert into distance (distance_pos1, distance_pos2, distance_etage, distance_distance)
                        values (ligne_position.pos_cod, ligne_position2.pos_cod, etage,
                                distance(ligne_position.pos_cod, ligne_position2.pos_cod));
                        insert into distance (distance_pos1, distance_pos2, distance_etage, distance_distance)
                        values (ligne_position2.pos_cod, ligne_position.pos_cod, etage,
                                distance(ligne_position.pos_cod, ligne_position2.pos_cod));
                    end if;

                end loop;
            commit;

        end loop;

    return 'termine';
end;


$_$;


ALTER FUNCTION public.remplissage_table_distance_etage(integer) OWNER TO delain;
