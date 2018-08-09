CREATE OR REPLACE FUNCTION public.trg_new_evt()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$/*********************************************************/
/* trigger new_evt                                       */
/*  filtre les evènements pour les envois de mail        */
/* ***************************************************** */
/* 17/09/2013 : Rajout des envois vers les familiers     */
/*********************************************************/
declare
	v_type_evt integer;
	v_perso_cod integer;
	v_compt_cod integer;
	v_envoi_mail integer;
	v_cible integer;
	v_mail integer;
	v_attaquant integer;
	v_texte_evt text;
	v_texte_mail text;
	v_ltype_evt text;
	v_nom_perso text;
begin
	v_mail := 0;
	v_envoi_mail := 0;
	v_type_evt := NEW.levt_tevt_cod;
	v_perso_cod := NEW.levt_perso_cod1;
	v_attaquant := NEW.levt_attaquant;
	v_cible := NEW.levt_cible;
	v_texte_evt := NEW.levt_texte;
	-- on trie sur les types d’événements
	if v_type_evt in (9, 10, 14, 15, 16, 17, 24, 34, 35, 36, 37, 39, 40, 42, 47, 48, 50, 51, 58, 59, 60, 66, 75, 78, 80, 84, 85) then
	
		-- on regarde si le perso est rattaché à un compte
		select into v_compt_cod, v_envoi_mail
			compt_cod, compt_envoi_mail
			from perso_compte, compte
			where pcompt_perso_cod = v_perso_cod
			and pcompt_compt_cod = compt_cod;
			
		-- sinon, on regarde si ce n’est pas un familier rattaché à un compte
		if not found then
			select into v_compt_cod, v_envoi_mail
				compt_cod, compt_envoi_mail
			from perso_familier
			inner join perso_compte on pcompt_perso_cod = pfam_perso_cod
			inner join compte on compt_cod = pcompt_compt_cod
			where pfam_familier_cod = v_perso_cod;
		end if;
		-- FIN : on regarde si le perso est rattaché à un compte (ou est un familier)

		-- on regarde si le perso veut recevoir des mail
		if v_envoi_mail = 1 then
			-- a partir d’ici, on sait qu’on peut commencer à préparer tout ça
			-- événements cible
			if v_type_evt in (9, 10, 14, 16, 17, 34, 40, 42, 48, 50, 51, 58, 59, 60, 75, 78, 80, 84, 85) then
				if v_cible = v_perso_cod then
					if v_cible != v_attaquant then
						v_mail := 1;
					end if;
				end if;
			end if;
			-- FIN : événements cible
			-- événements perso_cod1
			if v_type_evt in (24, 35, 36, 37, 39, 47, 66) then
				v_mail := 1;
			end if;
			-- FIN : événements perso_cod1
			-- événements attaquant
			if v_type_evt in (15) then
				if v_attaquant = v_perso_cod then
					v_mail := 1;
				end if;
			end if;
			-- FIN : événements attaquant
		end if;
		-- FIN : on regarde si le perso veut recevoir des mail

		-- insertion dans table
		if v_mail = 1 then
			-- construction du texte
			select into v_ltype_evt 
				tevt_libelle
			from type_evt
			where tevt_cod = v_type_evt;

			v_texte_mail := 'Le ' || to_char(now(), 'DD/MM/YYYY') || ' à ' || to_char(now(), 'hh24:mi:ss') || ' : ';
			-- nom attaquant
			if v_attaquant is not null then
				select into v_nom_perso perso_nom from perso
					where perso_cod = v_attaquant;
				v_texte_evt := replace(v_texte_evt, '[attaquant]', v_nom_perso);
			end if;
			-- fin nom attaquant	
			-- nom cible
			if v_attaquant is not null then
				select into v_nom_perso perso_nom from perso
					where perso_cod = v_cible;
				v_texte_evt := replace(v_texte_evt, '[cible]', v_nom_perso);
			end if;
			-- fin nom cible
			select into v_nom_perso perso_nom from perso
			where perso_cod = v_perso_cod;

			v_texte_evt := replace(v_texte_evt, '[perso_cod1]', v_nom_perso);
			v_texte_mail := v_texte_mail || v_texte_evt || ' (' || v_ltype_evt || ').';
			
			insert into envois_mail (menv_perso_cod, menv_compt_cod, menv_texte)
			values (v_perso_cod, v_compt_cod, v_texte_mail);
		end if;
		-- FIN insertion dans table
	end if;
	-- FIN : on trie sur les types d’événements
	return NEW;
end;$function$

