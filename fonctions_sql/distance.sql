--
-- Name: distance(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE or replace FUNCTION public.distance(integer, integer) RETURNS integer
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function distance :Procédure de calcul de distance entre      */
/* deux positions passées en paramètre                           */
/*                                                                */
/* On passe en paramètres                                        */
/*    $1 = pos_cod n°1                                           */
/*    $2 = pos_cod n°2                                           */
/* Le code sortie est la distance entre ces positions, ou -1 si  */
/*    erreur, ou -2 si étages différents                         */
/*****************************************************************/
/* Créé le 08/03/2003                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
    code_retour   integer;
    position1 alias for $1; --pos_cod 1
    position2 alias for $2; --pos_cod 2
    x1            positions.pos_x%type;
    y1            positions.pos_y%type;
    e1            positions.pos_etage%type;
    x2            positions.pos_x%type;
    y2            positions.pos_y%type;
    e2            positions.pos_etage%type;
    compt_pos1    integer;
    compt_pos2    integer;
    distance_x    integer;
    distance_y    integer;
    temp_distance integer;
begin
    code_retour := 0; -- par défaut

    select into x1,y1,e1 pos_x, pos_y, pos_etage
    from positions
    where pos_cod = position1;
    if not found then
        return 99999;
    end if;

    select into temp_distance distance_distance
    from distance
    where distance_etage = e1
      and distance_pos1 = position1
      and distance_pos2 = position2;
    if found then
        return temp_distance;
    end if;

    select into x2,y2,e2 pos_x, pos_y, pos_etage
    from positions
    where pos_cod = position2;
    if not found then
        return 99999;
    end if;
    if e1 != e2 then
        code_retour := 99999;
        return code_retour;
    else -- if e1 == e2;
        distance_x = @(x1 - x2);
        distance_y = @(y1 - y2);
        if distance_x >= distance_y then
            code_retour := distance_x;
        else -- if distance_x >= distance_y
            code_retour := distance_y;
        end if; -- if distance_x >= distance_y
    end if;
    -- if e1 != e2;
    /*perform create_partition_distance(e1);
    insert into distance
    (distance_pos1, distance_pos2, distance_etage, distance_distance)
    values
    (position1,position2,e1,code_retour);
    insert into distance
    (distance_pos1, distance_pos2, distance_etage, distance_distance)
    values
    (position2,position1,e1,code_retour);*/

    return code_retour;
end ;
$_$;


ALTER FUNCTION public.distance(integer, integer) OWNER TO delain;