create or replace function remplissage_table_distance_etage_offset(integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$
declare
    code_retour     text;
    ligne_position  record;
    ligne_position2 record;
    temp            integer;
    etage alias for $1;
    myoffset alias for $2;
    compt           integer;

begin
    RAISE NOTICE 'Osset %', myoffset;

    perform create_partition_distance(etage);
    compt := 0;
    for ligne_position in select distinct(pos_cod) as pos_cod
                          from positions
                          where pos_etage = etage
                          order by pos_cod
                          limit 200
                          offset
                          myoffset
        loop
            compt := compt + 1;
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

        end loop;
    if compt < 50
    then
        return 'termine';
    else
        return 'encore ' || trim(to_char(compt, 99999999));
    end if;

end;


$_$;


ALTER FUNCTION public.remplissage_table_distance_etage_offset(integer,integer) OWNER TO delain;
