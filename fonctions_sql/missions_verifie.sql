CREATE OR REPLACE FUNCTION public.missions_verifie(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction mission_verifie                                  */
/*   Vérifie les conditions de validation des missions du    */
/*          perso                                            */
/*   on passe en paramètres :                                */
/*   $1 = perso_cod                                          */
/* on a en sortie une chaine à afficher                      */
/*************************************************************/
/* Créé le 28/01/2013                                        */
/*************************************************************/
declare
	code_retour text;        -- Le résultat, affichable, de la fonction
	personnage alias for $1; -- Le personnage pour lequel on valide les missions en cours
	ligne record;            -- Contient les données des missions en cours
	requete_validation text; -- Contient le texte de la requête à exécuter pour valider les missions en cours
	temp_resultat text;      -- Variable tampon pour le résultat
begin
	code_retour := ''; -- Par défaut, aucun retour

	-- Boucle sur les missions en cours
	for ligne in select mpf_cod, miss_fonction_valide from mission_perso_faction_lieu
		inner join missions on miss_cod = mpf_miss_cod
		where mpf_perso_cod = personnage and (mpf_statut / 10) = 1
	loop
		if ligne.miss_fonction_valide <> '' then
			requete_validation := 'select ' || ligne.miss_fonction_valide || '(' || ligne.mpf_cod::text || ') as validation';
			execute requete_validation into temp_resultat;
			code_retour := code_retour || temp_resultat;
		end if;
	end loop;
	return code_retour;
end;$function$

