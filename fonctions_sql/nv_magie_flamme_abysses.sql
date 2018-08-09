CREATE OR REPLACE FUNCTION public.nv_magie_flamme_abysses(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function magie_chasse_joueur : lance le sort chasse joueur    */
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
	-------------------------------------------------------------
	-- variables concernant le sort
	-------------------------------------------------------------
	num_sort integer;				-- numéro du sort à lancer
	type_lancer alias for $3;	-- type de lancer (memo ou rune)
	cout_pa integer;				-- Cout en PA du sort
	px_gagne numeric;				-- PX gagnes
	ligne record;					-- enregistrements
	pos_lanceur integer;			-- pos_cod du lanceur
	x_lanceur integer;			-- x du lanceur
	y_lanceur integer;			-- y du lanceur
	e_lanceur integer;			-- etage du lanceur
	v_degats integer;				-- dégâts effectués
	reussite_esquive integer;	-- réussite on non de l’esquive
	-------------------------------------------------------------
	-- variables de contrôle
	-------------------------------------------------------------
	magie_commun_txt text;		-- texte pour magie commun
	res_commun integer;			-- partie 1 du commun
	distance_cibles integer;	-- distance entre lanceur et cible
	ligne_rune record;			-- record des rune à dropper
	temp_ameliore_competence text;	-- chaine temporaire pour amélioration
	-------------------------------------------------------------
	-- variables de calcul
	-------------------------------------------------------------
	des integer;					-- lancer de dés
	compt integer;					-- fourre tout
	v_act_numero integer;
begin
	-------------------------------------------------------------
	-- Etape 1 : intialisation des variables
	-------------------------------------------------------------
	-- on renseigne d abord le numéro du sort
	num_sort := 100;
	-- les px
	px_gagne := 0;
	-------------------------------------------------------------
	-- Etape 2 : contrôles
	-------------------------------------------------------------
	v_act_numero := nextval('seq_act_numero');
	select into nom_cible perso_nom from perso
	where perso_cod = cible;

	select into nom_sort sort_nom from sorts
	where sort_cod = num_sort;
	magie_commun_txt := magie_commun(lanceur, cible, type_lancer, num_sort);
	res_commun := split_part(magie_commun_txt, ';', 1);
	if res_commun = 0 then
		code_retour := split_part(magie_commun_txt, ';', 2);
		return code_retour;
	end if;
	code_retour := split_part(magie_commun_txt, ';', 3);
	px_gagne := to_number(split_part(magie_commun_txt, ';', 4), '99999999999999D99');

	-- a partir d’ici on s’amuse
	-- on prend la position
	select into pos_lanceur, x_lanceur, y_lanceur, e_lanceur
		pos_cod, pos_x, pos_y, pos_etage
	from positions, perso_position
	where ppos_perso_cod = lanceur
		and ppos_pos_cod = pos_cod;

	-- on commence par les gens sur la même case
	for ligne in select perso_cod, perso_nom, perso_pv
		from perso, perso_position
		where perso_actif = 'O'
			and perso_type_perso = 1
			and perso_tangible = 'O'
			and ppos_perso_cod = perso_cod
			and perso_cod != lanceur
			and ppos_pos_cod = pos_lanceur
	loop
		texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible]';
		v_degats := lancer_des(1, 10);

		-- on regarde si esquive
		code_retour := code_retour || 'Sur <b>' || ligne.perso_nom || '</b>, vous provoquez ' || trim(to_char(v_degats, '99999')) || ' dégâts';
		reussite_esquive := f_esquive(1, ligne.perso_cod, 1);
		if reussite_esquive != 0 then
			code_retour := code_retour || ' que votre adversaire arrive à esquiver.<br><br>';
			texte_evt := texte_evt || ' qui a esquivé les dégâts';

			perform insere_evenement(lanceur, ligne.perso_cod, 14, texte_evt, 'O', '[sort_cod]=' || num_sort::text);
			insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee, act_numero) values (2, lanceur, ligne.perso_cod, 1, v_act_numero);
		else
			texte_evt := texte_evt || ' causant ' || trim(to_char(v_degats, '999999')) || ' dégâts';
			code_retour := code_retour || ' que votre adversaire n’arrive pas à esquiver.<br>';

			perform insere_evenement(lanceur, ligne.perso_cod, 14, texte_evt, 'O', '[sort_cod]=' || num_sort::text);
			insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee, act_numero) values (2, lanceur, ligne.perso_cod, v_degats, v_act_numero);

			if ligne.perso_pv <= v_degats then
				-- on a tué l’adversaire !!
				px_gagne := px_gagne + to_number(split_part(tue_perso_final(lanceur, ligne.perso_cod), ';', 1), '9999999999999');
				code_retour := code_retour || 'Vous avez <b>tué</b> ' || ligne.perso_nom || '<br><br>';
			else
				code_retour := code_retour || ligne.perso_nom || ' a survécu à votre attaque<br><br>';
				update perso set perso_pv = perso_pv - v_degats where perso_cod = ligne.perso_cod;
			end if;
		end if;
	end loop;

	-- ensuite on fait les gens à 1 de distance
	for ligne in select perso_cod, perso_nom, perso_pv
		from perso, perso_position, positions
		where perso_actif = 'O'
			and perso_type_perso = 1
			and ppos_perso_cod = perso_cod
			and ppos_pos_cod = pos_cod
			and perso_tangible = 'O'
			and pos_cod != pos_lanceur
			and pos_x between (x_lanceur - 1) and (x_lanceur + 1)
			and pos_y between (y_lanceur - 1) and (y_lanceur + 1)
			and not exists
				(select 1 from lieu_position, lieu
				where lpos_pos_cod = pos_cod
				and lieu_refuge = 'O')
			and pos_etage = e_lanceur
	loop
		texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible]';
		v_degats := lancer_des(10, 10)+10;
		-- on regarde si esquive
		code_retour := code_retour || 'Sur <b>' || ligne.perso_nom || '</b>, vous provoquez ' || trim(to_char(v_degats, '99999')) || ' dégâts';
		reussite_esquive := f_esquive(1, ligne.perso_cod, 1);
		if reussite_esquive != 0 then
			code_retour := code_retour || ' que votre adversaire arrive à esquiver.<br><br>';
			texte_evt := texte_evt || ' qui a esquivé les dégâts';

			perform insere_evenement(lanceur, ligne.perso_cod, 14, texte_evt, 'O', '[sort_cod]=' || num_sort::text);
			insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee) values (2, lanceur, ligne.perso_cod, 1);
		else
			texte_evt := texte_evt || ' causant ' || trim(to_char(v_degats, '999999')) || ' dégâts';
			perform insere_evenement(lanceur, ligne.perso_cod, 14, texte_evt, 'O', '[sort_cod]=' || num_sort::text);
			insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee) values (2, lanceur, ligne.perso_cod, v_degats);
		end if;

		code_retour := code_retour || ' que votre adversaire n’arrive pas à esquiver.<br>';

		if ligne.perso_pv <= v_degats then
		-- on a tué l’adversaire !!
			px_gagne := px_gagne + to_number(split_part(tue_perso_final(lanceur, ligne.perso_cod), ';', 1), '9999999999999');
			code_retour := code_retour || 'Vous avez <b>tué</b> ' || ligne.perso_nom || '<br><br>';
		else
			code_retour := code_retour || ligne.perso_nom || ' a survécu à votre attaque<br><br>';
			update perso set perso_pv = perso_pv - v_degats where perso_cod = ligne.perso_cod;
		end if;
	end loop;
	code_retour := code_retour || '<br>Vous gagnez ' || trim(to_char(px_gagne, '9999990D99')) || ' PX pour cette action.<br>';
	return code_retour;
end;$function$

