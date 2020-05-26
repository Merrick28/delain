--
-- Name: ajoute_bonus_equipement(integer, text, integer, integer, numeric); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function ajoute_bonus_equipement(integer, text, integer, integer, numeric) RETURNS integer
LANGUAGE plpgsql
AS $_$-- Rajoute un bonus d'équipement à un perso
-- $1 = Le code du perso en question
-- $2 = Le type de bonus
-- $3 = le code du bonus
-- $4 = le code de l'objet qui donne ce bonus
-- $5 = La valeur du bonus

declare
  v_perso alias for $1;
  v_type alias for $2;
  v_objbm_cod alias for $3;
  v_obj_cod alias for $4;
  v_valeur alias for $5;

	temp integer;	-- variable fourre tout
	v_valeur_avant integer;	-- BM avant l'ajout
	v_valeur_apres integer;	-- BM après l'ajout
  code_retour text;
begin

  -- Pour déclenchement d'EA sur changement de BM, on mémorise la valeur avant modification.
  v_valeur_avant := valeur_bonus(v_perso, v_type);

  -- 08/11/2019 - Marlyza - Ajout de bonus de carac (FOR/INT/DEX/CON) à l'aide de f_modif_carac
  if v_type in ('DEX', 'INT', 'FOR', 'CON')  then

    -- la fontion ajoute_bonus_equipement() retourne un texte, mais pas de code d'erreur, on va ignorer le resultat de f_modif_carac_base()
    -- perform f_modif_carac_base(v_perso, v_type, 'T', v_obj_cod, v_valeur::integer, 'E')	;

    -- cas d'un bonus de carac en mode équipement !--
    insert into carac_orig(corig_perso_cod, corig_type_carac, corig_carac_valeur_orig, corig_valeur, corig_mode, corig_obj_cod, corig_objbm_cod)
    values (v_perso, v_type, f_carac_base(v_perso, v_type), v_valeur, 'E', v_obj_cod, v_objbm_cod);

    -- appliquer réellement les modifications sur la carac du perso (en respectant les contraintes de limite min/max paramétrées)
    perform f_modif_carac_perso(v_perso, v_type);

  else

    -- cas des bonus standards en mode équipement !--
    insert into bonus (bonus_perso_cod, bonus_tbonus_libc, bonus_valeur, bonus_mode, bonus_obj_cod, bonus_objbm_cod)  values (v_perso, v_type, v_valeur, 'E', v_obj_cod, v_objbm_cod);

  end if;

  -- On vérifie s'il y a un changement de BM, si oui on verifie le déclenchement des EA
  v_valeur_apres := valeur_bonus(v_perso, v_type);
  if v_valeur_apres != v_valeur_avant then
      perform execute_fonctions(v_perso, null, 'BMC', json_build_object('bonus_type', v_type, 'valeur_avant', v_valeur_avant, 'valeur_apres', v_valeur_apres) );
  end if;

  return 1;
end;$_$;


ALTER FUNCTION public.ajoute_bonus_equipement(integer, text, integer, integer, numeric) OWNER TO delain;
