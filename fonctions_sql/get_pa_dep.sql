--
-- Name: get_pa_dep(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION public.get_pa_dep(integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$/*****************************************************/
/* fonction get_pa_dep : retourne le nombre de PA    */
/*   nécessaire pour un déplacement                  */
/* on passe en paramètre :                           */
/*   $1 = perso_cod                                  */
/*****************************************************/
declare
	code_retour integer;
	personnage alias for $1;
	v_bonus integer;
	v_etage integer;
	v_type_perso integer;
	v_pos_modif_pa_dep integer;
	v_monture integer;

begin

  -- si le perso se deplace avec une monture on prend les PA nécéssaires pour la monture !
  select m.perso_cod into v_monture
      from perso as p
      join perso as m on m.perso_cod=p.perso_monture and m.perso_actif = 'O' and m.perso_type_perso=2
      where p.perso_cod=personnage and p.perso_type_perso=1 ;
  if found then
  	  code_retour := get_pa_dep(v_monture);
		  return code_retour;
	end if;

  -- cas d'un perso sans monture ou d'un monstre (monture ou non)
	code_retour := getparm_n(9) + valeur_bonus(personnage, 'DEP');
	select into v_etage,v_pos_modif_pa_dep
		pos_etage,pos_modif_pa_dep
		from positions,perso_position
		where ppos_perso_cod = personnage
		and ppos_pos_cod = pos_cod;
	if v_etage = 16 then
		select into v_type_perso perso_type_perso
			from perso
			where perso_cod = personnage;
		if v_type_perso != 2 then
			code_retour := code_retour + 2;
		end if;
	end if;
	code_retour := code_retour + v_pos_modif_pa_dep;
	if exists (select 1 from perso_objets, objets where perobj_obj_cod = obj_cod and perobj_perso_cod = personnage and obj_gobj_cod = 860 and perobj_equipe = 'O') then
		-- Attelle. Malus de 1 au déplacement
		code_retour := code_retour + 1;
	end if;
	if code_retour < 2 then
		code_retour := 2;
	end if;
	return code_retour;
end;
	$_$;


ALTER FUNCTION public.get_pa_dep(integer) OWNER TO delain;