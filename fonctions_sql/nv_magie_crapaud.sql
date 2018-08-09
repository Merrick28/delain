CREATE OR REPLACE FUNCTION public.nv_magie_crapaud(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function magie_crapaud : lance le sort crapaud              . */
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
	code_retour text;    -- chaine html de sortie
	texte_evt text;      -- texte pour évènements
	nom_sort text;       -- nom du sort

	-------------------------------------------------------------
	-- variables concernant le lanceur et la cible
	-------------------------------------------------------------
	lanceur alias for $1;  -- perso_cod du lanceur
	v_pos_lanceur integer; -- position du lanceur
	cible alias for $2;    -- perso_cod de la cible
	nom_cible text;        -- nom de la cible

	-------------------------------------------------------------
	-- variables concernant le sort
	-------------------------------------------------------------
	num_sort integer;         -- numéro du sort à lancer
	type_lancer alias for $3; -- type de lancer (memo ou rune)
	px_gagne text;            -- PX gagnes

	-------------------------------------------------------------
	-- variables de contrôle
	-------------------------------------------------------------
	magie_commun_txt text;    -- texte pour magie commun
	res_commun integer;       -- partie 1 du commun

begin
	-------------------------------------------------------------
	-- Etape 1 : intialisation des variables
	-------------------------------------------------------------
	-- on renseigne d’abord le numéro du sort
	num_sort := 164;
	-- les px
	px_gagne := 0;

	-------------------------------------------------------------
	-- Etape 2 : contrôles
	-------------------------------------------------------------
	select into nom_cible perso_nom from perso where perso_cod = cible;
	select into nom_sort sort_nom from sorts where sort_cod = num_sort;
	magie_commun_txt := magie_commun(lanceur, cible, type_lancer, num_sort);
	res_commun := split_part(magie_commun_txt, ';', 1);

	if res_commun = 0 then
		code_retour := split_part(magie_commun_txt, ';', 2);
		return code_retour;
	end if;

	code_retour := split_part(magie_commun_txt, ';', 3);
	px_gagne := split_part(magie_commun_txt, ';', 4);

	-- Résolution du sort : on téléporte la cible sur la case du lanceur.
	select into v_pos_lanceur ppos_pos_cod from perso_position where ppos_perso_cod = lanceur;
	update perso_position set ppos_pos_cod = v_pos_lanceur where ppos_perso_cod = cible;

	-- on enlève les locks et les transactions de la cible
	delete from lock_combat where lock_attaquant = cible or lock_cible = cible;

	-- On supprime les transactions en cours
	delete from transaction where tran_vendeur = cible or tran_acheteur =cible;

	code_retour := code_retour || '<br>' || nom_cible || ' est attiré(e) sur sa case !';
	code_retour := code_retour || '<br>Vous gagnez ' || px_gagne || ' PX pour cette action.<br>';
	texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible].';
   
	perform insere_evenement(lanceur, cible, 14, texte_evt, 'O', '[sort_cod]=' || num_sort::text);

	return code_retour;
end;
$function$

