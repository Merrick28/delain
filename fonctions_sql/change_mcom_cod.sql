CREATE OR REPLACE FUNCTION public.change_mcom_cod(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*************************************************/
/* function change_mcom_cod                      */
/*  $1 = perso_cod                               */
/*  $2 = perso_mcom_cod                          */
/*************************************************/
declare
	code_retour text;
	personnage alias for $1;
	v_mode_combat alias for $2;
	nb_ch integer;
	v_nom_mode text;
	v_dchange timestamptz;
	date_creation timestamptz;

begin
	select into v_dchange,date_creation
		(perso_dchange_mcom + '1 days'::interval),(perso_dcreat + '1 days'::interval)
		from perso
		where perso_cod = personnage;
	if v_dchange > now() and date_creation < now () then
		code_retour := 'Vous avez déjà changé de mode de combat au cours des dernières 24 heures. Vous ne pourrez pas changer avant '||to_char(v_dchange,'DD/MM/YYYY hh24:mi:ss');
		return code_retour;
	end if;
	if v_mode_combat < 0 then
		code_retour := 'Mode de combat incorrect.';
		return code_retour;
	end if;
	if v_mode_combat > 2 then
		code_retour := 'Mode de combat incorrect.';
		return code_retour;
	end if;
	select into v_nom_mode mcom_nom
		from mode_combat
		where mcom_cod = v_mode_combat;
	update perso
		set perso_nb_ch_mcom = 1,
		perso_mcom_cod = v_mode_combat,
		perso_dchange_mcom = now()
		where perso_cod = personnage;
	code_retour := 'Vous avez changé de mode de combat. Vous êtes maintenant en mode <b>'||v_nom_mode||'</b>.<br>';
	return code_retour;
end;$function$

