CREATE OR REPLACE FUNCTION public.fuite(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function fuite                                                */						/*							         */
/* On passe en paramètres                                        */
/*    $1 = perso_cod                                             */
/* Le code sortie est une chaine html                            */
/*****************************************************************/
/* Créé le 14/12/2007                                            */
/* Liste des modifications :                                     */
/*    Détourage de la fonction fuite                             */
/*****************************************************************/
declare
------------------------------------------------
-- variables de retour
------------------------------------------------
	code_retour text;			-- chaine de retour
------------------------------------------------
------------------------------------------------
-- variables du perso 
------------------------------------------------
	num_perso alias for $1;	-- perso_cod
	ancien_code_pos integer;						-- ancienne position du perso
	nb_lock integer;			-- nombre de locks
	nb_lock_attaquant integer;	
									-- nombre de locks attaquant
	nb_lock_cible integer;	-- nombre de locks cible
        nb_lock_malus integer;                                      -- malus liés aux lock
	v_competence_init integer;
									-- competence init pour fuite
	v_competence_modifie integer;
									-- competence finale pour fuite
	nb_persos integer;		-- nombre de persos sur la case (fuite)
        malus_nb_persos integer;
	nb_concentrations integer;
									-- nombre de concentrations
        v_compt_pvp integer;                                            -- compteur pvp
        v_malus_pvp integer;
        v_ajustement_pvp integer;                                       -- ajustement esquive 
        bonmal integer;
        malus integer;
      
------------------------------------------------
-- variables fourre tout
------------------------------------------------	
	texte text;					-- texte pour évènement
	des integer;				-- lancer de dés pour fuite
	tmp_txt text;				-- texte pour améliore (fuite)
 	ligne record;
	nb_def integer;
	force_affichage integer;
begin
		code_retour := ''; -- on débute un paragraphe
		force_affichage := 1;
		
		/* Comptage des locks de combat */
	select count(lock_cod) into nb_lock_cible from lock_combat where lock_cible = num_perso;
	select count(lock_cod) into nb_lock_attaquant from lock_combat where lock_attaquant = num_perso;
	nb_lock := nb_lock_cible + nb_lock_attaquant;
        nb_lock_malus := nb_lock_cible * 5;
        nb_lock_malus :=  nb_lock_malus + ( nb_lock_attaquant * 2);
        

		if valeur_bonus(num_perso, 'HON') != 0 then
			code_retour := '1#Vous ne pouvez pas fuir en étant sous l''effet du sort <b>Honneur</b>.<br>';
			return code_retour;	
		end if;	
		select into v_competence_init pcomp_modificateur
			from perso_competences
			where pcomp_perso_cod = num_perso
			and pcomp_pcomp_cod = 47;
		/* on cherche les infos qui peuvent modifier cette compétence en moins */
			select into nb_persos count(*)
						from perso_position,perso
						where perso_actif = 'O'
						and ppos_pos_cod = (select ppos_pos_cod from perso_position where ppos_perso_cod = num_perso)
						and ppos_perso_cod = perso_cod;
		nb_persos := nb_persos - 1;
                malus_nb_persos := nb_persos * 2;
                /* pour les combats pvp difficulté de fuite */
                select into v_compt_pvp perso_compt_pvp from perso
                where perso_cod = num_perso;
                v_malus_pvp := v_compt_pvp * 15;
                v_competence_modifie := v_competence_init - malus_nb_persos - nb_lock_malus - v_malus_pvp;

		/* on cherche ce qui peut améliorer en plus */
		select into nb_concentrations concentration_cod from concentrations
			where concentration_perso_cod = num_perso;
		if found then
			v_competence_modifie := v_competence_modifie + 20;
			delete from concentrations
			where concentration_perso_cod = num_perso;
		end if;
		if valeur_bonus(num_perso, 'FUI') != 0 then
			v_competence_modifie := v_competence_modifie + valeur_bonus(num_perso, 'FUI');
		end if;	
		if v_competence_modifie <= 10 then
			v_competence_modifie := 10;
		end if;
		code_retour := code_retour||'Vous avez utilisé la compétence <b>fuite</b> ';
		code_retour := code_retour||'<br>(compétence en tenant compte des modificateurs : '||trim(to_char(v_competence_modifie,'99999'))||' %)';
                code_retour := code_retour||'<br>malus lié au nombre de persos sur la case : '||trim(to_char(malus_nb_persos,'99'))||' %)';
                code_retour := code_retour||'<br>malus lié aux lock de combat : '||trim(to_char(nb_lock_malus,'99'))||' %)';
                code_retour := code_retour||'<br>malus lié au Pvp : '||trim(to_char(v_malus_pvp,'99'))||' %)';
                -- etape 5.3.1 on regarde si la cible est bénie ou maudite
	  bonmal := valeur_bonus(num_perso, 'BEN') + valeur_bonus(num_perso, 'MAU');
	if bonmal <> 0 then
        des := lancer_des3(1,100,bonmal);
                else
        des := lancer_des(1,100);
        end if;

	code_retour := code_retour||'<br>Votre lancer de dés est de <b>'||trim(to_char(des,'9999'))||'</b>, ';
		if des > v_competence_modifie then
			code_retour := code_retour||' vous avez donc <b>échoué.</b><br>';
			--
			-- fuite échouée !
			--
			for ligne in select lock_cible as v_perso
				from lock_combat
				where lock_attaquant = num_perso
				union
				select lock_attaquant as v_perso
				from lock_combat
				where lock_cible = num_perso loop
				texte := '[attaquant] a tenté de fuir le combat avec [cible]';
				insert into ligne_evt (levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
				values (51,now(),num_perso,texte,'O','O',num_perso,ligne.v_perso);
				insert into ligne_evt (levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
				values (51,now(),ligne.v_perso,texte,'N','O',num_perso,ligne.v_perso);		
			end loop;
			if v_competence_init <= getparm_n(1) then
				code_retour := code_retour||'Votre compétence est inférieure à '||getparm_n(1)::text||'.<br>';
				tmp_txt := ameliore_competence(num_perso,47,v_competence_init);
				code_retour := code_retour||'Votre jet d''amélioration est de '||split_part(tmp_txt,';',1)||'. ';
				if split_part(tmp_txt,';',2) = '1' then
					code_retour := code_retour||'Vous avez donc amélioré cette compétence. Sa nouvelle valeur est de '||split_part(tmp_txt,';',3);
				else
					code_retour := code_retour||'Vous n''avez pas amélioré cette compétence. ';
				end if;
			end if;
                        update perso set perso_pa = perso_pa - getparm_n(19)
					where perso_cod = num_perso;
                        
		code_retour := '1#'||code_retour;
		return code_retour;
		end if;
		--
		-- fuite réussie
		--
		for ligne in select lock_cible as v_perso
				from lock_combat
				where lock_attaquant = num_perso
				union
				select lock_attaquant as v_perso
				from lock_combat
				where lock_cible = num_perso loop
				texte := '[attaquant] a fui le combat avec [cible]';
				insert into ligne_evt (levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
				values (50,now(),num_perso,texte,'O','O',num_perso,ligne.v_perso);
				insert into ligne_evt (levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
				values (50,now(),ligne.v_perso,texte,'N','O',num_perso,ligne.v_perso);		
			end loop;
		code_retour := code_retour||' vous avez donc <b>réussi</b>.<br>';
		tmp_txt := ameliore_competence_px(num_perso,47,v_competence_init);
		code_retour := code_retour||'Votre jet d''amélioration est de '||split_part(tmp_txt,';',1)||'. ';
		if split_part(tmp_txt,';',2) = '1' then
			code_retour := code_retour||'Vous avez donc <b>amélioré</b> cette compétence. Sa nouvelle valeur est de <b>'||split_part(tmp_txt,';',3)||'</b>.<br><br>';
		else
			code_retour := code_retour||'Vous n''avez pas amélioré cette compétence.<br><br>'; 
		end if;
		update perso
			set perso_renommee = perso_renommee - 1
			where perso_cod = num_perso;
code_retour := '0#'||code_retour;

---------------------------
-- on enlève les locks
---------------------------
	delete from lock_combat where lock_attaquant = num_perso;
	delete from lock_combat where lock_cible = num_perso;

return code_retour;	

end;$function$

