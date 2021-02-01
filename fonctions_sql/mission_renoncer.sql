
--
-- Name: mission_renoncer(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION public.mission_renoncer(integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*************************************************************/
/* fonction mission_renoncer                                  */
/*   rennoncer à une mission et en perdre la renommé         */
/*   on passe en paramètres :                                */
/*   $1 = perso_cod                                          */
/*   $2 = fac_cod                                            */
/* on a en sortie une chaine à afficher                      */
/*************************************************************/
/* Créé le 28/01/2021                                        */
/*************************************************************/
declare
	personnage alias for $1; -- Le personnage pour lequel on renonce a la missions en cours
  v_fac_cod alias for $2;    -- faction de la mission

	v_mission record;             -- Les infos de la mission
	v_avancement integer;         -- Le %age de réussite de la mission.
	resultat text;                -- Le retour de la fonction
	v_gain_points integer;        -- Le gain en points pour la faction
begin
	resultat := '';
	-- On récupère les informations de la mission
	select into v_mission * from mission_perso_faction_lieu	where mpf_perso_cod = personnage and mpf_fac_cod=v_fac_cod and mpf_statut <20 limit 1;

  if not found then
    return resultat || 'Erreur ! Aucune mission à renoncer pour cette faction !';
  end if;

	v_gain_points := 2 * max(1, v_mission.mpf_recompense / 500);

	-- Mise à jour de la punition
	update faction_perso set pfac_points = GREATEST(0, pfac_points - v_gain_points)  where pfac_perso_cod = v_mission.mpf_perso_cod AND pfac_fac_cod = v_mission.mpf_fac_cod;

	-- Mise à jour du statut
	perform mission_modifie_statut(v_mission.mpf_cod, 50, 0);
	resultat := resultat || 'Nous sommes extrêmement déçus ! Ce n’était pourtant pas si compliqué que ça... Il vous faudra faire mieux si vous souhaitez continuer de traiter avec nous.<br />';
	resultat := resultat || 'Vous perdez <b>' || v_gain_points::text || '</b> points de réputation envers notre faction.<br />';


	return resultat;
end;$_$;


ALTER FUNCTION public.mission_renoncer(integer, integer) OWNER TO delain;

--
-- Name: FUNCTION mission_renoncer(integer, integer); Type: COMMENT; Schema: public; Owner: delain
--
