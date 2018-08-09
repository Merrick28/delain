CREATE OR REPLACE FUNCTION public.desengagement(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/***************************************************************/
/* désengagement                                               */
/*  on passe en param :                                        */
/*    $1 = le perso_cod qui doit se désengager                 */
/*    $2 = la cible/assaillant que l'on veut désengager        */
/***************************************************************/
declare
	code_retour text;
	personnage alias for $1;
	cible alias for $2;
	v_pa integer;
	nom_cible text;
      	nb_off integer;			-- locks offensifs
	nb_def integer;			-- locks défensifs
	v_competence_init integer;
	v_competence_modifie integer;
	nb_concentrations integer;
	des integer;
	texte text;
	tmp_txt text;
	temp integer;
        bonmal integer;
        malus integer;

begin
	code_retour := '';
	select into v_pa perso_pa from perso
		where perso_cod = personnage;
	if not found then
		code_retour := 'Anomalie ! Personnage non trouvé !';
		return code_retour;
	end if;
	if v_pa < getparm_n(60) then
		code_retour := 'Anomalie ! pas assez de PA pour se désengager !';
		return code_retour;
	end if;
	select into nom_cible perso_nom
		from perso
		where perso_cod = cible;	
	if not found then
		code_retour := 'Anomalie ! perso cible non trouvé !';
		return code_retour;
	end if;
	select into nb_off count(lock_cod)
		from lock_combat
		where lock_attaquant = personnage
		and lock_cible = cible;
	select into nb_def count(lock_cod)
		from lock_combat
		where lock_attaquant = cible
		and lock_cible = personnage;	
	if (nb_off + nb_def) = 0 then
		code_retour := 'Aucune action n''a été effectuée car vous n''êtes pas en combat avec <b>'||nom_cible||'</b>.<br>';
		return code_retour;
	end if;
	if valeur_bonus(personnage, 'HON') != 0 then
		code_retour := 'Vous ne pouvez pas vous désengager en étant sous l''effet du sort <b>Honneur</b>.<br>';
		return code_retour;	
	end if;
-- on fait les jets de compétence
	select into v_competence_init pcomp_modificateur
			from perso_competences
			where pcomp_perso_cod = personnage
			and pcomp_pcomp_cod = 71;
	v_competence_modifie := v_competence_init - (2 * nb_def) - (2 * nb_off);
	select into nb_concentrations concentration_cod from concentrations
		where concentration_perso_cod = personnage;
	if found then
		v_competence_modifie := v_competence_modifie + 20;
		delete from concentrations
		where concentration_perso_cod = personnage;
	end if;
	if v_competence_modifie <= 10 then
		v_competence_modifie := 10;
	end if;
	code_retour := code_retour||'Vous avez utilisé la compétence <b>désengagement</b> ';
	code_retour := code_retour||'(compétence en tenant compte des modificateurs : '||trim(to_char(v_competence_modifie,'99999'))||' %)<br>';
-- on regarde si la cible est bénie ou maudite
bonmal = 0;
         malus = 0;
	  bonmal := valeur_bonus(personnage, 'BEN') + valeur_bonus(personnage, 'MAU');
	if bonmal <> 0 then
        des := lancer_des3(1,100,bonmal);
                else
        des := lancer_des(1,100);
        end if;
	code_retour := code_retour||'Votre lancer de dés est de <b>'||trim(to_char(des,'999'))||'</b>, ';
     if des >= 96 then -- echec critique
     texte := '[attaquant] a tenté de rompre le combat avec [cible]mais a lamentablement échoué.';
     insert into ligne_evt (levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
		values (52,'now()',1,personnage,texte,'O','O',personnage,cible);
      insert into ligne_evt (levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
		values (52,'now()',1,cible,texte,'N','O',personnage,cible);
		code_retour := code_retour||' vous avez donc <b>échoué.</b><br>';
         -- on enlève les pa
	update perso set perso_pa = perso_pa - getparm_n(60) where perso_cod = personnage;
	else
	if des > v_competence_modifie then
		texte := '[attaquant] a tenté de rompre le combat avec [cible].';
	insert into ligne_evt (levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
		values (52,'now()',1,personnage,texte,'O','O',personnage,cible);
	insert into ligne_evt (levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
		values (52,'now()',1,cible,texte,'N','O',personnage,cible);
		code_retour := code_retour||' vous avez donc <b>échoué.</b><br>';
		if v_competence_init <= getparm_n(1) then
			code_retour := code_retour||'Votre compétence est inférieure à '||trim(to_char(getparm_n(1),'999999999'))||'.<br>';
			tmp_txt := ameliore_competence_px(personnage,71,v_competence_init);
			code_retour := code_retour||'Votre jet d''amélioration est de '||split_part(tmp_txt,';',1)||'. ';
			if split_part(tmp_txt,';',2) = '1' then
				code_retour := code_retour||'Vous avez donc amélioré cette compétence. Sa nouvelle valeur est de <b>'||split_part(tmp_txt,';',3)||'</b>.<br><br>';
				code_retour := code_retour||'Vous gagnez un PX.<br>';
			else
				code_retour := code_retour||'Vous n''avez pas amélioré cette compétence. ';
			end if;
		end if;
        -- on enlève les pa
	update perso set perso_pa = perso_pa - getparm_n(60) where perso_cod = personnage;
		return code_retour;
        else
	code_retour := code_retour||' vous avez donc <b>réussi</b>.<br>';
	tmp_txt := ameliore_competence_px(personnage,71,v_competence_init);
	code_retour := code_retour||'Votre jet d''amélioration est de '||split_part(tmp_txt,';',1)||'. ';
	   if split_part(tmp_txt,';',2) = '1' then
		code_retour := code_retour||'Vous avez donc <b>amélioré</b> cette compétence. Sa nouvelle valeur est de <b>'||split_part(tmp_txt,';',3)||'</b>.<br><br>';
		code_retour := code_retour||'Vous gagnez un PX.<br>';
	   else
		code_retour := code_retour||'Vous n''avez pas amélioré cette compétence.<br><br>'; 
	   end if;
-- on efface les locks
	delete from lock_combat
		where lock_attaquant = cible
		and lock_cible = personnage;	
	delete from lock_combat
		where lock_attaquant = personnage
		and lock_cible = cible;
-- on enlève les pa
	update perso set perso_pa = perso_pa - getparm_n(60) where perso_cod = personnage;
-- on met les évènements
	texte := '[attaquant] a rompu le combat avec [cible].';
	insert into ligne_evt (levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
		values (nextval('seq_levt_cod'),34,'now()',1,personnage,texte,'O','O',personnage,cible);
	insert into ligne_evt (levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
		values (nextval('seq_levt_cod'),34,'now()',1,cible,texte,'N','O',personnage,cible);
-- on met un code retour
	code_retour := code_retour||'Vous avez rompu tous les blocages de combat avec <b>'||nom_cible||'</b>.<br>';
--
        end if;
     end if;
return code_retour;	
end;$function$

