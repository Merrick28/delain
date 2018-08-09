CREATE OR REPLACE FUNCTION public.nv_magie_projectile_magique(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function magie_projectile_magique : lance le sort projectile  */
/*  magique                                                      */
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
/*   01/03/2018 : correction bug affichage des px reçu (Marlyza) */
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
        v_voie_magique integer;         -- voie magique du lanceur
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
	cible alias for $2;			-- perso_cod de la cible
	nom_cible text;				-- nom de la cible
	total_degats integer;		-- degats occasionnés
	has_bloque integer; 			-- compétence blocage ?
	pv_cible integer;				-- pv de la cible
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
	num_sort integer;				-- numéro du sort à lancer
	type_lancer alias for $3;	-- type de lancer (memo ou rune)
	cout_pa integer;				-- Cout en PA du sort
	px_gagne numeric;				-- PX gagnes
	duree integer;					-- Durée du sort
	temp_nb_proj numeric;		-- nb temp de projectiles
	nb_proj	integer;				-- nb de projectiles
	proj_bloque integer;			-- projectile bloqué ?
	degats_proj integer;			-- degats du projectile
	niveau_sort integer;			-- niveau du sort
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
	magie_commun_txt text;		-- texte pour magie commun
	res_commun integer;			-- partie 1 du commun
	distance_cibles integer;	-- distance entre lanceur et cible
	ligne_rune record;			-- record des rune à dropper
	temp_ameliore_competence text;
										-- chaine temporaire pour amélioration
	v_bloque_magie integer;		-- vérif si bloque magique
	
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
	des integer;					-- lancer de dés
	compt integer;					-- fourre tout
	v_reussite integer;	
	v_pv_cible integer;
	texte_mort text;
        maximum integer;
        bonus_degats integer;
begin
-------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
	num_sort := 13;
-- les px
	px_gagne := 0;
-------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------	
	select into niveau_sort,nom_sort sort_niveau,sort_nom from sorts where sort_cod = num_sort;
	select into nom_cible,v_pv_cible perso_nom,perso_pv_max from perso
		where perso_cod = cible;
	magie_commun_txt := magie_commun(lanceur,cible,type_lancer,num_sort);
	res_commun := split_part(magie_commun_txt,';',1);
	if res_commun = 0 then
		code_retour := split_part(magie_commun_txt,';',2);
		return code_retour;
	end if;
	code_retour := split_part(magie_commun_txt,';',3);
	px_gagne := split_part(magie_commun_txt,';',4);
	v_reussite := to_number(split_part(magie_commun_txt,';',5),'99999999999999D99');
-- a partir d ici on va rigoler deux minutes.....
-- il faut calculer le nombre de projectiles et faire un affichage "standard"	
	select into v_perso_niveau,v_voie_magique perso_niveau,perso_voie_magique from perso
		where perso_cod = lanceur;
	temp_nb_proj = v_perso_niveau / 4;
	nb_proj = floor(temp_nb_proj);
	if nb_proj > 4 then
		nb_proj := 4;
	end if;
        bonus_degats := 0;
        if v_voie_magique = 6 then
        maximum := floor(v_perso_niveau / 30);
        bonus_degats := maximum;
        maximum := maximum + 4;
        temp_nb_proj = v_perso_niveau / 4;
	nb_proj = floor(temp_nb_proj);
	   if nb_proj > maximum then
		nb_proj := maximum;
	   end if;
        end if;
	code_retour := code_retour||'<br>Vous lancez <b>'||trim(to_char(nb_proj,'99'))||'</b> projectile(s) sur '||nom_cible||'<br>';
	total_degats := 0;
	for num_proj in 1..nb_proj loop
		proj_bloque := 0;
		degats_proj := lancer_des(1,5);
                degats_proj := degats_proj + bonus_degats;
		
		des := effectue_degats_perso(cible,degats_proj,lanceur);
		if des != degats_proj then
			code_retour := code_retour||'<br>Les dégats rééls liés à l''initiative sont de '||trim(to_char(des,'999999999')) || '.<br />';
			insert into trace (trc_texte) values ('att '||trim(to_char(lanceur,'99999999'))||' cib '||trim(to_char(cible,'99999999'))||' init '||trim(to_char(degats_proj,'99999999'))||' fin '||trim(to_char(des,'99999999')));
		end if;
		degats_proj := des;
		
		code_retour := code_retour||'Le projectile n°'||trim(to_char(num_proj,'999'))||' fait <b>'||trim(to_char(degats_proj,'99'))||'</b> dégats ';
	-- on regarde si bloque magie
		select into has_bloque
			pcomp_modificateur
			from perso_competences
			where pcomp_perso_cod = cible
			and pcomp_pcomp_cod = 27;
		if has_bloque is not null then
			v_bloque_magie := bloque_magie(cible,niveau_sort,v_reussite);
			if v_bloque_magie != 0 then
				code_retour := code_retour||' que votre adversaire réussit à bloquer.<br>';
				proj_bloque := 1;
			end if;
		end if;
		if proj_bloque = 0 then -- projectile non bloqué
			if resiste_magie(cible,lanceur,niveau_sort) = 0 then
				-- magie non résistée
				total_degats := total_degats + degats_proj;
				code_retour := code_retour||'que votre adversaire encaisse de plein fouet.<br>';
			else
				-- magie résistée
				degats_proj := ceil(degats_proj/2);
				total_degats := total_degats + degats_proj;
				code_retour := code_retour||'que votre adversaire arrive à repousser partiellement. Il ne subit que '||trim(to_char(degats_proj,'99'))||' dégats.<br>';
			end if;
		end if;
	end loop;
-- maintenant on regarde les PV de la cible
	select into pv_cible perso_pv from perso
		where perso_cod = cible;
	code_retour := code_retour||'<hr>';
	code_retour := code_retour||'<p>'||nom_cible||' subit un total de <b>'||trim(to_char(total_degats,'9999'))||'</b> dégats.<br>';
	texte_evt := '[attaquant] a lancé '||nom_sort||' sur [cible], effectuant '||trim(to_char(total_degats,'999'))||' dégats.';
	insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
  		values(nextval('seq_levt_cod'),14,now(),1,lanceur,texte_evt,'O','O',lanceur,cible);
   if (lanceur != cible) then
    insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     	values(nextval('seq_levt_cod'),14,now(),1,cible,texte_evt,'N','O',lanceur,cible);
   end if;
			
insert into action (act_tact_cod,act_perso1,act_perso2,act_donnee) values (2,lanceur,cible,0.25*total_degats*ln(v_pv_cible));
if pv_cible > total_degats then
		-- pas mort
		update perso set perso_pv = perso_pv - total_degats where perso_cod = cible;
		code_retour := code_retour||'<p>Votre adversaire a survécu à cette attaque. Vous gagnez '||trim(to_char(px_gagne,'9999990D99'))||' PX pour cette action.';
	else
		-- on appelle la fonction qui gère la mort
		texte_mort := tue_perso_final(lanceur,cible);
		-- 
		px_gagne := px_gagne + to_number(split_part(texte_mort,';',1),'999999999');
		code_retour := code_retour||'<p>Vous avez tué votre adversaire.<br>';
		code_retour := code_retour||split_part(texte_mort,';',2);
		code_retour := code_retour||'<hr><br>Vous gagnez '||trim(to_char(px_gagne,'9999990D99'))||' PX pour cette action.<br>';	end if;
	return code_retour;
end;
$function$

