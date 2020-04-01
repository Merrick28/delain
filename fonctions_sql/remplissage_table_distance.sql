create or replace function remplissage_table_distance() RETURNS text
    LANGUAGE plpgsql
AS
$_$
declare
    code_retour     text;
    ligne_etage     record;
    ligne_position  record;
    ligne_position2 record;
    temp            integer;

begin
    for ligne_etage in select distinct(pos_etage) as pos_etage from positions
        loop
            for ligne_position in select distinct(pos_cod) as pos_cod
                                  from positions
                                  where pos_etage = ligne_etage.pos_etage
                loop
                    for ligne_position2 in
                        select distinct(pos_cod) as pos_cod
                        from positions
                        where pos_etage = ligne_etage.pos_etage
                          and pos_cod != ligne_position.pos_cod
                        loop
                            select into temp distance_distance
                            from distance
                            where distance_pos1 = ligne_position.pos_cod
                              and distance_pos2 = ligne_position2.pos_cod;
                            if not found
                            then

                                insert into distance (distance_pos1, distance_pos2, distance_etage, distance_distance)
                                values (ligne_position.pos_cod, ligne_position2.pos_cod, ligne_etage.pos_etage,
                                        distance(ligne_position.pos_cod, ligne_position2.pos_cod));
                                insert into distance (distance_pos1, distance_pos2, distance_etage, distance_distance)
                                values (ligne_position2.pos_cod, ligne_position.pos_cod, ligne_etage.pos_etage,
                                        distance(ligne_position.pos_cod, ligne_position2.pos_cod));
                            end if;

                        end loop;


                end loop;


        end loop;
    return 'termine';
end;


$_$;


ALTER FUNCTION public.remplissage_table_distance() OWNER TO delain;
