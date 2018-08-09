CREATE OR REPLACE FUNCTION public.offre_boire(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$declare
	donneur alias for $1;
	cible alias for $2;
	code_retour text;
	v_pa integer;
	v_brouzouf integer;
	v_pos_donneur integer;
	v_pos_cible integer;
	v_nom_cible text;
	temp integer;
	texte_evt text;
begin
	select into v_pa,v_brouzouf,v_pos_donneur
		perso_pa,perso_po,ppos_pos_cod
		from perso,perso_position
		where perso_cod = donneur
		and ppos_perso_cod = perso_cod;
	if not found then
		code_retour := 'Erreur ! Donneur non trouvé !';
		return code_retour;
	end if;
	if v_pa < 4 then
		code_retour := 'Erreur ! Pas assez de Pa !';
		return code_retour;
	end if;
	if v_brouzouf < 10 then
		code_retour := 'Erreur ! Pas assez de brouzoufs !';
		return code_retour;
	end if;
	select into v_pos_cible,v_nom_cible
		ppos_pos_cod,perso_nom
		from perso,perso_position
		where perso_cod = cible
		and ppos_perso_cod = perso_cod;
	if not found then
		code_retour := 'Erreur ! Cible non trouvée !';
		return code_retour;
	end if;
	if v_pos_cible != v_pos_donneur then
		code_retour := 'Erreur ! le donneur et la cible ne sont pas sur la même position !';
		return code_retour;
	end if;
	select into temp
		lieu_tlieu_cod
		from lieu,lieu_position
		where lpos_pos_cod = v_pos_donneur
		and lpos_lieu_cod = lieu_cod;
	if not found then
		code_retour := 'Erreur ! Le donneur n''est pas sur un lieu !';
		return code_retour;
	end if;
	if temp != 4 then
		code_retour := 'Erreur ! Le donneur n''est pas sur une auberge !';
		return code_retour;
	end if;
-- actions
	update perso
		set perso_pa = perso_pa - 4, perso_po = perso_po - 10
		where perso_cod = donneur;
	texte_evt := '[attaquant] a offert à boire à [cible]';
	insert into ligne_evt
		(levt_tevt_cod,levt_perso_cod1,levt_attaquant,levt_cible,levt_lu,levt_visible,levt_texte)
		values
		(67,donneur,donneur,cible,'O','O',texte_evt);
	insert into ligne_evt
		(levt_tevt_cod,levt_perso_cod1,levt_attaquant,levt_cible,levt_lu,levt_visible,levt_texte)
		values
		(67,cible,donneur,cible,'N','O',texte_evt);	
	code_retour := 'Vous avez offert un coup à boire à <b>'||v_nom_cible||'</b><br>';
	return code_retour;
end;$function$

