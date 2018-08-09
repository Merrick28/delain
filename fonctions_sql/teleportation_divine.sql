CREATE OR REPLACE FUNCTION public.teleportation_divine(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$declare
	code_retour text;
	personnage alias for $1;
	destination alias for $2;
	-- variables de controle
	v_pa integer;				-- pa du perso
	v_dieu_cod integer; 		-- religion du perso
	v_dieu_niveau integer; 	-- niveau de religion
	v_pos_cod integer;		-- position de début
	-- variables de retour
	v_texte_evt text;			-- texte pour les évènements
begin
	/*********************/
	/* DEBUT : controles */
	/*********************/	
	--
	/************************/
	/* DEBUT : controles PA */
	/************************/	
	select into v_pa perso_pa
		from perso
		where perso_cod = personnage;
	if not found then
		code_retour := 'Anomalie ! Personnage non trouvé !';
		return code_retour;
	end if;
	if v_pa < getparm_n(100) then
		code_retour := 'Anomalie ! Pas assez de PA pour effectuer cette action !';
		return code_retour;
	end if;
	/************************/
	/* FIN   : controles PA */
	/************************/	
	--
	/******************************/
	/* DEBUT : controles religion */
	/******************************/	
	select into v_dieu_cod,v_dieu_niveau
		dper_dieu_cod,dper_niveau
		from dieu_perso
		where dper_perso_cod = personnage;
	if not found then
		code_retour := 'Anomalie ! Religion du personnage non trouvée !';
		return code_retour;
	end if;	
	if v_dieu_niveau < 3 then
		code_retour := 'Anomalie ! Niveau dans la religion non correspondant !';
		return code_retour;
	end if;	
	/******************************/
	/* FIN   : controles religion */
	/******************************/
	--
	/*******************************/
	/* DEBUT : controles positions */
	/*******************************/	
	select into v_pos_cod
		ppos_pos_cod
		from perso_position,lieu_position,lieu
		where ppos_perso_cod = personnage
		and ppos_pos_cod = lpos_pos_cod
		and lpos_lieu_cod = lieu_cod
		and lieu_dieu_cod = v_dieu_cod;
	if not found then
		code_retour := 'Anomalie ! Le personnage n''est pas sur un temple !';
		return code_retour;
	end if;	
	--
	select into v_pos_cod
		lpos_pos_cod
		from lieu,lieu_position
		where lpos_pos_cod = destination
		and lpos_lieu_cod = lieu_cod
		and lieu_dieu_cod = v_dieu_cod;
	if not found then
		code_retour := 'Anomalie ! La destination n''est pas un temple !';
		return code_retour;
	end if;
	select into v_pos_cod
		lpos_pos_cod
		from lieu,lieu_position
		where lpos_pos_cod = destination
		and lpos_lieu_cod = lieu_cod
		and lieu_dieu_cod = v_dieu_cod;
	if not found then
		code_retour := 'Anomalie ! La destination n''est pas un temple !';
		return code_retour;
	end if;
	perform *
 		from choix_temple_vus(personnage) 
		where choix_temple_vus = destination;
	if not found then
		code_retour := 'Anomalie ! Vous désirez vous rendre dans un temple jamais visité !';
		return code_retour;
	end if;
	perform *
 		from perso_objets , objets
		where perobj_perso_cod = personnage and perobj_obj_cod = obj_cod and obj_gobj_cod between 86 and 88;
	if found then
		code_retour := 'Anomalie ! Vous ne pouvez vous téléporter en portant un médaillon !';
		return code_retour;
	end if;

		
	/*******************************/
	/* FIN   : controles positions */
	/*******************************/
	--
	/*********************/
	/* FIN   : controles */
	/*********************/	
	--
	/*********************/
	/* DEBUT : actions   */
	/*********************/	
	--
	update perso
		set perso_pa = perso_pa - getparm_n(100)
		where perso_cod = personnage;
	--
	update perso_position
		set ppos_pos_cod = destination
		where ppos_perso_cod = personnage;
	--
	v_texte_evt := '[perso_cod1] s''est téléporté.';
	insert into ligne_evt
		(levt_tevt_cod,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
		values
		(38,personnage,v_texte_evt,'O','O');
	--
	code_retour := 'Vous vous êtes téléporté avec succès.';
	return code_retour;
	--	
	/*********************/
	/* FIN   : actions   */
	/*********************/	
end;$function$

