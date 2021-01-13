
--
-- Name: get_pa_dep_terrain(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION public.get_pa_dep_terrain(integer, integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$/**********************************************************/
/* fonction get_pa_dep_terrain : retourne le nombre de PA */
/*   nécessaire pour un déplacement  (en ignorant les BM) */
/* on passe en paramètre :                                */
/*   $1 = perso_cod                                       */
/*   $1 = pos_cod                                         */
/**********************************************************/
declare
	code_retour integer;
	personnage alias for $1;
	v_pos alias for $2; 	-- pos_cod de destination
	v_ter_cod integer;    -- terrain de la case
	v_monture_pa integer;  -- modificateur de pa de la monture

begin

  -- cas d'un joueur qui se déplace seul, on ne prend en compte que les modifications de pa !
  select  getparm_n(9) + pos_modif_pa_dep, pos_ter_cod into code_retour, v_ter_cod from positions where pos_cod=v_pos ;

  -- s'il y a un terrain specifique a cette position, on regarde si c'est une monture avec des caracs speciales sur ce terrain
  if v_ter_cod is not null then
      select tmon_terrain_pa into v_monture_pa
          from perso as p
          join perso as m on m.perso_cod=p.perso_monture and m.perso_actif = 'O' and m.perso_type_perso=2
          join monstre_terrain on tmon_gmon_cod = m.perso_gmon_cod and tmon_ter_cod=v_ter_cod
          where p.perso_cod=personnage and p.perso_type_perso=1 limit 1;
      if found then
          -- cas d'un joueur qui se déplace avec une monture sur un terrain où la monture a une capacité spéciales !
          code_retour := code_retour + v_monture_pa ;
      end if;
  end if;

	return code_retour;
end;
	$_$;


ALTER FUNCTION public.get_pa_dep_terrain(integer, integer) OWNER TO delain;