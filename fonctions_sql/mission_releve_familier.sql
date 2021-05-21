--
-- Name: mission_releve_familier(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION public.mission_releve_familier(integer, integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $_$/*************************************************************/
/* fonction mission_releve_familier                          */
/*   Actions à exécuter lorsqu’une mission                   */
/*   de type « familier » est relevée par un perso           */
/*   on passe en paramètres :                                */
/*   $1 = mpf_cod : l’identifiant de l’instance de mission   */
/*   $2 = perso_cod : le perso qui prend la mission          */
/* on a en sortie true ou false suivant que ça se soit bien  */
/* passé ou non.                                             */
/*************************************************************/
/* Créé le 13/12/2013                                        */
/*************************************************************/
declare
	v_mpf_cod alias for $1;      -- Le code de l’instance de mission
	v_personnage alias for $2;   -- Le perso qui relève la mission

	v_familier integer;          -- Le perso_cod du familier
	v_resultat_creation text;    -- Le résultat de la création de familier
	v_fam_statut text;           -- 0 si OK, 1 si NOK
	v_gmon_cod integer;          -- Le gmon_cod du familier
	v_etage integer;             -- L’étage où l’on se place

begin
	-- Détermination de la race du familier
	select into v_etage mpf_etage_numero from mission_perso_faction_lieu where mpf_cod = v_mpf_cod;
	select into v_gmon_cod choix_monstre_etage(v_etage, 0);

	-- Création du familier
	select into v_resultat_creation ajoute_familier(v_gmon_cod, v_personnage);

	v_fam_statut := split_part(v_resultat_creation, ';', 1)::integer;
	if (v_fam_statut = '1') then
		return false;
	end if;
	v_familier := split_part(v_resultat_creation, ';', 2)::integer;

	-- Mise à jour de la mission
	update mission_perso_faction_lieu
	set mpf_cible_perso_cod = v_familier
	where mpf_cod = v_mpf_cod;

	return found;
end;$_$;


ALTER FUNCTION public.mission_releve_familier(integer, integer) OWNER TO delain;

--
-- Name: FUNCTION mission_releve_familier(integer, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION public.mission_releve_familier(integer, integer) IS 'Sert à créer le familier qui doit être escorté lorsqu’un perso relève une mission de type « familier »';
