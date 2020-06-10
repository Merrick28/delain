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

  temp integer;
begin

  -- cette fonction est princpalement utilisé par les potions qui donne un bonus sur une base horaire.
  -- la fonction va appeler f_modif_carac_base() qui permet de faire la modification sur base de temps ou de tour.

	--
	-- on fait d’abord les contrôles possibles
	--
	select into temp perso_cod from perso where perso_cod = personnage;
	if not found then
		return 'perso non trouvé.';
	end if;

  if v_type_carac not in ('DEX', 'INT', 'FOR', 'CON')  then
    return 'caractéristique non valide.' ;
	end if;

	if v_temps = 0 then
		return 'delai non valide.' ;
	end if;

	--
  -- appel de la fonction de base avec le type 'H' (nombre d'heure) et non cumulatif (cas standard pour les potions par exemple)
  --
	temp := f_modif_carac_base(personnage, v_type_carac, v_type_carac, 'H', v_temps, v_modificateur, 'S')	;

  -- on retourn OK, suivi du bonus/malus réellement appliqué
	return 'OK;' || temp::text ;

end;$_$;


ALTER FUNCTION public.f_modif_carac(integer, text, integer, integer) OWNER TO delain;

--
-- Name: FUNCTION f_modif_carac(integer, text, integer, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION f_modif_carac(integer, text, integer, integer) IS 'Modifie de façon temporaire une caractéristique primaire (CON, FOR, INT, DEX)
$1 = perso_cod ; $2 IN (''CON'', ''FOR'', ''INT'', ''DEX'') ; $3 = durée en heures ; $4 = valeur du bonus / malus.';

