CREATE OR REPLACE FUNCTION public.nv_magie_lache_ca(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function magie_enchevetrement : lance le sort enchevetrement  */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = cible                                                  */
/*   $3 = type lancer                                            */
/*        0 = rune                                               */
/*        1 = mémo                                               */
/* Le code sortie est une chaine html utilisable directement     */
/*****************************************************************/
/* Créé le 20/07/2003                                            */
/* Liste des modifications :                                     */
/*   08/09/2003 : ajout d un tag pour amélioration auto          */
/*   29/01/2004 : modif du type code sortie                      */
/*****************************************************************/
declare
-------------------------------------------------------------
-- variables servant pour la sortie
-------------------------------------------------------------
	code_retour text;				-- chaine html de sortie
	texte_evt text;				-- texte pour évènements
	nom_sort text;					-- nom du sort
-------------------------------------------------------------
-- variables concernant le lanceur	
-------------------------------------------------------------
	lanceur alias for $1;		-- perso_cod du lanceur
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
	cible alias for $2;			-- perso_cod de la cible
	nom_cible text;				-- nom de la cible
	pos_cible integer;
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
	num_sort integer;				-- numéro du sort à lancer
	type_lancer alias for $3;	-- type de lancer (memo ou rune)
	cout_pa integer;				-- Cout en PA du sort
	px_gagne text;				-- PX gagnes
	v_pa_attaque integer;		-- Pa modifiés
	v_obj_porte integer;			-- obj de l'arme portée
	v_obj_deposable text;	-- is déposable ?
	v_gobj_arme_naturelle text; 	--arme naturelle des monstres
	v_obj integer;
	v_arene text;			-- Pas de "Lâche ça" en arène.
        v_type_perso integer;           -- Type de perso, retour en inventaire pour les perso joueurs
	
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
	magie_commun_txt text;		-- texte pour magie commun
	res_commun integer;			-- partie 1 du commun
	distance_cibles integer;	-- distance entre lanceur et cible
	ligne_rune record;			-- record des rune à dropper
	temp_ameliore_competence text;
										-- chaine temporaire pour amélioration
	v_bloque_magie integer;		-- vérif si résistance magique
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
	des integer;					-- lancer de dés
	compt integer;					-- fourre tout
	v_gobj_cod integer;
	v_pv_cible integer;
begin
-------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
	num_sort := 65;
-- les px
	px_gagne := 0;
-------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------	
	select into v_type_perso,nom_cible,v_pv_cible,v_arene 
		perso_type_perso,perso_nom,perso_pv_max, etage_arene 
		from perso, perso_position, positions, etage 
		where ppos_perso_cod = perso_cod
		and ppos_pos_cod = pos_cod
		and pos_etage = etage_numero 
		and perso_cod = cible;
	select into nom_sort sort_nom from sorts
		where sort_cod = num_sort;
	magie_commun_txt := magie_commun(lanceur,cible,type_lancer,num_sort);
	res_commun := split_part(magie_commun_txt,';',1);
	if res_commun = 0 then
		code_retour := split_part(magie_commun_txt,';',2);
		return code_retour;
	end if;
	code_retour := split_part(magie_commun_txt,';',3);
	px_gagne := split_part(magie_commun_txt,';',4);
	
	select into v_obj,v_obj_deposable,v_gobj_cod,v_gobj_arme_naturelle
		perobj_obj_cod,gobj_deposable,obj_gobj_cod,gobj_arme_naturelle
		from perso_objets,objets,objet_generique
		where perobj_perso_cod = cible
		and perobj_obj_cod = obj_cod
		and obj_gobj_cod = gobj_cod
		and gobj_tobj_cod = 1
		and perobj_equipe = 'O';
	if v_gobj_arme_naturelle = 'O' then
		code_retour := code_retour||'<p>Malgré la réussite de votre sort, vous ne pouvez pas arracher une arme naturelle d''un monstre, ou certaines armes particulières.';
	texte_evt := '[attaquant] a lancé '||nom_sort||' sur [cible] ';
   insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     	values(nextval('seq_levt_cod'),14,now(),1,lanceur,texte_evt,'O','O',lanceur,cible);
   insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     	values(nextval('seq_levt_cod'),14,now(),1,cible,texte_evt,'N','O',lanceur,cible);
		return code_retour;
	end if;
	if not found then
		code_retour := code_retour||'<p>Malgré la réussite de votre invocation, rien ne se passe car la cible ne porte pas d''arme';
	texte_evt := '[attaquant] a lancé '||nom_sort||' sur [cible] ';
   insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     	values(nextval('seq_levt_cod'),14,now(),1,lanceur,texte_evt,'O','O',lanceur,cible);
   insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     	values(nextval('seq_levt_cod'),14,now(),1,cible,texte_evt,'N','O',lanceur,cible);
		return code_retour;
	else
		insert into action (act_tact_cod,act_perso1,act_perso2,act_donnee) values (2,lanceur,cible,3*ln(v_pv_cible));
		if v_obj_deposable = 'N' then
			compt := f_del_objet(v_obj);
			code_retour := code_retour||'<p>L''arme de votre cible a été détruite !<br>';
		elsif v_arene = 'O' then
			update perso_objets set perobj_equipe = 'N' where perobj_obj_cod = v_obj;
			code_retour := code_retour||'<p>L''arme de votre cible est retournée dans son inventaire !<br />';
                elsif v_type_perso = 1 then
			update perso_objets set perobj_equipe = 'N' where perobj_obj_cod = v_obj;
			code_retour := code_retour||'<p>L''arme de votre cible est retournée dans son inventaire !<br />';
		else
			delete from perso_objets
				where perobj_perso_cod = cible
				and perobj_obj_cod = v_obj;
			select into pos_cible
				ppos_pos_cod from perso_position
				where ppos_perso_cod = cible;
			insert into objet_position (pobj_pos_cod,pobj_obj_cod)
				values (pos_cible,v_obj);
			code_retour := code_retour||'<p>L''arme de votre adversaire est tombée au sol !<br>';
		end if;
	end if;
	
	code_retour := code_retour||'<br>Vous gagnez '||px_gagne||' PX pour cette action.<br>';
	texte_evt := '[attaquant] a lancé '||nom_sort||' sur [cible] ';
   insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     	values(nextval('seq_levt_cod'),14,now(),1,lanceur,texte_evt,'O','O',lanceur,cible);
   if (lanceur != cible) then
    insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     	values(nextval('seq_levt_cod'),14,now(),1,cible,texte_evt,'N','O',lanceur,cible);
   end if;

	return code_retour;
end;$function$

