--
-- Name: modif_bonus_equipement(integer, text, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function modif_bonus_equipement(integer, text, integer, integer) RETURNS integer
LANGUAGE plpgsql
AS $_$-- Rajoute un bonus d'équipement à un perso
-- $1 = Le code du perso en question
-- $2 = le type de modification: C (create), U (update), D (delete)
-- $3 = le code du bonus l'objet concerné
-- $4 = le code du bonus l'objet concerné par la modif

--
-- Si un BM est modifié sur un objet, il faut répliquer cette modification sur les bonus du joueurs

declare
  v_perso alias for $1;
  v_type_modif alias for $2;
  v_objbm_cod alias for $3;
  v_obj_cod alias for $4;

  v_type text;
  v_valeur integer;
  code_retour text;
begin

  -- récupération de la nouvelle valeur de bonus (et du type de bonus)
  select tbonus_libc, objbm_bonus_valeur into v_type, v_valeur from objets_bm join bonus_type on tbonus_cod=objbm_tbonus_cod where objbm_cod=v_objbm_cod ;


  IF v_type in ('DEX', 'INT', 'FOR', 'CON')  then

    -- cas d'un bonus de carac ---------------------------------------------------------------

    if v_type_modif = 'U' then

      -- mise à jour des bonus carac
      update carac_orig set corig_valeur=v_valeur where corig_perso_cod=v_perso and corig_objbm_cod=v_objbm_cod ;

      -- remise des caracs en état après la modification du bonus
      perform f_modif_carac_perso(v_perso, v_type);


    elsif v_type_modif = 'C' then

      -- ajout du bonus de carac
      insert into carac_orig(corig_perso_cod, corig_type_carac, corig_carac_valeur_orig, corig_valeur, corig_mode, corig_obj_cod, corig_objbm_cod)
      values (v_perso, v_type, f_carac_base(v_perso, v_type), v_valeur, 'E', v_obj_cod, v_objbm_cod);

      -- remise des caracs en état après la modification du bonus
      perform f_modif_carac_perso(v_perso, v_type);


    elsif v_type_modif = 'D' then

      -- NE PAS SUPPRIMER sinon on perd la valeur de la carac origine !!! On met à 0 tour et f_remise_caracs() se charge de faire le boulot!
      update carac_orig set corig_nb_tours=0 where corig_perso_cod=v_perso and corig_objbm_cod=v_objbm_cod ;

      -- remise des caracs en état après la suppression  (s'il y a eu des supressions)
      perform f_remise_caracs(v_perso) ;

    end if;


  ELSE

    -- cas des bonus standards  ---------------------------------------------------------------

    if v_type_modif = 'U' then

      -- mise à jour du bonus normal
      update bonus set bonus_valeur=v_valeur where bonus_perso_cod=v_perso and bonus_objbm_cod=v_objbm_cod ;

    elsif v_type_modif = 'C' then

     -- ajout du bonus normal
      insert into bonus (bonus_perso_cod, bonus_tbonus_libc, bonus_valeur, bonus_mode, bonus_obj_cod, bonus_objbm_cod)  values (v_perso, v_type, v_valeur, 'E', v_obj_cod, v_objbm_cod);


    elsif v_type_modif = 'D' then

      -- supression du bonus normal
      delete from bonus where bonus_perso_cod=v_perso and bonus_objbm_cod=v_objbm_cod ;

    end if;

  END IF;


  return 0;
end;$_$;


ALTER FUNCTION public.modif_bonus_equipement(integer, text, integer, integer) OWNER TO delain;
