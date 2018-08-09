CREATE OR REPLACE FUNCTION public.start_redispatch(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/************************************************************/
/* redispatch                                               */
/************************************************************/
declare
	code_retour text;
	personnage alias for $1;
	compt integer;
	redispatch text;
	compt_total integer;
begin
	compt_total := 0;
	select into redispatch
		perso_redispatch
		from perso
		where perso_cod = personnage;
	if redispatch != 'P' then
		code_retour := 'Vous n''êtes pas autorisé à faire une répartition des niveaux ! ';
		return code_retour;
	end if;
	-- generique
	update perso set perso_redispatch = 'E' where perso_cod = personnage;
	-- degats distance
	select into compt
		perso_amel_deg_dex
		from perso
		where perso_cod = personnage;
	if compt is null then
		compt := 0;
	end if;
	update perso set perso_amel_deg_dex = 0,perso_nb_redist = perso_nb_redist + compt
		where perso_cod = personnage;
	compt_total := compt_total + compt;
	-- regen
	select into compt
		perso_des_regen
		from perso
		where perso_cod = personnage;
	if compt is null then
		compt := 0;
	end if;
	update perso set perso_des_regen = 1,perso_nb_redist = perso_nb_redist + (compt - 1)
		where perso_cod = personnage;
	compt_total := compt_total + compt - 1;
	-- corps à corps
	select into compt
		perso_amelioration_degats
		from perso
		where perso_cod = personnage;
	if compt is null then
		compt := 0;
	end if;
	update perso set perso_amelioration_degats = 0,perso_nb_redist = perso_nb_redist + compt
		where perso_cod = personnage;
	compt_total := compt_total + compt;
	--armure
	select into compt
		perso_amelioration_armure
		from perso
		where perso_cod = personnage;
	if compt is null then
		compt := 0;
	end if;
	update perso set perso_amelioration_armure = 0,perso_nb_redist = perso_nb_redist + compt
		where perso_cod = personnage;
	compt_total := compt_total + compt;
	-- vue
	select into compt
		perso_amelioration_vue
		from perso
		where perso_cod = personnage;
	if compt is null then
		compt := 0;
	end if;
	update perso set perso_amelioration_vue = 0,perso_nb_redist = perso_nb_redist + compt
		where perso_cod = personnage;
	compt_total := compt_total + compt;
	-- reparation
	select into compt
		perso_nb_amel_repar
		from perso
		where perso_cod = personnage;
	if compt is null then
		compt := 0;
	end if;
	update perso set perso_nb_amel_repar = 0,perso_nb_redist = perso_nb_redist + compt
		where perso_cod = personnage;
	update perso set perso_capa_repar = ((perso_dex+perso_int)*3) where perso_cod = personnage;
	compt_total := compt_total + compt;
	-- nombre de sorts
	select into compt
		perso_amelioration_nb_sort
		from perso
		where perso_cod = personnage;
	if compt is null then
		compt := 0;
	end if;
	update perso set perso_amelioration_nb_sort = 0,perso_nb_redist = perso_nb_redist + compt
		where perso_cod = personnage;
	compt_total := compt_total + compt;
	-- receptacle
	select into compt
		perso_nb_receptacle
		from perso
		where perso_cod = personnage;
	delete from recsort
		where recsort_perso_cod = personnage;
	if compt is null then
		compt := 0;
	end if;
	update perso set perso_nb_receptacle = 0,perso_nb_redist = perso_nb_redist + compt
		where perso_cod = personnage;
	update perso set perso_nb_amel_comp = perso_nb_amel_comp - compt
		where perso_cod = personnage;	
	compt_total := compt_total + compt;
	-- amé de mémo
	select into compt
		perso_nb_amel_chance_memo
		from perso
		where perso_cod = personnage;
	if compt is null then
		compt := 0;
	end if;
	update perso set perso_nb_amel_chance_memo = 0,perso_nb_redist = perso_nb_redist + compt
		where perso_cod = personnage;
	update perso set perso_nb_amel_comp = perso_nb_amel_comp - compt
		where perso_cod = personnage;	
	compt_total := compt_total + compt;
	code_retour := 'Redistribution terminée. Vous avez '||trim(to_char(compt_total,'999999'))||' points à redistribuer.<br>';
	return code_retour;
end;$function$

