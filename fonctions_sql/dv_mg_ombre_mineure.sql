CREATE OR REPLACE FUNCTION public.dv_mg_ombre_mineure(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function dv_mg_aimant : lance le sort chance                  */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = cible                                                  */
/*   $3 = type lancer                                            */
/*        doit être fixé à 3                                     */
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
	v_niveau_lanceur integer;
	v_pv_lanceur integer;
	v_pv_max_lanceur integer;
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
	cible alias for $2;			-- perso_cod de la cible
	nom_cible text;				-- nom de la cible
	v_pv_cible integer;
	v_pos_cible integer;
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
	num_sort integer;				-- numéro du sort à lancer
	type_lancer alias for $3;	-- type de lancer (memo ou rune)
	cout_pa integer;				-- Cout en PA du sort
	px_gagne integer;				-- PX gagnes
	v_bonus integer;
	temp_ia text;
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
	magie_commun_txt text;		-- texte pour magie commun
	res_commun integer;			-- partie 1 du commun
	distance_cibles integer;	-- distance entre lanceur et cible
	ligne record;			-- record des rune à dropper
	temp_ameliore_competence text;
										-- chaine temporaire pour amélioration
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
	des integer;					-- lancer de dés
	compt integer;					-- fourre tout
	v_x integer;
	v_y integer;
	v_e integer;
	cpt_brouzoufs integer;
	tmp_brouzoufs integer;
	des_objet integer;
	v_gobj_cod integer;
	nom_obj text;
	nb_des_deg integer;
	val_des_deg integer;
	v_degats integer;
	v_monstre integer;
begin
-------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
	num_sort := 85;
-- les px
	px_gagne := 0;
-------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------	
	select into nom_cible perso_nom from perso
		where perso_cod = cible;
	select into nom_sort sort_nom from sorts
		where sort_cod = num_sort;
	magie_commun_txt := magie_commun_dieu(lanceur,cible,num_sort);
	res_commun := split_part(magie_commun_txt,';',1);
	if res_commun = 0 then
		code_retour := split_part(magie_commun_txt,';',2);
		return code_retour;
	end if;
	code_retour := split_part(magie_commun_txt,';',3);
	select into v_pos_cible ppos_pos_cod
		from perso_position
		where ppos_perso_cod = cible;
	v_monstre := cree_monstre_pos(206,v_pos_cible);
	update perso set perso_cible = cible,perso_dfin = now() + '1 day'::interval where perso_cod = v_monstre;
	temp_ia := ia_monstre(v_monstre);
	insert into action (act_tact_cod,act_perso1,act_perso2,act_donnee) values (2,lanceur,cible,2);
	code_retour := code_retour||'Vous avez invoqué une ombre mineure qui attaque votre cible.<br>';
	code_retour := code_retour||'<br>Vous gagnez '||trim(to_char(px_gagne,'999'))||' PX pour cette action.<br>';
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

