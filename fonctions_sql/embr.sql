CREATE OR REPLACE FUNCTION public.embr(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$declare
	v_lanceur alias for $1;
	v_cible alias for $2;
	v_crapaud integer;
	v_sexe_lanceur text;
	v_sexe_cible text;
	v_nom_cible text;
	v_pa integer;
	code_retour text;
	texte_evt text;
	v_crapaud_lanceur integer;
	pos_lanceur integer;
	pos_cible integer;
	
begin
	select into v_sexe_cible, v_nom_cible, v_crapaud perso_sex, perso_nom, perso_crapaud
	from perso where perso_cod = v_cible;

	select into v_sexe_lanceur, v_crapaud_lanceur, v_pa perso_sex, perso_crapaud, perso_pa
	from perso where perso_cod = v_lanceur;	
	if v_crapaud_lanceur = 1 then
		code_retour := 'Vous êtes un crapaud ! Vous ne pouvez pas embrasser !';
		return code_retour;
	end if;

	if v_crapaud != 1 then
		code_retour := v_nom_cible || ' n’est pas un crapaud !';
		return code_retour;
	end if;

	select into pos_lanceur ppos_pos_cod
	from perso_position where ppos_perso_cod = v_lanceur;

	select into pos_cible ppos_pos_cod
	from perso_position where ppos_perso_cod = v_cible;

	if pos_cible <> pos_lanceur then
		code_retour := 'Vous êtes trop loin de ' || v_nom_cible || ' pour l’embrasser !';
		return code_retour;
	end if;

	if v_pa < 2 then
		code_retour := 'Vous n’avez pas assez de PA pour embrasser ' || v_nom_cible;
		return code_retour;
	end if;

	update perso set perso_pa = perso_pa - 2, perso_nb_embr = perso_nb_embr + 1 where perso_cod = v_lanceur;
	update perso
		set perso_avatar = perso_ancien_avatar,
		perso_crapaud = 0
		where perso_cod = v_cible;
	texte_evt := '[attaquant] a embrassé [cible] !';
	
	perform insere_evenement(v_lanceur, v_cible, 57, texte_evt, 'O', NULL);

	code_retour := 'Pouahhhh !!!! Vous avez embrassé un crapaud !!!<br />';
	if v_sexe_cible = v_sexe_lanceur then
		code_retour := code_retour || 'En plus, vous constatez que ce crapaud était de même sexe que vous ! Ah, mais peut-être était-ce volontaire ?<br />';
	end if;
	code_retour := code_retour || 'Toutefois, cela semble être bénéfique puisque ' || v_nom_cible || ' a retrouvé son état normal !';
	return code_retour;
end;$function$

