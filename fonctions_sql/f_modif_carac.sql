--
-- Name: f_modif_carac(integer, text, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION f_modif_carac(integer, text, integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*************************************************/
/* fonction f_modif_carac                        */
/*-----------------------------------------------*/
/* paramètres :                                  */
/* $1 = perso_cod                                */
/* $2 = type carac                               */
/*   possibles : FOR, DEX, INT et CON            */
/*   attention : majuscules !                    */
/* $3 = nombre d’heures                          */
/* $4 = modificateur à mettre                    */
/*-----------------------------------------------*/
/* code retour : texte                           */
/*  si tout bon, on sort 'OK'                    */
/*  sinon, message d’erreur complet              */
/*-----------------------------------------------*/
/* créé le 19/10/2006 par Merrick                */
/*************************************************/
declare
	code_retour text;
	personnage alias for $1;
	v_type_carac alias for $2;
	v_temps alias for $3;
	v_modificateur alias for $4;

begin

  -- appel de la fonction de base avec le type 'H' (nombre d'heure)
	select into code_retour f_modif_carac_base(personnage, v_type_carac, 'H', v_temps, v_modificateur)	;

	return code_retour;

end;$_$;


ALTER FUNCTION public.f_modif_carac(integer, text, integer, integer) OWNER TO delain;

--
-- Name: FUNCTION f_modif_carac(integer, text, integer, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION f_modif_carac(integer, text, integer, integer) IS 'Modifie de façon temporaire une caractéristique primaire (CON, FOR, INT, DEX)
$1 = perso_cod ; $2 IN (''CON'', ''FOR'', ''INT'', ''DEX'') ; $3 = durée en heures ; $4 = valeur du bonus / malus.';

