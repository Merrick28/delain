--
-- Name: lancer_position(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--


CREATE OR REPLACE FUNCTION public.lancer_position(integer, integer) RETURNS SETOF integer
    LANGUAGE plpgsql
    AS $_$declare
			pos_attaquant alias for $1;
			distance_lancer alias for $2;
			ligne_position record;
			x_attaquant integer;
			y_attaquant integer;
			e_attaquant integer;
			num integer;
			position_arrivee integer;
			curs2 refcursor;
			v_req text;
			v_pos integer;

begin
		select into x_attaquant,y_attaquant,e_attaquant pos_x,pos_y,pos_etage from positions where pos_cod = pos_attaquant;
		   v_req = 'select pos_cod from positions
                                where pos_x between ('||trim(to_char(x_attaquant,'99999999999'))||' - '||trim(to_char(distance_lancer,'99999999999'))||') and ('||trim(to_char(x_attaquant,'99999999999'))||' + '||trim(to_char(distance_lancer,'99999999999'))||')
                                and pos_y  between ('||trim(to_char(y_attaquant,'99999999999'))||' - '||trim(to_char(distance_lancer,'99999999999'))||') and ('||trim(to_char(y_attaquant,'99999999999'))||' + '||trim(to_char(distance_lancer,'99999999999'))||')
                                and (pos_etage = '||trim(to_char(e_attaquant,'99999999999'))||') order by random()';
		    open curs2 for execute v_req;
				loop
					fetch curs2 into v_pos;
					exit when NOT FOUND;
					select into position_arrivee distance(v_pos,pos_attaquant);
					if position_arrivee = distance_lancer then
					return next v_pos;
					end if;
				end loop;
				close curs2;
end;
$_$;


ALTER FUNCTION public.lancer_position(integer, integer) OWNER TO delain;

--
-- Name: FUNCTION lancer_position(integer, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION public.lancer_position(integer, integer) IS 'permet de définir la liste des positions à x distance de la position initiale, première varialbe = position initiale, deuxième variable = distance à prendre en compte. Rajout d''un random qui renvoie unne liste non ordonnée';
