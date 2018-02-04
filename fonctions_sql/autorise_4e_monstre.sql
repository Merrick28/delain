--
-- Name: autorise_4e_monstre(character, timestamp with time zone); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function autorise_4e_monstre(character, timestamp with time zone) RETURNS boolean
LANGUAGE plpgsql
AS $_$/********************************************************/
/* function autorise_4e_monstre                               */
/* Indique si le compte donné a droit à un quatrième perso    */
/* sous forme de monstre                                      */
/* Cette fonction permet de centraliser le test.              */
/* parametres :                                               */
/*  $1 = compt_quatre_perso du compte à tester                */
/*  $2 = compt_dcreat du compte à tester                      */
/* Sortie :                                                   */
/*  code_retour = True ou False                               */
/**************************************************************/
/**************************************************************/
/* Création - 16/10/2012 - Reivax                             */
/**************************************************************/
declare
  compt_quatre_perso alias for $1; -- compt_cod
  compt_dcreat alias for $2;       -- compt_cod
  delai interval;                  -- le temps de jeu nécessaire pour créer un quatrième perso-monstre
begin
  /*********************************************************/
  /*        I N I T I A L I S A T I O N S                  */
  /*********************************************************/
  delai := '12 months'::interval;

  /*********************************************************/
  /*             E X É C U T I O N                         */
  /*********************************************************/
  return (compt_quatre_perso = 'O' AND (now() - delai > compt_dcreat));
end;		$_$;


ALTER FUNCTION public.autorise_4e_monstre(character, timestamp with time zone) OWNER TO delain;

--
-- Name: FUNCTION autorise_4e_monstre(character, timestamp with time zone); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION autorise_4e_monstre(character, timestamp with time zone) IS 'Centralise le test sur l’autorisation de jouer un monstre.';

