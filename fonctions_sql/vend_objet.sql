CREATE OR REPLACE FUNCTION public.vend_objet(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/************************************************/
/* fonction vend_objet                          */
/* cette fonction a pour but de vendre un objet */
/* venu des postes de garde                     */
/* on passe en paramètres :                     */
/*  $1 = le perso_cod qui achète                */
/*  $2 = l'objet acheté                         */
/*     1 = tonneau de bière                     */
/*     2 = opium                                */
/* on a en retour une zone texte exploitable    */
/*  dans action.php                             */
/************************************************/
/* Modifications :                              */
/* 21/04/2006 : Modification des prix pour les  */
/*              égouts                          */
/************************************************/ 



declare
	code_retour text;
	personnage alias for $1;
	v_objet alias for $2;
	v_type_lieu integer;
	v_objet_generique integer;
	v_prix integer;
	v_po integer;
	temp_txt text;
	v_etage integer;
	nb_objet integer;
	gain_po integer;
	temp_int integer;
	ligne record;
	v_comp integer;
	temp_ameliore_competence text;
	gain_px integer;
	
begin
	code_retour := '';
-- vérification de la position du perso
	select into v_type_lieu lieu_tlieu_cod
		from perso_position,lieu_position,lieu
		where ppos_perso_cod = personnage
		and lpos_pos_cod = ppos_pos_cod
		and lpos_lieu_cod = lieu_cod
		and lieu_tlieu_cod in (4);
	if not found then 
		code_retour := 'Anomalie ! Vous ne pouvez pas vendre ces objets ici !';
		return code_retour;
	end if;
	if v_objet < 1 then
		code_retour := 'Anomalie ! paramètre objet incorrect !';
		return code_retour;
	end if;
	if v_objet > 2 then
		code_retour := 'Anomalie ! paramètre objet incorrect !';
		return code_retour;
	end if;
	select into v_etage
		etage_reference
		from perso_position,positions, etage
		where ppos_perso_cod = personnage
		and ppos_pos_cod = pos_cod
		and pos_etage = etage_numero;
	if not found then 
		code_retour := 'Anomalie ! position perso non trouvée !';
		return code_retour;
	end if;
	

	if v_objet = 1 then
		v_prix := 1000 - (v_etage * 500);
		v_objet_generique := 196;
	end if;
	if v_objet = 2 then
		v_prix := 2500 - (v_etage * 500);
		v_objet_generique := 186;
	end if;
	if v_etage = 7 then
		if v_objet = 1 then
			v_prix := 2200;
		end if;
		if v_objet = 2 then
			v_prix := 2200;
		end if;
	end if;
if v_etage = 0 then
		if v_objet = 1 then
			v_prix := 200;
		end if;
		if v_objet = 2 then
			v_prix := 425;
		end if;
	end if;
	select into nb_objet count(perobj_cod) from perso_objets,objets
		where perobj_perso_cod = personnage
		and perobj_obj_cod = obj_cod 
		and obj_gobj_cod = v_objet_generique;
	if nb_objet = 0 then
		code_retour := 'Anomalie ! Vous n''avez pas d''objets de ce type à vendre !';
		return code_retour;
	end if;
--
-- on s'attaque à la vente proprement dite
-- 
-- les brouzoufs
	gain_po := nb_objet * v_prix;
	update perso set perso_po = perso_po + gain_po
		where perso_cod = personnage;
-- les PX
	gain_px := round(nb_objet/2);
	if gain_px < 1 then
		gain_px := 1;
	end if;
	update perso set perso_px = perso_px + gain_px where perso_cod = personnage;
-- les objets
	for ligne in
		select obj_cod from perso_objets,objets
		where perobj_perso_cod = personnage
		and perobj_obj_cod = obj_cod 
		and obj_gobj_cod = v_objet_generique loop
		temp_int := f_del_objet(ligne.obj_cod);
	end loop;
-- éventuellement les compétences
	if v_objet = 2 then
		-- s'il est louche, on le blanchit
		delete from perso_louche where plouche_perso_cod = personnage;
		-- compétence contrebande
		select into v_comp 
			pcomp_modificateur
			from perso_competences
			where pcomp_perso_cod = personnage
			and pcomp_pcomp_cod = 78;
		if not found then
			-- il faut créer la compétence
			insert into perso_competences
				(pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur)
				select
				perso_cod,78,(25+((10-perso_int)*2))
				from perso
				where perso_cod = personnage;
			code_retour := 'C''est votre premier dépot de matières prohibées. Vous apprenez à cette occasion la compétence <b>Contrebande</b> !<br>';
		else
			-- on va l'améliorer	
			temp_ameliore_competence := ameliore_competence_px(personnage,78,v_comp);
			code_retour := 'Vous tentez d''améliorer cotre compétence <b>Contrebande</b>.<br>';
			code_retour := code_retour||'Votre jet d''amélioration est de '||split_part(temp_ameliore_competence,';',1)||', '; 
			if split_part(temp_ameliore_competence,';',2) = '1' then
				code_retour := code_retour||'vous avez donc <b>amélioré</b> cette compétence. <br>';
				code_retour := code_retour||'Sa nouvelle valeur est '||split_part(temp_ameliore_competence,';',3)||'<br><br>';
				gain_px := gain_px + 1;
			else
				code_retour := code_retour||'vous n''avez pas amélioré cette compétence.<br><br> ';
			end if;
		end if;
	end if;
	code_retour := code_retour||'Vous venez de vendre l''objet. Vous gagnez '||trim(to_char(gain_px,'99999999999'))||' PX et '||trim(to_char(gain_po,'99999999999'))||' brouzoufs pour cette action.';
	if v_objet = 1 then
		update parametres set parm_valeur = parm_valeur + 1 where parm_cod in (78,79);
	end if;
	if v_objet = 2 then
		update parametres set parm_valeur = parm_valeur + 1 where parm_cod in (83,84);
	end if;
	return code_retour;
end;
	
	$function$

