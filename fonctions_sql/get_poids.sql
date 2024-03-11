--
-- Name: get_poids(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION public.get_poids(integer) RETURNS numeric
    LANGUAGE plpgsql
    AS $_$/************************************************/
/* fonction get_poids : retourne le poids porté */
/*   par le perso passé en $ 1                  */
/************************************************/
declare
	code_retour numeric;
	equipe numeric;
	non_equipe numeric;
	sac numeric;
	v_perso alias for $1;
	temp numeric;

begin
	-- equipe
	select into equipe sum(obj_poids)
		from objet_generique,objets,perso,perso_objets
		where perso_cod = v_perso
		and perobj_perso_cod = perso_cod
		and perobj_obj_cod = obj_cod
		and obj_gobj_cod = gobj_cod
		and perobj_equipe = 'O'
		and obj_poids >0
		and gobj_tobj_cod != 25;
	if equipe is null then
		equipe := 0;
	end if;
	-- non equipe
	select into non_equipe sum(obj_poids)
		from objet_generique,objets,perso,perso_objets
		where perso_cod = v_perso
		and perobj_perso_cod = perso_cod
		and perobj_obj_cod = obj_cod
		and obj_gobj_cod = gobj_cod
		and perobj_equipe = 'N'
		and obj_poids >0
		and gobj_tobj_cod != 25;
	if non_equipe is null then
		non_equipe := 0;
	end if;
	-- sac Marlyza 2024-03-11: type contenant ou objet à poids négatif (depuis les jambieres d'hermes)
	select into sac sum(obj_poids)
		from objet_generique,objets,perso,perso_objets
		where perso_cod = v_perso
		and perobj_perso_cod = perso_cod
		and perobj_obj_cod = obj_cod
		and obj_gobj_cod = gobj_cod
		and (gobj_tobj_cod = 25 or obj_poids<0)
		and perobj_equipe = 'O';
	if sac is null then
		sac := 0;
	end if;
	temp := non_equipe + sac;
	if temp < 0 then
		temp := 0;
	end if;
	code_retour := equipe + temp;
	if code_retour is null then
		code_retour := 0;
	end if;
	if code_retour < 0 then
		code_retour := 0;
	end if;
	return code_retour;
end;

	$_$;


ALTER FUNCTION public.get_poids(integer) OWNER TO delain;
