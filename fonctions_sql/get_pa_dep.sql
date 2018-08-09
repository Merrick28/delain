CREATE OR REPLACE FUNCTION public.get_pa_dep(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*****************************************************/
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
	
begin
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
	$function$

