CREATE OR REPLACE FUNCTION public.nv_magie_distortion(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function lancement : Distorsion                               */
/*  temporelle                                                   */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = cible                                                  */
/*   $3 = type lancer                                            */
/*        0 = rune                                               */
/*        1 = mémo                                               */
/* Le code sortie est une chaine html utilisable directement     */
/*****************************************************************/
/* Créé le 22/09/2008                                            */
/* Liste des modifications :                                     */
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
	v_perso_int integer;            -- int du lanceur
        v_voie_magique integer;         -- voie magique du lanceur
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
	cible alias for $2;			-- perso_cod de la cible
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
	num_sort integer;		-- numéro du sort à lancer
	type_lancer alias for $3;	-- type de lancer (memo ou rune)
	px_gagne numeric;		-- PX gagnes
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
	magie_commun_txt text;		-- texte pour magie commun
	res_commun integer;		-- partie 1 du commun
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
	duree_distortion integer ;
	bonus_voie integer;

begin
	-------------------------------------------------------------
	-- Etape 1 : intialisation des variables
	-------------------------------------------------------------
	-- on renseigne d abord le numéro du sort
	num_sort := 146;
	-- les px
	px_gagne := 0;

	-------------------------------------------------------------
	-- Etape 2 : contrôles
	-------------------------------------------------------------
	select into nom_sort sort_nom from sorts where sort_cod = num_sort;

	select into v_perso_int, v_voie_magique perso_int, perso_voie_magique from perso
	where perso_cod = lanceur;

	magie_commun_txt := magie_commun(lanceur, cible, type_lancer, num_sort);
	res_commun := split_part(magie_commun_txt, ';', 1);
	if res_commun = 0 then
		code_retour := split_part(magie_commun_txt, ';', 2);
		return code_retour;
	end if;
	code_retour := split_part(magie_commun_txt, ';', 3);

	px_gagne := to_number(split_part(magie_commun_txt, ';', 4), '99999999999999D99');

	-- On détermine la durée de la distorsion et on ajoute le bonus de la voie magique
	if v_voie_magique = 2 then
		bonus_voie := 20;
	else
		bonus_voie :=0;
	end if;

	duree_distortion := 0;
	duree_distortion := min((v_perso_int * 6) + bonus_voie, 170);

	-- on met à jour la table bonus pour le lanceur et la cible
	perform ajoute_bonus(cible, 'DIS', 3, duree_distortion);
	perform ajoute_bonus(lanceur, 'DIT', 3, duree_distortion);

	-- on genere du texte
	code_retour := code_retour || '<br>Vous êtes maintenant sous l’effet d’une distorsion temporelle et ne pourrez donc plus être affecté par un autre sortilège de distorsion temporelle.<br>';
	code_retour := code_retour || '<br>Vous gagnez ' || px_gagne || ' PX pour cette action.<br>';

	-- on met les événements à jour
	texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible] ';
	perform insere_evenement(lanceur, cible, 14, texte_evt, 'O', '[sort_cod]=' || num_sort::text);

	return code_retour;
end;$function$

