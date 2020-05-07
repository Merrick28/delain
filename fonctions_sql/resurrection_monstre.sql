--
-- Name: resurrection_monstre(integer, integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE  FUNCTION public.resurrection_monstre(integer, integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*************************************************************/
/* fonction resurrection_monstre                             */
/*   Recrée un monstre du type donné à la mort du monstre,   */
/*    dans le même étage.                                    */
/*   on passe en paramètres :                                */
/*   $1 = perso_cod : le perso_cod du monstre mort           */
/*   $2 = nombre : le nombre de rejetons à créer             */
/*   $3 = gmon_cod : le type de rejetons à créer             */
/*   $4 = chance : la proba que chaque monstre apparaisse    */
/* on a en sortie un message texte vide                      */
/*************************************************************/
/* Créé le 20/05/2014                                        */
/*************************************************************/
declare
	v_perso_cod alias for $1;  -- Le code du monstre générant ses rejetons
	v_nombre alias for $2;     -- Le nombre de rejetons
	v_gmon_cod alias for $3;   -- Le type de rejetons à créer
	v_chance alias for $4;     -- La proba que chaque monstre apparaisse

	code_retour text;          -- Le retour de la fonction
	v_etage_cod integer;         -- La position du monstre parent
	v_compteur integer;        -- Compteur de boucle
	v_code_monstre integer;    -- Le code du monstre créé

begin
	code_retour := '';

	select into v_etage_cod pos_etage from perso_position
	inner join positions on pos_cod = ppos_pos_cod
	where ppos_perso_cod = v_perso_cod;

	for v_compteur in 1..v_nombre loop
		if lancer_des(1, 100) <= v_chance then
			v_code_monstre := cree_monstre_hasard(v_gmon_cod, v_etage_cod);
		end if;
	end loop;

	return code_retour;
end;$_$;


ALTER FUNCTION public.resurrection_monstre(integer, integer, integer, integer) OWNER TO delain;

--
-- Name: FUNCTION resurrection_monstre(integer, integer, integer, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION public.resurrection_monstre(integer, integer, integer, integer) IS 'Recrée un ou plusieurs monstres du type donné à la mort du monstre déclencheur, dans le même étage.';

