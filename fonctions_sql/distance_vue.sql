--
-- Name: distance_vue(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION public.distance_vue(integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$/***********************************************************/
/* fonction distance_vue : determine la distance de vision */
/*   d un perso                                            */
/* on passe en parametre :                                 */
/*   $1 = perso_cod du monstre                             */
/* on a un entier en sortie                                */
/***********************************************************/
declare
	code_retour integer;
	v_perso alias for $1;
	distance_vue integer;
	v_perso_vue integer;
	v_perso_amelioration_vue integer;
	bonus_vue integer;
	v_etage integer;
	v_x integer;
	v_y integer;
begin

--if to_char(now(),'DD/MM/YYYY') = '06/02/2011' then
--		return 2;
--	end if;

/*******************************************************/
/* Etape 1 : on regarde si il y a quelque chose en vue */
/*******************************************************/
	select into v_perso_vue,v_perso_amelioration_vue perso_vue,perso_amelioration_vue from perso
		where perso_cod = v_perso;
	distance_vue := v_perso_vue + v_perso_amelioration_vue + valeur_bonus(v_perso, 'VUE') + valeur_bonus(v_perso, 'PVU') + bonus_art_vue(v_perso);

--if to_char(now(),'DD/MM/YYYY') = '07/02/2011' then
--		if distance_vue > 3 then
--			return 3;
--		end if;
--	end if;
--if to_char(now(),'DD/MM/YYYY') = '08/02/2011' then
--		if distance_vue > 4 then
--			return 4;
--		end if;
--	end if;

	if distance_vue < 1 then
		distance_vue := 1;
	end if;
-- Construction au village.
--if v_etage = -8 and (v_x >= 20 and v_x <= 26) and (v_y >= 1 and v_y <= 11) then
--	distance_vue := min(4, distance_vue);
--end if;
-- modification de az pour le conte de noel 2008 et rebelotte haloween 2009
-- distance_vue := 1;
	return distance_vue;
end;$_$;


ALTER FUNCTION public.distance_vue(integer) OWNER TO delain;