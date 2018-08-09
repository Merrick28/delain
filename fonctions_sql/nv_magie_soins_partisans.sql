CREATE OR REPLACE FUNCTION public.nv_magie_soins_partisans(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function magie_mercu_groupe : lance le sort                   */
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
	v_karma numeric;
	v_type_perso integer;		-- type, perso ou monstre
	pos_lanceur integer;
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
	v_pos alias for $2;			-- perso_cod de la cible
	nom_cible text;				-- nom de la cible
	pv_cible integer;				-- pv de la cible
	v_pv integer;
	v_pv_max integer;
	nouveau_pv integer;
	amel_pv integer;
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
	num_sort integer;				-- numéro du sort à lancer
	type_lancer alias for $3;	-- type de lancer (memo ou rune)
	cout_pa integer;				-- Cout en PA du sort
	px_gagne text;				-- PX gagnes
	v_bonus_toucher integer;	-- bonus toucher
	drain_pv integer;				-- nombre de PV retirés
	pv_lanceur integer;			-- pv du lanceur
	pv_max_lanceur integer;		-- pv max du lanceur
	diff_pv integer;				-- différence de pv
	v_x integer;
	v_y integer;
	v_etage integer;
	temp_bonus integer;
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
	v_monstre integer;			--numéro du monstre créé
	nb_monstre integer;
	num_monstre integer;
	ligne record;
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
	des integer;					-- lancer de dés
	compt integer;					-- fourre tout
	v_act_numero integer;
	nb_cible integer;
begin
	v_act_numero := nextval('seq_act_numero');
-------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
	num_sort := 50;
-- les px
	px_gagne := 0;
-------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------	
	select into nom_sort sort_nom from sorts
		where sort_cod = num_sort;
	magie_commun_txt := magie_commun_case(lanceur,v_pos,type_lancer,num_sort);
	res_commun := split_part(magie_commun_txt,';',1);
	if res_commun = 0 then
		code_retour := split_part(magie_commun_txt,';',2);
		return code_retour;
	end if;
	code_retour := split_part(magie_commun_txt,';',3);
	px_gagne := split_part(magie_commun_txt,';',4);
	select into v_x,v_y,v_etage
		pos_x,pos_y,pos_etage
		from positions
		where pos_cod = v_pos;
	select into v_karma,v_perso_niveau,v_type_perso perso_kharma,perso_niveau,perso_type_perso from perso where perso_cod = lanceur;
	pos_lanceur := v_pos;

	-- on soigne

	-- on compte les cibles potentielles
	select into nb_cible (count(perso_cod))
		from perso,perso_position
		where perso_actif = 'O'
		and perso_type_perso = v_type_perso
		and perso_kharma * v_karma >= 0
		and ppos_perso_cod = perso_cod
		and ppos_pos_cod = pos_lanceur;

	-- on boucle sur chacun
	for ligne in select perso_cod,perso_nom,perso_pv,perso_pv_max,perso_sex
		from perso,perso_position
		where perso_actif = 'O'
		and perso_type_perso = v_type_perso	-- même type de perso
		and perso_kharma * v_karma >= 0		-- Kharma de même signe
		and ppos_perso_cod = perso_cod
		and ppos_pos_cod = pos_lanceur loop

		-- et on les soigne
		select into v_pv,v_pv_max,nom_cible perso_pv,perso_pv_max,perso_nom from perso
			where perso_cod = ligne.perso_cod;
		compt := 1;
		temp_bonus := 0;	
		if v_perso_niveau > 5 then
			v_perso_niveau := 5;
		end if;	
		while compt <= v_perso_niveau loop
			temp_bonus := temp_bonus + lancer_des(1,3);
			compt := compt + 1;
		end loop;
		nouveau_pv := v_pv + temp_bonus;
		if nouveau_pv > v_pv_max then
			nouveau_pv := v_pv_max;
		end if;
		update perso set perso_pv = nouveau_pv where perso_cod = ligne.perso_cod;
		perform soin_compteur_pvp(ligne.perso_cod);
		amel_pv := nouveau_pv - v_pv;

		-- on inscrit les événements correspondants
		insert into action (act_tact_cod,act_perso1,act_perso2,act_donnee) values (3,lanceur,ligne.perso_cod,(0.5*ln(ligne.perso_pv_max)*amel_pv)/nb_cible);
		code_retour := code_retour||'<br>'||nom_cible||' a regagné '||trim(to_char(amel_pv,'9999'))||' points de vie.<br>';
		texte_evt := '[attaquant] a lancé '||nom_sort||' sur [cible], lui faisant gagner '||trim(to_char(amel_pv,'9999'))||' points de vie.';
		if lanceur = ligne.perso_cod then
			code_retour := code_retour||'Vous êtes maintenant ';
		elsif ligne.perso_sex = 'M' then
			code_retour := code_retour||'Il est maintenant ';
		else 
			code_retour := code_retour||'Elle est maintenant ';
		end if;
		code_retour := code_retour||'<b>'||etat_perso(ligne.perso_cod)||'</b>.<br>';
	   	insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     		values(nextval('seq_levt_cod'),14,now(),1,ligne.perso_cod,texte_evt,'N','O',lanceur,ligne.perso_cod);
	end loop;

	texte_evt := '[attaquant] a lancé '||nom_sort||'.';
	code_retour := code_retour||'<br>Vous gagnez '||px_gagne||' PX pour cette action.<br>';
   insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant)
     	values(nextval('seq_levt_cod'),14,now(),1,lanceur,texte_evt,'O','O',lanceur);
	return code_retour;
end;
$function$

