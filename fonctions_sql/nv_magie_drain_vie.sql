CREATE OR REPLACE FUNCTION public.nv_magie_drain_vie(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function magie_drain_vie : lance le sort drain de vie         */
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
        v_perso_int integer;            -- int du lanceur
        v_voie_magique integer;         -- voie magique du lanceur
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
	cible alias for $2;			-- perso_cod de la cible
	nom_cible text;				-- nom de la cible
	pv_cible integer;				-- pv de la cible
        v_for_cible integer;                    -- force de la cible
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
	num_sort integer;				-- numéro du sort à lancer
	type_lancer alias for $3;	-- type de lancer (memo ou rune)
	cout_pa integer;				-- Cout en PA du sort
	px_gagne numeric;				-- PX gagnes
	v_bonus_toucher integer;	-- bonus toucher
	drain_pv integer;				-- nombre de PV retirés
	pv_lanceur integer;			-- pv du lanceur
	pv_max_lanceur integer;		-- pv max du lanceur
	diff_pv integer;				-- différence de pv
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
	v_pv_cible integer;
        v_bonus_sorcier integer;                        -- bonus du sorcier
begin
-------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
	num_sort := 34;
-- les px
	px_gagne := 0;
-------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------	
	select into nom_cible,v_pv_cible,v_for_cible perso_nom,perso_pv_max,perso_for from perso
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
	
	v_bloque_magie := split_part(magie_commun_txt,';',2);
	select into v_perso_niveau,v_perso_int,v_voie_magique perso_niveau,perso_int,perso_voie_magique from perso
		where perso_cod = lanceur;
	v_bonus_toucher := 3*v_perso_niveau;
        compt := floor(v_perso_int/4);
-- bonus pour le sorcier
 if v_voie_magique = 3 then
         v_for_cible := 50 - v_for_cible; 
             if v_for_cible > v_perso_niveau then
             v_bonus_sorcier := floor(v_for_cible/5);
             else
             v_bonus_sorcier := floor(v_perso_niveau/5);
             end if;
         code_retour := code_retour||'votre pouvoir de sorcier vous permet d''augmenter la puissance du drain de vie.<br>';
      else
        v_bonus_sorcier := 0;
      end if;
	
	if v_bloque_magie = 0 then
------------------------
-- magie non résistée --
------------------------
		drain_pv := lancer_des(2,10);
		code_retour := code_retour||'Votre adversaire n''arrive pas à résister au sort.<br>';
	else
--------------------
-- magie résistée --
--------------------		   
		drain_pv := lancer_des(1,10);
		code_retour := code_retour||'Votre adversaire résiste partiellement au sort.<br>';
	end if;	
        -- ajout du bonus d'int suite au chantier magie 2008
                drain_pv := drain_pv + compt + v_bonus_sorcier;
		des := effectue_degats_perso(cible,drain_pv,lanceur);
	if des != drain_pv then
		code_retour := code_retour||'<br>Les dégats rééls liés à l''initiative sont de '||trim(to_char(des,'999999999'));
		insert into trace (trc_texte) values ('att '||trim(to_char(lanceur,'99999999'))||' cib '||trim(to_char(cible,'99999999'))||' init '||trim(to_char(drain_pv,'99999999'))||' fin '||trim(to_char(des,'99999999')));
	end if;
	drain_pv := des;
select into pv_max_lanceur,pv_lanceur
		perso_pv_max,perso_pv
		from perso where perso_cod = lanceur;
	code_retour := code_retour||'<br>'||nom_cible||' perd '||trim(to_char(drain_pv,'99'))||' points de vie.<br>';
insert into action (act_tact_cod,act_perso1,act_perso2,act_donnee) values (2,lanceur,cible,0.5*ln(v_pv_cible)*drain_pv);
	select into pv_cible perso_pv from perso
		where perso_cod = cible;
	if pv_cible > drain_pv then
	-- pas tuée	
		code_retour := code_retour||nom_cible||' survit à ce sortilège.<br>';	
		update perso set perso_pv = perso_pv - drain_pv
			where perso_cod = cible;
	else
	-- tuée
		code_retour := code_retour||'Vous avez <b>tué</b> '||nom_cible||'<br>';	
		px_gagne := px_gagne + to_number(split_part(tue_perso_final(lanceur,cible),';',1),'999999999999999');
	end if;
	diff_pv := pv_max_lanceur - pv_lanceur;
	if diff_pv > drain_pv then
		diff_pv := drain_pv;
	end if;
	update perso set perso_pv = perso_pv + diff_pv
		where perso_cod = lanceur;
	perform soin_compteur_pvp(lanceur);
	code_retour := code_retour||'Vous avez régénéré '||trim(to_char(diff_pv,'999999'))||' points de vie.<br>';		
	code_retour := code_retour||'<br>Vous gagnez '||trim(to_char(px_gagne,'9999990D99'))||' PX pour cette action.<br>';
	texte_evt := '[attaquant] a lancé '||nom_sort||' sur [cible], ';
   if v_bloque_magie != 0 then
   	texte_evt := texte_evt||'qui a partiellement résisté au sort, ';
   end if;
   texte_evt := texte_evt||' occasionnant '||trim(to_char(drain_pv,'999'))||' dégats';
   insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     	values(nextval('seq_levt_cod'),14,now(),1,lanceur,texte_evt,'O','O',lanceur,cible);
   if (lanceur != cible) then
    insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     	values(nextval('seq_levt_cod'),14,now(),1,cible,texte_evt,'N','O',lanceur,cible);
   end if;

	return code_retour;
end;
$function$

