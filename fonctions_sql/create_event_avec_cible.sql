--
-- Name: create_event_avec_cible(integer, integer, integer, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE OR REPLACE FUNCTION create_event_avec_cible(source integer, cible integer, type_evt integer, texte_evt text) RETURNS void
    LANGUAGE plpgsql
    AS $_$/**********************************************************/
/* create_event_avec_cible : cree un event ciblé          */
/* on passe en paramètres :                               */
/*   $1 = le perso_cod de la source                       */
/*   $2 = le perso_cod de la cible                        */
/*   $1 = le type d'event                                 */
/*   $1 = le texte de l'event                             */
/**********************************************************/

declare
	source alias for $1;		-- perso_cod de la source

	cible alias for $2;			-- perso_cod de la cible

	type_evt alias for $3; -- type d event

	texte_evt alias for $4; -- texte de l event
begin
	-- EVENT SOURCE
	insert into ligne_evt(
		levt_cod, levt_tevt_cod, levt_date, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant,	levt_cible)
     values(
		nextval('seq_levt_cod'), type_evt, now(), source, texte_evt, 'O', 'O', source, cible);
	-- EVENT CIBLE
	if (lanceur != cible) then
	insert into ligne_evt(
		levt_cod, levt_tevt_cod, levt_date, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant,	levt_cible)
     values(
		nextval('seq_levt_cod'), type_evt, now(), cible, texte_evt, 'N', 'O', source, cible);
   end if;
end;
$_$;


ALTER FUNCTION public.create_event_avec_cible(source integer, cible integer, type_evt integer, texte_evt text) OWNER TO postgres;
