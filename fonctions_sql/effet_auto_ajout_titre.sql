CREATE OR REPLACE FUNCTION public.effet_auto_ajout_titre(integer, text, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction effet_auto_ajout_titre                           */
/*   Ajoute automatiquement un titre au personnage donné     */
/*   on passe en paramètres :                                */
/*   $1 = perso_cod : le perso_cod à qui ajouter le titre    */
/*   $2 = texte     : le texte du titre à ajouter            */
/*   $3 = source    : le code du monstre dont émane le titre */
/* on a en sortie un texte résumé                            */
/*************************************************************/
/* Créé le 17/06/2014                                        */
/*************************************************************/
declare
	v_perso_cod alias for $1;  -- Le code de la source
	v_texte alias for $2;      -- Le texte du titre
	v_source alias for $3;     -- Le code du monstre ayant généré le titre

	code_retour text;          -- Le retour de la fonction
	nom_monstre text;          -- Le nom du monstre qui déclenche le titre

begin
	code_retour := '';
	select into nom_monstre perso_nom from perso where perso_cod = v_source;

	insert into perso_titre (ptitre_perso_cod, ptitre_titre, ptitre_date)
	values (
		v_perso_cod,
		replace (v_texte, '[monstre]', nom_monstre),
		now()
	);

	return 'Nouveau titre ! ' || replace (v_texte, '[monstre]', nom_monstre);
end;$function$

