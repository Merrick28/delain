--
-- Name: cout_pa_combo(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION cout_pa_combo(integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*****************************************************************/
/* function cout_pa_combo : calcul le cout en pa pour la combo   */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = numéro de la combo lancé                               */
/*****************************************************************/
/* Créé le 15/09/2021                                            */
/*****************************************************************/
declare

	code_retour text;				-- chaine html de sortie
	lanceur alias for $1;		-- perso_cod du lanceur
	num_combo alias for $2;		-- numéro de la combo a lancer

begin
-------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
	code_retour := '';


if num_combo = 1 then
    -- combo BS + ATT + MTS = Nombr de PA = 3* sort basique comme BS
    code_retour := (3 * TO_NUMBER(cout_pa_magie(lanceur, 2, 1), '99'))::text;
else
    code_retour := 20 ; -- combo inconnue,, ne peut pas être lancé !
end if;


	return code_retour;
end;
$_$;


ALTER FUNCTION public.cout_pa_combo(integer, integer) OWNER TO delain;