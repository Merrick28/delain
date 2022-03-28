--
-- Name: pos_aleatoire_ref(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION public.pos_aleatoire_ref(integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$/*****************************************************************/
/* function pos_aleatoire_ref : retourne un pos_cod aléatoire en */
/*    fonction de l etage passé en paramètre, sur les étages     */
/*    ayant la même référence.                                   */
/* On passe en paramètres                                        */
/*    $1 = etage                                                 */
/* Le code sortie est un entier (pos_cod)                        */
/*****************************************************************/
/* Créé le 02/04/2003                                            */
/* Liste des modifications :                                     */
/*   06/05/2003 : ajout du contrôle pour les murs                */
/*   06/05/2003 : ajout du contrôle pour le zéro                 */
/*****************************************************************/
declare
	code_retour integer;
	v_etage alias for $1;
begin
	code_retour := 62514; -- Au proving ground par défaut

	select into code_retour
		pos_cod
	from positions, etage
	where etage_reference = v_etage
		and etage_retour_rune_monstre != 100
		and pos_etage = etage_numero
		and (pos_etage <> 0
			or pos_x between -20 and 20
				and pos_y between -20 and 20)
		and not exists
			(select 1 from murs
			where mur_pos_cod = pos_cod)
		and not exists
			(select 1 from lieu_position
			where lpos_pos_cod = pos_cod)
    and pos_modif_pa_dep < 10
	order by random()
	limit 1;

	return code_retour;
end;$_$;


ALTER FUNCTION public.pos_aleatoire_ref(integer) OWNER TO delain;

--
-- Name: FUNCTION pos_aleatoire_ref(integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION public.pos_aleatoire_ref(integer) IS 'Retourne un pos_cod aléatoire sur un des étages ayant la même référence que l’étage donné.';
