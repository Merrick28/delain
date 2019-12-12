--
-- Name: f_modif_carac_base(integer, text, text, integer, integer, text); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION f_modif_carac_base(integer, text, text, integer, integer, text) RETURNS integer
    LANGUAGE plpgsql
    AS $_$/*************************************************/
/* fonction f_modif_carac_base                   */
/*-----------------------------------------------*/
/* paramètres :                                  */
/* $1 = perso_cod                                */
/* $2 = type carac                               */
/*   possibles : FOR, DEX, INT et CON            */
/*   attention : majuscules !                    */
/* $3 = H (pour Heure) ou 'T' (pour tour)        */
/* $4 = nb d’heure/de tour/ ou code obj si Equip.*/
/* $5 = modificateur à mettre                    */
/* $6 = S/C si bonus Standard ou cumulatif       */
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
	v_type_delai alias for $3;
	v_temps alias for $4;
	v_modificateur alias for $5;
	v_cumulatif alias for $6;

	temp integer;	-- variable fourre tout

	v_corig_cod integer;
	v_corig_valeur integer;
	v_carac_actuelle integer;
	v_carac_base integer;
	v_temps_inter interval;
	v_pv integer;
	temp_tue text;

begin
	code_retour := 'OK';
  v_temps_inter := null; -- par défaut (bonus en tour pas en heure)

	--
	-- on fait d’abord les contrôles possibles
	--
	select into temp perso_cod from perso where perso_cod = personnage;
	if not found then
		return 0 ;
	end if;

  if v_type_carac not in ('DEX', 'INT', 'FOR', 'CON')  then
    return 0 ;
	end if;

	if v_temps = 0 then
		return 0 ;
	end if;

  if v_type_delai = 'H' then
    v_temps_inter := trim(to_char(v_temps,'999999999'))||' hours';
  end if;



  --
  -- on regarde s’il y a déjà quelque chose (seulement pour le cas Standard)! En Equipement et en Cumulatif c'est toujours un nouveau bonus/malus!
  --
  select into v_corig_cod, v_corig_valeur corig_cod, corig_valeur from carac_orig where corig_perso_cod = personnage and corig_type_carac = v_type_carac and corig_mode ='S' and v_cumulatif = 'S';
  if found then
    -- update d'un bonus (en mode Standard)

    if v_type_delai = 'H' then
      update carac_orig set corig_dfin = now() + v_temps_inter, corig_nb_tours = null, corig_valeur = v_modificateur  where corig_cod = v_corig_cod;
    else
      update carac_orig set corig_dfin = null, corig_nb_tours=v_temps, corig_valeur = v_modificateur  where corig_cod = v_corig_cod;
    end if;

  else
    -- insertion du nouveau bonus (mode cumulatif)

    if v_type_delai = 'H' then
      insert into carac_orig(corig_perso_cod, corig_type_carac, corig_carac_valeur_orig, corig_dfin, corig_valeur, corig_mode)
      values (personnage, v_type_carac, f_carac_base(personnage, v_type_carac), now() + v_temps_inter, v_modificateur, v_cumulatif);
    else
      insert into carac_orig(corig_perso_cod, corig_type_carac, corig_carac_valeur_orig, corig_nb_tours, corig_valeur, corig_mode)
      values (personnage, v_type_carac, f_carac_base(personnage, v_type_carac), v_temps, v_modificateur, v_cumulatif);
    end if;

  end if;

  -- Maintenant que le bonus a été inséré, on applique réellement les modifications sur la carac du perso (en respectant les contraintes de limite min/max paramétrées)
  code_retour := f_modif_carac_perso(personnage, v_type_carac);

	return code_retour;

end;$_$;


ALTER FUNCTION public.f_modif_carac_base(integer, text, text, integer, integer, text) OWNER TO delain;

--
-- Name: FUNCTION f_modif_carac_base(integer, text, text, integer, integer, text); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION f_modif_carac_base(integer, text, text, integer, integer, text) IS 'Modifie de façon temporaire une caractéristique primaire (CON, FOR, INT, DEX)
$1 = perso_cod ; $2 IN (''CON'', ''FOR'', ''INT'', ''DEX'') ; $3 = H ou T ; $4 = durée en heures ; $5 = valeur du bonus / malus. ; $6 = S/C (Standard ou Cumulatif)';

