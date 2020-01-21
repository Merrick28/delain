--
-- Name: nv_magie_effacement_runique(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION public.nv_magie_effacement_runique(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*****************************************************************/
/* function dissipation runique : Effacement runique             */
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
	code_retour text;			-- chaine html de sortie
	texte_evt text;				-- texte pour évènements
	nom_sort text;				-- nom du sort
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
	px_gagne text;				-- PX gagnes

-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
	magie_commun_txt text;		-- texte pour magie commun
	res_commun integer;			-- partie 1 du commun
	v_voie_magique integer;         --  voie magique du lanceur
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
	des integer;					-- lancer de dés
	compt integer;					-- fourre tout
	v_pv_cible integer;
	v_bonus_tour integer;
	v_bonus_valeur integer;
	v_nombre_supp_temp integer;
	v_nombre_supp_tot integer;
begin
-------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort
	num_sort := 167;
-- les px
	px_gagne := 0;
-------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------
	select into nom_cible, v_pv_cible perso_nom, perso_pv_max from perso
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
	px_gagne := split_part(magie_commun_txt, ';', 4);

	-- effet de la voie magique
	if v_voie_magique = 5 then
		select into v_bonus_tour, v_bonus_valeur bonus_nb_tours, bonus_valeur from bonus
		where bonus_perso_cod = lanceur
			and bonus_valeur > 0
			and bonus_tbonus_libc = 'ARM';
		if not found then
			if ajoute_bonus(cible, 'ARM', 2, 3) != 0 then
				insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee) values (3, lanceur, cible, 0.10 * ln(v_pv_cible));
			end if;
		end if;
	end if;

	-- Suppression des effets (en deux temps)
	delete from bonus
	where bonus_perso_cod = cible
		and bonus_valeur < 0
		and bonus_mode != 'E' -- sauf equipement
		and bonus_tbonus_libc IN
			(select tbonus_libc from bonus_type
			where tbonus_nettoyable = 'O' and tbonus_gentil_positif = 't');
	GET DIAGNOSTICS v_nombre_supp_tot = ROW_COUNT;
	delete from bonus
	where bonus_perso_cod = cible
		and bonus_valeur > 0
		and bonus_mode != 'E' -- sauf equipement
		and bonus_tbonus_libc IN
			(select tbonus_libc from bonus_type
			where tbonus_nettoyable = 'O' and tbonus_gentil_positif = 'f');
	GET DIAGNOSTICS v_nombre_supp_temp = ROW_COUNT;
	v_nombre_supp_tot := v_nombre_supp_tot + v_nombre_supp_temp;

	insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee) values (3, lanceur, cible, 0.10 * ln(v_pv_cible) * v_nombre_supp_tot);

	code_retour := code_retour || '<br>Vous supprimez tous les effets néfastes qui affectent la cible, lui laissant uniquement les enchantements bénéfiques.<br>';
	code_retour := code_retour || '<br>' || v_nombre_supp_tot::text || ' effets supprimés.<br>';
	code_retour := code_retour || '<br>Vous gagnez ' || px_gagne || ' PX pour cette action.<br>';

	texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible] ';
	perform insere_evenement(lanceur, cible, 14, texte_evt, 'O', '[sort_cod]=' || num_sort::text);

	return code_retour;
end;$_$;


ALTER FUNCTION public.nv_magie_effacement_runique(integer, integer, integer) OWNER TO delain;

--
-- Name: FUNCTION nv_magie_effacement_runique(integer, integer, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION public.nv_magie_effacement_runique(integer, integer, integer) IS 'Lance la fonction Dissipation Runique';
