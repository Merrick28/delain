CREATE OR REPLACE FUNCTION public.milice_tel(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/**********************************************/
/* milice_tel                                 */
/* on passe en params :                       */
/*  $1 = perso_cod                            */
/*  $2 = pos_cod                              */
/* on a en retour un texte exploitable        */
/**********************************************/
declare
	code_retour text;
	personnage alias for $1;
	destination alias for $2;
	v_pa integer;
	texte_evt text;
begin
	select into v_pa perso_pa
		from perso
		where perso_cod = personnage;
	if not found then
		code_retour := 'Erreur ! Perso non trouvé !';
		return code_retour;
	end if;
	if v_pa < getparm_n(68) then
		code_retour := 'Erreur ! pas assez de PA pour effectuer cette action !';
		return code_retour;
	end if;
	if is_milice(personnage) = 0 then
		code_retour := 'Erreur ! Action itnerdite pour ce personnage !';
		return code_retour;
	end if;
	select into v_pa lpos_lieu_cod from lieu_position
		where lpos_pos_cod = destination;
	if not found then
		code_retour := 'Erreur ! Pas de batiment en destination !';
		return code_retour;
	end if;
	select into v_pa lpos_lieu_cod from lieu_position,perso_position
		where ppos_perso_cod = personnage
		and ppos_pos_cod = lpos_pos_cod;
	if not found then
		code_retour := 'Erreur ! Pas de batiment en départ !';
		return code_retour;
	end if;
	update perso
		set perso_pa = perso_pa - getparm_n(68)
		where perso_cod = personnage;
	update perso_position set ppos_pos_cod = destination
		where ppos_perso_cod = personnage;
	texte_evt := '[perso_cod1] s''est téléporté.';
	insert into ligne_evt
		(levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
		values
		(38,now(),personnage,texte_evt,'O','O');
	code_retour := 'Téléporation effectuée.';
	return code_retour;
end;$function$

