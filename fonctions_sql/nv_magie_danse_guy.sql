CREATE OR REPLACE FUNCTION public.nv_magie_danse_guy(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function magie_danse_guy : lance le sort dans de St Guy       */
/*   importants                                                  */
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
	v_perso_niveau integer;		-- niveau du lanceur
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
	cible alias for $2;			-- perso_cod de la cible
	nom_cible text;				-- nom de la cible
	v_pv integer;					-- PV de la cible
	v_pv_max integer;				-- PV max de la cible
nouveau_pv integer;
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
	num_sort integer;				-- numéro du sort à lancer
	type_lancer alias for $3;	-- type de lancer (memo ou rune)
	cout_pa integer;				-- Cout en PA du sort
	px_gagne text;				-- PX gagnes
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
	magie_commun_txt text;		-- texte pour magie commun
	res_commun integer;			-- partie 1 du commun
	distance_cibles integer;	-- distance entre lanceur et cible
	ligne_rune record;			-- record des rune à dropper
	temp_ameliore_competence text;
										-- chaine temporaire pour amélioration
	bonus_esquive integer;		-- bonus gagné
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
	des integer;					-- lancer de dés
	compt integer;					-- fourre tout
	temp_bonus integer;			-- comptage temp
	amel_pv integer;				-- nb de PV ajoutés
	v_pv_cible integer;
	nb_sort_tour integer;
        nb_sort_tour1 integer;
begin
-------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
	num_sort := 23;
-- les px
	px_gagne := 0;
-------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------	
	select into nom_cible,v_pv_cible perso_nom,perso_pv_max from perso
		where perso_cod = cible;
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
	select into v_pv,v_pv_max perso_pv,perso_pv_max from perso
			where perso_cod = cible;
		select into v_perso_niveau perso_niveau from perso
			where perso_cod = lanceur;
	
 		bonus_esquive := round(v_perso_niveau * 1.5);
       
	if ajoute_bonus(cible, 'ESQ', 2, bonus_esquive) then
		insert into action (act_tact_cod,act_perso1,act_perso2,act_donnee) values (3,lanceur,cible,1.5*ln(v_pv_cible));
	end if;
	-- ajout azaghal le 26/03 : danse de saint guy simule un mode défensif
        if ajoute_bonus(cible, 'DSG', 2, 20) then
		-- on enlève les bonus existants
		insert into action (act_tact_cod,act_perso1,act_perso2,act_donnee) values (3,lanceur,cible,1.5*ln(v_pv_cible));
	end if;
	code_retour := code_retour||'<br>'||nom_cible||' a un bonus de '||trim(to_char(bonus_esquive,'99999'))||' pendant 2 tours. Durant 2 tours il est aussi considéré en mode défensif';
	code_retour := code_retour||'<br>Vous gagnez '||px_gagne||' PX pour cette action.<br>';
	texte_evt := '[attaquant] a lancé '||nom_sort||' sur [cible]';
   insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     	values(nextval('seq_levt_cod'),14,now(),1,lanceur,texte_evt,'O','O',lanceur,cible);
   if (lanceur != cible) then
    insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     	values(nextval('seq_levt_cod'),14,now(),1,cible,texte_evt,'N','O',lanceur,cible);
   end if;

	return code_retour;
end;

$function$

