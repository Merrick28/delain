CREATE OR REPLACE FUNCTION public.f_modif_carac(integer, text, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*************************************************/
/* fonction f_modif_carac                        */
/*-----------------------------------------------*/
/* paramètres :                                  */
/* $1 = perso_cod                                */
/* $2 = type carac                               */
/*   possibles : FOR, DEX, INT et CON            */
/*   attention : majuscules !                    */
/* $3 = nombre d’heures                          */
/*   si en nb tours, mettre 0                    */
/* $4 = modificateur à mettre                    */
/*-----------------------------------------------*/
/* code retour : texte                           */
/*  si tout bon, on sort 'OK'                    */
/*  sinon, message d’erreur complet              */
/*-----------------------------------------------*/
/* créé le 19/10/2006 par Merrick                */
/*************************************************/
declare
	code_retour text;
	personnage alias for $1;
	v_type_carac alias for $2;
	v_temps alias for $3;
	v_modificateur alias for $4;
	temp integer;	-- variable fourre tout
	v_temps_inter interval;
	v_carac_orig integer;
	v_carac_limite integer;
	v_diff integer;
	v_nouvelle_valeur integer;
begin
	code_retour := 'OK';
	--
	-- on fait d’abord les contrôles possibles
	--
	select into temp
		perso_cod
	from perso
	where perso_cod = personnage;
	if not found then
		return 'Personnage non trouvé !';
	end if;
	if v_temps = 0 then
		return 'Paramètre de durée non valide !';
	end if;
	v_temps_inter := trim(to_char(v_temps,'999999999'))||' hours';

	select into v_carac_orig
		case v_type_carac when 'FOR' then perso_for
		                  when 'DEX' then perso_dex
		                  when 'INT' then perso_int
		                  when 'CON' then perso_con
		else NULL end
	from perso
	where perso_cod = personnage;
	if v_carac_orig is null then
		return 'Type de caractéristique non valide !';
	end if;
	--
	-- on regarde s’il ya déjà quelque chose
	--
	select into temp 
		corig_carac_valeur_orig
	from carac_orig
	where corig_perso_cod = personnage 
		and corig_type_carac = v_type_carac;
	if found then
		v_carac_limite := temp;
	else
		v_carac_limite := v_carac_orig;
	end if;
	--
	-- on commence maintenant les modifications
	--
	-- on commence par regarder s’il ya déjà des modifs pour la même carac
	select into temp 
		corig_carac_valeur_orig
	from carac_orig
	where corig_perso_cod = personnage 
		and corig_type_carac = v_type_carac;
	if found then
		update carac_orig
		set corig_dfin = now() + v_temps_inter
		where corig_perso_cod = personnage 
			and corig_type_carac = v_type_carac;
	else
		insert into carac_orig(corig_perso_cod, corig_type_carac, corig_carac_valeur_orig, corig_dfin)
		values (personnage, v_type_carac, v_carac_orig, now() + v_temps_inter);
	end if;

	-- on regarde ou en est la nouvelle carac
	v_nouvelle_valeur := v_carac_orig + v_modificateur;
	if v_nouvelle_valeur > v_carac_limite then
		if v_nouvelle_valeur > (v_carac_limite * 1.5) then
			v_nouvelle_valeur := floor(v_carac_limite * 1.5);
		end if;
	else
		if v_nouvelle_valeur < (v_carac_limite * 0.5) then
			v_nouvelle_valeur := ceil(v_carac_limite * 0.5);
		end if;
	end if;
	v_diff := v_nouvelle_valeur - v_carac_orig;

	if v_type_carac = 'FOR' then
		update perso
		set perso_for = v_carac_orig + v_diff,
			perso_enc_max = perso_enc_max + (v_diff * 3)
		where perso_cod = personnage;
	elsif v_type_carac = 'DEX' then
		update perso
		set perso_dex = v_carac_orig + v_diff
		where perso_cod = personnage;
	elsif v_type_carac = 'INT' then
		update perso
		set perso_int = v_carac_orig + v_diff
		where perso_cod = personnage;
	elsif v_type_carac = 'CON' then
		update perso
		set perso_con = v_carac_orig + v_diff,
			perso_pv_max = perso_pv_max + (v_diff * 3)
		where perso_cod = personnage;
		if v_diff > 0 then
			update perso set perso_pv = perso_pv + (v_diff * 3) where perso_cod = personnage;
		end if;
	end if;
	
	return code_retour;
end;$function$

