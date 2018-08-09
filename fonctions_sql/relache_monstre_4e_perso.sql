CREATE OR REPLACE FUNCTION public.relache_monstre_4e_perso(integer)
 RETURNS character varying
 LANGUAGE plpgsql
AS $function$/********************************************************/
/* function relache_monstre_4e_perso                          */
/* Relâche un monstre précédemment attribué à un compte en    */
/* guise de 4e perso.                                         */
/* parametres :                                               */
/*  $1 = perso_cod du monstre à relâcher                      */
/* Sortie :                                                   */
/*  code_retour = texte à afficher                            */
/**************************************************************/
/**************************************************************/
/* Création - 24/09/2012 - Reivax                             */
/**************************************************************/
declare
	code_perso alias for $1;       -- perso_cod
	v_trop_recent smallint;        -- vaut 1 si le monstre a été attribué trop récemment
	v_actif varchar(1);            -- Type choisi pour le 4e perso
	retour varchar(200);           -- code retour contenant le texte à afficher
begin
/*********************************************************/
/*        I N I T I A L I S A T I O N S                  */
/*********************************************************/
	retour := 'Le monstre n’est pas supprimé, mais son contrôle vous en est retiré.'; --par défaut

/*********************************************************/
/*                  C O N T R O L E S                    */
/*********************************************************/
	-- controle sur la possibilité d’ajouter un 4e perso
	select into v_trop_recent, v_actif
		case when (now() - pcompt_date_attachement) < '1 day'::interval then 1 else 0 end, perso_actif
	from perso
	inner join perso_compte on pcompt_perso_cod = perso_cod
	where perso_cod = code_perso;
	if not found then
		return 'Erreur ! Le monstre n’a pas été trouvé.';
	end if;
	if v_trop_recent = 1 AND v_actif = 'O' then
		return 'Ce monstre vous a été affecté trop récemment, gardez-le au moins 24 heures !';
	end if;

/*********************************************************/
/*             E X É C U T I O N                         */
/*********************************************************/
	-- Suppression du monstre du compte du joueur
	DELETE FROM perso_compte WHERE pcompt_perso_cod = code_perso;
	
	-- Réactivation de l’IA
	UPDATE perso SET perso_dirige_admin = 'N' WHERE perso_cod = code_perso;

	return retour;
end;		$function$

CREATE OR REPLACE FUNCTION public.relache_monstre_4e_perso(integer, smallint)
 RETURNS character varying
 LANGUAGE plpgsql
AS $function$/**************************************************************/
/* function relache_monstre_4e_perso                          */
/* Relâche un monstre précédemment attribué à un compte en    */
/* guise de 4e perso.                                         */
/* parametres :                                               */
/*  $1 = perso_cod du monstre à relâcher                      */
/*  $2 = La raison du lâcher                                  */
/*   0 = temps limite, 1 = mort, 2 = autre (volontairement    */
/*                                 ou par changement d’étage) */
/* Sortie :                                                   */
/*  code_retour = texte à afficher                            */
/**************************************************************/
/**************************************************************/
/* Création - 24/09/2012 - Reivax                             */
/* Modif    - 13/12/2012 - Reivax : rajout du 2nd paramètre   */
/**************************************************************/
declare
	code_perso alias for $1;       -- perso_cod
	code_raison alias for $2;      -- Le code de raison du lâcher du monstre
	v_actif varchar(1);            -- Actif ou non ?
	v_pcompt integer;              -- Code de l’association monstre / compte
	v_compt integer;               -- Code du compte possédant le monstre
	ligne record;                  -- données de personnage
	ligne_orig record;             -- données d’origine de personnage
	retour varchar(200);           -- code retour contenant le texte à afficher
begin
/*********************************************************/
/*        I N I T I A L I S A T I O N S                  */
/*********************************************************/
	retour := 'Le monstre n’est pas supprimé, mais son contrôle vous en est retiré.'; --par défaut

/*********************************************************/
/*                  C O N T R O L E S                    */
/*********************************************************/
	-- controle sur la possibilité d’ajouter un 4e perso
	select into v_actif, v_pcompt, v_compt
		perso_actif, pcompt_cod, pcompt_compt_cod
	from perso
	inner join perso_compte on pcompt_perso_cod = perso_cod
	where perso_cod = code_perso;
	if not found then
		return 'Erreur ! Le monstre n’a pas été trouvé.';
	end if;

/*********************************************************/
/*             E X É C U T I O N                         */
/*********************************************************/
	-- Suppression du monstre du compte du joueur
	DELETE FROM perso_compte WHERE pcompt_cod = v_pcompt;
	
	-- Réactivation de l’IA, si monstre actif
	if v_actif <> 'N' then
		UPDATE perso SET perso_dirige_admin = 'N' WHERE perso_cod = code_perso;
	end if;
	
	-- Sauvegarde des données du monstre
	select into ligne * from perso where perso_cod = code_perso;
	select into ligne_orig * from perso_compte_monstre where pcm_pcompt_cod = v_pcompt;
	insert into compte_monstre_historique(cmon_compt_cod, cmon_nom, cmon_gmon_cod, cmon_renommee, cmon_renommee_magique,
		cmon_karma, cmon_kill_perso, cmon_kill_monstres, cmon_px, cmon_description, cmon_fin)
	values (v_compt, ligne.perso_nom, ligne.perso_gmon_cod,
		ligne.perso_renommee - ligne_orig.pcm_renommee,
		ligne.perso_renommee_magie - ligne_orig.pcm_renommee_magie,
		ligne.perso_kharma - ligne_orig.pcm_karma,
		ligne.perso_nb_joueur_tue,
		ligne.perso_nb_monstre_tue,
		ligne.perso_px - ligne_orig.pcm_px,
		case when ligne_orig.pcm_description <> ligne.perso_description then ligne.perso_description else NULL end,
		code_raison);

	return retour;
end;		$function$

