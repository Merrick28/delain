CREATE OR REPLACE FUNCTION public.mission_attribue(integer, integer)
 RETURNS void
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction mission_attribue                                 */
/*   Attribue une mission à un personnage                    */
/*   on passe en paramètres :                                */
/*   $1 = perso_cod : le perso qui prend la mission          */
/*   $2 = mpf_cod : l’identifiant de la mission              */
/* on a en sortie une chaine à afficher                      */
/*************************************************************/
/* Créé le 22/05/2013                                        */
/*************************************************************/
declare
	v_perso alias for $1;      -- Le résultat, affichable, de la fonction
	code_mission alias for $2; -- Le code de la mission à valider
	faction_cod integer;       -- Le code de la faction concernée
	v_temp integer;            -- variable temporaire
begin
	update mission_perso_faction_lieu
	set mpf_perso_cod = v_perso,
		mpf_date_debut = now(),
		mpf_date_fin = now() + (mpf_delai::text || ' days')::interval,
		mpf_statut = 10
	where mpf_cod = code_mission;

	-- Recherche de la relation entre le perso et la faction
	select into faction_cod mpf_fac_cod from mission_perso_faction_lieu where mpf_cod = code_mission;
	select into v_temp pfac_fac_cod from faction_perso where pfac_perso_cod = v_perso and pfac_fac_cod = faction_cod;
	if not found then
		insert into faction_perso (pfac_fac_cod, pfac_perso_cod, pfac_points, pfac_date_mission, pfac_rang_numero)
		values (faction_cod, v_perso, 0, now(), 0);
	else
		update faction_perso set pfac_date_mission = now() where pfac_fac_cod = faction_cod AND pfac_perso_cod = v_perso;
	end if;
end;$function$

