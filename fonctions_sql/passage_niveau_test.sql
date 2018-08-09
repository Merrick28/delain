CREATE OR REPLACE FUNCTION public.passage_niveau_test(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/********************************************************/
/* fonction f_pass_niveau : effectue toutes les actions */
/*   liées au passage de niveau et retourne une chaine  */
/*   exploitable en html                                */
/* on passe en paramètres :                             */
/*   $1 = le perso_cod du passage                       */
/*   $2 = le type d'amélioration choisie                */
/*        1 = temps                                     */
/*        2 = dégats distance                           */
/*        3 = régénération                              */
/*        4 = dégats corps à corps                      */
/*        5 = armure                                    */
/*        6 = vue                                       */
/*        7 = réparation                                */
/*        8 = sorts mémorisables                        */
/*        9 = vampirisme                                */
/*       10 = af lvl 2                                  */
/*       11 = af lvl 3                                  */
/*       12 = feinte                                    */
/*       13 = feinte lvl 2                              */
/*       14 = feinte lvl 3                              */
/*       15 = Coup de grace                             */
/*       16 = Coup de grace lvl 2                       */
/*       17 = Coup de grace lvl 3                       */
/*       18 = af                                        */
/*       19 = réceptacle magique                        */
/*       20 = amel memo                                 */
/*       21 -> 23 : bour portant                        */
/*       24 -> 26 : tir précis                          */
/********************************************************/
declare
	code_retour text;					-- chaine de retour
	personnage alias for $1;		-- perso_cod
	amel alias for $2;				-- type amélioration
	v_pa integer;						-- nombre de PA du perso
	v_niveau_actu integer;			-- niveau actuel du perso
	v_limite_niveau integer;		-- PX nécessaires pour monter de niveau
	v_px numeric;						-- PX du perso
	v_nom_perso text;					-- nom du perso
	pv_max_actuel integer;			-- PV max du perso
	v_constitution integer;			-- constit du perso
	gain_pv integer;					-- gain en PV du perso
	temp integer;						-- temp
	fait integer;						-- amélioration faite ou non ?
	v_temps_actuel integer;			-- temps de tour du perso en minutes
	amel_temps integer;				-- amélioration de temps en minutes
	texte_evt text;					-- texte pour évènements
	v_repar integer;					-- capacité de réparation
	v_modif_repar integer;			-- ce qu'on monte en répar
	v_lvl_vamp integer;
	v_nb_amel_comp integer;
	compt integer;
	v_force integer;
	v_race integer;
	v_deg_dex integer;
	v_regen integer;
	v_degats integer;
	v_armure integer;
	v_vue integer;
	v_int integer;
	v_con integer;
	v_dex integer;
	v_for integer;
        v_multi integer;
	limite integer; 				-- Seuil limite qui permet ou pas l'amélioration
begin
-- etape 1 : on vérifie
	select into 	v_pa,
						v_niveau_actu,
						v_limite_niveau,
						v_px,
						v_nom_perso,
						pv_max_actuel,
						v_constitution,
						v_repar,
						v_nb_amel_comp,
						v_race,
						v_deg_dex,
						v_regen,
						v_degats,
						v_armure,
						v_vue,
						v_int,
						v_con,
						v_dex,
						v_for				
			perso_pa,
			perso_niveau,
			limite_niveau(perso_cod),
			perso_px,
			perso_nom,
			perso_pv_max,
			perso_con,
			perso_capa_repar,
			perso_nb_amel_comp,
			perso_race_cod,
			perso_amel_deg_dex,
			perso_des_regen,
			perso_amelioration_degats,
			perso_amelioration_armure,
			perso_amelioration_vue,
			perso_int,
			perso_con,
			perso_dex,
			perso_for
		from perso
		where perso_cod = personnage
		and perso_actif = 'O';
	if not found then
		code_retour := '<p>Erreur ! Perso non trouvé !';
		return code_retour;
	end if;
	if v_px < v_limite_niveau then
		code_retour := '<p>Erreur ! Pas assez de PX pour monter de niveau !';
		return code_retour;
	end if;
	if v_pa < getparm_n(8) then
		code_retour := '<p>Erreur ! Pas assez de PA pour monter de niveau !';
		return code_retour;
	end if;
--------------------------------------------------
-- Controle sur les seuils : on calcule la limite
--------------------------------------------------
	if v_niveau_actu <= 3 then
		limite := 2;
	else limite := floor(v_niveau_actu::numeric/2);
	End if;


--------------------------------------------------
-- tout semble correct, on passe aux améliorations	
--------------------------------------------------
-- génériques
	temp := round(floor(v_constitution/4));
	gain_pv := lancer_des(1,temp);
	gain_pv := gain_pv + 1;
	update perso
		set perso_pa = perso_pa - getparm_n(8), perso_niveau = perso_niveau + 1, perso_pv_max = pv_max_actuel + gain_pv
		where perso_cod = personnage;
	v_niveau_actu := v_niveau_actu + 1;
	
-- spécifiques
-- temps	
	if amel = 1 then
		select into v_temps_actuel
			perso_temps_tour from perso
			where perso_cod = personnage;
		amel_temps := 30;
		if v_temps_actuel > 660 then
			amel_temps := 30;
		end if;
		if ((v_temps_actuel >585) and (v_temps_actuel <= 660)) then
			amel_temps := 25;
		end if;
		if ((v_temps_actuel >525) and (v_temps_actuel <= 585)) then
			amel_temps := 20;
		end if;
		if ((v_temps_actuel >480) and (v_temps_actuel <= 525)) then
			amel_temps := 15;
		end if;
		if ((v_temps_actuel >450) and (v_temps_actuel <= 480)) then
			amel_temps := 10;
		end if;
		if ((v_temps_actuel >360) and (v_temps_actuel <= 450)) then
			amel_temps := 5;
		end if;
                if v_temps_actuel = 360 then
                        code_retour := '<p>Erreur !vous avez atteint la dlt minimum autorisée !';
		        return code_retour;
                end if;
		update perso set perso_temps_tour = perso_temps_tour - amel_temps where perso_cod = personnage;
-- dégats à distance
	elsif amel = 2 then
		if v_deg_dex >= limite then
				return '<p>Vous ne pouvez pas prendre cette amélioration, vous êtes déjà au niveau maximum pour cette amélioration !';
		end if;
		update perso set perso_amel_deg_dex = perso_amel_deg_dex + 1 where perso_cod = personnage;
-- régénération
	elsif amel = 3 then
		if v_regen >= limite then
				return '<p>Vous ne pouvez pas prendre cette amélioration, vous êtes déjà au niveau maximum pour cette amélioration !';
		end if;
		update perso set perso_des_regen = perso_des_regen + 1 where perso_cod = personnage;
-- dégats corps à corps
	elsif amel = 4 then
		if v_degats >= limite then
				return '<p>Vous ne pouvez pas prendre cette amélioration, vous êtes déjà au niveau maximum pour cette amélioration !';
		end if;
		update perso set perso_amelioration_degats = perso_amelioration_degats + 1 where perso_cod = personnage;
-- armure
	elsif amel = 5 then
		if v_armure >= limite then
				return '<p>Vous ne pouvez pas prendre cette amélioration, vous êtes déjà au niveau maximum pour cette amélioration !';
		end if;
		update perso set perso_amelioration_armure = perso_amelioration_armure + 1 where perso_cod = personnage;
-- vue
	elsif amel = 6 then
		if v_vue >= limite or v_vue >= 5 then
				return '<p>Vous ne pouvez pas prendre cette amélioration, vous êtes déjà au niveau maximum pour cette amélioration !';
		end if;
		update perso set perso_amelioration_vue = perso_amelioration_vue + 1 where perso_cod = personnage;	
-- réparation
	elsif amel = 7 then
		v_modif_repar := 1;
		if v_repar <= 40 then
			v_modif_repar := 5;
		end if;
		if v_repar > 40  and v_repar <= 50 then
			v_modif_repar := 4;
		end if;
		if v_repar > 50  and v_repar <= 60 then
			v_modif_repar := 3;
		end if;
		if v_repar > 60  and v_repar <= 60 then
			v_modif_repar := 1;
		end if;
		if v_repar > 70 then
			v_modif_repar := 1;
		end if;
		update perso set perso_capa_repar = perso_capa_repar + v_modif_repar, perso_nb_amel_repar = perso_nb_amel_repar + 1
			where perso_cod = personnage;
	elsif amel = 8 then
		if v_race in (1,3) then
			update perso set perso_amelioration_nb_sort = perso_amelioration_nb_sort + 3 where perso_cod = personnage;
		else
			update perso set perso_amelioration_nb_sort = perso_amelioration_nb_sort + 1 where perso_cod = personnage;
		end if;
	elsif amel = 9 then
		select into v_lvl_vamp perso_niveau_vampire from perso
			where perso_cod = personnage;
		if v_lvl_vamp = 0 then
			code_retour := 'Erreur ! Vous ne pouvez pas prendre cette amélioration !';
			return code_retour;
		else
			update perso set perso_vampirisme = perso_vampirisme + 0.05 where perso_cod = personnage;
		end if;
	elsif amel = 10 then
		if (floor(v_niveau_actu::numeric/7) <= v_nb_amel_comp) then
			code_retour := 'Erreur ! Vous ne pouvez pas prendre cette amélioration !';
			return code_retour;
		end if;
		select into compt pcomp_modificateur
			from perso_competences
			where pcomp_perso_cod = personnage
			and pcomp_pcomp_cod = 25;
		if not found then
			code_retour := 'Erreur ! Vous ne pouvez pas prendre cette amélioration !';
			return code_retour;
		end if;
		-- on met le nb d'amélioration
		update perso set perso_nb_amel_comp = perso_nb_amel_comp + 1
			where perso_cod = personnage;
		-- on supprime l'AF
		delete from perso_competences
			where pcomp_perso_cod = personnage
			and pcomp_pcomp_cod = 25;
		-- on met l'AF lvl 2
		insert into perso_competences
			(pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur)
			values
			(personnage,61,round(compt));
	elsif amel = 11 then
		if (floor(v_niveau_actu::numeric/7) <= v_nb_amel_comp) then
			code_retour := 'Erreur ! Vous ne pouvez pas prendre cette amélioration !';
			return code_retour;
		end if;
		select into compt pcomp_modificateur
			from perso_competences
			where pcomp_perso_cod = personnage
			and pcomp_pcomp_cod = 61;
		if not found then
			code_retour := 'Erreur ! Vous ne pouvez pas prendre cette amélioration !';
			return code_retour;
		end if;
		-- on met le nb d'amélioration
		update perso set perso_nb_amel_comp = perso_nb_amel_comp + 1
			where perso_cod = personnage;
		-- on supprime l'AF
		delete from perso_competences
			where pcomp_perso_cod = personnage
			and pcomp_pcomp_cod = 61;
		-- on met l'AF lvl 3
		insert into perso_competences
			(pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur)
			values
			(personnage,62,round(compt));
	elsif amel = 12 then
		if (floor(v_niveau_actu::numeric/7) <= v_nb_amel_comp) then
			code_retour := 'Erreur ! Vous ne pouvez pas prendre cette amélioration !';
			return code_retour;
		end if;
		select into v_force perso_for from perso
			where perso_cod = personnage;
		v_force := v_force + 15;
		-- on met le nb d'amélioration
		update perso set perso_nb_amel_comp = perso_nb_amel_comp + 1
			where perso_cod = personnage;
		-- on met feinte
		insert into perso_competences
			(pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur)
			values
			(personnage,63,v_force);
	elsif amel = 13 then
		if (floor(v_niveau_actu::numeric/7) <= v_nb_amel_comp) then
			code_retour := 'Erreur ! Vous ne pouvez pas prendre cette amélioration !';
			return code_retour;
		end if;
		select into compt pcomp_modificateur
			from perso_competences
			where pcomp_perso_cod = personnage
			and pcomp_pcomp_cod = 63;
		if not found then
			code_retour := 'Erreur ! Vous ne pouvez pas prendre cette amélioration !';
			return code_retour;
		end if;
		-- on met le nb d'amélioration
		update perso set perso_nb_amel_comp = perso_nb_amel_comp + 1
			where perso_cod = personnage;
		-- on supprime l'AF
		delete from perso_competences
			where pcomp_perso_cod = personnage
			and pcomp_pcomp_cod = 63;
		-- on met l'AF lvl 2
		insert into perso_competences
			(pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur)
			values
			(personnage,64,round(compt));
	elsif amel = 14 then
		if (floor(v_niveau_actu::numeric/7) <= v_nb_amel_comp) then
			code_retour := 'Erreur ! Vous ne pouvez pas prendre cette amélioration !';
			return code_retour;
		end if;
		select into compt pcomp_modificateur
			from perso_competences
			where pcomp_perso_cod = personnage
			and pcomp_pcomp_cod = 64;
		if not found then
			code_retour := 'Erreur ! Vous ne pouvez pas prendre cette amélioration !';
			return code_retour;
		end if;
		-- on met le nb d'amélioration
		update perso set perso_nb_amel_comp = perso_nb_amel_comp + 1
			where perso_cod = personnage;
		-- on supprime l'AF
		delete from perso_competences
			where pcomp_perso_cod = personnage
			and pcomp_pcomp_cod = 64;
		-- on met l'AF lvl 2
		insert into perso_competences
			(pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur)
			values
			(personnage,65,round(compt));
	elsif amel = 15 then
		if (floor(v_niveau_actu::numeric/7) <= v_nb_amel_comp) then
			code_retour := 'Erreur ! Vous ne pouvez pas prendre cette amélioration !';
			return code_retour;
		end if;
		select into v_force perso_for from perso
			where perso_cod = personnage;
		v_force := v_force + 15;
		-- on met le nb d'amélioration
		update perso set perso_nb_amel_comp = perso_nb_amel_comp + 1
			where perso_cod = personnage;
		-- on met feinte
		insert into perso_competences
			(pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur)
			values
			(personnage,66,v_force);
	elsif amel = 16 then
		if (floor(v_niveau_actu::numeric/7) <= v_nb_amel_comp) then
			code_retour := 'Erreur ! Vous ne pouvez pas prendre cette amélioration !';
			return code_retour;
		end if;
		select into compt pcomp_modificateur
			from perso_competences
			where pcomp_perso_cod = personnage
			and pcomp_pcomp_cod = 66;
		if not found then
			code_retour := 'Erreur ! Vous ne pouvez pas prendre cette amélioration !';
			return code_retour;
		end if;
		-- on met le nb d'amélioration
		update perso set perso_nb_amel_comp = perso_nb_amel_comp + 1
			where perso_cod = personnage;
		-- on supprime l'AF
		delete from perso_competences
			where pcomp_perso_cod = personnage
			and pcomp_pcomp_cod = 66;
		-- on met l'AF lvl 2
		insert into perso_competences
			(pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur)
			values
			(personnage,67,round(compt));	
	elsif amel = 17 then
		if (floor(v_niveau_actu::numeric/7) <= v_nb_amel_comp) then
			code_retour := 'Erreur ! Vous ne pouvez pas prendre cette amélioration !';
			return code_retour;
		end if;
		select into compt pcomp_modificateur
			from perso_competences
			where pcomp_perso_cod = personnage
			and pcomp_pcomp_cod = 67;
		if not found then
			code_retour := 'Erreur ! Vous ne pouvez pas prendre cette amélioration !';
			return code_retour;
		end if;
		-- on met le nb d'amélioration
		update perso set perso_nb_amel_comp = perso_nb_amel_comp + 1
			where perso_cod = personnage;
		-- on supprime l'AF
		delete from perso_competences
			where pcomp_perso_cod = personnage
			and pcomp_pcomp_cod = 67;
		-- on met l'AF lvl 2
		insert into perso_competences
			(pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur)
			values
			(personnage,68,round(compt));
	elsif amel = 18 then
		if (floor(v_niveau_actu::numeric/7) <= v_nb_amel_comp) then
			code_retour := 'Erreur ! Vous ne pouvez pas prendre cette amélioration !';
			return code_retour;
		end if;
		select into v_force perso_for from perso
			where perso_cod = personnage;
		v_force := v_force + 15;
		-- on met le nb d'amélioration
		update perso set perso_nb_amel_comp = perso_nb_amel_comp + 1
			where perso_cod = personnage;
		-- on met feinte
		insert into perso_competences
			(pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur)
			values
			(personnage,25,v_force);
	elsif amel = 19 then
		if (floor(v_niveau_actu::numeric/7) <= v_nb_amel_comp) then
			code_retour := 'Erreur ! Vous ne pouvez pas prendre cette amélioration !';
			return code_retour;
		end if;
		-- on met le nb d'amélioration
		update perso set perso_nb_amel_comp = perso_nb_amel_comp + 1
			where perso_cod = personnage;
		-- on met le recept
		update perso set perso_nb_receptacle = perso_nb_receptacle + 1 where perso_cod = personnage;
	elsif amel = 20 then
		if (floor(v_niveau_actu::numeric/7) <= v_nb_amel_comp) then
			code_retour := 'Erreur ! Vous ne pouvez pas prendre cette amélioration !';
			return code_retour;
		end if;
		-- on met le nb d'amélioration
		update perso set perso_nb_amel_comp = perso_nb_amel_comp + 1
			where perso_cod = personnage;
		-- on met le recept
		update perso set perso_nb_amel_chance_memo = perso_nb_amel_chance_memo + 1 where perso_cod = personnage;
	elsif amel = 21 then
		if (floor(v_niveau_actu::numeric/7) <= v_nb_amel_comp) then
			code_retour := 'Erreur ! Vous ne pouvez pas prendre cette amélioration !';
			return code_retour;
		end if;
		select into v_force perso_dex from perso
			where perso_cod = personnage;
		v_force := v_force + 15;
		-- on met le nb d'amélioration
		update perso set perso_nb_amel_comp = perso_nb_amel_comp + 1
			where perso_cod = personnage;
		-- on met feinte
		insert into perso_competences
			(pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur)
			values
			(personnage,72,v_force);
	elsif amel = 22 then
		if (floor(v_niveau_actu::numeric/7) <= v_nb_amel_comp) then
			code_retour := 'Erreur ! Vous ne pouvez pas prendre cette amélioration !';
			return code_retour;
		end if;
		select into compt pcomp_modificateur
			from perso_competences
			where pcomp_perso_cod = personnage
			and pcomp_pcomp_cod = 72;
		if not found then
			code_retour := 'Erreur ! Vous ne pouvez pas prendre cette amélioration !';
			return code_retour;
		end if;
		-- on met le nb d'amélioration
		update perso set perso_nb_amel_comp = perso_nb_amel_comp + 1
			where perso_cod = personnage;
		-- on supprime l'AF
		delete from perso_competences
			where pcomp_perso_cod = personnage
			and pcomp_pcomp_cod = 72;
		-- on met l'AF lvl 2
		insert into perso_competences
			(pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur)
			values
			(personnage,73,round(compt));
	elsif amel = 23 then
		if (floor(v_niveau_actu::numeric/7) <= v_nb_amel_comp) then
			code_retour := 'Erreur ! Vous ne pouvez pas prendre cette amélioration !';
			return code_retour;
		end if;
		select into compt pcomp_modificateur
			from perso_competences
			where pcomp_perso_cod = personnage
			and pcomp_pcomp_cod = 73;
		if not found then
			code_retour := 'Erreur ! Vous ne pouvez pas prendre cette amélioration !';
			return code_retour;
		end if;
		-- on met le nb d'amélioration
		update perso set perso_nb_amel_comp = perso_nb_amel_comp + 1
			where perso_cod = personnage;
		-- on supprime l'AF
		delete from perso_competences
			where pcomp_perso_cod = personnage
			and pcomp_pcomp_cod = 73;
		-- on met l'AF lvl 2
		insert into perso_competences
			(pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur)
			values
			(personnage,74,round(compt));
	elsif amel = 24 then
		if (floor(v_niveau_actu::numeric/7) <= v_nb_amel_comp) then
			code_retour := 'Erreur ! Vous ne pouvez pas prendre cette amélioration !';
			return code_retour;
		end if;
		select into v_force perso_dex from perso
			where perso_cod = personnage;
		v_force := v_force + 15;
		-- on met le nb d'amélioration
		update perso set perso_nb_amel_comp = perso_nb_amel_comp + 1
			where perso_cod = personnage;
		-- on met feinte
		insert into perso_competences
			(pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur)
			values
			(personnage,75,v_force);
	elsif amel = 25 then
		if (floor(v_niveau_actu::numeric/7) <= v_nb_amel_comp) then
			code_retour := 'Erreur ! Vous ne pouvez pas prendre cette amélioration !';
			return code_retour;
		end if;
		select into compt pcomp_modificateur
			from perso_competences
			where pcomp_perso_cod = personnage
			and pcomp_pcomp_cod = 75;
		if not found then
			code_retour := 'Erreur ! Vous ne pouvez pas prendre cette amélioration !';
			return code_retour;
		end if;
		-- on met le nb d'amélioration
		update perso set perso_nb_amel_comp = perso_nb_amel_comp + 1
			where perso_cod = personnage;
		-- on supprime l'AF
		delete from perso_competences
			where pcomp_perso_cod = personnage
			and pcomp_pcomp_cod = 75;
		-- on met l'AF lvl 2
		insert into perso_competences
			(pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur)
			values
			(personnage,76,round(compt));
	elsif amel = 26 then
		if (floor(v_niveau_actu::numeric/7) <= v_nb_amel_comp) then
			code_retour := 'Erreur ! Vous ne pouvez pas prendre cette amélioration !';
			return code_retour;
		end if;
		select into compt pcomp_modificateur
			from perso_competences
			where pcomp_perso_cod = personnage
			and pcomp_pcomp_cod = 76;
		if not found then
			code_retour := 'Erreur ! Vous ne pouvez pas prendre cette amélioration !';
			return code_retour;
		end if;
		-- on met le nb d'amélioration
		update perso set perso_nb_amel_comp = perso_nb_amel_comp + 1
			where perso_cod = personnage;
		-- on supprime l'AF
		delete from perso_competences
			where pcomp_perso_cod = personnage
			and pcomp_pcomp_cod = 76;
		-- on met l'AF lvl 2
		insert into perso_competences
			(pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur)
			values
			(personnage,77,round(compt));
	elsif amel = 27 then
        -- modification d'azaghal avec un nouveau seuil en fonction du lvl
             if v_for > 29 then
             v_multi := 2;
             elseif v_for > 24 then
             v_multi := 1.75;
             else
             v_multi := 1.5;
             end if;
		if v_niveau_actu >= floor(v_multi * v_for::numeric) then
				return '<p>Vous ne pouvez pas prendre cette amélioration, vous êtes déjà au niveau maximum pour cette amélioration !';
		end if;
		update perso set perso_for = perso_for + 1,perso_enc_max = perso_enc_max + 3 where perso_cod = personnage;
		select into temp 
			corig_carac_valeur_orig
			from carac_orig
			where corig_perso_cod = personnage 
			and corig_type_carac = 'FOR';
		if found then
			update carac_orig
				set corig_carac_valeur_orig = corig_carac_valeur_orig + 1
				where corig_perso_cod = personnage 
				and corig_type_carac = 'FOR';
		end if;
	elsif amel = 28 then
-- modification d'azaghal avec un nouveau seuil en fonction du lvl
             if v_dex > 29 then
             v_multi := 2;
             elseif v_dex > 24 then
             v_multi := 1.75;
             else
             v_multi := 1.5;
             end if;
		if v_niveau_actu >= floor(v_multi * v_dex::numeric) then
				return '<p>Vous ne pouvez pas prendre cette amélioration, vous êtes déjà au niveau maximum pour cette amélioration !';
		end if;
		update perso set perso_dex = perso_dex + 1,perso_capa_repar = perso_capa_repar + 3 where perso_cod = personnage;
		select into temp 
			corig_carac_valeur_orig
			from carac_orig
			where corig_perso_cod = personnage 
			and corig_type_carac = 'DEX';
		if found then
			update carac_orig
				set corig_carac_valeur_orig = corig_carac_valeur_orig + 1
				where corig_perso_cod = personnage 
				and corig_type_carac = 'DEX';
		end if;
	elsif amel = 29 then
-- modification d'azaghal avec un nouveau seuil en fonction du lvl
             if v_con > 29 then
             v_multi := 2;
             elseif v_con > 24 then
             v_multi := 1.75;
             else
             v_multi := 1.5;
             end if;
		if v_niveau_actu >= floor(v_multi * v_con::numeric) then
				return '<p>Vous ne pouvez pas prendre cette amélioration, vous êtes déjà au niveau maximum pour cette amélioration !';
		end if;
		update perso set perso_con = perso_con + 1,perso_pv_max = perso_pv_max + 3, perso_pv = perso_pv + 3 where perso_cod = personnage;
		gain_pv := gain_pv + 3;
		select into temp 
			corig_carac_valeur_orig
			from carac_orig
			where corig_perso_cod = personnage 
			and corig_type_carac = 'CON';
		if found then
			update carac_orig
				set corig_carac_valeur_orig = corig_carac_valeur_orig + 1
				where corig_perso_cod = personnage 
				and corig_type_carac = 'CON';
		end if;	
	elsif amel = 30 then
-- modification d'azaghal avec un nouveau seuil en fonction du lvl
             if v_int > 29 then
             v_multi := 2;
             elseif v_int > 24 then
             v_multi := 1.75;
             else
             v_multi := 1.5;
             end if;
		if v_niveau_actu >= floor(v_multi * v_int::numeric) then
				return '<p>Vous ne pouvez pas prendre cette amélioration, vous êtes déjà au niveau maximum pour cette amélioration !';
		end if;
		update perso set perso_int = perso_int + 1,perso_capa_repar = perso_capa_repar + 3 where perso_cod = personnage;
		select into temp 
			corig_carac_valeur_orig
			from carac_orig
			where corig_perso_cod = personnage 
			and corig_type_carac = 'INT';
		if found then
			update carac_orig
				set corig_carac_valeur_orig = corig_carac_valeur_orig + 1
				where corig_perso_cod = personnage 
				and corig_type_carac = 'INT';
		end if;
	end if;	
	code_retour := '<p>Vous êtes maintenant niveau <b>'||trim(to_char(v_niveau_actu,'999999'))||'</b>.<br>';
	code_retour := code_retour||'Vous gagnez <b>'||trim(to_char(gain_pv,'999999'))||'</b> points de vie.<br>';
	texte_evt := '[perso_cod1] s''est entrainé, est passé au niveau '||trim(to_char(v_niveau_actu,'999'));
	texte_evt := texte_evt||' et a gagné '||trim(to_char(gain_pv,'999'))||' points de vie.';
	insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
			values(nextval('seq_levt_cod'),11,now(),1,personnage,texte_evt,'O','N');
	texte_evt := 'Passage de niveau, option choisie : '||trim(to_char(amel,'999999'));
	insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
			values(nextval('seq_levt_cod'),99,now(),1,personnage,texte_evt,'O','N');
	return code_retour;
end;
$function$

